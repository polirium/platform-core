<?php

namespace Polirium\Core\Media\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Polirium\Core\Media\Models\Media;

class CleanupOrphanedMedia extends Command
{
    protected $signature = 'media:cleanup
        {--dry-run : Show what would be deleted without actually deleting}
        {--files : Clean orphaned physical files only}
        {--records : Clean orphaned DB records only}';

    protected $description = 'Clean up orphaned media files and database records';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $cleanFiles = $this->option('files');
        $cleanRecords = $this->option('records');

        // If neither specified, do both
        if (! $cleanFiles && ! $cleanRecords) {
            $cleanFiles = true;
            $cleanRecords = true;
        }

        $disk = config('media.default_disk', 'public');
        $storage = Storage::disk($disk);

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No files will be deleted');
            $this->newLine();
        }

        // 1. Find and clean orphaned DB records (records without physical files)
        if ($cleanRecords) {
            $this->info('📋 Checking for orphaned database records...');
            $orphanedRecords = [];

            Media::chunk(100, function ($mediaItems) use ($storage, &$orphanedRecords) {
                foreach ($mediaItems as $media) {
                    $path = $media->getPath();
                    if (! $storage->exists($path)) {
                        $orphanedRecords[] = $media;
                    }
                }
            });

            if (count($orphanedRecords) > 0) {
                $this->warn('Found ' . count($orphanedRecords) . ' orphaned DB records:');
                foreach ($orphanedRecords as $record) {
                    $this->line("  - ID: {$record->id} | {$record->file_name} | Path: {$record->getPath()}");
                }

                if (! $dryRun) {
                    if ($this->confirm('Delete these orphaned records from database?')) {
                        foreach ($orphanedRecords as $record) {
                            $record->delete();
                        }
                        $this->info('✓ Deleted ' . count($orphanedRecords) . ' orphaned records');
                    }
                }
            } else {
                $this->info('✓ No orphaned DB records found');
            }
            $this->newLine();
        }

        // 2. Find and clean orphaned files (files without DB records)
        if ($cleanFiles) {
            $this->info('📁 Checking for orphaned physical files...');

            // Get all file paths from DB
            $dbPaths = Media::all()->map(fn ($m) => $m->getPath())->toArray();

            // Scan storage for all files
            $orphanedFiles = [];
            $this->scanDirectory($storage, '', $dbPaths, $orphanedFiles);

            if (count($orphanedFiles) > 0) {
                $this->warn('Found ' . count($orphanedFiles) . ' orphaned physical files:');
                $totalSize = 0;
                foreach ($orphanedFiles as $file) {
                    $size = $storage->size($file);
                    $totalSize += $size;
                    $this->line("  - {$file} (" . $this->formatBytes($size) . ')');
                }
                $this->line('  Total: ' . $this->formatBytes($totalSize));

                if (! $dryRun) {
                    if ($this->confirm('Delete these orphaned files from disk?')) {
                        foreach ($orphanedFiles as $file) {
                            $storage->delete($file);
                        }
                        $this->info('✓ Deleted ' . count($orphanedFiles) . ' orphaned files');

                        // Clean up empty directories
                        $this->cleanEmptyDirectories($storage, '');
                        $this->info('✓ Cleaned up empty directories');
                    }
                }
            } else {
                $this->info('✓ No orphaned physical files found');
            }
        }

        $this->newLine();
        $this->info('🎉 Cleanup complete!');

        return 0;
    }

    protected function scanDirectory($storage, $directory, array $dbPaths, array &$orphanedFiles)
    {
        // Skip system directories
        $skipDirs = ['livewire-tmp', '.gitignore'];

        $files = $storage->files($directory);
        foreach ($files as $file) {
            // Skip .gitignore and similar
            if (str_ends_with($file, '.gitignore')) {
                continue;
            }

            if (! in_array($file, $dbPaths)) {
                $orphanedFiles[] = $file;
            }
        }

        $directories = $storage->directories($directory);
        foreach ($directories as $dir) {
            $dirName = basename($dir);
            if (in_array($dirName, $skipDirs)) {
                continue;
            }

            $this->scanDirectory($storage, $dir, $dbPaths, $orphanedFiles);
        }
    }

    protected function cleanEmptyDirectories($storage, $directory)
    {
        $directories = $storage->directories($directory);

        foreach ($directories as $dir) {
            // Recursively clean subdirectories first
            $this->cleanEmptyDirectories($storage, $dir);

            // Check if directory is now empty
            $files = $storage->files($dir);
            $subdirs = $storage->directories($dir);

            if (count($files) === 0 && count($subdirs) === 0) {
                $storage->deleteDirectory($dir);
            }
        }
    }

    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

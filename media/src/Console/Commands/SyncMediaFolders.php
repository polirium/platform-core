<?php

namespace Polirium\Core\Media\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Polirium\Core\Media\Models\MediaFolder;

class SyncMediaFolders extends Command
{
    protected $signature = 'media:sync-folders {--dry-run : Show what would be synced without making changes}';
    protected $description = 'Sync existing physical folders to the media_folders table';

    public function handle(): int
    {
        $disk = config('media.default_disk', 'public');
        $dryRun = $this->option('dry-run');

        // Ensure 'uploads' root folder exists
        if (!Storage::disk($disk)->exists('uploads')) {
            if (!$dryRun) {
                Storage::disk($disk)->makeDirectory('uploads');
            }
            $this->info('Created uploads root folder');
        }

        // Get all directories under uploads
        $directories = Storage::disk($disk)->allDirectories('uploads');

        $this->info('Found ' . count($directories) . ' physical folders under uploads/');

        $created = 0;
        $skipped = 0;

        foreach ($directories as $path) {
            // Check if folder already exists in DB
            $exists = MediaFolder::where('path', $path)->exists();

            if ($exists) {
                $this->line("  [SKIP] {$path} - already in DB");
                $skipped++;
                continue;
            }

            // Determine parent
            $parentPath = dirname($path);
            $parentFolder = null;

            if ($parentPath !== 'uploads' && $parentPath !== '.') {
                $parentFolder = MediaFolder::where('path', $parentPath)->first();
            }

            $name = basename($path);

            if (!$dryRun) {
                MediaFolder::create([
                    'name' => $name,
                    'path' => $path,
                    'parent_id' => $parentFolder?->id,
                ]);
            }

            $this->info("  [CREATE] {$path}");
            $created++;
        }

        $this->newLine();
        $this->info("Summary: {$created} folders created, {$skipped} skipped");

        if ($dryRun) {
            $this->warn('This was a dry run. No changes were made.');
        }

        return Command::SUCCESS;
    }
}

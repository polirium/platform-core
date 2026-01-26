<?php

namespace Polirium\Core\Media\Commands;

use Illuminate\Console\Command;
use Polirium\Core\Media\Models\Media;
use Illuminate\Support\Facades\Storage;

/**
 * Cleanup orphaned media records from database
 *
 * This command removes media records where the actual file
 * no longer exists in storage, preventing 404 errors in the UI.
 */
class CleanupOrphanedMedia extends Command
{
    protected $signature = 'media:cleanup-orphaned {--force}';
    protected $description = 'Remove orphaned media records (files not in storage)';

    public function handle()
    {
        $this->info('Scanning for orphaned media records...');

        // Get all media records
        $allMedia = Media::all();
        $orphaned = [];
        $valid = [];

        $disk = config('media.default_disk', 'public');

        foreach ($allMedia as $media) {
            $path = $media->getPath();

            if (!Storage::disk($media->disk ?? 'public')->exists($path)) {
                $orphaned[] = [
                    'id' => $media->id,
                    'uuid' => $media->uuid,
                    'file_name' => $media->file_name,
                    'file_path' => $path,
                    'disk' => $media->disk,
                ];
                $this->line("  ❌ Orphaned: {$media->file_name} (ID: {$media->id})");
            } else {
                $valid[] = [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                ];
            }
        }

        $this->newLine();
        $this->info('Summary:');
        $this->line("  Total records: " . count($allMedia));
        $this->line("  Valid files: " . count($valid));
        $this->line("  Orphaned: " . count($orphaned));

        if (count($orphaned) === 0) {
            $this->info('✅ No orphaned records found!');
            return;
        }

        $this->newLine();
        $this->warn("Found " . count($orphaned) . " orphaned records:");

        if (!$this->option('force')) {
            $this->newLine();
            $confirm("Run this command to remove orphaned records? (y/n)");
        }

        // Delete orphaned records
        $deleted = 0;
        foreach ($orphaned as $item) {
            $media = Media::find($item['id']);
            if ($media) {
                $media->delete();
                $deleted++;
                $this->line("  🗑️  Deleted: {$item['file_name']}");
            }
        }

        $this->newLine();
        $this->info("✅ Deleted {$deleted} orphaned records!");
        $this->info('✅ Media manager will now only show valid media files.');
    }
}

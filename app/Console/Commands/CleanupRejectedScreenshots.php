<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupRejectedScreenshots extends Command
{
    protected $signature = 'payments:cleanup-rejected {--days=7 : Delete files older than this many days}';
    protected $description = 'Deletes screenshots in payment-screenshots/reject/ older than N days';

    public function handle(): int
    {
        $days  = (int) $this->option('days');
        $cutoff = now()->subDays($days);
        $disk   = Storage::disk('local');

        $files = $disk->files('payment-screenshots/reject');
        $deleted = 0;

        foreach ($files as $file) {
            $modifiedAt = $disk->lastModified($file);

            if ($modifiedAt && now()->createFromTimestamp($modifiedAt)->lt($cutoff)) {
                $disk->delete($file);
                $deleted++;
            }
        }

        $this->info("Deleted {$deleted} rejected screenshot(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}
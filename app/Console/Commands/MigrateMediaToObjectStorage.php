<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Copies files that were written to a container-local disk into the
 * object-storage-backed disk.
 *
 * Switching MEDIA_DISK does not move anything: Spatie MediaLibrary keeps
 * resolving the same relative paths, it just resolves them against a
 * different disk. Every photo seeded or uploaded before the switch would
 * therefore 404 until its bytes exist at the same path on the new disk.
 * This command is what puts them there.
 *
 * It is safe to run repeatedly -- a file already present at the destination
 * with the same size is skipped -- and it never deletes from the source, so
 * a rollback is only an env var away.
 */
class MigrateMediaToObjectStorage extends Command
{
    protected $signature = 'media:migrate-to-object-storage
        {--from=public : Disk to copy files from}
        {--to=media : Disk to copy files to}
        {--dry-run : List what would be copied without writing anything}';

    protected $description = 'Copy media files from a local disk into object storage';

    public function handle(): int
    {
        $fromName = (string) $this->option('from');
        $toName = (string) $this->option('to');
        $dryRun = (bool) $this->option('dry-run');

        if ($fromName === $toName) {
            $this->error("Source and destination are the same disk ({$fromName}). Nothing to do.");

            return self::FAILURE;
        }

        try {
            $from = Storage::disk($fromName);
            $to = Storage::disk($toName);
        } catch (Throwable $e) {
            $this->error("Could not open a disk: {$e->getMessage()}");

            return self::FAILURE;
        }

        $files = $from->allFiles();

        if ($files === []) {
            $this->info("Nothing on the [{$fromName}] disk to copy.");

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%s %d file(s) from [%s] to [%s]%s',
            $dryRun ? 'Would copy' : 'Copying',
            count($files),
            $fromName,
            $toName,
            $dryRun ? ' (dry run)' : '',
        ));

        $copied = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($files as $file) {
            // A file already there at the same size is the same file: these
            // are content-addressed upload paths, never rewritten in place.
            if ($to->exists($file) && $to->size($file) === $from->size($file)) {
                $skipped++;

                continue;
            }

            if ($dryRun) {
                $this->line("  would copy: {$file}");
                $copied++;

                continue;
            }

            try {
                $stream = $from->readStream($file);

                if ($stream === false || $stream === null) {
                    throw new \RuntimeException('could not open the source file for reading');
                }

                $to->writeStream($file, $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }

                $copied++;
            } catch (Throwable $e) {
                $this->error("  failed: {$file} -- {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Copied: {$copied}   Already present: {$skipped}   Failed: {$failed}");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}

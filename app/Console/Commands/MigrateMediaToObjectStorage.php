<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        if ($failed > 0) {
            $this->error('Some files did not copy -- not repointing the media table, since a row whose bytes are missing would render a broken image.');

            return self::FAILURE;
        }

        $this->repointMediaRecords($fromName, $toName, $dryRun);

        return self::SUCCESS;
    }

    /**
     * Points existing MediaLibrary records at the new disk.
     *
     * Copying the bytes is only half the job. Spatie stores the disk name on
     * every `media` row, so changing MEDIA_DISK affects new uploads only --
     * everything already in the library keeps resolving through whichever
     * disk it was created on. Without this, the files sit in the bucket
     * while the site carries on serving them from the container, and the
     * migration silently accomplishes nothing.
     *
     * Only ever runs after a clean copy: a row pointed at a disk that does
     * not hold its bytes is a broken image on a page.
     */
    private function repointMediaRecords(string $fromName, string $toName, bool $dryRun): void
    {
        if (! Schema::hasTable('media')) {
            $this->line('No media table -- nothing to repoint.');

            return;
        }

        $originals = DB::table('media')->where('disk', $fromName)->count();
        $conversions = Schema::hasColumn('media', 'conversions_disk')
            ? DB::table('media')->where('conversions_disk', $fromName)->count()
            : 0;

        if ($originals === 0 && $conversions === 0) {
            $this->line("No media records still point at [{$fromName}].");

            return;
        }

        if ($dryRun) {
            $this->line("Would repoint {$originals} record(s) and {$conversions} conversion disk(s) from [{$fromName}] to [{$toName}].");

            return;
        }

        DB::table('media')->where('disk', $fromName)->update(['disk' => $toName]);

        if ($conversions > 0) {
            DB::table('media')->where('conversions_disk', $fromName)->update(['conversions_disk' => $toName]);
        }

        $this->info("Repointed {$originals} media record(s) and {$conversions} conversion disk(s) to [{$toName}].");
    }
}

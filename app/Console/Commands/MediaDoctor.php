<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Reports whether media delivery is actually wired correctly.
 *
 * Object storage has a failure mode that no test suite can reach: the disk,
 * the route and the URL are each correct in isolation, but the deployed
 * MEDIA_URL makes them disagree, and the only symptom is that every photo
 * 404s in a browser nobody has opened yet. That already happened once here
 * -- Laravel's S3 adapter appends the disk root to MEDIA_URL, so a value of
 * https://host/media produced https://host/media/media/...
 *
 * This runs inside the container, against the real configuration, and
 * prints the URL it would put in an <img> tag. Reading one line of a deploy
 * log then answers the question that otherwise needs a browser.
 */
class MediaDoctor extends Command
{
    protected $signature = 'media:doctor';

    protected $description = 'Check that media storage and its public URLs are configured coherently';

    public function handle(): int
    {
        // MediaLibrary's own default when MEDIA_DISK is unset, with the
        // framework default as a last resort so this never inspects a disk
        // name of null.
        $diskName = config('media-library.disk_name') ?: config('filesystems.default');
        $this->line("MediaLibrary disk: {$diskName}");

        $driver = config("filesystems.disks.{$diskName}.driver");
        $this->line("Driver:            {$driver}");

        if ($driver !== 's3') {
            $this->info('Local disk in use -- object storage is off. Nothing further to check.');

            return self::SUCCESS;
        }

        $root = config("filesystems.disks.{$diskName}.root");
        $configuredUrl = config("filesystems.disks.{$diskName}.url");
        $this->line("Bucket prefix:     {$root}");
        $this->line("Configured URL:    {$configuredUrl}");

        if (blank($configuredUrl)) {
            $this->error('MEDIA_URL is empty. Media URLs cannot be built.');

            return self::FAILURE;
        }

        $disk = Storage::disk($diskName);

        try {
            $files = $disk->files();
            $sample = $files[0] ?? null;

            if ($sample === null) {
                // Look one level down: media paths are usually nested.
                foreach ($disk->directories() as $directory) {
                    $nested = $disk->files($directory);

                    if ($nested !== []) {
                        $sample = $nested[0];
                        break;
                    }
                }
            }
        } catch (Throwable $e) {
            $this->error("Could not list the bucket: {$e->getMessage()}");

            return self::FAILURE;
        }

        if ($sample === null) {
            $this->warn('The bucket is reachable but empty -- nothing to build a URL from.');

            return self::SUCCESS;
        }

        $url = $disk->url($sample);
        $this->newLine();
        $this->line("Sample object:     {$sample}");
        $this->line("Public URL:        {$url}");

        // The URL must land back on this application's /media route with the
        // object's own key after the prefix -- that is the whole contract.
        $expected = rtrim($configuredUrl, '/').'/'.trim((string) $root, '/').'/'.$sample;

        if ($url !== $expected) {
            $this->error("URL does not match the expected shape. Expected: {$expected}");

            return self::FAILURE;
        }

        $path = ltrim((string) parse_url($url, PHP_URL_PATH), '/');
        $prefix = trim((string) $root, '/').'/';

        if (! str_starts_with($path, $prefix)) {
            $this->error("The URL path does not start with the route prefix [{$prefix}].");

            return self::FAILURE;
        }

        $key = substr($path, strlen($prefix));

        if ($key !== $sample) {
            $this->error("The route would resolve [{$key}], not [{$sample}]. MEDIA_URL likely has a path appended.");

            return self::FAILURE;
        }

        try {
            $signed = $disk->temporaryUrl($sample, now()->addMinutes(5));
        } catch (Throwable $e) {
            $this->error("Could not sign a URL: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->line('Signed URL host:   '.parse_url($signed, PHP_URL_HOST));
        $this->newLine();
        $this->info('Media delivery is configured coherently.');

        return self::SUCCESS;
    }
}

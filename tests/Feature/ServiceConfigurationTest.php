<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ServiceConfigurationTest extends TestCase
{
    /**
     * Every config('services.*') key the application reads must be declared.
     *
     * A key that is merely absent from config/services.php does not fail
     * loudly -- config() hands back null, the service constructs itself with
     * no credentials, and the mistake only surfaces much later as a 401 or a
     * "Failed to generate" exception with no hint of the real cause. That is
     * exactly how services.openai went missing while two AI features shipped
     * on top of it.
     *
     * Declared-but-empty is fine and expected: most of these integrations
     * have no credentials in this product. The requirement is only that the
     * key exists, so its absence is a deliberate state rather than an
     * oversight.
     */
    public function test_every_service_key_read_by_the_app_is_declared(): void
    {
        $missing = [];

        foreach ($this->serviceKeysReferencedInApp() as $key => $files) {
            if (! Config::has('services.'.$key)) {
                $missing[$key] = $files;
            }
        }

        $this->assertSame([], $missing, sprintf(
            "These config('services.*') keys are read by the app but not declared in config/services.php:\n%s",
            collect($missing)
                ->map(fn (array $files, string $key) => "  services.{$key}  <- ".implode(', ', $files))
                ->implode("\n"),
        ));
    }

    /**
     * @return array<string, list<string>> key => files referencing it
     */
    private function serviceKeysReferencedInApp(): array
    {
        $appPath = base_path('app');
        $found = [];

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($appPath, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            if ($contents === false || ! str_contains($contents, 'services.')) {
                continue;
            }

            preg_match_all(
                "/config\(\s*['\"]services\.([a-zA-Z0-9_.]+)['\"]/",
                $contents,
                $matches,
            );

            foreach ($matches[1] as $key) {
                $relative = str_replace(base_path().'/', '', $file->getPathname());
                $found[$key][] = $relative;
                $found[$key] = array_values(array_unique($found[$key]));
            }
        }

        ksort($found);

        return $found;
    }

    public function test_the_ai_provider_key_is_declared(): void
    {
        // Named explicitly because this is the one whose absence actually
        // broke user-facing features.
        $this->assertTrue(Config::has('services.openai.api_key'));
        $this->assertTrue(Config::has('services.openai.chat_model'));
        $this->assertTrue(Config::has('services.openai.base_uri'));
    }
}

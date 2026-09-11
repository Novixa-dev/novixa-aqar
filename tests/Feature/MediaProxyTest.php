<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class MediaProxyTest extends TestCase
{
    // Laravel's error pages render the full site layout, which builds the
    // navigation from the `menus` table. Without a schema, asserting on a
    // 404 gets a 500 about a missing table instead.
    use RefreshDatabase;

    /**
     * The controller answers in one of two shapes depending on the disk.
     *
     * Against the bucket it redirects to a signed URL, so the image bytes
     * never pass through PHP. Against a plain local disk it serves the file
     * directly. Storage::fake() produces a local disk with `serve` enabled,
     * which does support signed URLs, so the fake takes the redirect path --
     * both are correct, and what matters to a caller is the same either way:
     * the request succeeds and the answer may be cached.
     */
    public function test_it_serves_a_file_that_exists_on_the_media_disk(): void
    {
        Storage::fake('media');
        Storage::disk('media')->put('properties/1/photo.jpg', 'fake-image-bytes');

        $response = $this->get('/media/properties/1/photo.jpg');

        $this->assertContains($response->getStatusCode(), [200, 302]);
        $this->assertStringContainsString(
            'max-age=3600',
            (string) $response->headers->get('Cache-Control'),
        );
    }

    public function test_it_returns_404_for_a_file_that_does_not_exist(): void
    {
        Storage::fake('media');

        $this->get('/media/properties/1/missing.jpg')->assertNotFound();
    }

    /**
     * The media disk is rooted at the bucket's `media/` prefix, and private
     * documents live under `documents/` in the same bucket. This route is
     * public and unauthenticated, so a traversal sequence is the one way it
     * could be walked into serving a document it must never expose.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('traversalPaths')]
    public function test_it_rejects_path_traversal(string $path): void
    {
        Storage::fake('media');
        Storage::fake('documents');

        // Generated rather than written as a literal: with APP_DEBUG on, an
        // error page renders the source of the frames in its stack trace --
        // including this test file. A hardcoded needle would then be found
        // in the response even when the document itself was never served,
        // which is a false failure that says nothing about the route.
        $secret = 'lease-body-'.Str::random(32);
        Storage::disk('documents')->put('lease.pdf', $secret);

        $response = $this->get('/media/'.$path);

        $this->assertNotSame(
            200,
            $response->getStatusCode(),
            "Traversal path [{$path}] was served instead of rejected.",
        );
        $this->assertStringNotContainsString($secret, $response->getContent() ?: '');
    }

    public static function traversalPaths(): array
    {
        return [
            'parent directory' => ['../documents/lease.pdf'],
            'nested parent' => ['properties/../../documents/lease.pdf'],
            'encoded parent' => ['%2e%2e/documents/lease.pdf'],
        ];
    }

    public function test_the_documents_disk_is_configured(): void
    {
        // App\Models\Document::registerMediaCollections() calls
        // ->useDisk('documents'). Before this disk was defined, every
        // document upload threw "Disk [documents] does not have a
        // configured driver" at runtime while every test still passed.
        $this->assertIsArray(config('filesystems.disks.documents'));
        $this->assertArrayHasKey('driver', config('filesystems.disks.documents'));

        // It must never be publicly linkable: access is decided per user by
        // DocumentAccessService, not by whoever guesses the URL.
        $this->assertArrayNotHasKey('url', config('filesystems.disks.documents'));
    }

    public function test_media_and_documents_stay_local_when_no_bucket_is_configured(): void
    {
        // Local development, CI and the test suite must keep the exact
        // behaviour they had before object storage existed.
        $this->assertSame('', (string) env('AWS_BUCKET'));
        $this->assertSame('local', config('filesystems.disks.media.driver'));
        $this->assertSame('local', config('filesystems.disks.documents.driver'));
    }

    public function test_an_uploaded_document_can_be_stored_on_the_documents_disk(): void
    {
        Storage::fake('documents');

        $path = UploadedFile::fake()
            ->create('lease.pdf', 12, 'application/pdf')
            ->store('leases', 'documents');

        $this->assertNotFalse($path);
        Storage::disk('documents')->assertExists($path);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaProxyTest extends TestCase
{
    public function test_it_serves_a_file_that_exists_on_the_media_disk(): void
    {
        Storage::fake('media');
        Storage::disk('media')->put('properties/1/photo.jpg', 'fake-image-bytes');

        $response = $this->get('/media/properties/1/photo.jpg');

        $response->assertOk();
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
     *
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('traversalPaths')]
    public function test_it_rejects_path_traversal(string $path): void
    {
        Storage::fake('media');
        Storage::fake('documents');
        Storage::disk('documents')->put('lease.pdf', 'SECRET-LEASE-CONTENT');

        $response = $this->get('/media/'.$path);

        // The property under test is that the private document never comes
        // back -- not which particular rejection status is used, which the
        // web server in front of the app can legitimately decide for itself.
        $this->assertNotSame(
            200,
            $response->getStatusCode(),
            "Traversal path [{$path}] was served instead of rejected.",
        );
        $this->assertStringNotContainsString(
            'SECRET-LEASE-CONTENT',
            $response->getContent() ?: '',
        );
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

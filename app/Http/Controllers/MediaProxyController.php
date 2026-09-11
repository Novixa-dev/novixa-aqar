<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves files from the private media bucket.
 *
 * Railway Buckets -- like most S3-compatible object stores used safely --
 * are private, so a bucket URL cannot be written into an <img> tag. The two
 * usual answers are to stream every byte through the application, or to put
 * an expiring signed URL directly in the HTML. Both are bad here: the first
 * pushes every image through a single Octane worker and turns free bucket
 * egress into paid service egress, and the second produces a URL that
 * changes on every render, so nothing downstream can ever cache an image.
 *
 * This takes the third option. The URL in the HTML is stable and cacheable,
 * and it answers with a redirect to a short-lived signed bucket URL. PHP
 * only computes a signature; the image bytes go from the bucket to the
 * browser directly. The redirect itself carries cache headers, so a warm
 * browser does not come back here at all until they expire.
 */
class MediaProxyController extends Controller
{
    /**
     * How long the signed bucket URL stays valid.
     */
    private const SIGNATURE_TTL_MINUTES = 120;

    /**
     * How long the redirect may be cached.
     *
     * Deliberately shorter than the signature's own lifetime: a cached
     * redirect must never outlive the signature it points at, or a visitor
     * would follow a perfectly good cache entry to an expired URL.
     */
    private const REDIRECT_CACHE_SECONDS = 3600;

    public function __invoke(Request $request, string $path): Response
    {
        $path = $this->normalise($path);

        if ($path === null) {
            abort(404);
        }

        $disk = Storage::disk('media');

        if (! $disk->exists($path)) {
            abort(404);
        }

        // The local disk has no concept of a signed URL. Nothing points here
        // in that configuration, but answering correctly rather than with a
        // 500 keeps a misconfigured environment debuggable.
        if (! $this->supportsTemporaryUrls($disk)) {
            return response()->file($disk->path($path), [
                'Cache-Control' => 'public, max-age='.self::REDIRECT_CACHE_SECONDS,
            ]);
        }

        $signed = $disk->temporaryUrl($path, now()->addMinutes(self::SIGNATURE_TTL_MINUTES));

        return redirect()->away($signed, 302, [
            'Cache-Control' => 'public, max-age='.self::REDIRECT_CACHE_SECONDS,
        ]);
    }

    /**
     * Reject anything that is not a plain relative key inside the disk.
     *
     * The media disk is rooted at the bucket's `media/` prefix and private
     * documents live under `documents/`, so a traversal sequence is the one
     * way this public, unauthenticated route could be walked into reading
     * something it must never serve.
     */
    private function normalise(string $path): ?string
    {
        $path = ltrim($path, '/');

        if ($path === '' || str_contains($path, '..') || str_contains($path, "\0")) {
            return null;
        }

        return $path;
    }

    private function supportsTemporaryUrls(Filesystem $disk): bool
    {
        return method_exists($disk, 'providesTemporaryUrls')
            ? $disk->providesTemporaryUrls()
            : false;
    }
}

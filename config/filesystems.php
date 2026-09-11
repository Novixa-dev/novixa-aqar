<?php

/*
|--------------------------------------------------------------------------
| Object storage switch
|--------------------------------------------------------------------------
|
| The `media` and `documents` disks below are the same two disks in two
| different shapes: backed by S3-compatible object storage when a bucket is
| configured, and by the container's own disk when it isn't.
|
| The switch is deliberately driven by AWS_BUCKET rather than by a separate
| "use s3?" flag, because those two facts can never disagree: there is no way
| to end up asking for the S3 shape without the credentials that shape needs.
| Local development, CI and the test suite set no bucket, so they keep the
| local shape and behave exactly as they did before object storage existed.
|
*/
$usesObjectStorage = filled(env('AWS_BUCKET'));

$s3Credentials = [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'auto'),
    'bucket' => env('AWS_BUCKET'),
    'endpoint' => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'throw' => false,
];

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            // Deliberately relative (not env('APP_URL').'/storage'): media
            // URLs (Spatie Medialibrary, Storage::disk('public')->url())
            // then resolve against whatever origin actually served the page,
            // instead of being baked to the configured APP_URL. This is the
            // fix for a real, live-reproduced bug — if the serving origin
            // ever doesn't exactly match APP_URL (a different port locally,
            // a proxy/load balancer in front of the app, staging vs.
            // production domains), every absolute-URL image on the public
          // site silently fails the CSP `img-src 'self' data: https:`
            // directive and the photo never loads. A relative URL is always
            // same-origin, so this class of failure can't happen. Override
            // with ASSET_URL only if media is ever served from a separate
            // asset host/CDN (e.g. a different subdomain or S3) — leave
            // unset for the standard same-origin `storage:link` setup.
            'url' => env('ASSET_URL', '/storage'),
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            ...$s3Credentials,
            'url' => env('AWS_URL'),
        ],

        /*
         * Public-facing media: property photos, avatars, anything an <img>
         * tag points at. Spatie MediaLibrary is pointed here with MEDIA_DISK.
         *
         * The bucket is private -- Railway Buckets do not support public
         * objects -- so `url` is NOT the bucket's own address. It is this
         * application's /media route, which answers with a redirect to a
         * short-lived signed bucket URL. That keeps the URL written into the
         * HTML stable and cacheable while the image bytes still travel from
         * the bucket straight to the browser, never through PHP. See
         * App\Http\Controllers\MediaProxyController.
         */
        'media' => $usesObjectStorage
            ? [
                ...$s3Credentials,
                'root' => 'media',
                'url' => env('MEDIA_URL'),
                'visibility' => 'private',
            ]
            : [
                'driver' => 'local',
                'root' => storage_path('app/public'),
                'url' => env('ASSET_URL', '/storage'),
                'visibility' => 'public',
                'throw' => false,
            ],

        /*
         * Private documents: contracts, compliance files, lease agreements.
         *
         * This disk is referenced by App\Models\Document::registerMediaCollections()
         * via ->useDisk('documents') but was never defined here, so every
         * document upload threw "Disk [documents] does not have a configured
         * driver". Defining it is a fix, not just a migration to S3.
         *
         * It deliberately has no `url`: nothing may link to these files
         * directly. Access goes through DocumentAccessService, which decides
         * per user and per document, and only then hands out a signed URL.
         */
        'documents' => $usesObjectStorage
            ? [
                ...$s3Credentials,
                'root' => 'documents',
                'visibility' => 'private',
            ]
            : [
                'driver' => 'local',
                'root' => storage_path('app/documents'),
                'visibility' => 'private',
                'throw' => false,
            ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];

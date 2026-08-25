<?php

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
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
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

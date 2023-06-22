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

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'gcs',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'path' => '/test'
        ],


        'conversation' => [
            'driver' => 'gcs',
            'root' => storage_path('app/public/conversation'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'path' => 'test/conversation',
        ],

        'admin' => [
            'driver' => 'gcs',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'key_file' => env('GOOGLE_CLOUD_KEY_FILE'),
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'path' => '/test'
        ],


        'profile' => [
            'driver' => 'gcs',
            'root' => storage_path('app/public/profile'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'path' => 'test/profile',
        ],

        'ticket' => [
            'driver' => 'gsc',
            'root' => storage_path('app/public/ticket'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'path' => 'test/ticket'
        ],

        'rooms' => [
            'driver' => 'gsc',
            'root' => storage_path('app/public/rooms'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'path' => 'test/rooms'
        ],
        'unions' => [
            'driver' => 'gsc',
            'root' => storage_path('app/public/unions'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'path' => 'test/unions'
        ],

        'families' => [
            'driver' => 'gsc',
            'root' => storage_path('app/public/families'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'path' => 'test/families'
        ],

        'images' => [
            'driver' => 'gsc',
            'root' => storage_path('app/public/images'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'path' => 'test/images'
        ],

        'videos' => [
            'driver' => 'gsc',
            'root' => storage_path('app/public/videos'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'path' => 'test/videos'
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
        ],

        'gcs' => [
            'driver' => 'gcs',
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'key_file' => env('GOOGLE_CLOUD_KEY_FILE'),
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'path' => '/test',
            'url' => 'https://storage.googleapis.com/'.env('GOOGLE_CLOUD_STORAGE_BUCKET'),
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

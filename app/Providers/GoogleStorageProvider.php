<?php

namespace App\Providers;

use Google\Cloud\Storage\StorageClient;
use League\Flysystem\Filesystem;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;
use Illuminate\Support\ServiceProvider;

class GoogleStorageProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {   
        \Storage::extend('gcs', function($app, $config){
            $storageClient = new StorageClient([
                'projectId' => $config['project_id'],
                'keyFilePath' => $config['key_file'],
            ]);
            $bucket = $storageClient->bucket($config['bucket']);
            
            $adapter = new GoogleStorageAdapter($storageClient, $bucket);
            
            $filesystem = new Filesystem($adapter);
            
            $filesystem->createDir($config['path']);

            return $filesystem;
        });
        
    }
}

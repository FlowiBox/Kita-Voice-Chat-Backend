<?php

namespace App\Providers;

use Dflydev\ApacheMimeTypes\PhpRepository;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use App\Facades\Chat;
use App\Repositories\Conversation\ConversationRepository;
use App\Services\Chat as ChatService;
use App\Services\UploadManager;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap(config('chat.relation'));
        /*
        $this->publishes([
            $this->configPath()     => config_path('chat.php'),
            $this->componentsPath() => base_path('resources/assets/js/components/chat'),
        ]);

        $this->loadMigrationsFrom($this->migrationsPath());*/
        $this->registerBroadcast();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //$this->mergeConfigFrom($this->configPath(), 'chat');
        $this->registerFacade();
        $this->registerChat();
        $this->registerUploadManager();
        $this->registerAlias();
    }

    protected function registerFacade()
    {
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Chat', Chat::class);
        });
    }

    protected function registerUploadManager()
    {
        $this->app->singleton('upload.manager', function ($app) {
            $mime = $app[PhpRepository::class];
            $config = $app['config'];

            return new UploadManager($config, $mime);
        });
        $this->app->alias('upload.manager', UploadManager::class);
    }

    protected function registerChat()
    {
        $this->app->bind('chat', function ($app) {
            $config = $app['config'];
            $conversation = $app['conversation.repository'];

            return new ChatService($config, $conversation);
        });
    }

    protected function registerAlias()
    {
        $this->app->singleton('conversation.repository', function ($app) {
            $manger = $app['upload.manager'];

            return new ConversationRepository($manger);
        });
        $this->app->alias('conversation.repository', ConversationRepository::class);
    }

    protected function registerBroadcast()
    {
        Broadcast::channel(
            $this->app['config']->get('chat.channel.chat_room').'-{conversationId}',
            function ($user, $conversationId) {
                if ($this->app['conversation.repository']->canJoinConversation($user, $conversationId)) {
                    return $user;
                }
            }
        );
    }

}

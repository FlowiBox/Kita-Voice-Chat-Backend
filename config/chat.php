<?php

return [
    'relation' => [
        'conversations'       => App\Models\Conversation\Conversation::class,
    ],
    'user' => [
        'model' => App\Models\User::class,
        'table' => 'users', // Existing user table name
    ],
    'table' => [
        'conversations_table'       => 'conversations',
        'messages_table'            => 'messages',
        'files_table'               => 'files',
    ],
    'channel' => [
        'new_conversation_created' => 'new-conversation-created',
        'chat_room'                => 'chat-room',
    ],
    'upload' => [
        'storage' => 'public',
    ],
];
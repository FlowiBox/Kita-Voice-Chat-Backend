<?php

namespace App\Models\Conversation\Relationship;

use App\Models\File\File;
use App\Models\Message\Message;

trait ConversationRelationship
{
    /**
     * @return mixed
     */
    public function messages()
    {
        return $this->morphMany(Message::class, 'conversation');
    }

    /**
     * @return mixed
     */
    public function files()
    {
        return $this->morphMany(File::class, 'conversation');
    }

    /**
     * @return mixed
     */
    public function firstUser()
    {
        return $this->belongsTo(config('chat.user.model'), 'first_user_id');
    }

    /**
     * @return mixed
     */
    public function secondUser()
    {
        return $this->belongsTo(config('chat.user.model'), 'second_user_id');
    }
}
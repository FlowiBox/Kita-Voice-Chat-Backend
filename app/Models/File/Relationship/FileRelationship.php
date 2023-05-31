<?php

namespace App\Models\File\Relationship;

use App\Models\Message\Message;

trait FileRelationship
{
    /**
     * @return mixed
     */
    public function conversation()
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    /**
     * @return mixed
     */
    public function sender()
    {
        return $this->belongsTo(config('chat.user.model'), 'user_id');
    }
}
<?php

namespace App\Models\Message\Relationship;

use App\Models\File\File;

trait MessageRelationship
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
    public function sender()
    {
        return $this->belongsTo(config('chat.user.model'), 'user_id');
    }

    /**
     * @return mixed
     */
    public function files()
    {
        return $this->hasMany(File::class, 'message_id');
    }
}
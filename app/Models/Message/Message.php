<?php

namespace App\Models\Message;

use Illuminate\Database\Eloquent\Model;
use App\Models\Message\Relationship\MessageRelationship;

class Message extends Model
{
    use MessageRelationship;

    protected $table;

    protected $fillable = [
        'user_id', 'text',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('chat.table.messages_table');
    }
}
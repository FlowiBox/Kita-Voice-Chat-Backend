<?php

namespace App\Models\Conversation;

use Illuminate\Database\Eloquent\Model;
use App\Models\Conversation\Relationship\ConversationRelationship;

class Conversation extends Model
{
    use ConversationRelationship;

    protected $table;

    protected $fillable = [
        'first_user_id', 'second_user_id', 'is_accepted',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('chat.table.conversations_table');
    }
}
<?php

namespace App\Models\File;

use Illuminate\Database\Eloquent\Model;
use App\Models\File\Attribute\FileAttribute;
use App\Models\File\Relationship\FileRelationship;

class File extends Model
{
    use FileRelationship,FileAttribute;

    protected $table;

    protected $fillable = [
        'message_id', 'user_id', 'name',
    ];

    protected $appends = [
        'file_details',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('chat.table.files_table');
    }
}
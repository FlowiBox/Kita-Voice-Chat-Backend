<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'shares_num'=>'integer',
        'comments_num'=>'integer',
        'likes_num'=>'integer',
        'views_num'=>'integer'
    ];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function author(){
        return $this->belongsTo (User::class,'author_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVip extends Model
{
    protected $table = 'users_vips';
    protected $guarded = ['id'];
}

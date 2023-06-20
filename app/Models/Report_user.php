<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report_user extends Model
{
    use HasFactory;
    protected $table = 'reports';
    protected $fillable = [
        'type',
        'report_details',
        'user_id',
        'Reporter_id',
        'image',


    ];


    public function report()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Reporter()
    {
        return $this->belongsTo(User::class, 'Reporter_id');
    }
}

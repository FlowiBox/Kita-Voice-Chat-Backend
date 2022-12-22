<?php

namespace App\Models;

use App\Helpers\Common;
use App\Traits\FollowTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable ,FollowTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['my_store','lang','avatar','gender'];


    public function profile(){
        return $this->hasOne (Profile::class);
    }

    public function setPasswordAttribute($value){
        if ($value){
            $this->attributes['password'] = bcrypt ($value);
        }
    }

    public function getMyStoreAttribute(){
        return Common::my_store ($this->attributes['id']);
    }

    public function getAvatarAttribute(){
        return @$this->profile ()->first ()->avatar?:'';
    }

    public function getGenderAttribute(){
        return @$this->profile ()->first ()->gender == 1?'male':'female';
    }


    public function setDec($field_name,$value){
        $this->attributes[$field_name] -= $value;
    }

    public function country(){
        return $this->belongsTo (Country::class)->select ('id','name','flag');
    }

    public function getLangAttribute(){
        return @$this->country()->first ()->language?:'en';
    }

    public function rooms(){
        return $this->hasMany (Room::class,'uid');
    }


}

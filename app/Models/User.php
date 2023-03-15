<?php

namespace App\Models;

use App\Helpers\Common;
use App\Traits\FollowTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
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

    protected $appends = [
        'my_store',
        'lang',
        'avatar',
        'gender',
        'flag',
        'usd',
        'is_family_admin',
        'is_family_owner',
        'intro',
        'frame'
    ];


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
        return @$this->profile ()->first ()->avatar?:Common::getConf ('default_img');
    }

    public function getGenderAttribute(){
        return @$this->profile ()->first ()->gender == 1?'male':'female';
    }



    public function country(){
        return $this->belongsTo (Country::class)->select ('id','name','flag');
    }

    public function getFlagAttribute(){
        return @$this->country()->first ()->flag?:'';
    }

    public function getLangAttribute(){
        return @$this->country()->first ()->language?:'en';
    }

    public function rooms(){
        return $this->hasMany (Room::class,'uid');
    }

    public function getNicknameAttribute($val){
        return $val?:'';
    }

    public function profileVisits(){
        return $this->belongsToMany (User::class,'profile_visitors','user_id','visitor_id','id','id') ->withPivot('updated_at')
            ->orderBy('pivot_updated_at', 'desc');
    }

    public function is_in_live(){
        return $this->rooms ()->where ('room_status',1)->where ('room_visitor','!=','')->exists ();
    }

    public function agency(){
        return $this->belongsTo (Agency::class);
    }


    public function scopeOfAgency($q){
        $user = Auth::user ();
        if (Auth::user ()->isRole('agency')){
            $q->whereNotNull('agency_id')->where('agency_id', '=', @$user->agency_id);
        }

    }

    public function getUsdAttribute(){
        return $this->attributes['old_usd'] + $this->attributes['target_usd'] - $this->attributes['target_token_usd'];
    }



//    protected static function booted()
//    {
//        if (Auth::user ()->isRole('agency')){
//            static::addGlobalScope('of_agency', function (Builder $builder){
//                $builder->where('agency_id', '=', Auth::id ());
//            });
//        }
//
//    }



    public function getIsFamilyAdminAttribute(){
        $family_user = FamilyUser::query ()->where ('user_id',$this->id)->where ('status',1)->first ();
        if ($family_user){
            if ($family_user->user_type == 1 ){
                return true;
            }
            return false;
        }
        return false;
    }

    public function getIsFamilyOwnerAttribute(){
        $family = Family::query ()->where ('user_id',$this->id)->exists ();
        if ($family){
            return true;
        }
        return false;
    }

    public function getImgAttribute(){
        return $this->avatar;
    }


    public function targets(){
        return $this->hasMany (UserTarget::class);
    }

    public function getFollowDate($id){
        $f = Follow::query ()->where ('user_id',$this->id)->where ('followed_user_id',request ()->user ()->id)->value ('created_at');
        if ($f){
            return $f;
        }
        return '';
    }

    public function getFollowDateAttribute(){
        $f = Follow::query ()->where ('user_id',$this->id)->where ('followed_user_id',request ()->user ()->id)->value ('created_at');
        if ($f){
            return $f;
        }
        return '';
    }

    public function getFollowedDateAttribute(){
        $f = Follow::query ()->where ('user_id',request ()->user ()->id)->where ('followed_user_id',$this->id)->value ('created_at');
        if ($f){
            return $f;
        }
        return '';
    }

    public function getIntroAttribute(){
        return Common::getUserDress($this->id,$this->dress_3,6,'show_img');
    }

    public function getFrameAttribute(){
        return Common::getUserDress($this->id,$this->dress_1,4,'show_img');
    }

    public function getBubbleAttribute(){
        return Common::getUserDress($this->id,$this->dress_2,5,'show_img');
    }

    public function intros_count(){
        return Pack::query ()
            ->where ('user_id',$this->id)
            ->where ('type',6)
            ->where (function ($q){
                $q->where('expire',0)->orWhere('expire','>=',now ()->timestamp);
            })
            ->count ();
    }

    public function frames_count(){
        return Pack::query ()
            ->where ('user_id',$this->id)
            ->where ('type',4)
            ->where (function ($q){
                $q->where('expire',0)->orWhere('expire','>=',now ()->timestamp);
            })
            ->count ();
    }

    public function bubble_count(){
        return Pack::query ()
            ->where ('user_id',$this->id)
            ->where ('type',5)
            ->where (function ($q){
                $q->where('expire',0)->orWhere('expire','>=',now ()->timestamp);
            })
            ->count ();
    }

    public function hasRoom(){
        return Room::query ()
            ->where ('uid',$this->id)
            ->exists ();
    }


    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

}

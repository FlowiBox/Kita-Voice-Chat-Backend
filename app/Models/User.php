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


    /*
     * To enable and disable observer saving and updating methods
     */
    public $enableSaving = true;

    public static $withoutAppends = false;
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
        'frame',

    ];

    public function scopeWithoutAppends($query)
    {
        self::$withoutAppends = true;

        return $query;
    }

    public function ips(){
        return $this->hasMany (Ip::class,'uid');
    }


    public function profile(){
        return $this->hasOne (Profile::class);
    }

    public function setPasswordAttribute($value){
        if ($value){
            $this->attributes['password'] = bcrypt ($value);
        }
    }

    public function getMyStoreAttribute(){
        if (self::$withoutAppends){
            return;
        }
        return Common::my_store ($this->attributes['id']);
    }

    public function getAvatarAttribute(){
        if (self::$withoutAppends){
            return ;
        }
        return @$this->profile ()->first ()->avatar?:Common::getConf ('default_img');
    }

    public function getGenderAttribute(){
        if (self::$withoutAppends){
            return;
        }
        return @$this->profile ()->first ()->gender == 1?'male':'female';
    }



    public function country(){
        return $this->belongsTo (Country::class)->select ('id','name','flag', 'language');
    }

    public function getFlagAttribute(){
        if (self::$withoutAppends){
            return ;
        }
        return @$this->country()->first ()->flag?:'';
    }

    public function getLangAttribute(){
        if (self::$withoutAppends){
            return ;
        }
        return @$this->country()->language?:'en';
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
        if (self::$withoutAppends){
            return ;
        }
        return $this->old_usd + $this->target_usd - $this->target_token_usd;
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
        if (self::$withoutAppends){
            return ;
        }
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
        if (self::$withoutAppends){
            return ;
        }
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
        $f = Follow::query ()->where ('user_id',$this->id)->where ('followed_user_id',@request ()->user ()->id)->value ('created_at');
        if ($f){
            return $f;
        }
        return '';
    }

    public function getFollowDateAttribute(){
        $f = Follow::query ()->where ('user_id',$this->id)->where ('followed_user_id',@request ()->user ()->id)->value ('created_at');
        if ($f){
            return $f;
        }
        return '';
    }

    public function getFollowedDateAttribute(){
        $f = Follow::query ()->where ('user_id',@request ()->user ()->id)->where ('followed_user_id',$this->id)->value ('created_at');
        if ($f){
            return $f;
        }
        return '';
    }

    public function getIntroAttribute(){
        if (self::$withoutAppends){
            return ;
        }
        return Common::getUserDress($this->id,$this->dress_3,6,'img2');
    }

    public function getFrameAttribute(){
        if (self::$withoutAppends){
            return ;
        }
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

    public function ownAgency(){
        return $this->hasOne (Agency::class,'app_owner_id','id');
    }

    public function getIsAgentAttribute(){
        return $this->ownAgency ()->exists ();
    }

    public function UserVip()
    {
        return $this->hasOne(UserVip::class, 'user_id');
    }

    public function countGiftPrice($name)
    {
        return $this->hasMany(GiftLog::class, $name, 'id')->sum('giftPrice');
    }

    public function getImageReceiverOrSender($name,$type)
    {
        //get max from table vips
//        $max = Vip::query()->where('type', $type)->max('level');
//        $exp = $this->countGiftPrice($name);
        $amount = $type == 2 ? $this->sender_level + $this->sub_sender_level : $this->received_level + $this->sub_receiver_level;
//        if ($amount <= 0 ) $amount = 1;
//        if ($amount > $max) $amount = $max;
        $level = Vip::query()->where('type',$type)->where('level',$amount)->orderByDesc('exp')->first();

        return $level;
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'followed_user_id', 'id');
    }

    public function followeds()
    {
        return $this->hasMany(Follow::class, 'user_id', 'id');
    }

    public function canJoinRoom(int $roomId): bool
    {
        return $this->id == $roomId;
    }

    public function getSalaryAttribute(){
        if ($this->agency_id){
            $userSallary = UserSallary::query ()->where ('user_id',$this->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->where ('is_paid',0)
                ->first();

            if($userSallary)
                return ($userSallary->sallary - $userSallary->cut_amount);
            else
                return 0;
        }else{
            return 0;
        }
    }

    public function setSalaryAttribute(){
        if ($this->agency_id){
            $salary = UserSallary::query ()->where ('user_id',$this->id)->where ('is_paid',0)->sum (\DB::raw('sallary - cut_amount'));
            $this->attributes['salary'] = $salary;
        }else{
            $this->attributes['salary'] = 0;
        }
    }

    public function getOldAttribute(){
        $currentYear = date ('Y');
        $currentMonth = date ('m');
        $old = UserSallary::query ()->where ('user_id',$this->id)
            ->whereRaw("CONCAT(year, LPAD(month, 2, '0')) != CONCAT('$currentYear', LPAD('$currentMonth', 2, '0'))")
            ->where ('is_paid',0)
            ->sum (\DB::raw('sallary - cut_amount'));
        return $old;
    }

    public function setOldUsdAttribute(){
        $currentYear = date ('Y');
        $currentMonth = date ('m');
        $old = UserSallary::query ()->where ('user_id',$this->id)
            ->whereRaw("CONCAT(year, LPAD(month, 2, '0')) != CONCAT('$currentYear', LPAD('$currentMonth', 2, '0'))")
            ->where ('is_paid',0)
            ->sum (\DB::raw('sallary - cut_amount'));
        $this->attributes['old_usd'] = $old;
    }

    public function target($month = null,$year = null){
        if (!$month){
            $month = date ('m');
        }
        if (!$year){
            $year = date ('Y');
        }
        return $this->hasMany (UserSallary::class)->where ('month',$month)->where ('year',$year)->first ();
    }


    public function family()
    {
        return $this->belongsTo(Family::class, 'id', 'user_id');
    }

}

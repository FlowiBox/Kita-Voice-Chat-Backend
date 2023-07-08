<?php
namespace App\Traits\HelperTraits;

use App\Models\Family;
use App\Models\FamilyLevel;
use App\Models\GiftLog;
use App\Models\OfficialMessage;
use App\Models\OVip;
use App\Models\User;
use App\Models\UserLevelLog;
use App\Models\UserVip;
use App\Models\Vip;
use Illuminate\Support\Facades\DB;

Trait CalcsTrait
{



    public static function handelLevelLog($user_id = null , $type = null ,$level = 0,$total = 0){
        if ($type == 1){
            $key = 'host';
        }
        elseif ($type == 2 ){
            $key = 'normal';
        }
        else{
            $key = 'vip';
            return;
        }
        $level_log = UserLevelLog::query ()
            ->where ('user_id',$user_id)
            ->where ('type',$type)
            ->where ('level',$level)
            ->exists ();
        if (!$level_log && $level != 0){

            try {
                DB::beginTransaction ();
                UserLevelLog::query ()->create (
                    [
                        'user_id'=>$user_id,
                        'type'=>$type,
                        'level'=>$level,
                        'total'=>$total
                    ]
                );
                self::sendOfficialMessage ($user_id,__ ('congratulations'),__ ("you $key level up"));
                DB::commit ();
            }catch (\Exception $exception){
                DB::rollBack ();
            }
        }
    }

    public static function getLevel ( $user_id = null , $type = null , $is_image = false)
    {
        $user = User::query ()->find($user_id);

        if ( $type == 1 ) {
            $star_num = GiftLog ::where ( 'receiver_id' , $user_id ) -> sum ( 'giftPrice' );

            $total = $star_num;
            $exp = $star_num * 1;

        }
        elseif ( $type == 2 ) {
            $gold_num = GiftLog ::where ( 'sender_id' , $user_id ) -> sum ( 'giftPrice' );

            $total = $gold_num;
            $exp = $gold_num * 1;

        }
        elseif ( $type == 3 ) {
            $vip_num  = GiftLog ::where ( 'sender_id' , $user_id ) -> sum ( 'giftPrice' ); //count by purchased coins

            $total = $vip_num;
            $exp = $vip_num * 1;
        }
        else {
            $total = 0;
            $exp = 0;
        }


        $level = Vip ::query () -> where ( ['type' => $type] ) -> where ( 'exp' , '<=' , $exp ) -> orderByDesc ( 'exp' ) -> limit ( 1 ) -> value ( 'level');

        //------------------------------------------------
        if ($type == 1){
            $level += @$user->sub_receiver_level;
        }elseif ($type == 2){
            $level += @$user->sub_sender_level;
        }

        //--------------------------------------------------------

        if ( $is_image != false ) {
            if ( $level > 0 ) {
                $img = Vip ::query () -> where ( ['level' => $level , 'type' => $type] ) -> value ( 'img' );
                return $img;
            }
            else {
                if ( $level == '0' ) {
                    $img = Vip ::query () -> where ( ['level' => $level , 'type' => $type] ) -> value ( 'img' );
                    return $img;
                }
                else {
                    return '';
                }
            }
        }
        else {
            self::handelLevelLog ($user_id,$type,$level,$total);
            return $level?:0;
        }

    }

    public static function getHzLevel ( $user_id , $is_img = false )
    {
        $vip_level = static ::getLevel ( $user_id , 3 );
        if ( is_numeric ( $vip_level ) ) {
            $level = ceil ( $vip_level / 2 );
        }
        else {
            $level = $vip_level;
        }

        if ( $is_img ) {
            $img = Vip ::query () -> where ( ['level' => $level , 'type' => 4] ) -> value ( 'img' );
            return $img;
        }
        else {
            return $level?:0;
        }
    }

    public static function getCpLevel($cp_id)
    {
        $exp = DB::table('cp')->where(['id' => $cp_id])->value('exp');
        $where['type'] = 5;
        $where['exp'] = ['elt', $exp];
        $level = DB::table('vips')->where($where)->orderByRaw('id desc')->limit(1)->value('level');
        return $level?:0;
    }

    public static function room_hot ( $hot = null )
    {
        $hot = (int)$hot;
        if ( ! $hot ) return 0;
        if ( $hot <= 9999 ) {
            return $hot;
        }
        elseif ( $hot > 9999 && $hot <= 99999999 ) {
            $hot = round ( $hot / 10000 , 1 );
            return $hot . 'w';
        }
        elseif ( $hot > 99999999 ) {
            $hot = round ( $hot / 100000000 , 2 );
            return $hot . 'm';
        }
    }

    public static function getUserGifts ( $user_id )
    {

        $gifts = GiftLog ::query () -> where ( 'receiver_id' , $user_id )
            -> where ( 'type' , '2' )
            -> with (
                'gifts' , function ( $q ) {
                $q -> select ( 'show_img,price' );
            }
            )
            -> groupBy ( 'giftId' )
            -> orderBy ( 'gifts.price' );

        return $gifts;
    }


    //دخلي
    public static function user_income ( $user_id )
    {

        $days = array_map (
            function ( $val ) {
                return strtotime ( $val );
            } , self ::star_end_time ( 1 )
        );

        $weeks     = array_map (
            function ( $val ) {
                return strtotime ( $val );
            } , self ::star_end_time ( 2 )
        );
        $mons      = array_map (
            function ( $val ) {
                return strtotime ( $val );
            } , self ::star_end_time ( 3 )
        );
        $last_mons = array_map (
            function ( $val ) {
                return strtotime ( $val );
            } , self ::star_end_time ( 4 )
        );


        $arr['user_coins'] = DB ::table ( 'users' ) -> where ( 'id' , $user_id ) -> value ( 'coins' );

        $arr['day_sum']      = DB ::table ( 'store_logs' ) -> where ( 'user_id' , $user_id ) -> where ( 'get_type' , 21 )
            -> where ( 'created_at' , 'between time' , $days )
            -> sum ( 'get_nums' );
        $arr['week_sum']     = DB ::table ( 'store_logs' ) -> where ( 'user_id' , $user_id ) -> where ( 'get_type' , 21 )
            -> where ( 'created_at' , 'between time' , $weeks )
            -> sum ( 'get_nums' );
        $arr['mon_sum']      = DB ::table ( 'store_logs' ) -> where ( 'user_id' , $user_id ) -> where ( 'get_type' , 21 )
            -> where ( 'created_at' , 'between time' , $mons )
            -> sum ( 'get_nums' );
        $arr['last_mon_sum'] = DB ::table ( 'store_logs' ) -> where ( 'user_id' , $user_id ) -> where ( 'get_type' , 21 )
            -> where ( 'created_at' , 'between time' , $last_mons )
            -> sum ( 'get_nums' );
        $arr                 = array_map (
            function ( $val ) {
                return bcadd ( $val , 0 , 2 );
            } , $arr
        );
        $is_leader           = DB ::table ( 'users' ) -> where ( 'id' , $user_id ) -> value ( 'is_sign' );
        $res['is_leader']    = $is_leader;

        if ( $is_leader ) {
            $room['room_coins']   = DB ::table ( 'users' ) -> where ( 'id' , $user_id ) -> value ( 'room_coins' );
            $room['day_sum']      = DB ::table ( 'store_logs' ) -> where ( 'user_id' , $user_id )
                -> whereIn ( 'get_type' , [31 , 32] )
                -> where ( 'created_at' , 'between time' , $days )
                -> sum ( 'get_nums' );
            $room['week_sum']     = DB ::table ( 'store_logs' ) -> where ( 'user_id' , $user_id )
                -> whereIn ( 'get_type' , [31 , 32] )
                -> where ( 'created_at' , 'between time' , $weeks )
                -> sum ( 'get_nums' );
            $room['mon_sum']      = DB ::table ( 'store_logs' ) -> where ( 'user_id' , $user_id )
                -> whereIn ( 'get_type' , [31 , 32] )
                -> where ( 'created_at' , 'between time' , $mons )
                -> sum ( 'get_nums' );
            $room['last_mon_sum'] = DB ::table ( 'store_logs' ) -> where ( 'user_id' , $user_id )
                -> whereIn ( 'get_type' , [31 , 32] )
                -> where ( 'created_at' , 'between time' , $last_mons )
                -> sum ( 'get_nums' );
            $room                 = array_map (
                function ( $val ) {
                    return bcadd ( $val , 0 , 2 );
                } , $room
            );
        }
        else {
            $room = (object)[];
        }
        $res['gift_income'] = $arr;
        $res['room_income'] = $room;
        return $res;
    }

    //مركز الصف
    public static function level_center_deprecated ( $user_id )
    {
        $user = User::find($user_id);
        $star_num = DB ::table ( 'gift_logs' ) -> where ( 'receiver_id' , $user->id ) -> sum ( 'giftPrice' );
        $gold_num = DB ::table ( 'gift_logs' ) -> where ( 'sender_id' , $user->id ) -> sum ( 'giftPrice' );

        //--------------------------------------
        $star_num += $user->sub_receiver_num;
        $gold_num += $user->sub_sender_num;
        //--------------------------------------

        $star_level      = self ::getLevel ( $user->id , 1 );

        $star_level_img      = self ::getLevel ( $user->id , 1 ,true);
        $current_star_num = self::getCurrentLevel (1,$star_level,'exp');
        $next_star_num   = self ::getNextLevel ( 1 , $star_level , 'exp' );
        $next_star_level = self ::getNextLevel ( 1 , $star_level , 'level' );

        $gold_level      = self ::getLevel ( $user->id , 2 );


        $gold_level_img      = self ::getLevel ( $user->id , 2 ,true);
        $current_gold_num = self::getCurrentLevel (2,$gold_level,'exp');
        $next_gold_num   = self ::getNextLevel ( 2 , $gold_level , 'exp' );
        $next_gold_level = self ::getNextLevel ( 2 , $gold_level , 'level' );

        $data['receiver_num']        = (integer)$star_num;
        $data['receiver_img']        = $star_level_img;
        $data['sender_num']          = (integer)$gold_num;
        $data['sender_rem']          = (integer)(($next_gold_num - $current_gold_num) -($gold_num - $current_gold_num));
        $data['receiver_rem']        = (integer)(($next_star_num - $current_star_num) - ($star_num - $current_star_num));
        $data['sender_img']          = $gold_level_img;

        $data['receiver_level']      = (integer)$star_level;
        $data['next_receiver_num']   = (integer)$next_star_num?:0;
        $data['next_receiver_level'] = (integer)$next_star_level?:0;

        $data['sender_level']      = (integer)$gold_level;
        $data['next_sender_num']   = (integer)($next_gold_num);
        $data['next_sender_level'] = (integer)$next_gold_level?:0;

        $data['prev_receiver_num'] = (integer)$current_star_num?:0;
        $data['prev_sender_num'] = (integer)($current_gold_num);
        $data['current_receiver_num'] = $current_star_num;
        $data['current_sender_num'] = $current_gold_num;

        $rt = $data['next_receiver_num']-$data['prev_receiver_num'];
        $st = $data['next_sender_num']-$data['prev_sender_num'];
        $rc = $data['receiver_num'] - $data['prev_receiver_num'];
        $sc = $data['sender_num'] - $data['prev_sender_num'];
        if ($rt > 0 && ($rc/$rt) < 1 && ($rc/$rt) > 0){
            $data['receiver_per'] = (double)($rc/$rt);
        }else{
            $data['receiver_per'] = (double)0.00;
        }
        if($st > 0 && ($sc/$st) < 1 && ($sc/$st) > 0){
            $data['sender_per']=(double)($sc/$st);
        }else{
            $data['sender_per']= (double)0.00;
        }

        return $data;
    }

    public static function level_center ( $user_id)
    {
        $user = User::find($user_id);
        $star_num = $user->total_diamond_received + $user->sub_receiver_num;
        $gold_num = $user->total_diamond_send + $user->sub_sender_num;

        $star_level      = $user->received_level + $user->sub_receiver_level;

        $star_level_img = self ::getLevel ( $user->id , 1 ,true);
        $firstVip          = Vip::where('level', $star_level)->where('type', 1)->first();

        $star_level_img = !is_null($firstVip) ? $firstVip->img : '';
        //


        $current_star_num       = self::getCurrentLevel (1, $star_level,'exp');
        $next_star_num          = self::getNextLevel ( 1 , $star_level , 'exp' );
        $next_star_level        = self::getNextLevel ( 1 , $star_level , 'level' );

        $gold_level             = $user->sender_level + $user->sub_sender_level;


        $firstVip          = Vip::where('level', $gold_level)->where('type', 2)->first();
        $gold_level_img = !is_null($firstVip) ? $firstVip->img : '';;

        $current_gold_num   = self::getCurrentLevel (2,$gold_level,'exp');
        $next_gold_num   = self ::getNextLevel ( 2 , $gold_level , 'exp' );
        $next_gold_level = self ::getNextLevel ( 2 , $gold_level , 'level' );

        $data['receiver_num']        = (integer)$star_num;
        $data['receiver_img']        = $star_level_img;
        $data['sender_num']          = (integer)$gold_num;
        $data['sender_rem']          = (integer)(($next_gold_num - $current_gold_num) -($gold_num - $current_gold_num));
        $data['receiver_rem']        = (integer)(($next_star_num - $current_star_num) - ($star_num - $current_star_num));
        $data['sender_img']          = $gold_level_img;

        $data['receiver_level']      = (integer)$star_level;
        $data['next_receiver_num']   = (integer)$next_star_num?:0;
        $data['next_receiver_level'] = (integer)$next_star_level?:0;

        $data['sender_level']      = (integer)$gold_level;
        $data['next_sender_num']   = (integer)($next_gold_num);
        $data['next_sender_level'] = (integer)$next_gold_level?:0;

        $data['prev_receiver_num'] = (integer)$current_star_num?:0;
        $data['prev_sender_num'] = (integer)($current_gold_num);
        $data['current_receiver_num'] = $current_star_num;
        $data['current_sender_num'] = $current_gold_num;

        $rt = $data['next_receiver_num']-$data['prev_receiver_num'];
        $st = $data['next_sender_num']-$data['prev_sender_num'];
        $rc = $data['receiver_num'] - $data['prev_receiver_num'];
        $sc = $data['sender_num'] - $data['prev_sender_num'];
        if ($rt > 0 && ($rc/$rt) < 1 && ($rc/$rt) > 0){
            $data['receiver_per'] = (double)($rc/$rt);
        }else{
            $data['receiver_per'] = (double)0.00;
        }
        if($st > 0 && ($sc/$st) < 1 && ($sc/$st) > 0){
            $data['sender_per']=(double)($sc/$st);
        }else{
            $data['sender_per']= (double)0.00;
        }

        return $data;
    }


    public static function level_center_min ( $user_id )
    {
        $user = User::query()->find($user_id);
        $star_num = DB ::table ( 'gift_logs' ) -> where ( 'receiver_id' , $user->id ) -> sum ( 'giftPrice' );
        $gold_num = DB ::table ( 'gift_logs' ) -> where ( 'sender_id' , $user->id ) -> sum ( 'giftPrice' );

        //--------------------------------------
        $star_num += $user->sub_receiver_num;
        $gold_num += $user->sub_sender_num;
        //--------------------------------------

        $star_level      = self ::getLevel ( $user->id , 1 );

        $star_level_img      = self ::getLevel ( $user->id , 1 ,true);
        $current_star_num = self::getCurrentLevel (1,$star_level,'exp');
        $next_star_num   = self ::getNextLevel ( 1 , $star_level , 'exp' );
        $next_star_level = self ::getNextLevel ( 1 , $star_level , 'level' );

        $gold_level      = self ::getLevel ( $user->id , 2 );


        $gold_level_img      = self ::getLevel ( $user->id , 2 ,true);
        $current_gold_num = self::getCurrentLevel (2,$gold_level,'exp');
        $next_gold_num   = self ::getNextLevel ( 2 , $gold_level , 'exp' );
        $next_gold_level = self ::getNextLevel ( 2 , $gold_level , 'level' );

        $data['receiver_img']        = $star_level_img;

        $data['sender_img']          = $gold_level_img;


        // if ($rt > 0 && ($rc/$rt) < 1 && ($rc/$rt) > 0){
        //     $data['receiver_per'] = (double)($rc/$rt);
        // }else{
        //     $data['receiver_per'] = (double)0.00;
        // }
        // if($st > 0 && ($sc/$st) < 1 && ($sc/$st) > 0){
        //     $data['sender_per']=(double)($sc/$st);
        // }else{
        //     $data['sender_per']= (double)0.00;
        // }

        return $data;
    }


    //مركز الأعضاء
    public static function vip_center ( $user_id ,$level = null)
    {
        $vip_num        = DB ::table ( 'gift_logs' ) -> where ( 'sender_id' , $user_id ) -> sum ( 'giftPrice' );
        $vip_level      = self ::getLevel ( $user_id , 3 );
        $next_vip_num   = self ::getNextLevel ( 3 , $vip_level , 'di' );
        $next_vip_level = self ::getNextLevel ( 3 , $vip_level , 'level' );

        $data['vip_num']        = (integer)$vip_num?:0;
        $data['vip_level']      = (integer)$vip_level?:0;
        $data['next_vip_num']   = (integer)$next_vip_num?:0;
        $data['next_vip_level'] = (integer)$next_vip_level?:0;


        $vip_auth = DB ::table ( 'vip_auth' ) -> where ( ['type' => 3 , 'enable' => 1] ) ;
        if ($level){
            $vip_auth = $vip_auth->where ('level',$level);
        }
        $vip_auth = $vip_auth-> get ();
        foreach ($vip_auth as $k => &$v) {
            $v->is_on = ($vip_level >= $v->level) ? 1 : 0;
        }
        unset( $v );
        $arr['my_data']     = $data;
        $arr['vip_prev'] = $vip_auth;
        return $arr;
    }

    public static function ovip_center ( $user_id)
    {
        if (gettype($user_id) == 'integer') {
            $user = User::query ()->find ($user_id);
            if (!$user) return new \stdClass();
        } else {
            $user = $user_id;
        }
        $uvip = UserVip::query ()->find ($user->vip);
        if (!$uvip) return new \stdClass();
        $vip = OVip::query ()->find ($uvip->vip_id);
        if (!$vip) return new \stdClass();
//        $p = $vip->privilegs;
//        return $vip;
        return [
            'id'        => 1,
            'level'     => $vip->level,
            'name'      => $vip->name,
            'price'     => $vip->price,
            'image'     => $vip->image,
            'expire'    => $vip->expire
        ];
    }

    //حقيبتي
    public static function my_store ( $user_id )
    {
        $user     = User ::query () -> find ( $user_id );
        $cou_list = Db ::table ( 'user_coupons' ) -> where ( ['user_id' => $user_id , 'status' => 1] ) -> get ();
        foreach ($cou_list as $k => $va) {
            if ( $va['expire'] <= time () ) {
                Db ::table ( 'user_coupons' ) -> where ( ['id' => $va['id']] ) -> update ( ['status' => 3] );
            }
        }
        return [
            'id'=>$user->id,
            'coins'=>$user->di,
            'diamonds'=>$user->coins,
            'silver_coins'=>$user->gold,
            'usd' => (double)$user->sallary ,
        ];
    }



    //my backpack  type

    //1 gem
    //2 gifts - not used
    //3 coupons
    //4 avatar frames
    //5 bubble boxes
    //6 entry effects
    //7 mic on the aperture
    //8 badges

    public function my_pack($user_id,$type){
        if(!in_array($type,[1,2,3,4,5,6,7]))    return '';
        $where['a.user_id']=$user_id;
        $where['a.type']=$type;
        //$where['b.enable']=1;
        if($type == 2){
            $data=DB::table('pack')->alias('a')->join('gifts b','a.target_id = b.id')
                ->where($where)
                ->field("a.*,b.name,b.show_img,b.price")
                ->select();
        }else{
            $data=DB::table('pack')->alias('a')->join('wares b','a.target_id = b.id')
                ->where($where)
                ->field("a.*,b.name,b.show_img,b.title,b.color")
                ->select();
        }
        if(in_array($type,[4,5,6,7])){
            $dress_id=Db::table('users')->where(['id'=>$user_id])->value("dress_".$type);
        }
        foreach ($data as $k => &$v) {
            $v['show_img']=$this->auth->setFilePath($v['show_img']);
            $v['is_dress']=0;
            if(in_array($type,[4,5,6,7])){
                $v['title']= empty($v['expire']) ? "permanent" : date('Y-m-d H:i:s',$v['expire'])."expire";
                $v['is_dress']= $dress_id == $v['target_id']  ? 1 : 0;
                $v['color']= $v['color'] ? : '';
            }elseif($type == 2){
                $v['title'] = "have".$v['num']."value".$v['num']*$v['price']."diamond";
                $v['color'] = '';
            }else{
                $v['title']="have".$v['num']."indivual ".$v['title'];
                $v['color']= $v['color'] ? : '';
            }

            //status changed to read
            if ($v['is_read'] == 1){
                Db::table('pack')->where(array('id'=>$v['id']))->update(array('is_read'=>0));
            }

        }
        $this->ApiReturn(1,'',$data);
    }



    //المستوى الأقصى ونقاط الخبرة
    public static function getNextLevel ( $type = null , $level = 0 , $field = null )
    {
        if ( !$type || !$field ) return 0;

        $max  = DB ::table ( 'vips' ) -> where ( ['type' => $type] ) -> orderByDesc ( 'exp' ) -> limit ( 1 ) -> value ( $field );
        $next = DB ::table ( 'vips' ) -> where ( 'type' , $type ) -> where ( 'level' , '>' , $level ) -> orderBy ( 'level' ) -> limit ( 1 ) -> value ( $field )?:0;
        return ($next ?: $max);
    }


    public static function getCurrentLevel ( $type = null , $level = 0 , $field = null )
    {
        if ( !$type || !$field ) return 0;
        $le = DB ::table ( 'vips' ) -> where ( 'type' , $type ) -> where ( 'level' , '=' , $level ) -> value ( $field )?:0;
        return ($le);
    }


    //Get start and end time
//1 today, 2 this week, 3 this month, 4 last month, 5 yesterday
    public static function star_end_time ( $type = 1 , $class = 1 )
    {
        $today = date ( 'Y-m-d' , time () );
        if ( $type == 1 ) {
            $star = date ( 'Y-m-d H:i:s' , mktime ( 0 , 0 , 0 , date ( 'm' ) , date ( 'd' ) , date ( 'Y' ) ) );
            $end  = date ( 'Y-m-d H:i:s' , mktime ( 23 , 59 , 59 , date ( 'm' ) , date ( 'd' ) , date ( 'Y' ) ) );
        }
        elseif ( $type == 2 ) {
            $w    = date ( 'w' , strtotime ( $today ) );
            $star = date ( 'Y-m-d H:i:s' , mktime ( 0 , 0 , 0 , date ( 'm' ) , date ( 'd' ) - $w + 1 , date ( 'Y' ) ) );
            $end  = date ( 'Y-m-d H:i:s' , mktime ( 23 , 59 , 59 , date ( 'm' ) , date ( 'd' ) + (7 - $w) , date ( 'Y' ) ) );
        }
        elseif ( $type == 3 ) {
            $star            = date ( 'Y-m-d H:i:s' , mktime ( 0 , 0 , 0 , date ( 'm' ) , str_pad ( 1 , 2 , 0 , STR_PAD_LEFT ) , date ( 'Y' ) ) );
            $last_month_days = date ( 't' , strtotime ( date ( 'Y' ) . '-' . (date ( 'm' )) . '-' . str_pad ( 1 , 2 , 0 , STR_PAD_LEFT ) ) );
            $end             = date ( 'Y-m-d H:i:s' , mktime ( 23 , 59 , 59 , date ( 'm' ) , $last_month_days , date ( 'Y' ) ) );
        }
        elseif ( $type == 4 ) {
            $star            = date ( 'Y-m-d H:i:s' , mktime ( 0 , 0 , 0 , date ( 'm' ) - 1 , str_pad ( 1 , 2 , 0 , STR_PAD_LEFT ) , date ( 'Y' ) ) );
            $last_month_days = date ( 't' , strtotime ( date ( 'Y' ) . '-' . (date ( 'm' ) - 1) . '-' . str_pad ( 1 , 2 , 0 , STR_PAD_LEFT ) ) );
            $end             = date ( 'Y-m-d H:i:s' , mktime ( 23 , 59 , 59 , date ( 'm' ) - 1 , $last_month_days , date ( 'Y' ) ) );
        }
        elseif ( $type == 5 ) {
            $star = date ( 'Y-m-d H:i:s' , mktime ( 0 , 0 , 0 , date ( 'm' ) , date ( 'd' ) - 1 , date ( 'Y' ) ) );
            $end  = date ( 'Y-m-d H:i:s' , mktime ( 23 , 59 , 59 , date ( 'm' ) , date ( 'd' ) - 1 , date ( 'Y' ) ) );
        }
        else {
            return false;
        }
        if ( $class == 1 ) {
            $arr[] = $star;
            $arr[] = $end;
        }
        elseif ( $class == 2 ) {
            $arr[] = strtotime ( $star );
            $arr[] = strtotime ( $end );
        }
        return $arr;
    }


    public static function check_first_cp($user_id, $fromUid, $status)
    {
        $where = 'user_id|fromUid = '.$user_id;
        $where .=' and status = '. $status;
        $id = DB::table('cps')->whereRaw($where)->whereRaw("`user_id` = {$fromUid} OR `fromUid` = {$fromUid}")->value('id');
        return $id;
    }


    public static function getBrithdayMsg($birthday=null,$type = 1)
    {
        if(!$birthday)  return false;
        if(!in_array($type, [0,1,2]))   return false;
        list($year,$month,$day) = explode("-",$birthday);
        $year_diff = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff  = date("d") - $day;
        if ($month_diff < 0 || ($month_diff == 0 && $day_diff < 0)){
            $year_diff--;
        }
        $info[]=($year_diff < 0 ) ? 0 : $year_diff;

        $animals = array(
            'mouse', 'Cattle', 'Tiger', 'rabbit', 'dragon', 'snake',
            'horse', 'sheep', 'monkey', 'chicken', 'dog', 'pig'
        );
        $key = abs(($year - 1900) % 12);
        $info[]=$animals[$key];
        $signs = array(
            array('20'=>'Aquarius'),
            array('19'=>'Pisces'),
            array('21'=>'Aries'),
            array('20'=>'Taurus'),
            array('21'=>'Gemini'),
            array('22'=>'Cancer'),
            array('23'=>'Leo'),
            array('23'=>'Virgo'),
            array('23'=>'Libra'),
            array('24'=>'Scorpio'),
            array('22'=>'Sagittarius'),
            array('22'=>'Capricorn')
        );
        $arr=$signs[$month-1];
        if($day < key($arr)){
            $arr=$signs[($month-2 < 0) ? 11 : $month-2];
        }
        $info[]=current($arr);
        return $info[$type];
    }


    public static function updateFamilyLevel($family_id){
        $family = Family::query ()->find ($family_id);
        if ($family){
            $family->update (['today_rank'=>1,'week_rank'=>1,'month_rank'=>1]);
            $giftLogs = GiftLog::query ()->where (function ($q) use ($family){
                $q->where ('receiver_family_id',$family->id)->orWhere('sender_family_id',$family->id);
            })->sum ('giftPrice');
            $level = FamilyLevel::query ()->where ('exp','<=',$giftLogs)->orderByDesc ('exp')->first ();
            if ($level){
                if ($level->id != $family->current_level_id){
                    DB::beginTransaction ();
                    try {
                        OfficialMessage::query ()->create (
                            [
                                'title'=>'family level upgraded',
                                'user_id'=>$family->user_id,
                                'content'=>'congratulations your family level upgraded',
                                'url'=>''
                            ]
                        );
                        $family->update (['current_level_id' => $level->id]);
                        DB::commit ();
                    }catch (\Exception $exception){
                        DB::rollBack ();
                    }
                }
            }

        }
    }



}


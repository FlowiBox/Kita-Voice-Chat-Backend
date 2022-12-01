<?php
namespace App\Traits\HelperTraits;

use App\Models\GiftLog;
use App\Models\User;
use App\Models\Vip;
use Illuminate\Support\Facades\DB;

Trait CalcsTrait
{

    public static function getLevel ( $user_id = null , $type = null , $is_image = false )
    {
        $star_num = GiftLog ::where ( 'receiver_id' , $user_id ) -> sum ( 'giftPrice' );
        $gold_num = GiftLog ::where ( 'sender_id' , $user_id ) -> sum ( 'giftPrice' );
        $vip_num  = GiftLog ::where ( 'sender_id' , $user_id ) -> sum ( 'giftPrice' );

        if ( $type == 1 ) {
            $total = $star_num;
        }
        elseif ( $type == 2 ) {
            $total = $gold_num;
        }
        elseif ( $type == 3 ) {
            $total = $vip_num;
        }
        else {
            $total = 0;
        }

        if ( ! $total ) {
            return 0;
        }

        $level = Vip ::query () -> where ( ['type' => $type] ) -> where ( 'di' , '<=' , $total ) -> orderByDesc ( 'di' ) -> limit ( 1 ) -> value ( 'level' );

        if ( $is_image ) {
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
            return $level;
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
            return $level;
        }
    }

    public static function getCpLevel($cp_id)
    {
        $exp = DB::table('cp')->where(['id' => $cp_id])->value('exp');
        $where['type'] = 5;
        $where['exp'] = ['elt', $exp];
        $level = DB::table('vips')->where($where)->orderByRaw('id desc')->limit(1)->value('level');
        return $level;
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
    public static function level_center ( $user_id )
    {
        $star_num = DB ::table ( 'gift_logs' ) -> where ( 'receiver_id' , $user_id ) -> sum ( 'giftPrice' );
        $gold_num = DB ::table ( 'gift_logs' ) -> where ( 'sender_id' , $user_id ) -> sum ( 'giftPrice' );

        $star_level      = self ::getLevel ( $user_id , 1 );
        $next_star_num   = self ::getNextLevel ( 1 , $star_level , 'exp' );
        $next_star_level = self ::getNextLevel ( 1 , $star_level , 'level' );

        $gold_level      = self ::getLevel ( $user_id , 2 );
        $next_gold_num   = self ::getNextLevel ( 2 , $gold_level , 'exp' );
        $next_gold_level = self ::getNextLevel ( 2 , $gold_level , 'level' );

        $data['star_num']        = $star_num;
        $data['gold_num']        = $gold_num;
        $data['star_level']      = $star_level;
        $data['next_star_num']   = $next_star_num;
        $data['next_star_level'] = $next_star_level;

        $data['gold_level']      = $gold_level;
        $data['next_gold_num']   = $next_gold_num;
        $data['next_gold_level'] = $next_gold_level;
        return $data;
    }

    //مركز الأعضاء
    public static function vip_center ( $user_id )
    {
        $vip_num        = DB ::table ( 'gift_logs' ) -> where ( 'sender_id' , $user_id ) -> sum ( 'giftPrice' );
        $vip_level      = self ::getLevel ( $user_id , 3 );
        $next_vip_num   = self ::getNextLevel ( 3 , $vip_level , 'di' );
        $next_vip_level = self ::getNextLevel ( 3 , $vip_level , 'level' );

        $data['vip_num']        = $vip_num;
        $data['vip_level']      = $vip_level;
        $data['next_vip_num']   = $next_vip_num;
        $data['next_vip_level'] = $next_vip_level;


        $vip_auth = DB ::table ( 'vip_auth' ) -> where ( ['type' => 3 , 'enable' => 1] ) -> get ();
        foreach ($vip_auth as $k => &$v) {
            $v -> is_on = ($vip_level >= $v -> level) ? 1 : 0;
        }
        unset( $v );
        $arr['data']     = $data;
        $arr['vip_auth'] = $vip_auth;
        return $arr;
    }

    //حقيبتي
    public static function my_store ( $user_id )
    {
        $user     = User ::query () -> find ( $user_id );
        $coins    = bcadd ( $user -> coins , $user -> room_coins , 2 );
        $cou_list = Db ::table ( 'user_coupons' ) -> where ( ['user_id' => $user_id , 'status' => 1] ) -> get ();
        foreach ($cou_list as $k => $va) {
            if ( $va['expire'] <= time () ) {
                Db ::table ( 'user_coupons' ) -> where ( ['id' => $va['id']] ) -> update ( ['status' => 3] );
            }
        }
        return [
            'total_coins' => $coins ,
            'coupons' => Db ::table ( 'user_coupons' ) -> join ( 'wares' , 'user_coupons.ware_id' , '=' , 'wares.id' ) -> where ( ['user_coupons.user_id' => $user_id , 'wares.enable' => 1] ) -> count ()
        ];
    }

    //المستوى الأقصى ونقاط الخبرة
    public static function getNextLevel ( $type = null , $level = null , $field = null )
    {
        if ( ! $type || ! $field ) return 0;
        $max  = DB ::table ( 'vips' ) -> where ( ['type' => $type] ) -> orderByDesc ( 'id' ) -> limit ( 1 ) -> value ( $field );
        $next = DB ::table ( 'vips' ) -> where ( ['type' => $type] ) -> where ( 'level' , '>' , $level ) -> orderBy ( 'level' ) -> limit ( 1 ) -> value ( $field ) ?: 0;
        return $next ?: $max;
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





}


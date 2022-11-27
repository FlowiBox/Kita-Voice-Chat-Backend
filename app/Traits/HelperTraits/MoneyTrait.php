<?php


namespace App\Traits\HelperTraits;


use App\Models\User;
use Illuminate\Support\Facades\DB;

trait MoneyTrait
{


//Obtain information about the time of withdrawal
    public static function get_tixian_info ()
    {
        $info               = DB ::table ( 'tixians' )
            -> where ( 'status' , 'normal' )
            -> orderBy ( 'id' , 'desc' )
            -> first ();
        $info['start_time'] = strtotime ( date ( 'H:i' , $info['start_time'] ) );//start timestamp
        $info['end_time']   = strtotime ( date ( 'H:i' , $info['end_time'] ) );//end timestamp
        $info['week_ids']   = $week_names = array_filter ( explode ( ',' , $info['week_ids'] ) );
        return $info;
    }


    /**
     * withdraw
     * @param int       money       Withdrawal Amount
     * @param int       type        1 meter coin withdrawal 2 whale coin value withdrawal
     */
    public function tixian ($user_id)
    {
        //Judging whether to join the guild, joining can not be withdrawn
        $union_info = DB ::table ( 'user_union' )
            -> where ( ['check_status' => '1' , 'users_id' => $user_id] )
            -> first ();
        if ( $union_info ) {
            return 'Already joined the guild, temporarily unable to withdraw cash';
        }
        //Judging whether it is possible to withdraw cash
        $tixian_info = DB ::table ( 'tixians' )
            -> where ( 'status' , 'normal' )
            -> orderBy ( 'id' , 'desc' )
            -> first ();
        if ( $tixian_info ) {
            $tixian_info['week_ids'] = $week_names = array_filter ( explode ( ',' , $tixian_info['week_ids'] ) );
            $weekarray               = date ( "w" );
            if ( $weekarray == 0 ) {
                $weekarray = 7;
            }
            $start_time   = strtotime ( date ( 'H:i' , $tixian_info['start_time'] ) );//start timestamp
            $current_time = strtotime ( date ( 'H:i' , time () ) );//当前时间戳
            $end_time     = strtotime ( date ( 'H:i' , $tixian_info['end_time'] ) );//start timestamp
            if ( $start_time > $current_time || $current_time > $end_time ) {
                return 'Please withdraw cash at the specified time';
            }
            if ( ! in_array ( $weekarray , $tixian_info['week_ids'] ) ) {
                return 'Please withdraw cash on the specified working day';
            }
        }


        $money = request () -> get ( 'money' );
        $type  = request () -> get ( 'type' );
        if ( $money <= 0 || ! in_array ( $type , [1 , 2] ) ) return 'Missing parameters';
        $user       = DB ::table ( 'users' ) -> where ( 'id' , $user_id ) -> select ( 'coins' , 'room_coins' , 'flowers_value' ) -> first ();
        $min_tx_num = self::getConfig ( 'min_tx_num' );
        if ( $money < $min_tx_num ) return 'Withdrawal amount cannot be less than' . $min_tx_num;

        if ( $type == 1 ) {
            $sum = bcadd ( $user -> coins , $user -> room_coins , 2 );
            if ( $money > $sum ) return 'Insufficient balance';
        }
        elseif ( $type == 2 ) {
            if ( $money > $user -> flowers_value ) return 'Insufficient balance';
        }

        DB ::beginTransaction ();
        try {
            if ( $type == 1 ) {
                if ( $user['coins'] >= $money ) {
                    self::userStoreDec ( $user_id , $money , 23 , 'coins' );
                }
                else {
                    self::userStoreDec ( $user_id , $user->Room_coins , 34 , 'Room_coins' );
                    $num = bcsub ( $money , $user->Room_coins , 2 );
                    self::userStoreDec ( $user_id , $num , 23 , 'coins' );
                }
            }
            elseif ( $type == 2 ) {
                self::userStoreDec ( $user_id , $money , 52 , 'flowers_value' );
            }

            $arr['order_no'] = self::getOrderNo ();
            $arr['user_id']  = $user_id;
            $arr['money']    = $money;
            $arr['type']     = $type;
            $arr['addtime']  = time ();
            $res             = DB ::table ( 'tixian' ) -> insert ( $arr );
            //commit transaction
            DB ::commit ();
        } catch (\Exception $e) {
            //rollback transaction
            DB ::rollback ();
            return 'Withdrawal failed';
        }
        return 'The withdrawal application has been submitted and is under review';
    }



    //reduce the value
    function userStoreDec($user_id, $get_nums, $get_type, $jb_type) {
        $get_nums=abs($get_nums);
        if($get_nums == 0) return false;
        $res = User::query()->findOrFail ($user_id)->setDec($jb_type, $get_nums);
        if (!$res) return false;
        $now_nums = Db::table('users')->where(['id' => $user_id])->value($jb_type);
        self::addTranmoney($user_id, $get_nums, $get_type, $now_nums,'-');
        return $res;
    }


    //create record
    public static function addTranmoney($user_id, $get_nums, $get_type, $now_nums,$symbol,$union_id = 0) {
        $info['union_id']  = $union_id;
        $info['user_id']  = $user_id;
        $info['get_nums'] = $get_nums;
        $info['get_type'] = $get_type;
        $info['now_nums'] = $now_nums;
        $info['addtime']  = time();
        $info['symbol']    = $symbol;
        $info['types']    = self::getTypesByGettype($get_type);
        $res = Db::table('store_log')->insertGetId($info);
        return $res;
    }


    // Classification of user resources
    public static function getTypesByGettype($gettype){
        if (in_array($gettype, [11,12,13,14,15,16,17,18,19])){
            return 1;
        }else if(in_array($gettype, [21,22,23,24,25,26])){
            return 2;
        }else if(in_array($gettype, [31,32,33,34,35,36,37])){
            return 3;
        }else if(in_array($gettype, [41,43,44,45,46])){
            return 4;
        }else if(in_array($gettype, [51,52,53,64])){
            return 5;
        }else if(in_array($gettype, [61,62,63])){
            return 6;
        }
        return 0;
    }



    public static function getOrderNo($prefix = 'MN') {
        $order_no = $prefix . date("YmdHis") . rand(100000, 999999);
        return $order_no;
    }


}

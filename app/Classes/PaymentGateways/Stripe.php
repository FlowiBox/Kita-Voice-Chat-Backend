<?php
namespace App\Classes\PaymentGateways;
use App\Helpers\Common;
use App\Models\CoinLog;
use Illuminate\Http\Request;

class Stripe
{

    //email : test@rxample.com
    //Card information : 4242 4242 4242 4242     12/34    567
    //Name on card : Zhang San
    //Country or region : United States
    //zip : 12345

	 public static function redirect_if_payment_success($trx)
     {
        return url("/payment/payment-success?p_method=strip&&trx=$trx");
     }

    public static function redirect_if_payment_faild($trx)
    {
     return url("/payment/payment-fail?p_method=strip&&trx=$trx");
    }


    public function make($data){
        \Stripe\Stripe::setApiKey(Common::getConf ('strip_api_key')?:'sk_test_4eC39HqLyjWDarjtT1zdp7dc');
        $checkout_session = \Stripe\Checkout\Session::create(
            [
                'line_items' => [
                    [
                        'price_data' => [
                            'currency'=>'usd',
                            'product_data'=>[
                                'name'=>$data['name']
                            ],
                            'unit_amount'=>100 * $data['amount']
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => self::redirect_if_payment_success ($data['trx']),
                'cancel_url' => self::redirect_if_payment_faild ($data['trx']),
            ]
        );

        $c = CoinLog::query ()->where ('trx',$data['trx'])->where ('method','strip')->where ('status',0)->first ();
        if($c){
            $c->pid = $checkout_session->id;
            $c->save ();
        }
        return $checkout_session->url;

    }


    public static function status($session_id) {
        $stripe = new \Stripe\StripeClient(Common::getConf ('strip_api_key')?:'sk_test_4eC39HqLyjWDarjtT1zdp7dc');
        try {
            $session = $stripe->checkout->sessions->retrieve($session_id);
            dd ($session);
        }catch (\Exception $exception){
            return Common::apiResponse (0,'fail',null,400);
        }

    }
     public function __construct()
    {

    }

}

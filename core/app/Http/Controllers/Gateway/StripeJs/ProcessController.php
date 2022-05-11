<?php

namespace App\Http\Controllers\Gateway\StripeJs;

use App\Models\Deposit;
use App\Models\Order;
use App\Models\GatewayCurrency;
use App\Models\Gateway;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;


class ProcessController extends Controller
{

    public static function process($deposit)
    {
        //$StripeJSAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
         $gate = Gateway::where('code', $deposit->method_code)->first();
        
        $gatecur    = GatewayCurrency::where('method_code', $deposit->method_code)->first();
        //return $gate;
        $StripeJSAcc = json_decode($gatecur->gateway_parameter);
        
        $general =  GeneralSetting::first();
       
        $val['key'] = $StripeJSAcc->publishable_key;
        $val['name'] = $general->sitename;
        $val['email'] = Auth::user()->email;
        $val['description'] = "Pay with Stripe";
        $val['image'] = "https://budmall.ng/assets/images/logoIcon/logo.png";
        $val['amount'] = $deposit->total_amount_usd * 100;
        $val['currency'] = $deposit->method_currency;
        $send['val'] = $val;


        $alias = $gate->alias;

        $send['src'] = "https://checkout.stripe.com/checkout.js";
        $send['view'] = 'user.payment.' . $alias;
        $send['method'] = 'post';
        $send['url'] = route('ipn.'.$gate->alias);
        return json_encode($send);
    }

    public function ipn(Request $request)
    {

        $track = Session::get('Track');
        $deposit = Order::where('order_number', $track)->orderBy('id', 'DESC')->first();
        if ($deposit->status == 1) {
            $notify[] = ['error', 'Invalid request.'];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }
        
        
        $gatecur    = GatewayCurrency::where('method_code', $deposit->method_code)->first();
        //return $gate;
        $StripeJSAcc = json_decode($gatecur->gateway_parameter);
        

        Stripe::setApiKey($StripeJSAcc->secret_key);

        Stripe::setApiVersion("2020-03-02");

        $customer =  Customer::create([
            'email' => $request->stripeEmail,
            'source' => $request->stripeToken,
        ]);

        $charge = Charge::create([
            'customer' => $customer->id,
            'description' => 'Payment with Stripe',
            'amount' => round($deposit->total_amount_usd*100),
            'currency' => $deposit->method_currency,
        ]);


        if ($charge['status'] == 'succeeded') {
            $type = "Stripe JS";
            PaymentController::userOrderupdate($deposit->trx,$type);
            $notify[] = ['success', 'Payment captured successfully.'];
            return redirect()->route(gatewayRedirectUrl(true))->withNotify($notify);
        }else{
            $notify[] = ['success', 'Failed to process.'];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }
    }
}

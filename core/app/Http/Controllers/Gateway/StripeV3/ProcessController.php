<?php

namespace App\Http\Controllers\Gateway\StripeV3;

use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\Gateway;
use App\Models\GeneralSetting;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Session;


class ProcessController extends Controller
{

    public static function process($deposit)
    {
        $gate = Gateway::where('code', $deposit->method_code)->first();
        
        $gatecur    = GatewayCurrency::where('method_code', $deposit->method_code)->first();
        //return $gate;
        $StripeAcc = json_decode($gatecur->gateway_parameter);
       
        
        $alias = $gate->alias;
        $general =  GeneralSetting::first();
        \Stripe\Stripe::setApiKey("$StripeAcc->secret_key");

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'name' => $general->sitename,
                'description' => 'Pay with Stripe',
                'images' => [asset('assets/images/logoIcon/logo.png')],
                'amount' => round($deposit->total_amount_usd,2) * 100,
                'currency' => "$gatecur->currency",
                'quantity' => 1,
            ]],
            //'cancel_url' => route(gatewayRedirectUrl()),
            //'success_url' => route(gatewayRedirectUrl(true)),
            
            'cancel_url' => url('/'),
            'success_url' => route(gatewayRedirectUrlStripe(true)),
        ]);

        $send['view'] = 'user.payment.'.$alias;
        $send['session'] = $session;
        $send['StripeJSAcc'] = $StripeAcc;
        $deposit->api_response = json_decode(json_encode($session))->id;
        $deposit->api_response2 = json_decode(json_encode($session))->payment_intent;
        $deposit->save();
        return json_encode($send);
    }
    
    public function ipnpaid(Request $request)
    {
    $track  = session()->get('order_number');
    //$deposit = Order::where('api_response',  $session->id)->orderBy('id', 'DESC')->first();
    $deposit = Order::where('order_number',  $track)->orderBy('id', 'DESC')->firstOrFail();
    $gate = Gateway::where('code', $deposit->method_code)->first();
        
    $gatecur    = GatewayCurrency::where('method_code', $deposit->method_code)->first();
        //return $gate;
    $StripeAcc = json_decode($gatecur->gateway_parameter);
    $sk = $StripeAcc->secret_key;
    
    $url = "https://api.stripe.com/v1/checkout/sessions/".$deposit->api_response;
    
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $headers = array(
       "Authorization: Bearer ".$sk,
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
    $resp = curl_exec($curl);
    curl_close($curl);
    $reply = json_decode($resp,true);

    if(!isset($reply['id']))
    {
    $notify[] = ['error', 'Payment Session Not Faund'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    }
    if(!isset($reply['payment_intent']))
    {
    $notify[] = ['error', 'Payment Intent Not Faund'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    }
    
    if(!isset($reply['payment_status']))
    {
    $notify[] = ['error', 'Payment Status Not Faund'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify); 
    }
    $session_id = $reply['id'];
    $payment_intent = $reply['payment_intent'];
    $payment_status = $reply['payment_status'];
    
    if($payment_status != 'paid')
    {
    $notify[] = ['error', 'Payment Not Made Yet'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify); 
    }
    
    
    $order = Order::where('api_response',$session_id)->where('api_response2',$payment_intent)->firstOrFail();
    /*
    $amount = $reply['amount_total'];
    $amo = $amount/100;
    $oderamount = round($order->total_amount_usd,2);
    //return $oderamount;
    
    if($oderamount > $amo)
    {
      $notify[] = ['error', 'Lesser Amount Paid'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify); 
    } */
    
    if($payment_status == 'paid' && $deposit->status==0)
    {
       //return "PAID"; 
       $type = "Stripe Checkout";
        PaymentController::userOrderupdate($deposit->trx,$type);
         $notify[] = ['success', 'Your Order Has Been Placed'];
        session()->put('order_number_session', $deposit->order_number);
        $notify[] = ['success', 'Payment captured successfully.'];
        return redirect()->route('user.paymentsuccess');
    //return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    }
    
    else
    {
     $notify[] = ['error', 'Sorry We Cant Process This Payment At The Moment'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify);  
    }
    
    
   

    }


    public function ipn(Request $request)
    {
        $StripeAcc = GatewayCurrency::where('gateway_alias','StripeV3')->orderBy('id','desc')->first();
        $gateway_parameter = json_decode($StripeAcc->gateway_parameter);


        \Stripe\Stripe::setApiKey($gateway_parameter->secret_key);

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $gateway_parameter->end_point; // main
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];


        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the checkout.session.completed event
        
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            $deposit = Order::where('api_response',  $session->id)->orderBy('id', 'DESC')->first();

            if($deposit->status==0){
                $type = "Stripe Checkout";
                PaymentController::userOrderupdate($deposit->trx,$type);
                //PaymentController::userDataUpdate($deposit->trx);
            }
        }
        http_response_code(200);
    }
    
    
     public function ipndeposit(Request $request)
    {
    $track  = session()->get('Track'); 
    //$deposit = Order::where('api_response',  $session->id)->orderBy('id', 'DESC')->first();
    $deposit = Deposit::where('trx',  $track)->orderBy('id', 'DESC')->firstOrFail();
    $gate = Gateway::where('code', $deposit->method_code)->first();
        
    $gatecur    = GatewayCurrency::where('method_code', $deposit->method_code)->first();
        //return $gate;
    $StripeAcc = json_decode($gatecur->gateway_parameter);
    $sk = $StripeAcc->secret_key;
    
    $url = "https://api.stripe.com/v1/checkout/sessions/".$deposit->api_response;
    
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $headers = array(
       "Authorization: Bearer ".$sk,
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
    $resp = curl_exec($curl);
    curl_close($curl);
    $reply = json_decode($resp,true);

    if(!isset($reply['id']))
    {
    $notify[] = ['error', 'Deposit Session Not Faund'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    }
    if(!isset($reply['payment_intent']))
    {
    $notify[] = ['error', 'Deposit Intent Not Faund'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    }
    
    if(!isset($reply['payment_status']))
    {
    $notify[] = ['error', 'Deposit Status Not Faund'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify); 
    }
    $session_id = $reply['id'];
    $payment_intent = $reply['payment_intent'];
    $payment_status = $reply['payment_status'];
    
    if($payment_status != 'paid')
    {
    $notify[] = ['error', 'Deposit Not Made Yet'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify); 
    }
    
    
    $order = Deposit::where('api_response',$session_id)->where('api_response2',$payment_intent)->firstOrFail();
    
    /*
    $amount = $reply['amount_total'];
    $amo = $amount/100;
    $oderamount = round($order->amount,2);
    
    if($oderamount > $amo)
    {
      $notify[] = ['error', 'Lesser Amount Paid'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify); 
    } */
    
    if($payment_status == 'paid' && $deposit->status==0)
    {
       //return "PAID"; 
       $type = "Stripe Checkout";
            PaymentController::userDepositUpdate($deposit->trx);
            $notify[] = ['success', 'Payment captured successfully.'];
            return redirect()->route(gatewayRedirectUrl(true))->withNotify($notify);
    }
    
    else
    {
     $notify[] = ['error', 'Sorry We Cant Process This Payment At The Moment'];
    return redirect()->route(gatewayRedirectUrl())->withNotify($notify);  
    }
    
    
   

    }
}

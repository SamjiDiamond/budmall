<?php

namespace App\Http\Controllers\Gateway\Flutterwave;

use App\Models\Deposit;
use App\Models\Order;
use App\Models\Currency;
use App\Models\Orderpay;
use App\Models\GatewayCurrency;
use App\Models\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Auth;

class ProcessController extends Controller
{
    /*
     * flutterwave Gateway
     */

    public static function process($deposit)
    {
        $gatecur    = GatewayCurrency::where('method_code', $deposit->method_code)->first();
        $gate    = Gateway::where('code', $deposit->method_code)->first();
        $flutterAcc = json_decode($gatecur->gateway_parameter);
        
        $send['API_publicKey'] = $flutterAcc->public_key;
        $send['encryption_key'] = $flutterAcc->encryption_key;
        $send['customer_email'] = Auth::user()->email;
        $send['amount'] = round($deposit->total_amount_usd,2);
        $send['famount'] = round($deposit->final_amo,2);
        $send['customer_phone'] = Auth::user()->mobile;
        $send['currency'] = $deposit->method_currency;
        $send['txref'] = $deposit->trx;
        $send['usd'] = $deposit->total_amount_usd;
        $send['notify_url'] = url('ipn/flutterwave');

        $alias = $gate->alias;
        $send['view'] = 'user.payment.'.$alias;
        return json_encode($send);
    }

    public function ipn($track, $type)
    {

        $deposit = Order::where('order_number', $track)->orderBy('id', 'DESC')->first();
        
        $gate    = Gateway::where('code', $deposit->method_code)->first();
        $gatecur    = GatewayCurrency::where('method_code', $deposit->method_code)->first();
       

        if ($type == 'error') {
            $message = 'Transaction failed, Ref: ' . $track;
            $notify[] = ['error', $message];
            $notifyApi[] = $message;
            if ($deposit->from_api) {
                return response()->json([
                    'code'=>200,
                    'status'=>'ok',
                    'message'=>['error'=>$notifyApi]
                ]);
            }
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);

        }

        if (!isset($track)) {

            $message = 'Unable to process';
            $notify[] = ['error', $message];
            $notifyApi[] = $message;

            if ($deposit->from_api) {
                return response()->json([
                    'code'=>200,
                    'status'=>'ok',
                    'message'=>['error'=>$notifyApi]
                ]);
            }
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }

        $flutterAcc = json_decode($gatecur->gateway_parameter);
        
        $query = array(
            "SECKEY" =>  $flutterAcc->secret_key,
            "txref" => $track
        );

        $data_string = json_encode($query);
        $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);
        $rep = json_encode($response);
        
        $deposit->detail = $rep;
        $deposit->save();
        //return $response;


        if ($response->status == 'error') {
            $message = $response->message;
            $notify[] = ['error', $message];
            $notifyApi[] = $message;

            if ($deposit->from_api) {
                return response()->json([
                    'code'=>200,
                    'status'=>'ok',
                    'message'=>['error'=>$notifyApi]
                ]);
            }

            
            session()->forget('order_number');
            session()->forget('Track');
            return redirect()->route('user.orders','all')->withNotify($notify);
        }

        $rate = Currency::whereName('USD')->first()->rate;

        /*if ($response->data->status == "successful" && $response->data->chargecode == "00" && $deposit->method_currency = $response->data->currency && $deposit->status == '0' ) {
        return 56;
        }
        return $amount.$response->data->amount;*/
        $amount = $deposit->total_amount_usd;

        if ($response->data->status == "successful" && $response->data->chargecode == "00" && $amount = $response->data->amount && $deposit->method_currency == $response->data->currency && $deposit->status == '0') {
            $type = "Flutterwave";
            
             //return "Paid";
            PaymentController::userOrderupdate($deposit->trx,$type);


            $message = 'Transaction was successful, Ref: ' . $track;
            $notify[] = ['success', $message];
            $notifyApi[] = $message;

            if ($deposit->from_api) {
                return response()->json([
                    'code'=>200,
                    'status'=>'ok',
                    'message'=>['success'=>$notifyApi]
                ]);
            }
            
            session()->forget('order_number');
            session()->forget('Track');
            session()->forget('bfname');
            session()->forget('blname');
            session()->forget('bphone');
            session()->forget('bemail');
            session()->forget('bcity');
            session()->forget('bstate');
            session()->forget('bstaten');
            session()->forget('delicalc');
            session()->forget('shipmeth');
            
            return redirect()->route('user.orders','all')->withNotify($notify);
        }
        //return "Not Paid";
       

        $message = 'Unable to process';
        $notify[] = ['error', $message];
        $notifyApi[] = $message;

        if ($deposit->from_api) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$notifyApi]
            ]);
        }

        
        session()->forget('order_number');
        session()->forget('Track');
        session()->forget('delicalc');
        return redirect()->route('user.orders','all')->withNotify($notify);

    }
}

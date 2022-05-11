<?php

namespace App\Http\Controllers\Gateway;

use App\Models\Cart;
use App\Models\Admin;
use App\Models\AppliedCoupon;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Order;
use App\Models\Gateway;
use App\Models\Orderpay;
use App\Models\Deposit;
use App\Models\State;
use App\Models\Transaction;
use App\Models\Userwallet;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\GatewayCurrency;
use App\Rules\FileTypeValidate;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use Mail;

class PaymentController extends Controller
{
    public function __construct()
    {
        return $this->activeTemplate = activeTemplate();
    }

      public function fundwallet()
    {

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        $pageTitle = 'Fund Wallet';
        $logs = auth()->user()->deposits()->with(['gateway'])->whereOrderId(0)->orderBy('id','desc')->paginate(getPaginate());

        return view($this->activeTemplate . 'user.deposit.deposit', compact('logs','gatewayCurrency', 'pageTitle'));
    }

     public function fundwalletInsert(Request $request)
    {
        $request->validate([
            'method_code' => 'required',
            'amount' => 'required|numeric|max:100',
            'currency' => 'required',
        ]);

        $user = auth()->user();
        
        $depositsum = Deposit::whereUserId($user->id)->where('created_at', 'like', date("Y-m-d")."%")->whereStatus(1)->sum('amount');
       // return $depositsum;
        if ($depositsum >= 100) {
            $notify[] = ['error', 'You have reached the your daily limit of 100USD Deposit. Please come back tomorrow for more.'];
            return back()->withNotify($notify);
        }
        //return $depositsum;


        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }
        $charge     = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable    = $request->amount + $charge;
        $final_amo  = $payable * $gate->rate;

        $deposit               = new Deposit();
        $deposit->user_id          = $user->id;
        $deposit->method_code      = $gate->method_code;
        $deposit->method_currency  = strtoupper($gate->currency);
        $deposit->amount           = $request->amount;
        $deposit->charge           = $charge;
        $deposit->rate             = $gate->rate;
        $deposit->final_amo        = $final_amo;
        $deposit->btc_amo          = 0;
        $deposit->btc_wallet       = "";
        $deposit->trx              = getTrx();
        $deposit->try              = 0;
        $deposit->status           = 0;
        $deposit->order_id         = 0;
        $deposit->save();


        if ($deposit->method_code >= 1000) {
            $this->userDataUpdate($deposit);
            $notify[] = ['success', 'Your deposit request has been queued for approval.'];
            return back()->withNotify($notify);
        }
        session()->put('Track', $deposit->trx);
        return redirect()->route('user.fundwallet.preview');
    }

    public function fundwalletPreview()
    {
        $track      = session()->get('Track');

         $deposit    = Deposit::where('trx', $track)
                        ->where('status',0)
                        ->orderBy('id', 'DESC')
                        ->with('gateway')
                        ->firstOrFail();

        $dirName = $deposit->gateway->alias;

        $new = __NAMESPACE__ . '\\' . $dirName . '\\DepositController';
        //return $new;

        $data = $new::process($deposit);
        $data = json_decode($data);

        //return $data;

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if(@$data->session){
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Confirm Payment';
        $datas       = Deposit::where('trx', $track)->where('status',0)->orderBy('id', 'DESC')->firstOrFail();
        return view($this->activeTemplate . $data->view, compact('data', 'datas','pageTitle', 'deposit'));
    }
    
    
    public function OrderEnterOTP()
    {
        $token = session()->get('walletpay'); 
        
        if(!$token)
        {
            $notify[] = ['error', 'Invalid Order'];
            return back()->withNotify($notify);
        }
        $order    = Order::where('order_number', $token)
                        ->where('status',0)
                        ->firstOrFail();
        
        $pageTitle = 'Order OTP';
        return view($this->activeTemplate. 'user.orders.otp', compact('pageTitle', 'order', 'token'));

    }

    public function OrderConfirmOTP(Request $request)
    {
        $token = session()->get('walletpay'); 
        
        if(!$token)
        {
            $notify[] = ['error', 'Invalid Order Session'];
            return back()->withNotify($notify);
        }
        $order = Order::where('order_number', $token)->where('status',0)->firstOrFail();
        
        if($order->otp_fail > 0)
            {
        if($order->otp != $request->otp)
        {
            $notify[] = ['error', 'Invalid Activation Code. Please enter correct Code'];
            return back()->withNotify($notify);
         
        }
       
            }
       
        
        if($order->otp != $request->otp)
        {
            if($order->otp_try > 2)
            {
            
            if($order->otp_fail > 0)
            {
            $newotp = getTrx();
            $order->otp = $newotp;
            $order->otp_try = 0;
            $order->otp_fail = 1;
            $order->save();
          
            $user = User::whereId($order->user_id) ->firstOrFail();
            $text = "You have entered wrong OTP 3 times for your transaction with order number ".$order->order_number." Please enter the code below continue with the transaction.";
            $name = $user->username;
            $email = $user->email;
            $message = $text;
            $subject = "Activation Code";
             
              try {
                $data = array("name"=>$name, "content" => $message, "subject" => $subject, "otp" => $newotp);
            
                Mail::send("emails.otp", $data, function($message) use ($name, $email, $subject) {
                    $message->to($email, $name)->subject($subject);
                    $message->from(env("MAIL_USERNAME"),$subject);
                    });
                } catch (Exception $e) {
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                
                $headers .= 'From: <hi@budmall.ng>' . "\r\n";
                mail($email,$subject,$message,$headers);
                }
            }
             
                
            }
            if($order->otp_try < 4)
            {
            $order->otp_try += 1;
            $order->save();
            }
            
             if($order->otp_try > 2)
            {
            $order->otp_fail = 1;
            $order->save();
            
            if($order->otp != $request->otp)
            {
            $notify[] = ['error', 'Invalid Activation Code. Please enter correct Code'];
            return back()->withNotify($notify);
            }
            if($order->otp != $request->otp)
            {
            $order->otp_fail = 0;
            $order->otp_try = 0;
            $order->save();
            }
            
            
            
              }
            
            
            $notify[] = ['error', 'Invalid OTP. Please enter correct OTP'];
            return back()->withNotify($notify);
            
        }
            $order = Order::where('order_number', $token)->where('status',0)->firstOrFail();
            $user = User::whereId($order->user_id) ->firstOrFail();
            $general = GeneralSetting::first();
            $wallet = Userwallet::whereWcode($user->wcode)->first();
            $wallet->balance -= $order->total_amount_usd;
            $wallet->save();
            $type = "Wallet";
            
            PaymentController::userOrderUpdate($order->order_number,$type);
            
            $order->method_code      = 0; 
            $order->method_currency  = $general->cur_text;
            
             if(session('coupon'))
           {
            $coupon = Coupon::where('coupon_code', session('coupon')['code'])->first();
            $applied_coupon = new AppliedCoupon();
            $applied_coupon->user_id    = $user->id;
            $applied_coupon->coupon_id  = $coupon->id;
            $applied_coupon->order_id   = $order->id;
            $applied_coupon->amount     = $coupon_amount;
            $applied_coupon->save();
            session()->forget('coupon');
           }
           
            session()->put('order_number_session', $order->order_number);
            $order->save();
            
            
            session()->forget('order_number');
            session()->forget('walletpay');
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
             
            $notify[] = ['success', 'Your order has been received. Please check your email for confirmation message'];
        return redirect()->route('user.paymentsuccess');
             
 
         
    }




    public function OrderConfirm()
    {
        $track      = session()->get('Track'); 
        $deposit    = Order::where('order_number', $track)
                        ->where('status',0)
                        ->firstOrFail();
       

        if ($deposit->method_code >= 1000) {
            $this->userDataUpdate($deposit);
            $notify[] = ['success', 'Your order request is queued for approval.'];
            return back()->withNotify($notify);
        }
        
        $gate    = Gateway::where('code', $deposit->method_code)->first();
        $gatecur    = GatewayCurrency::where('method_code', $deposit->method_code)->first();
        $dirName = $gate->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if(@$data->session){
            $deposit->api_response = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('gatecur','gate','data', 'pageTitle', 'deposit'));
    }


    public static function userOrderupdate($trx,$type)
    {
        $general = GeneralSetting::first();
        $track  = session()->get('order_number');
        $data = Order::where('order_number', $track)->first();
        $user  = User::find($data->user_id);
 
        $gate    = Gateway::where('code', $data->method_code)->first();
        
        if(empty($gate))
        {
            $type = "Wallet";
        }
       
        if ($data->status == 0) {
            $data->payment_status = 1;
           
           
            $transaction = new Transaction();
            $transaction->user_id = $data->user_id;
            $transaction->amount = $data->total_amount;
            $transaction->usd_amount = $data->total_amount_usd;
            $transaction->charge = $data->total_amount;
            $transaction->trx_type = '-';
            $transaction->action = 'Payment Via ' . $type;
            $transaction->details = 'Payment For order with transaction number ' . $data->order_number;
            $transaction->trx = $data->order_number;
            
            
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'Order Payment successful via '.$type;
            $adminNotification->click_url = urlPath('admin.deposit.successful');
            $adminNotification->save();
            
            
           
            ####Send Message To Buyer#######
            OrderEmailController::emailbuyer($user, $track);
            
            ####Send Message To Beneficiary#######
             if ($data->notify > 0) 
             {
            OrderEmailController::emailbeneficiary($user, $track);
             }
            
            ####Send Message To Admin Of Paid Order#######
            OrderEmailController::emailadminorder($user, $track);
            
            ####Find Dispatch and Send Message To Dispatch Personnel, Admin as well as to buyer about the dispatched order#######
            $dispatcher = Admin::whereType(1)->whereStatus(1)->whereState($data->state)->first();
            if($dispatcher)
            {
             OrderEmailController::emailagent($user, $track);
             OrderEmailController::emailbuyersent($user, $track);
             OrderEmailController::emailadminsent($user, $track);
            }
            ####### if no dispatch, Send Message To admin#######
            else
            {
            OrderEmailController::emailadminnotsent($user, $track);
            }
            
              if(session('coupon'))
           {
            $coupon = Coupon::where('coupon_code', session('coupon')['code'])->first();
            
            if($coupon)
            {
            $applied_coupon = new AppliedCoupon();
            $applied_coupon->user_id    = $user->id;
            $applied_coupon->coupon_id  = $coupon->id;
            $applied_coupon->order_id   = $data->id;
            $applied_coupon->amount     = $data->coupon_amount;
            $applied_coupon->save();
            session()->forget('coupon');   
            }
           
           }
           
            
            
            $transaction->save();
            ###### Pay Referral Earning To Agent #######
            $ref = User::whereRefCode($user->ref)->first();
            
            if(!empty($ref))
            {
                $calc = $data->cart_amount/100;
                if($ref->sc > 0)
                {
                $earning = $calc*$general->scref;
                }
                else
                {
                $earning = $calc*$general->ref;
                }
                $refwallet = Userwallet::whereWcode($ref->ref_code)->first();
                if($refwallet)
                {
                $usd = $earning/$general->usdrate;
                $refwallet->balance += $usd;
                $refwallet->save();
                
                $refearn = new Transaction();
                $refearn->user_id = $ref->id;
                $refearn->amount = $earning;
                $refearn->usd_amount = $usd;
                $refearn->ref = 1;
                $refearn->refer = $user->id;
                $refearn->charge = 0;
                $refearn->trx_type = '+';
                $refearn->action = 'Referral Earning';
                $refearn->details = 'Referral earning from ' . $user->username;
                $refearn->trx = getTrx();
                $refearn->save();
                }
            } 
            

        }
            $data->save();
        
            Cart::where('user_id', auth()->id() )->delete();
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
            
    /*GOOGLE ANALYTICS TRACKER FOR SUCCESSFUL PURCHASE */  
        
    /*GOOGLE ANALYTICS TRACKER FOR SUCCESSFUL PURCHASE */
            
            
            
    }


    public static function userDepositUpdate($trx)
    {
        $general = GeneralSetting::first();
        $data = Deposit::where('trx', $trx)->first();
        $gate = Gateway::where('code', $data->method_code)->first();

        if ($data->status == 0) {
            $data->status = 1;
            $data->save();
            $user = User::find($data->user_id);
            
            $user = auth()->user();
            $wallet = Userwallet::whereWcode($user->wcode)->first();

            $wallet->balance += $data->amount;
            $wallet->save();
            
            $transaction = new Transaction();
            $transaction->user_id = $data->user_id;
            $transaction->amount = $data->amount;
            $transaction->action = "Deposit With ".$gate->name;
            $transaction->usd_amount = $data->amount;
            $transaction->post_balance = $wallet->balance;
            $transaction->charge = $data->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Deposit Via ' . $data->gatewayCurrency()->name;
            $transaction->trx = $data->trx;
            $transaction->save();

            

            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'Payment successful via '.$data->gatewayCurrency()->name;
            $adminNotification->click_url = urlPath('admin.deposit.successful');
            $adminNotification->save();

            notify($user, 'DEPOSIT_COMPLETE', [
                'method_name' => $data->gatewayCurrency()->name,
                'method_currency' => $data->method_currency,
                'method_amount' => showAmount($data->final_amo),
                'amount' => showAmount($data->amount),
                'charge' => showAmount($data->charge),
                'currency' => $general->cur_text,
                'rate' => showAmount($data->rate),
                'post_balance' => showAmount($wallet->balance),
                'trx' => $data->trx,
                'order_id' => $data->trx
            ]);

        }
    }




}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignProductAttribute;
use App\Models\Bill;
use App\Models\Cabletvbundle;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\GatewayCurrency;
use App\Models\GeneralSetting;
use App\Models\Internetbundle;
use App\Models\Network;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Power;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\State;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Userwallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mail;
use Session;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function orders()
    {
        $query = Order::where('user_id', auth()->user()->id)->whereIn('payment_status', [1, 2]);
        $orders = $query->latest()->with('deposit', 'orderDetail','appliedCoupon')->select('id', 'order_number', 'status', 'payment_status')->get();

//        $order = Order::where('order_number', $order_number)->where('user_id', auth()->user()->id)->with('deposit', 'orderDetail','appliedCoupon')->first();

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Orders fetched successfully',
            'data' => $orders
        ]);
    }

    public function orderDetails($order_number)
    {
        $order = Order::where('order_number', $order_number)->where('user_id', auth()->user()->id)->with('deposit', 'orderDetail', 'appliedCoupon')->first();

        if (!$order) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Order not found',
            ]);
        }

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Order fetched successfully',
            'data' => $order
        ]);
    }


    public function paymentcomplete()
    {
        $pageTitle = 'Payment Completed';
        $paysess = session('order_number_session');
        $order = Order::where('order_number', $paysess)->where('user_id', auth()->user()->id)->first();
        return view($this->activeTemplate . 'user.orders.complete', compact('order', 'pageTitle'));
    }


    public function confirmOrder(Request $request)
    {
        $general = GeneralSetting::first();

        $validator = Validator::make($request->all(), [
            'shipping_method' => 'required|integer',
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
            'mobile' => 'required|max:50',
            'email' => 'required|max:90',
            //'address'           => 'required|max:50',
            'location' => 'required',
            'state' => 'required|max:50',
            //'zip'               => 'required|max:50',
            //'country'           => 'required|max:50',
            'payment' => 'required',
            'notify' => 'required',
            'coupon_code' => 'nullable',
            'type' => 'required',
            'delivery_calculation' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => $validator->errors()->all(),
            ]);
        }


        $type = $request->type;
        $ordertrx = getTrx();

        if ($request->payment == "cod") {

            $payment_status = 2;

            if (!$general->cod) {
                return response()->json([
                    'code' => 200,
                    'status' => false,
                    'message' => 'Cash on delivery is not available now'
                ]);
            }
        }

        $general = GeneralSetting::first();

        $carts_data = Cart::where('session_id', session('session_id'))->orWhere('user_id', auth()->user()->id ?? null)->get();


        $coupon_amount = 0;
        $coupon_code = null;
        $cart_total = 0;
        $delivery = $request->delivery_calculation;
        $product_categories = [];
        //return $carts_data;

        if (count($carts_data) < 1){
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => "Your cart is currently empty"
            ]);
        }

        foreach ($carts_data as $cart) {
            //$product_categories[] = $cart->product->categories->pluck('id')->toArray();

            $product_categories[] = Category::whereId($cart->product_id)->pluck('id')->toArray();

            /* if($cart->product->offer && $cart->product->offer->activeOffer)
             {
                $offer_amount = calculateDiscount($cart->product->offer->activeOffer->amount, $cart->product->offer->activeOffer->discount_type, $cart->product->base_price);
             }else
             {
                 $offer_amount = 0;
             }*/
            $offer_amount = 0;
            $product = Product::whereId($cart->product_id)->first();


            if ($cart->attributes != null) {
                $attr_item = AssignProductAttribute::productAttributesDetails($cart->attributes);
                $attr_item['offer_amount'] = $offer_amount;
                $sub_total = (($product->base_price + $attr_item['extra_price']) - $offer_amount) * $cart->quantity;
                unset($attr_item['extra_price']);
            } else {
                $details['variants'] = null;
                $details['offer_amount'] = $offer_amount;
                $sub_total = ($product->base_price - $offer_amount) * $cart->quantity;
            }
            $cart_total += $sub_total;
        }

        if (isset($request->coupon_code)) {
            $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();
            // Check Minimum Subtotal
            if ($cart_total < $coupon->minimum_spend) {
                return response()->json([
                    'code' => 200,
                    'status' => false,
                    'message' => "Sorry your have to order minimum amount of $coupon->minimum_spend $general->cur_text"
                ]);
            }
            // Check Maximum Subtotal
            if ($coupon->maximum_spend != null && $cart_total > $coupon->maximum_spend) {
                return response()->json([
                    'code' => 200,
                    'status' => false,
                    'message' => "Sorry your have to order maximum amount of $coupon->maximum_spend $general->cur_text"
                ]);

            }
            //Check Limit Per Coupon
            if ($coupon->appliedCoupons->count() >= $coupon->usage_limit_per_coupon) {
                return response()->json([
                    'code' => 200,
                    'status' => false,
                    'message' => "Sorry your Coupon has exceeded the maximum Limit For Usage"
                ]);
            }
            //Check Limit Per User
            if ($coupon->appliedCoupons->where('user_id', auth()->id())->count() >= $coupon->usage_limit_per_user) {
                return response()->json([
                    'code' => 200,
                    'status' => false,
                    'message' => "Sorry you have already reached the maximum usage limit for this coupon"
                ]);
            }
            //$product_categories = array_unique(array_flatten($product_categories));
            if ($coupon) {
                /* $coupon_categories = $coupon->categories->pluck('id')->toArray();
                 $coupon_products = $coupon->products->pluck('id')->toArray();

                 $cart_products = $carts_data->pluck('product_id')->unique()->toArray();

                 if(empty(array_intersect($coupon_products, $cart_products))){
                     if(empty(array_intersect($product_categories, $coupon_categories))){
                         $notify[]=['error', 'The coupon is not available for some products on your cart.'];
                         return redirect()->back()->withNotify($notify);
                     }
                 }*/
                if ($coupon->discount_type == 1) {
                    $coupon_amount = $coupon->coupon_amount;
                } else {
                    $coupon_amount = ($cart_total * $coupon->coupon_amount) / 100;
                }
                $coupon_code = $coupon->coupon_code;
            }
        }

        foreach ($carts_data as $cd) {
            $pid = $cd->product_id;
            $attr = $cd->attributes;
            $attr = $cd->attributes ? json_encode($cd->attributes) : null;
            /*if($cd->product->track_inventory){
                $stock  = Product::where('id', $pid)->first();
                if($stock){
                    $stock->quantity   -= $cd->quantity;
                    $stock->save();
                    $log = new StockLog();
                    $log->stock_id  = $stock->id;
                    $log->quantity  = $cd->quantity;
                    $log->type      = 2;
                    $log->save();
                }
           }*/
        }

        $shipping_data = ShippingMethod::find($request->shipping_method);

        $shipping_address = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'country' => 'Nigeria',
            'city' => $request->location,
            'state' => State::whereId($request->state)->first()->state ?? "N/A",
            //'zip'       => $request->zip,
            //'address'   => $request->address,
        ];

        $takeorder = Order::where('order_number', $ordertrx)->first();
        if (empty($takeorder)) {


            $otp = random_int(100000, 999999);

            $order = new Order;
            $order->otp = $otp;
            $order->state = $request->state;
            $order->notify = $request->notify;
            $order->status = 0;
            $order->order_number = $ordertrx;
            $order->user_id = auth()->user()->id;
            $order->shipping_address = json_encode($shipping_address);
            $order->shipping_method_id = $request->shipping_method;
            $order->shipping_charge = $delivery;
            $order->order_type = $type;
            $order->payment_status = $payment_status ?? 0;

            if (isset($request->coupon_code)) {
                $order->coupon_code = $coupon_code;
                $order->coupon_amount = $coupon_amount;
            }

            $general = GeneralSetting::first();
            $calc = $cart_total - $coupon_amount;
            $vat = $calc / 100 * $general->vat;

            //return $coupon_amount;

            $total = $cart_total - $coupon_amount + $delivery + $vat;
            $rate = Currency::whereName('USD')->first();
            //return $rate;

            $order->vat = $vat;
            $order->cart_amount = $cart_total;
            $order->total_amount_usd = $total / $rate->rate_ngn;
            $order->total_amount = getAmount($total);
            $order->save();

            $details = [];

            foreach ($carts_data as $cart) {
                $product = Product::whereId($cart->product_id)->first();
                $od = new OrderDetail();
                $od->order_id = $order->id;
                $od->product_id = $cart->product_id;
                $od->product_name = $product->name;
                $od->quantity = $cart->quantity;
                $od->base_price = $product->base_price;
                $od->seller_id = $product->seller_id;

                /* if($cart->product->offer && $cart->product->offer->activeOffer){
                     $offer_amount       = calculateDiscount($cart->product->offer->activeOffer->amount, $cart->product->offer->activeOffer->discount_type, $cart->product->base_price);
                 }*/

                /*else */

                $offer_amount = 0;


                if ($cart->attributes != null) {
                    $attr_item = AssignProductAttribute::productAttributesDetails($cart->attributes);
                    $attr_item['offer_amount'] = $offer_amount;
                    //$sub_total                   = (($cart->product->base_price + $attr_item['extra_price']) - $offer_amount) * $cart->quantity;
                    $sub_total = (($product->base_price + $attr_item['extra_price']) - $offer_amount) * $cart->quantity;
                    $od->total_price = $sub_total;
                    unset($attr_item['extra_price']);
                    $od->details = json_encode($attr_item);
                } else {
                    $details['variants'] = null;
                    $details['offer_amount'] = $offer_amount;
                    $sub_total = ($product->base_price - $offer_amount) * $cart->quantity;
                    //$sub_total                  = ($cart->product->base_price  - $offer_amount) * $cart->quantity;

                    $od->total_price = $sub_total;
                    $od->details = json_encode($details);
                }
                $od->save();
            }

        }
        //return 76;
        $takeorder = Order::where('order_number', $ordertrx)->first();

        /*if($coupon_code != null)
        {
            $applied_coupon = new AppliedCoupon();
            $applied_coupon->user_id    = auth()->id();
            $applied_coupon->coupon_id  = $coupon->id;
            $applied_coupon->order_id   = $order->id;
            $applied_coupon->amount     = $coupon_amount;
            $applied_coupon->save();
        }*/

        //Remove coupon from session
        //if(session('coupon'))
        //    {
        //      session()->forget('coupon');
        //}


        $user = auth()->user();
        if ($request->payment == "wallet") {

            $wallet = Userwallet::whereWcode($user->wcode)->first();
            if (!$wallet) {
                $wcode = getTrx();
                $newwallet = new UserWallet;
                $newwallet->wcode = $wcode;
                $newwallet->balance = 0;
                $newwallet->save();
                $user->wcode = $wcode;
                $user->save();
            }
            $wallet = Userwallet::whereWcode($user->wcode)->first();


            if ($wallet->balance < $takeorder->total_amount_usd) {
                return response()->json([
                    'code' => 200,
                    'status' => false,
                    'message' => 'You dont have enough balance in your wallet to make this purchase. Please deposit fund into wallet and try again'
                ]);
            } elseif ($wallet->balance >= $takeorder->total_amount_usd) {
                $wallet->balance -= $takeorder->total_amount_usd;
                $wallet->save();

                $takeorder->payment_status=1;
                $takeorder->save();


                Cart::where('session_id', session('session_id'))->orWhere('user_id', auth()->user()->id ?? null)->delete();

                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'message' => 'Checkout successfully with wallet',
                    'data' => $takeorder->order_number
                ]);
            }

        }

        if($request->payment  == "cod")
        {

            Cart::where('session_id', session('session_id'))->orWhere('user_id', auth()->user()->id??null)->delete();

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Your order has been submitted successfully please wait for a confirmation email',
                'data' => $takeorder->order_number
            ]);
        }

        if ($request->payment  == "card") {

            $percent_charge = 2;

            $charge = $takeorder->total_amount * $percent_charge / 100;
            $payable = $takeorder->total_amount + $charge;
            $final_amo = $payable;

            $takeorder->method_code = "10";
            $takeorder->method_currency = strtoupper("NGN");

            $takeorder->save();

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Checkout Successful with card',
                'data' => $takeorder->order_number,
                'total' =>$final_amo
            ]);

        }


    }

    public function getOrderTrackData(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_number' => 'required|max:160',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $order_number = $request->order_number;
        $order_data = Order::where('order_number', $order_number)->first();
        if ($order_data) {
            $p_status = $order_data->payment_status;
            $status = $order_data->status;

            return response()->json(['success' => true, 'payment_status' => $p_status, 'status' => $status]);
        } else {
            $notify = 'No order found';
            return response()->json(['success' => false, 'message' => $notify]);
        }
    }


    public function airtime()
    {
        $network = Network::whereAirtime(1)->get();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => "Fetched successfully",
            'data' => $network
        ]);
    }

    public function airtimebuy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric',
            'network' => 'required',
            'amount' => 'required|numeric|min:50',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => $validator->errors()->all(),
            ]);
        }


        $user = auth()->user();
        $general = GeneralSetting::first();
        $wallet = Userwallet::whereWcode($user->wcode)->first();

        if (!$wallet) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Invalid Wallet',
            ]);
        }
        $network = Network::whereSymbol($request->network)->first();
        $usd = $request->amount;

        if (!$network) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Invalid Network',
            ]);
        }

        if ($usd < 1) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Invalid amount',
            ]);

        }

        if ($usd > $wallet->balance) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'You dont have enough balance to execute this transaction.',
            ]);

        }


        $mode = env('MODE');
        $username = env('VTPASSUSERNAME');
        $password = env('VTPASSPASSWORD');
        $str = $username . ':' . $password;
        $auth = base64_encode($str);

        $trx = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890'), 0, 10);
        if ($mode == 0) {
            $url = 'https://sandbox.vtpass.com/api/pay';
        } else {
            $url = 'https://vtpass.com/api/pay';
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => '{
        "amount": "' . $request->amount . '",
        "phone": "' . $request->phone . '",
        "serviceID": "' . $request->network . '",
        "request_id": "' . $trx . '"
        }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json',
            ),
        ));

        $resp = curl_exec($curl);
        $reply = json_decode($resp, true);
        curl_close($curl);
//    return $resp;
        //return $reply['content']['transactions']['transactionId'];


        if (!isset($reply['code'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant proceed with this payment at the moment. Please try again later',
            ]);
        }

        if (isset($reply['content']['errors'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'API ' . $reply['content']['errors'],
            ]);
        }

        if ($reply['code'] == 014) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => $reply['response_description'],
            ]);
        }

        if ($reply['code'] != "000") {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant process this payment at the moment',
            ]);
        }
        //return $reply;

        if (!isset($reply['content']['transactions']['transactionId'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant process this payment at the moment',
            ]);
        }

        if ($reply['code'] == "000") {
            $wallet->balance -= $usd;
            $wallet->save();

            $transaction = new Bill();
            $transaction->user_id = $user->id;
            $transaction->usd = $usd;
            $transaction->amount = $request->amount;
            $transaction->api_charge = $reply['content']['transactions']['total_amount'];
            $transaction->api_commission = $reply['content']['transactions']['commission'];
            $transaction->trx = getTrx();
            $transaction->phone = $request->phone;
            $transaction->network = $request->network;
            $transaction->newbalance = $wallet->balance;
            $transaction->type = 1;
            $transaction->api = json_encode($reply['content']['transactions']);
            $transaction->status = 1;
            $transaction->save();

            $transactions = new Transaction();
            $transactions->user_id = $user->id;
            $transactions->amount = $request->amount;
            $transactions->usd_amount = $usd;
            $transactions->charge = $request->amount;
            $transactions->trx_type = '-';
            $transactions->action = 'Purchased ' . $network->name . ' Airtime';
            $transactions->details = 'Payment For ' . $network->name . ' Airtime with transaction number ' . $transaction->trx;
            $transactions->trx = $transaction->trx;
            $transactions->save();

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Airtime Recharge Was Successful',
            ]);

        }

    }


    public function internet()
    {
        $plans = Internetbundle::whereStatus(1)->get();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => "Fetched successfully",
            'data' => $plans
        ]);
    }


    public function internetbuy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:11|',
            'network' => 'required|string|',
            'plan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => $validator->errors()->all(),
            ]);
        }

        $settings = GeneralSetting::first();

        $network = Network::whereAirtime(1)->whereSymbol($request->network)->first();
        $internet = Internetbundle::wherePlan($request->plan)->first();
        $mode = env('MODE');
        $username = env('VTPASSUSERNAME');
        $password = env('VTPASSPASSWORD');
        $str = $username . ':' . $password;
        $auth = base64_encode($str);

        $user = auth()->user();
        $general = GeneralSetting::first();
        $wallet = Userwallet::whereWcode($user->wcode)->first();

        $network = Network::whereSymbol($request->network)->first();
        $usd = $internet->cost / $general->usdrate;

        if ($wallet->balance < $usd) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'You dont have enough balance to execute this transcation.'
            ]);
        }

        if ($mode == 0) {
            $url = 'https://sandbox.vtpass.com/api/pay';
        } else {
            $url = 'https://vtpass.com/api/pay';
        }
        $code = getTrx();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{
         "amount": "' . $request->amount . '",
         "phone": "' . $request->phone . '",
         "billersCode": "' . $request->phone . '",
         "serviceID": "' . $internet->code . '",
         "variation_code": "' . $internet->plan . '",
         "request_id": "' . $code . '"
         }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json',
            ),
        ));

        $resp = curl_exec($curl);
        $reply = json_decode($resp, true);
        curl_close($curl);
        //return  $resp;


        if (!isset($reply['code'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant proceed with this payment at the moment. Please try again later'
            ]);
        }

        if (isset($reply['content']['errors'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'API ' . $reply['content']['errors']
            ]);
        }

        if ($reply['code'] != "000") {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant service your request at the moment'
            ]);
        }

        if (!isset($reply['content']['transactions']['transactionId'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant process tbis payment at the moment'
            ]);
        }

        if ($reply['code'] == "000") {

            $wallet->balance -= $usd;
            $wallet->save();

            $transaction = new Bill();
            $transaction->user_id = $user->id;
            $transaction->usd = $usd;
            $transaction->amount = $internet->cost;
            $transaction->api_charge = $reply['content']['transactions']['total_amount'];
            $transaction->api_commission = $reply['content']['transactions']['commission'];
            $transaction->api = json_encode($reply['content']['transactions']);
            $transaction->trx = getTrx();
            $transaction->phone = $request->phone;
            $transaction->network = $request->network;
            $transaction->accountname = $internet->name;
            $transaction->newbalance = $wallet->balance;
            $transaction->type = 2;
            $transaction->status = 1;
            $transaction->save();

            $transactions = new Transaction();
            $transactions->user_id = $user->id;
            $transactions->amount = $internet->cost;
            $transactions->usd_amount = $usd;
            $transactions->charge = $internet->cost;
            $transactions->trx_type = '-';
            $transactions->action = 'Purchased ' . $network->name . ' Internet Subscription';
            $transactions->details = 'Payment For ' . $network->name . ' Internet Subscription with transaction number ' . $transaction->trx;
            $transactions->trx = $transaction->trx;
            $transactions->save();

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Internet Subscription Was Successful'
            ]);

        }

    }


    public function cabletv()
    {
        $bill = Cabletvbundle::whereStatus(1)->latest()->get();

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => "Fetched successfully",
            'data' => $bill
        ]);
    }

    public function validatedecoder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required',
            'decoder' => 'required|string',
            'plan' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => $validator->errors()->all(),
            ]);
        }

        $settings = GeneralSetting::first();

        $decoder = Cabletvbundle::wherePlan($request->plan)->first();
        $mode = env('MODE');
        $username = env('VTPASSUSERNAME');
        $password = env('VTPASSPASSWORD');
        $str = $username . ':' . $password;
        $auth = base64_encode($str);

        $user = auth()->user();
        $general = GeneralSetting::first();
        $wallet = Userwallet::whereWcode($user->wcode)->first();
        $total = $decoder->cost + env('CABLECHARGE');
        $usd = $total;
        if ($usd > $wallet->balance) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Insufficient Balance',
            ]);
        }

        if ($mode == 0) {
            $url = 'https://sandbox.vtpass.com/api/merchant-verify';
        } else {
            $url = 'https://vtpass.com/api/merchant-verify';
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => '{
         "billersCode": "' . $request->number . '",
        "serviceID": "' . $decoder->code . '"
        }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json',
            ),
        ));

        $resp = curl_exec($curl);
        $reply = json_decode($resp, true);
        curl_close($curl);
        //return  $resp;

        //return $reply['code'];
        if (!isset($reply['code'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant proceed with this payment at the moment. Please try again later',
            ]);
        }

        if (isset($reply['content']['errors'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'API ' . $reply['content']['errors'],
            ]);
        }

        if ($reply['code'] != "000") {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant validate this decoder/IUC number at the moment',
            ]);
        }

        if (isset($reply['content']['Customer_Name'])) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Validated successfully',
                'data' => $reply['content']['Customer_Name']
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant validate this decoder/IUC number at the moment',
            ]);
        }

    }


    public function decoderpay(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'number' => 'required',
            'customer' => 'required',
            'plan' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => $validator->errors()->all(),
            ]);
        }


        $decoder = Cabletvbundle::wherePlan($request->plan)->first();

        $user = auth()->user();
        $general = GeneralSetting::first();
        $wallet = Userwallet::whereWcode($user->wcode)->first();

        $total = $decoder->cost + env('CABLECHARGE');
        $usd = $total;
        if ($usd > $wallet->balance) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Insufficient Wallet Balance'
            ]);
        }
        $mode = env('MODE');
        $username = env('VTPASSUSERNAME');
        $password = env('VTPASSPASSWORD');
        $str = $username . ':' . $password;
        $auth = base64_encode($str);


        if ($mode == 0) {
            $url = 'https://sandbox.vtpass.com/api/pay';
        } else {
            $url = 'https://vtpass.com/api/pay';
        }
        $code = getTrx();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => '{
     "billersCode": "' . $request->number . '",
     "variation_code": "' . $request->plan . '",
     "phone": "' . $user->mobile . '",
    "serviceID": "' . $decoder->code . '",
    "request_id": "' . $code . '"
    }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json',
            ),
        ));

        $resp = curl_exec($curl);
        $reply = json_decode($resp, true);
        curl_close($curl);
        //return  $resp.$decoder->cost;


        if (!isset($reply['code'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant proceed with this payment at the moment. Please try again later'
            ]);
        }


        if (isset($reply['content']['errors'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'API ' . $reply['content']['errors']
            ]);
        }


        if ($reply['code'] != "000") {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant process this payment at the moment'
            ]);
        }


        if (!isset($reply['content']['transactions']['transactionId'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant process tbis payment at the moment'
            ]);
        }

        if ($reply['code'] == "000") {


            $wallet->balance -= $usd;
            $wallet->save();


            $transaction = new Bill();
            $transaction->user_id = $user->id;
            $transaction->amount = $decoder->cost;
            $transaction->trx = getTrx();
            $transaction->phone = $request->number;
            $transaction->network = $decoder->network;
            $transaction->api_charge = $reply['content']['transactions']['total_amount'];
            $transaction->api_commission = $reply['content']['transactions']['commission'];
            $transaction->api = json_encode($reply['content']['transactions']);
            $transaction->plan = $decoder->name;
            $transaction->accountname = $request->customer;
            $transaction->newbalance = $wallet->balance;
            $transaction->usd = $usd;
            $transaction->type = 3;
            $transaction->status = 1;
            $transaction->save();


            $transactions = new Transaction();
            $transactions->user_id = $user->id;
            $transactions->amount = $decoder->cost;
            $transactions->usd_amount = $usd;
            $transactions->charge = env('CABLECHARGE');
            $transactions->trx_type = '-';
            $transactions->action = 'Purchased ' . $decoder->name . ' TV Subscription';
            $transactions->details = 'Payment For ' . $decoder . ' Cable TV Subscription with transaction number ' . $transaction->trx;
            $transactions->trx = $transaction->trx;
            $transactions->save();

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Payment Was Successfully'
            ]);
        }


    }


    public function utility()
    {
        $network = Power::whereStatus(1)->latest()->get();

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Payment Was Successfully',
            'data' => $network
        ]);
    }


    public function validatebill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required',
            'company' => 'required|string',
            'type' => 'required',
            'amount' => 'required|integer|min:1000',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => $validator->errors()->all(),
            ]);
        }


        $meter = Power::whereBillercode($request->company)->first();
        $mode = env('MODE');
        $username = env('VTPASSUSERNAME');
        $password = env('VTPASSPASSWORD');
        $str = $username . ':' . $password;
        $auth = base64_encode($str);
        $user = auth()->user();
        $total = $request->amount + env('POWERCHARGE');
        $general = GeneralSetting::first();
        $wallet = Userwallet::whereWcode($user->wcode)->first();

        $usd = $total / $general->usdrate;
        if ($usd > $wallet->balance) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Insufficient Wallet Balance',
            ]);
        }

        if ($mode == 0) {
            $url = 'https://sandbox.vtpass.com/api/merchant-verify';
        } else {
            $url = 'https://vtpass.com/api/merchant-verify';
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => '{
     "billersCode": "' . $request->number . '",
    "serviceID": "' . $meter->billercode . '",
    "type": "' . $request->type . '"
    }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json',
            ),
        ));

        $resp = curl_exec($curl);
        $reply = json_decode($resp, true);
        curl_close($curl);
//return  $resp;

        if ($reply['code'] != "000") {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant validate this decoder/IUC number at the moment'
            ]);
        }

        if (isset($reply['content']['errors'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'API ' . $reply['content']['errors']
            ]);
        }

        if ($reply['code'] == "000") {

            if (!isset($reply['content']['Customer_Name'])) {
                return response()->json([
                    'code' => 200,
                    'status' => false,
                    'message' => 'Sorry, We cant validate this Meter number at the moment'
                ]);
            }

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Validated successfully',
                'data' => ['customer' => $reply['content']['Customer_Name'], 'address' => $reply['content']['Address']]
            ]);
        }


    }

    public function billpay(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'company' => 'required|string',
            'type' => 'required',
            'amount' => 'required|integer|min:1000',

        ]);

        $mode = env('MODE');
        $username = env('VTPASSUSERNAME');
        $password = env('VTPASSPASSWORD');
        $str = $username . ':' . $password;
        $auth = base64_encode($str);
        $user = auth()->user();
        $general = GeneralSetting::first();
        $wallet = Userwallet::whereWcode($user->wcode)->first();
        $total = $request->amount + env('POWERCHARGE');

        $usd = $total;

        $meter = Power::whereBillercode($request->company)->first();
        if ($usd > $wallet->balance) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Insufficient Wallet Balance'
            ]);
        }

        if ($mode == 0) {
            $url = 'https://sandbox.vtpass.com/api/pay';
        } else {
            $url = 'https://vtpass.com/api/pay';
        }
        $code = getTrx();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{
     "billersCode": "' . $request->number . '",
     "variation_code": "' . $request->type . '",
     "phone": "' . $user->mobile . '",
    "serviceID": "' . $meter->billercode . '",
    "amount": "' . $request->amount . '",
    "request_id": "' . $code . '"
    }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json',
            ),
        ));

        $resp = curl_exec($curl);
        $reply = json_decode($resp, true);
        curl_close($curl);
//return $resp;


        if (!isset($reply['code'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant process this payment at the moment'
            ]);
        }

//return $reply;
        if (isset($reply['content']['errors'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'API ' . $reply['content']['errors']
            ]);
        }


        if ($reply['code'] != "000") {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant process this payment at the moment'
            ]);
        }


        if (!isset($reply['content']['transactions']['transactionId'])) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'message' => 'Sorry, We cant process this payment at the moment'
            ]);
        }
        if ($reply['code'] == "000") {

            $wallet->balance -= $usd;
            $wallet->save();


            $transaction = new Bill();
            $transaction->user_id = $user->id;
            $transaction->amount = $request->amount;
            $transaction->trx = $code;
            $transaction->phone = $request->number;
            $transaction->network = $meter->name;
            $transaction->usd = $usd;
            if (isset($reply['mainToken'])) {
                $transaction->accountnumber = $reply['mainToken'] . '<br> Units: ' . $reply['mainTokenUnits'];
            } else {
                $transaction->accountnumber = "Null";
            }

            $transaction->accountname = 'Meter: ' . $reply['content']['transactions']['product_name'] . '<br>Meter Number: ' . $reply['content']['transactions']['unique_element'];
            $transaction->newbalance = $wallet->balance;

            $transaction->api_charge = $reply['content']['transactions']['total_amount'];
            $transaction->api_commission = $reply['content']['transactions']['commission'];
            $transaction->api = json_encode($reply['content']['transactions']);

            $transaction->type = 4;
            $transaction->status = 1;
            $transaction->save();


            $transactions = new Transaction();
            $transactions->user_id = $user->id;
            $transactions->amount = $request->amount;
            $transactions->usd_amount = $usd;
            $transactions->charge = env('POWERCHARGE');
            $transactions->trx_type = '-';
            $transactions->action = 'Paid ' . $meter->name . ' Utility Bill';
            $transactions->details = 'Payment For ' . $meter->name . ' utility bill with transaction number ' . $transaction->trx;
            $transactions->trx = $transaction->trx;
            $transactions->save();

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Payment was successful'
            ]);
        }


    }


    public function utilitytoken($id)
    {


        $mode = env('MODE');
        $username = env('VTPASSUSERNAME');
        $password = env('VTPASSPASSWORD');
        $str = $username . ':' . $password;
        $auth = base64_encode($str);
        $user = auth()->user();
        $general = GeneralSetting::first();
        $bill = Bill::whereTrx($id)->whereUserId($user->id)->first();
        if (empty($bill)) {
            $notify[] = ['error', 'Sorry, Order Not Found'];
            return back()->withNotify($notify);
        }

        if ($mode == 0) {
            $url = 'https://sandbox.vtpass.com/api/requery';
        } else {
            $url = 'https://vtpass.com/api/requery';
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{
     "request_id": "' . $id . '"
    }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json',
            ),
        ));

        $resp = curl_exec($curl);
        $reply = json_decode($resp, true);
        curl_close($curl);
        if (!isset($reply['code'])) {
            $notify[] = ['error', 'Sorry, We cant process this payment at the moment'];
            return back()->withNotify($notify);
        }

//return $reply;
        if (isset($reply['content']['errors'])) {
            $notify[] = ['warning', $reply['content']['errors']];
            return back()->withNotify($notify);
        }


        if ($reply['code'] != "000") {
            $notify[] = ['error', 'Sorry, We cant process tbis payment at the moment'];
            return back()->withNotify($notify);
        }


        if (!isset($reply['content']['transactions']['product_name'])) {
            $notify[] = ['error', 'Sorry, We cant process this payment at the moment'];
            return back()->withNotify($notify);
        }
        //return $resp;
        $token = $reply['purchased_code'];
        $customer = $reply['CustomerName'];
        $address = $reply['CustomerAddress'];
        $unit = $reply['Units'];
        $status = $reply['content']['transactions']['status'];
        $meter = $reply['content']['transactions']['unique_element'];
        $disco = $reply['content']['transactions']['product_name'];
        $amount = $reply['content']['transactions']['unit_price'];

        $bill->api = json_encode($reply);
        $bill->save();


        $pageTitle = "Utility Token";
        return view($this->activeTemplate . 'user.bills.utility-token', compact('pageTitle', 'address', 'token', 'status', 'meter', 'unit', 'disco', 'amount', 'customer'));
    }


}

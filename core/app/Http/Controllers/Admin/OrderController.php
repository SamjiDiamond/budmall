<?php

namespace App\Http\Controllers\Admin;

use App\Models\GeneralSetting;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
use App\Models\SellLog;
use App\Models\User;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Mail;

class OrderController extends Controller
{
    public function ordered(Request $request)
    {
        $pageTitle     = "All Orders";
        $emptyMessage  = 'No order found';
        $admin = Auth::guard('admin')->user();
        
        $input = $request->all();
         
          $query = Order::where('payment_status', '!=' ,0)->latest()->get();
         
            
        
        if($admin->superadmin == 1)
        {
          $query  =  Order::where('payment_status', '!=' ,0);
             if($input)
            {
              $query = Order::where('payment_status', '!=' ,0)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        else
        {
          $query  =  Order::where('payment_status', '!=' ,0)->whereDispatcher($admin->id);
           if($input)
            {
              $query = Order::where('payment_status', '!=' ,0)->whereDispatcher($admin->id)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
       

        if(isset(request()->search)){
            $query->where('order_number', request()->search);
        }
        
        $staff = Admin::where('superadmin', '!=',1)->get();
        $orders = $query->with(['user', 'deposit', 'deposit.gateway'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.order.ordered', compact('staff','pageTitle', 'orders', 'emptyMessage'));
    }

    public function codOrders(Request $request)
    {
        $emptyMessage  = 'NO COD order found';
        $pageTitle     = "Cash On Delivery Orders";
        $query         = Order::where('payment_status',2);
        $input = $request->all();

        
        $admin = Auth::guard('admin')->user();
        if($admin->superadmin == 1)
        {
          $query  =  Order::where('payment_status',2);
           if($input)
            {
              $query = Order::where('payment_status', 2)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        else
        {
          $query  =  Order::where('payment_status',2)->whereDispatcher($admin->id);
           if($input)
            {
              $query = Order::where('payment_status', 2)->whereDispatcher($admin->id)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }

        if(isset(request()->search)){
            $query->where('order_number', request()->search);
        }
        $staff = Admin::where('superadmin', '!=',1)->get();
        $orders        = $query->with(['user', 'deposit'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.order.ordered', compact('staff','pageTitle', 'orders', 'emptyMessage'));
    }

    public function pending(Request $request)
    {
        $emptyMessage  = 'No pending order found';
        $pageTitle     = "Pending Orders";
        $input = $request->all();
        
        $admin = Auth::guard('admin')->user();
        if($admin->superadmin == 1)
        {
          $query  =  Order::where('payment_status', '!=' , 0)->where('status', 0);
           if($input)
            {
              $query = Order::where('payment_status','!=', 0)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        else
        {
          $query  =  Order::where('payment_status', '!=' , 0)->where('status', 0)->whereDispatcher($admin->id);
          if($input)
            {
              $query = Order::where('payment_status','!=', 0)->whereDispatcher($admin->id)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        
        

        if(isset(request()->search)){
            $query->where('order_number', request()->search);
        }
        $staff = Admin::where('superadmin', '!=',1)->get();
        $orders        = $query->with(['user', 'deposit', 'deposit.gateway'])->orderBy('id', 'DESC')->paginate(getPaginate());
        
        return view('admin.order.ordered', compact('staff','pageTitle', 'orders', 'emptyMessage'));

    }

    public function onProcessing(Request $request)
    {
        $emptyMessage  = 'No Data Found';
        $pageTitle     = "Orders on Processing";
        $staff = Admin::whereType(5)->get();
                $input = $request->all();

        
        $admin = Auth::guard('admin')->user();
        if($admin->superadmin == 1)
        {
          $query  =  Order::where('payment_status', '!=' ,0)->where('status', 1);
           if($input)
            {
              $query = Order::where('payment_status','!=', 0)->where('status', 1)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        else
        {
          $query  =  Order::where('payment_status', '!=' ,0)->where('status', 1)->whereDispatcher($admin->id);
           if($input)
            {
              $query = Order::where('payment_status','!=', 0)->where('status', 1)->whereDispatcher($admin->id)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        

        if(isset(request()->search)){
            $query->where('order_number', request()->search);
        }
        $staff = Admin::where('superadmin', '!=',1)->get();
        $orders        = $query->with(['user', 'deposit', 'deposit.gateway'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.order.ordered', compact('staff','pageTitle', 'orders', 'emptyMessage'));
    }

    public function dispatched(Request $request)
    {
        $emptyMessage  = 'No Data Found';
        $pageTitle     = "Orders Dispatched";
                $input = $request->all();

        
        $admin = Auth::guard('admin')->user();
        if($admin->superadmin == 1)
        {
          $query  =  Order::where('payment_status', '!=' ,0)->where('status', 2);
            if($input)
            {
              $query = Order::where('payment_status','!=', 0)->where('status', 2)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        else
        {
          $query  =  Order::where('payment_status', '!=' ,0)->where('status', 2)->whereDispatcher($admin->id);
           if($input)
            {
              $query = Order::where('payment_status','!=', 0)->where('status', 2)->whereDispatcher($admin->id)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        
        if(isset(request()->search)){
            $query->where('order_number', request()->search);
        }
        $staff = Admin::where('superadmin', '!=',1)->get();
        $orders        = $query->with(['user', 'deposit', 'deposit.gateway'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.order.ordered', compact('staff','pageTitle', 'orders', 'emptyMessage'));
    }

    public function canceledOrders()
    {
        $emptyMessage  = 'No Data Found';
        $pageTitle     = "Cancelled Orders";
 
        $admin = Auth::guard('admin')->user();
        if($admin->superadmin == 1)
        {
          $query  =  Order::where('payment_status', '!=' ,0)->where('status', 4);
           if($input)
            {
              $query = Order::where('payment_status','!=', 0)->where('status', 4)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        else
        {
          $query  =  Order::where('payment_status', '!=' ,0)->where('status', 4)->whereDispatcher($admin->id);
           if($input)
            {
              $query = Order::where('payment_status','!=', 0)->where('status', 4)->whereDispatcher($admin->id)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        
        
        if(isset(request()->search)){
            $query->where('order_number', request()->search);
        }
        $staff = Admin::where('superadmin', '!=',1)->get();
        $orders        = $query->with(['user', 'deposit', 'deposit.gateway'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.order.ordered', compact('staff','pageTitle', 'orders', 'emptyMessage'));
    }

    public function deliveredOrders(Request $request)
    {
        $emptyMessage  = 'No Data Found';
        $pageTitle     = "Delivered Orders";
                 $input = $request->all();

        $admin = Auth::guard('admin')->user();
        if($admin->superadmin == 1)
        {
          $query  =  Order::where('payment_status', '!=' ,0)->where('status', 3);
           if($input)
            {
              $query = Order::where('payment_status','!=', 0)->where('status', 3)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        else
        {
          $query  =  Order::where('payment_status', '!=' ,0)->where('status', 3)->whereDispatcher($admin->id);
           if($input)
            {
              $query = Order::where('payment_status','!=', 0)->where('status', 3)->whereDispatcher($admin->id)->whereBetween('created_at',[$request->from,$request->to]);
             }
        }
        
        
        if(isset(request()->search)){
            $query->where('order_number', request()->search);
        }
        $staff = Admin::where('superadmin', '!=',1)->get();
        $orders        = $query->with(['user', 'deposit', 'deposit.gateway'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.order.ordered', compact('staff','pageTitle', 'orders', 'emptyMessage'));
    }

    public function changeStatus(Request $request)
    {
        $general    = GeneralSetting::first();
        
        
        $admin = Auth::guard('admin')->user();
        if($admin->superadmin == 1)
        {
          $order  =  Order::findOrFail($request->id);
        }
        else
        {
          $order  =  Order::whereDispatcher($admin->id)->findOrFail($request->id);
        }
        
         

        if($order->status == 3){
            $notify[] = ['error', 'This order has already been delivered'];
            return back()->withNotify($notify);
        }


        $order->status = $request->action;

        if($request->action==1){
            $action = 'Processing';
        }elseif($request->action == 2){
            $staff = Admin::whereId($request->dispatcher)->first();
            //return $staff;
            
            if(!$staff)
            {
             $notify[] = ['error', 'Invalid state champion selected'];
            return back()->withNotify($notify);  
            }
            
            $order->dispatcher = $request->dispatcher;
            $order->save();
            $action = 'Dispatched';
        }elseif($request->action == 3){
            $action = 'Delivered';
            $order->payment_status = 1;
            
            
            $order->save();

            foreach($order->orderDetail as $detail) {
                $commission  = ($detail->total_price * $general->product_commission)/100;
                $finalAmount = $detail->total_price - $commission;

                $detail->product->sold += $detail->quantity;
                $detail->product->save();

                if($detail->seller_id != 0){
                    $seller = Seller::findOrFail($detail->seller_id);
                    $seller->balance += $finalAmount;
                    $seller->save();
                }

                $sellLog = new SellLog();
                $sellLog->seller_id       = $detail->seller_id;
                $sellLog->product_id      = $detail->product_id;
                $sellLog->order_id        = $order->order_number;
                $sellLog->qty             = $detail->quantity;
                $sellLog->product_price   = $detail->total_price;
                $sellLog->after_commission= $detail->seller_id == 0 ? 0 : $finalAmount;
                $sellLog->save();
            
            }
            //return 1;

        }elseif($request->action == 4){
            $action = 'Cancelled';
        }elseif($request->action == 0){
            $action = 'Pending';
        }

        $notify[] = ['success', 'Order status changed to '.$action];
        $order->save();

        $short_code = [
            'site_name' => $general->sitename,
            'order_id'  => $order->order_number
        ];

        if($request->action == 1){
            $act = 'ORDER_ON_PROCESSING_CONFIRMATION';
        }elseif($request->action == 2){
            $act = 'ORDER_DISPATCHED_CONFIRMATION';
        }elseif($request->action == 3){
            $act = 'ORDER_DELIVERY_CONFIRMATION';
            $beneficiary = json_decode($order->shipping_address,true);
            $body = "Dear ".$beneficiary['firstname']." ".$beneficiary['lastname'].", your order on BudMall with Transaction ID ".$order->order_number." has been delivered sucessfully. "; 
            $name = $beneficiary['firstname'];
            $email = $beneficiary['email'];
            $message = $body;
            $subject = "Order Delivered";
             
              try {
                $data = array("name"=>$name, "content" => $message, "subject" => $subject, "otp" => "Thank You");
            
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
            
            $user = User::whereId($order->user_id)->first();
            $body = "Dear ".$user->firstname.", your order on BudMall with Transaction ID ".$order->order_number." placed for ".$beneficiary['firstname']." ".$beneficiary['lastname']." has been delivered sucessfully. We hope to receive your next order soonest "; 
            $name = $user->username;
            $email = $user->email;
            $message = $body;
            $subject = "Order Delivered";
         
                 try {
                $data = array("name"=>$name, "content" => $message, "subject" => $subject, "otp" => "Thank You");
            
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
        }elseif($request->action == 4){
            $act = 'ORDER_CANCELLATION_CONFIRMATION';
        }elseif($request->action == 0){
            $act = 'ORDER_RETAKE_CONFIRMATION';
        }
        notify($order->user, $act, $short_code);
        return back()->withNotify($notify);
    }

    public function orderDetails($id)
    {
        $pageTitle = 'Order Details';
        
        $admin = Auth::guard('admin')->user();
        if($admin->superadmin == 1)
        {
          $order = Order::where('id', $id)->with('user','deposit','deposit.gateway','orderDetail', 'appliedCoupon')->firstOrFail();
        }
        else
        {
          $order  =  Order::where('id', $id)->with('user','deposit','deposit.gateway','orderDetail', 'appliedCoupon')->whereDispatcher($admin->id)->firstOrFail();
        }
        
        
        
        return view('admin.order.order_details', compact('order', 'pageTitle'));
    }

    public function adminSellsLog()
    {
        $emptyMessage  = 'No sales log Found';
        $pageTitle     = "My Sales";
        $logs          = SellLog::when(request()->search,function($q){
                            return $q->where('order_id',request()->search);
                        })->where('seller_id',0)->latest()->paginate(getPaginate());

        return view('admin.order.sell_log', compact('pageTitle','emptyMessage','logs'));
    }
    public function sellerSellsLog()
    {
        $emptyMessage  = 'No sales log found';
        $pageTitle     = "Seller Sales Log";
        $logs          = SellLog::when(request()->search,function($q){
                            return $q->where('order_id',request()->search);
                        })->where('seller_id','!=',0)->latest()->paginate(getPaginate());

        return view('admin.order.sell_log', compact('pageTitle','emptyMessage','logs'));
    }


    public function airtime(Request $request)
    {
        $pageTitle = 'Purchased Airtime';
        $bills = Bill::whereType(1)->get();
        $input = $request->all();

         if($input)
            {
              $bills = Bill::whereType(1)->whereBetween('created_at',[$request->from,$request->to])->get();
             }
             
        $emptyMessage = "No Transction At The Moment";
        return view('admin.order.airtime', compact('pageTitle', 'emptyMessage', 'bills'));
    }
    public function internet(Request $request)
    {
        $pageTitle = 'Internet Data Subscription';
        $bills = Bill::whereType(2)->get();
         $input = $request->all();

         if($input)
            {
              $bills = Bill::whereType(2)->whereBetween('created_at',[$request->from,$request->to])->get();
             }
        $emptyMessage = "No Transction At The Moment";
        return view('admin.order.internet', compact('pageTitle', 'emptyMessage', 'bills'));
    }

    public function cabletv(Request $request)
    {
        $pageTitle = 'Cable TV Subscription';
        $bills = Bill::whereType(3)->get();
         $input = $request->all();

         if($input)
            {
              $bills = Bill::whereType(3)->whereBetween('created_at',[$request->from,$request->to])->get();
             }
        $emptyMessage = "No Transction At The Moment";
        return view('admin.order.cabletv', compact('pageTitle', 'emptyMessage', 'bills'));
    }


    public function utility(Request $request)
    {
        $pageTitle = 'Electricity Bills Payment';
        $bills = Bill::whereType(4)->get();
         $input = $request->all();

         if($input)
            {
              $bills = Bill::whereType(4)->whereBetween('created_at',[$request->from,$request->to])->get();
             }
        $emptyMessage = "No Transction At The Moment";
        return view('admin.order.utility', compact('pageTitle', 'emptyMessage', 'bills'));
    }


}

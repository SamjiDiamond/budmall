<?php

namespace App\Http\Controllers\Gateway;

use App\Models\Cart;
use App\Models\Admin;
use App\Models\User;
use App\Models\Order;
use App\Models\Gateway;
use App\Models\Orderpay;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\Userwallet;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\GatewayCurrency;
use App\Rules\FileTypeValidate;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use Mail;

class OrderEmailController extends Controller
{
    public function __construct()
    {
        return $this->activeTemplate = activeTemplate();
    }
    
    
     public static function emailbuyer($user,$track)
    {
       $general = GeneralSetting::first();
        $order = Order::where('order_number', $track)->first();
        $smstoken = "TLp7bDYI2D9Krvnuf8Ng9GCi3zeJKUxz0N70Y34o1Ni0R5rYyLcJEVs04riMtf";
        $body = "Dear ".$user->firstname.",you have just created an order with ID ".$order->order_number." on Bud Mall. Delivery will be made to beneficiary within the next 48 Working Hours.";
       
        $to = $user->email;
           
        $subject = "Order Purchased";
        $beneficiary = json_decode($order->shipping_address,true);
       
        $message = "
        <html>
        <head>
        <title>Order Purchased</title>
        <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even) {
          background-color: #dddddd;
        }
        </style>
        </head>
        
        <body>
        <p><b>".$body."</b> <br> Please find below a summary of your transaction</p>
        
        <table>
        <tr>
        <th># </th>
        <th>Details</th>
        </tr>
        <tr>
        <td>Beneficiary's Name</td>
        <td>".$beneficiary['firstname']." ".$beneficiary['lastname']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Phone Number</td>
        <td>".$beneficiary['mobile']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Email</td>
        <td>".$beneficiary['email']."</td>
        </tr>
        
        <tr>
        <td>Beneficiary's Address</td>
        <td>".$beneficiary['city']." ".$beneficiary['state']." ".$beneficiary['country']."</td>
        </tr>
        
        
        <tr>
        <td>Order Amount</td>
        <td>$".$order->total_amount_usd."</td>
        </tr>
        
        
        <tr>
        <td>Order ID</td>
        <td>".$order->order_number ."</td>
        </tr>
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <hi@budmall.ng>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);
        
                
        /*try {
        //Server settings
        $name = $user->username;
        $email = $user->email;
        $text = $message;
        $message = $body.$text;
        $subject = "Payment Successful";
        
        $data = array("name"=>$name, "content" => $message, "subject" => $subject);
    
        Mail::send("mails.mail", $data, function($message) use ($name, $email, $subject) {
            $message->to($email, $name)->subject($subject);
            $message->from(env("MAIL_USERNAME"),$subject);
            });
        } catch (Exception $e) {
        mail($to,$subject,$message,$headers);
        }*/

    }
    
     public static function emailbeneficiary($user,$track)
    {
       $general = GeneralSetting::first();
        $order = Order::where('order_number', $track)->first();
        $smstoken = "TLp7bDYI2D9Krvnuf8Ng9GCi3zeJKUxz0N70Y34o1Ni0R5rYyLcJEVs04riMtf";
        $beneficiary = json_decode($order->shipping_address,true);
        $body = "Dear ".$beneficiary['firstname']." , an order with ID ".$order->order_number." has been placed for you on BudMall. Delivery will be made to you within the next 48 Working Hours. You can visit our order tracking page on  www.budmall.ng to track your order status";
        
        $to = $beneficiary['email'];
        $subject = "Order Purchased";
        $message = "
        <html>
        <head>
        <title>Order Placed</title>
        <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even) {
          background-color: #dddddd;
        }
        </style>
        </head>
        
        <body>
        <p><b>".$body."</b> <br> Please find below a summary of your transaction</p>
        
        <table>
        <tr>
        <th># </th>
        <th>Details</th>
        </tr>
        <tr>
        <td>Beneficiary's Name</td>
        <td>".$beneficiary['firstname']." ".$beneficiary['lastname']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Phone Number</td>
        <td>".$beneficiary['mobile']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Email</td>
        <td>".$beneficiary['email']."</td>
        </tr>
        
        <tr>
        <td>Beneficiary's Address</td>
        <td>".$beneficiary['city']." ".$beneficiary['state']." ".$beneficiary['country']."</td>
        </tr>
         
        <tr>
        <td>Order ID</td>
        <td>".$order->order_number ."</td>
        </tr>
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <hi@budmall.ng>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);
        

    }
    
      public static function emailadminorder($user,$track)
    {
       $general = GeneralSetting::first();
        $order = Order::where('order_number', $track)->first();
        $smstoken = "TLp7bDYI2D9Krvnuf8Ng9GCi3zeJKUxz0N70Y34o1Ni0R5rYyLcJEVs04riMtf";
        $beneficiary = json_decode($order->shipping_address,true);
        $body = "Dear Admin an order with Transaction ID ".$order->order_number." has been placed on BudMall.";
       
        $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://termii.com/api/sms/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '      {
            "to": "' . $general->system_phone . '",
            "from": "BudMall",
            "sms": "' . $body . '",
            "type": "plain",
            "channel": "dnd",
            "api_key": "' . $smstoken. '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: termii-sms=iizYGwU6UJvPbs7Pw49595Aa157h8zzc5ZMbJs2l'
            ),
             ));

        $response = curl_exec($curl);
        $to = $general->email;
        $subject = "Order Placed";
        $message = "
        <html>
        <head>
        <title>Order Placed</title>
        <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even) {
          background-color: #dddddd;
        }
        </style>
        </head>
        
        <body>
        <p><b>".$body."</b> <br> Please find below a summary of the transaction</p>
        
        <table>
        <tr>
        <th># </th>
        <th>Details</th>
        </tr>
        <tr>
        <td>Buyer's Name</td>
        <td>".$user->firstname." ".$user->lastname."</td>
        </tr>
        <tr>
        <td>Buyer's Email</td>
        <td>".$user->email."</td>
        </tr>
        <tr>
        <td>Buyer's Phone Number</td>
        <td>".$user->mobile."</td>
        </tr>
        <tr>
        <td>Beneficiary's Name</td>
        <td>".$beneficiary['firstname']." ".$beneficiary['lastname']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Phone Number</td>
        <td>".$beneficiary['mobile']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Email</td>
        <td>".$beneficiary['email']."</td>
        </tr>
        
        <tr>
        <td>Beneficiary's Address</td>
        <td>".$beneficiary['city']." ".$beneficiary['state']." ".$beneficiary['country']."</td>
        </tr>
        
        <tr>
        <td>Order Amount</td>
        <td>".$general->cur_sym.$order->total_amount."</td>
        </tr>
        <tr>
        <td>Order Amount (USD)</td>
        <td>$".$order->total_amount_usd."</td>
        </tr>
        
        
        <tr>
        <td>Order ID</td>
        <td>".$order->order_number ."</td>
        </tr>
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <hi@budmall.ng>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);

    }
      public static function emailagent($user,$track)
    {
        $general = GeneralSetting::first();
        $order = Order::where('order_number', $track)->first();
        $smstoken = "TLp7bDYI2D9Krvnuf8Ng9GCi3zeJKUxz0N70Y34o1Ni0R5rYyLcJEVs04riMtf";
        $beneficiary = json_decode($order->shipping_address,true);
        $dispatcher = Admin::whereType(1)->whereStatus(1)->whereState($order->state)->inRandomOrder()->first();
        
        $order->dispatcher = $dispatcher->id;
        $order->status = 2;
        $order->save();
        
        if($order->state = 50)
        {
           $dispatchlagos = Admin::whereType(1)->whereStatus(1)->whereState($order->state)->get();
         foreach($dispatchlagos as $dispatchlag)
         {
           $body = "Dear ".$dispatchlag->username." you have an order with Transaction ID ".$order->order_number." to dispatch on BudMall. Please login to your accout to check order.";
        
        $to = $dispatchlag->email;
        $subject = "Pending Order";
        $message = "
        <html>
        <head>
        <title>Pending Order</title>
        <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even) {
          background-color: #dddddd;
        }
        </style>
        </head>
        
        <body>
        <p><b>".$body."</b> <br> Please find below a summary of the pending order</p>
        
        <table>
        <tr>
        <th># </th>
        <th>Details</th>
        </tr>
        <tr>
        <td>Buyer's Name</td>
        <td>".$user->firstname." ".$user->lastname."</td>
        </tr>
        <tr>
        <td>Buyer's Email</td>
        <td>".$user->email."</td>
        </tr>
        <tr>
        <td>Buyer's Phone Number</td>
        <td>".$user->mobile."</td>
        </tr>
        <tr>
        <td>Beneficiary's Name</td>
        <td>".$beneficiary['firstname']." ".$beneficiary['lastname']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Phone Number</td>
        <td>".$beneficiary['mobile']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Email</td>
        <td>".$beneficiary['email']."</td>
        </tr>
        
        <tr>
        <td>Beneficiary's Address</td>
        <td>".$beneficiary['city']." ".$beneficiary['state']." ".$beneficiary['country']."</td>
        </tr>
         
        <tr>
        <td>Order ID</td>
        <td>".$order->order_number ."</td>
        </tr>
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <hi@budmall.ng>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);
         }
       
        }
        
        $body = "Dear ".$dispatcher->username." you have an order with Transaction ID ".$order->order_number." to dispatch on BudMall. Please login to your accout to check order.";
       
        $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://termii.com/api/sms/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '      {
            "to": "' . $dispatcher->phone . '",
            "from": "BudMall",
            "sms": "' . $body . '",
            "type": "plain",
            "channel": "dnd",
            "api_key": "' . $smstoken. '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: termii-sms=iizYGwU6UJvPbs7Pw49595Aa157h8zzc5ZMbJs2l'
            ),
             ));

        $response = curl_exec($curl);
        $to = $dispatcher->email;
        $subject = "Pending Order";
        $message = "
        <html>
        <head>
        <title>Pending Order</title>
        <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even) {
          background-color: #dddddd;
        }
        </style>
        </head>
        
        <body>
        <p><b>".$body."</b> <br> Please find below a summary of the pending order</p>
        
        <table>
        <tr>
        <th># </th>
        <th>Details</th>
        </tr>
        <tr>
        <td>Buyer's Name</td>
        <td>".$user->firstname." ".$user->lastname."</td>
        </tr>
        <tr>
        <td>Buyer's Email</td>
        <td>".$user->email."</td>
        </tr>
        <tr>
        <td>Buyer's Phone Number</td>
        <td>".$user->mobile."</td>
        </tr>
        <tr>
        <td>Beneficiary's Name</td>
        <td>".$beneficiary['firstname']." ".$beneficiary['lastname']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Phone Number</td>
        <td>".$beneficiary['mobile']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Email</td>
        <td>".$beneficiary['email']."</td>
        </tr>
        
        <tr>
        <td>Beneficiary's Address</td>
        <td>".$beneficiary['city']." ".$beneficiary['state']." ".$beneficiary['country']."</td>
        </tr>
         
        
        <tr>
        <td>Order ID</td>
        <td>".$order->order_number ."</td>
        </tr>
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <hi@budmall.ng>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);

    }
    
      public static function emailbuyersent($user,$track)
    {
       $general = GeneralSetting::first();
        $order = Order::where('order_number', $track)->first();
        $smstoken = "TLp7bDYI2D9Krvnuf8Ng9GCi3zeJKUxz0N70Y34o1Ni0R5rYyLcJEVs04riMtf";
        $body = "Dear ".$user->firstname.",your order on BudMall with ID ".$order->order_number." has been dispatched for delivery. Thank you for choosing BudMall";
       
        $to = $user->email;
           
        $subject = "Order Dispatched";
        $beneficiary = json_decode($order->shipping_address,true);
       
        $message = "
        <html>
        <head>
        <title>Order Dispatched</title>
        <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even) {
          background-color: #dddddd;
        }
        </style>
        </head>
        
        <body>
        <p><b>".$body."</b> <br> Please find below a summary of the order</p>
        
        <table>
        <tr>
        <th># </th>
        <th>Details</th>
        </tr>
        <tr>
        <td>Beneficiary's Name</td>
        <td>".$beneficiary['firstname']." ".$beneficiary['lastname']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Phone Number</td>
        <td>".$beneficiary['mobile']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Email</td>
        <td>".$beneficiary['email']."</td>
        </tr>
        
        <tr>
        <td>Beneficiary's Address</td>
        <td>".$beneficiary['city']." ".$beneficiary['state']." ".$beneficiary['country']."</td>
        </tr>
        
        
        <tr>
        <td>Order Amount</td>
        <td>".$general->cur_sym.$order->total_amount."</td>
        </tr>
        
        
        <tr>
        <td>Order ID</td>
        <td>".$order->order_number ."</td>
        </tr>
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <hi@budmall.ng>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);

    }
    
       public static function emailadminsent($user,$track)
    {
       $general = GeneralSetting::first();
        $order = Order::where('order_number', $track)->first();
        $smstoken = "TLp7bDYI2D9Krvnuf8Ng9GCi3zeJKUxz0N70Y34o1Ni0R5rYyLcJEVs04riMtf";
        $beneficiary = json_decode($order->shipping_address,true);
        $body = "Dear Admin the order with Transaction ID ".$order->order_number." has been dispatched. ";
        $agent = Admin::where('id', $order->dispatcher)->first();
        
        if($agent)
        {
       
        $to = $general->email;
        $subject = "Order Dispatched";
        $message = "
        <html>
        <head>
        <title>Order Dispatched</title>
        <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even) {
          background-color: #dddddd;
        }
        </style>
        </head>
        
        <body>
        <p><b>".$body."</b> <br> Please find below a summary of the transaction</p>
        
        <table>
        <tr>
        <th># </th>
        <th>Details</th>
        </tr>
        <tr>
        <td>Buyer's Name</td>
        <td>".$user->firstname." ".$user->lastname."</td>
        </tr>
        <tr>
        <td>Buyer's Email</td>
        <td>".$user->email."</td>
        </tr>
        <tr>
        <td>Buyer's Phone Number</td>
        <td>".$user->mobile."</td>
        </tr>
        <tr>
        <td>Beneficiary's Name</td>
        <td>".$beneficiary['firstname']." ".$beneficiary['lastname']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Phone Number</td>
        <td>".$beneficiary['mobile']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Email</td>
        <td>".$beneficiary['email']."</td>
        </tr>
        
        <tr>
        <td>Beneficiary's Address</td>
        <td>".$beneficiary['city']." ".$beneficiary['state']." ".$beneficiary['country']."</td>
        </tr>
        
        <tr>
        <td>Order Amount</td>
        <td>".$general->cur_sym.$order->total_amount."</td>
        </tr>
        <tr>
        <td>Order Amount (USD)</td>
        <td>$".$order->total_amount_usd."</td>
        </tr>
        
        
        <tr>
        <td>Order ID</td>
        <td>".$order->order_number ."</td>
        </tr>
        <tr>
        <td>Dispatcher Name</td>
        <td>".$agent->name ."</td>
        </tr>
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <hi@budmall.ng>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);
        }

    }
    
        public static function emailadminnotsent($user,$track)
    {
        $general = GeneralSetting::first();
        $order = Order::where('order_number', $track)->first();
        $beneficiary = json_decode($order->shipping_address,true);
        $body = "Dear Admin the order with Transaction ID ".$order->order_number." has been placed but not dispatched as there is no dispatcher for this order. ";
       
        $order->status = 1;
        $order->save();
        
        $to = $general->email;
        $subject = "Order Not Dispatched";
        $message = "
        <html>
        <head>
        <title>Order Not Dispatched</title>
        <style>
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        td, th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even) {
          background-color: #dddddd;
        }
        </style>
        </head>
        
        <body>
        <p><b>".$body."</b> <br> Please find below a summary of the transaction</p>
        
        <table>
        <tr>
        <th># </th>
        <th>Details</th>
        </tr>
        <tr>
        <td>Buyer's Name</td>
        <td>".$user->firstname." ".$user->lastname."</td>
        </tr>
        <tr>
        <td>Buyer's Email</td>
        <td>".$user->email."</td>
        </tr>
        <tr>
        <td>Buyer's Phone Number</td>
        <td>".$user->mobile."</td>
        </tr>
        <tr>
        <td>Beneficiary's Name</td>
        <td>".$beneficiary['firstname']." ".$beneficiary['lastname']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Phone Number</td>
        <td>".$beneficiary['mobile']."</td>
        </tr>
        <tr>
        <td>Beneficiary's Email</td>
        <td>".$beneficiary['email']."</td>
        </tr>
        
        <tr>
        <td>Beneficiary's Address</td>
        <td>".$beneficiary['city']." ".$beneficiary['state']." ".$beneficiary['country']."</td>
        </tr>
        
        <tr>
        <td>Order Amount</td>
        <td>".$general->cur_sym.$order->total_amount."</td>
        </tr>
        <tr>
        <td>Order Amount (USD)</td>
        <td>$".$order->total_amount_usd."</td>
        </tr>
        
        
        <tr>
        <td>Order ID</td>
        <td>".$order->order_number ."</td>
        </tr>
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <hi@budmall.ng>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);
        

    }
    
}

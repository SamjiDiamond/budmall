<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\SellLog;
use App\Models\EmailLog;
use App\Models\UserLogin;
use App\Models\SellerLogin;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function transaction()
    {
        $pageTitle = 'Transaction Logs';
        $transactions = Transaction::with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transactions.';
        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'emptyMessage'));
    }

    public function transactionSearch(Request $request)
    {
        $request->validate(['search' => 'required']);
        $search = $request->search;
        $pageTitle = 'Transactions Search - ' . $search;
        $emptyMessage = 'No transactions.';

        $transactions = Transaction::with(['user','seller'])->whereHas('user', function ($user) use ($search) {
            $user->where('username', 'like',"%$search%");
        })->orWhereHas('seller', function ($user) use ($search) {
            $user->where('username', 'like',"%$search%");
        })->orWhere('trx', $search)->orderBy('id','desc')->paginate(getPaginate());

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'emptyMessage','search'));
    }

    public function orderByUser($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'Order Logs of '. $user->fullname;
        $orders     =  Order::where('user_id', $id)->where('payment_status', '!=' ,0)->with('user', 'deposit')->paginate(getPaginate());
        $emptyMessage = 'No orders.';
        return view('admin.reports.orders', compact('pageTitle', 'user', 'orders', 'emptyMessage'));
    }


    public function userOrderSearch(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $key    = $request->search??'';

        if($key){
            $orders = Order::where('user_id', $id)
            ->where('payment_status', '!=' ,0)
            ->with('user', 'deposit')->where('order_number', 'like', "%$key%")
            ->paginate(getPaginate());

            $pageTitle = 'Order Search of' . $user->fullname .' Results - Order ID : ' . $key;
        }elseif($request->has('date')){
            $request->validate([
                'date' => 'required|string',
            ]);

            $date               = explode('to', $request->date);

            if(count($date) == 2) {

                $start_date       = date('Y-m-d H:i:s',strtotime(trim($date[0])));
                $end_date         = date('Y-m-d H:i:s',strtotime(trim($date[1])));

                $orders     = Order::where('user_id', $id)->where('payment_status', '!=' ,0)->with('user', 'deposit')->whereBetween('created_at', [$start_date, $end_date])->paginate(getPaginate());

                $pageTitle = 'Orders of '. $user->name .' between : ' . showDateTime($start_date, 'd M, Y') .' to '. showDateTime($end_date, 'd M, Y');

            }else{
                $start_date       = date('Y-m-d', strtotime(trim($date[0])));
                $orders           = Order::where('user_id', $id)->where('payment_status', '!=' ,0)->with('user', 'deposit')->whereDate('created_at',$start_date)->paginate(getPaginate());

                $pageTitle     = 'Orders of '.$user->name .' '. showDatetime($start_date, 'M d, y');
            }

        }

        $emptyMessage  = 'No Order Yet.';

        return view('admin.reports.orders', compact('pageTitle', 'user', 'orders', 'emptyMessage', 'key'));
    }

    public function sellerLoginHistory(Request $request)
    {
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Seller Login History Search - ' . $search;
            $emptyMessage = 'No search result found.';
            $loginLogs = SellerLogin::whereHas('seller', function ($query) use ($search) {
                $query->where('username', $search);
            })->orderBy('id','desc')->with('seller')->paginate(getPaginate());
            return view('admin.reports.seller_logins', compact('pageTitle', 'emptyMessage', 'search', 'loginLogs'));
        }
        $pageTitle = 'Seller Login History';
        $emptyMessage = 'No seller login found.';
        $loginLogs = SellerLogin::orderBy('id','desc')->with('seller')->paginate(getPaginate());
        return view('admin.reports.seller_logins', compact('pageTitle', 'emptyMessage', 'loginLogs'));
    }

    public function loginHistory(Request $request)
    {
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'User Login History Search - ' . $search;
            $emptyMessage = 'No search result found.';
            $loginLogs = UserLogin::whereHas('user', function ($query) use ($search) {
                $query->where('username', $search);
            })->orderBy('id','desc')->with('user')->paginate(getPaginate());
            return view('admin.reports.logins', compact('pageTitle', 'emptyMessage', 'search', 'loginLogs'));
        }
        $pageTitle = 'User Login History';
        $emptyMessage = 'No users login found.';
        $loginLogs = UserLogin::orderBy('id','desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'emptyMessage', 'loginLogs'));
    }

    public function sellerLoginIpHistory($ip)
    {
        $pageTitle = 'Login By - ' . $ip;
        $loginLogs = SellerLogin::where('seller_ip',$ip)->orderBy('id','desc')->with('seller')->paginate(getPaginate());
        $emptyMessage = 'No users login found.';
        return view('admin.reports.seller_logins', compact('pageTitle', 'emptyMessage', 'loginLogs','ip'));

    }
    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login By - ' . $ip;
        $loginLogs = UserLogin::where('user_ip',$ip)->orderBy('id','desc')->with('user')->paginate(getPaginate());
        $emptyMessage = 'No users login found.';
        return view('admin.reports.logins', compact('pageTitle', 'emptyMessage', 'loginLogs','ip'));

    }

    public function emailHistory(){
        $pageTitle = 'Email history';
        $logs = EmailLog::with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.reports.email_history', compact('pageTitle', 'emptyMessage','logs'));
    }

    public function commissionLogs()
    {
        $emptyMessage  = 'No Data Found';
        $pageTitle     = "My Commission Log";
        $logs          = SellLog::when(request()->search,function($q){
                            return $q->where('order_id',request()->search);
                        })->where('seller_id','!=',0)->latest()->paginate(getPaginate()); 

        return view('admin.reports.commission_log', compact('pageTitle','emptyMessage','logs'));
    }
}

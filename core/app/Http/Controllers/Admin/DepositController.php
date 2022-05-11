<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;

class DepositController extends Controller
{
    protected $depositRelations = ['user', 'gateway'];
    
    
     public function pendingdepo()
    {
        $pageTitle = 'Pending Deposit';
        $emptyMessage = 'No pending deposit.';
        $deposits = Deposit::whereStatus(0)->latest()->get();
        return view('admin.deposit.depolog', compact('pageTitle', 'emptyMessage', 'deposits'));
    }
    
       public function successfuldepo()
    {
        $pageTitle = 'Successful Deposit';
        $emptyMessage = 'No successful deposit.';
        $deposits = Deposit::whereStatus(1)->latest()->get();
        return view('admin.deposit.depolog', compact('pageTitle', 'emptyMessage', 'deposits'));
    }
       public function listdepo()
    {
        $pageTitle = 'All Deposit';
        $emptyMessage = 'No deposit.';
        $deposits = Deposit::latest()->get();
        return view('admin.deposit.depolog', compact('pageTitle', 'emptyMessage', 'deposits'));
    }







    public function pending()
    {
        $pageTitle = 'Pending Payments';
        $emptyMessage = 'No pending payments.';
        $deposits = Order::wherePaymentStatus(0)->latest()->paginate(getPaginate());
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits'));
    }



    public function successful()
    {
        $pageTitle = 'Successful Payments';
        $emptyMessage= 'No successful payments.';
        $deposits = Order::where('payment_status', 1)->latest()->paginate(getPaginate());
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits'));
    }
    
    
    public function approved()
    {
        $pageTitle = 'Approved Payments';
        $emptyMessage = 'No approved payments.';
        $deposits = Order::where('payment_status', 1)->latest()->paginate(getPaginate());
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits'));
    }
    
     public function deposit()
    {
        $pageTitle = 'Payments History';
        $emptyMessage= 'No Payments history available.';
        $deposits = Order::orderBy('id','desc')->paginate(getPaginate());
        $successful = Order::where('payment_status',1)->sum('total_amount');
        $pending = Order::where('payment_status' ,"!=", 1)->sum('total_amount');
        $rejected = Order::where('status',0)->sum('total_amount');
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits','successful','pending','rejected'));
    }


    public function rejected()
    {
        $pageTitle = 'Rejected Payments';
        $emptyMessage= 'No rejected payments.';
        $deposits = Deposit::where('status', 3)->with($this->depositRelations)->latest()->paginate(getPaginate());
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits'));
    }
   
    public function depositViaMethod($method,$type = null){
        $method = Gateway::where('alias',$method)->firstOrFail();
        if ($type == 'approved') {
            $pageTitle = 'Approved Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('method_code',$method->code)->where('status', 1)->orderBy('id','desc')->with($this->depositRelations);
        }elseif($type == 'rejected'){
            $pageTitle = 'Rejected Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('method_code',$method->code)->where('status', 3)->orderBy('id','desc')->with($this->depositRelations);

        }elseif($type == 'successful'){
            $pageTitle = 'Successful Payment Via '.$method->name;
            $deposits = Deposit::where('status', 1)->where('method_code',$method->code)->orderBy('id','desc')->with($this->depositRelations);
        }elseif($type == 'pending'){
            $pageTitle = 'Pending Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('method_code',$method->code)->where('status', 2)->orderBy('id','desc')->with($this->depositRelations);
        }else{
            $pageTitle = 'Payment Via '.$method->name;
            $deposits = Deposit::where('status','!=',0)->where('method_code',$method->code)->orderBy('id','desc')->with($this->depositRelations);
        }
        $deposits = $deposits->paginate(getPaginate());
        $successful = $deposits->where('status',1)->sum('amount');
        $pending = $deposits->where('status',2)->sum('amount');
        $rejected = $deposits->where('status',3)->sum('amount');
        $methodAlias = $method->alias;
        $emptyMessage= 'No Payments Found';
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits','methodAlias','successful','pending','rejected'));
    }

    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $emptyMessage= 'No search result was found.';
        $deposits = Order::where('status','!=',0)->where(function ($q) use ($search) {
            $q->where('order_number', 'like', "%$search%")->orWhereHas('user', function ($user) use ($search) {
                $user->where('username', 'like', "%$search%");
            });
        });
        if ($scope == 'pending') {
            $pageTitle = 'Pending Payments Search';
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 2);
        }elseif($scope == 'approved'){
            $pageTitle = 'Approved Payments Search';
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 1);
        }elseif($scope == 'rejected'){
            $pageTitle = 'Rejected Payments Search';
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 3);
        }else{
            $pageTitle = 'Payments History Search';
        }

        $deposits = $deposits->paginate(getPaginate());
        $pageTitle .= '-' . $search;

        return view('admin.deposit.log', compact('pageTitle', 'search', 'scope', 'emptyMessage', 'deposits'));
    }

    public function dateSearch(Request $request,$scope = null){
        $search = $request->date;
        if (!$search) {
            return back();
        }
        $date = explode('-',$search);
        $start = @$date[0];
        $end = @$date[1];
        // date validation
        $pattern = "/\d{2}\/\d{2}\/\d{4}/";
        if ($start && !preg_match($pattern,$start)) {
            $notify[] = ['error','Invalid date format'];
            return redirect()->route('admin.deposit.list')->withNotify($notify);
        }
        if ($end && !preg_match($pattern,$end)) {
            $notify[] = ['error','Invalid date format'];
            return redirect()->route('admin.deposit.list')->withNotify($notify);
        }


        if ($start) {
            $deposits = Order::where('status','!=',0)->whereDate('created_at',Carbon::parse($start));
        }
        if($end){
            $deposits = Order::where('status','!=',0)->whereDate('created_at','>=',Carbon::parse($start))->whereDate('created_at','<=',Carbon::parse($end));
        }
        if ($request->method) {
            $method = Gateway::where('alias',$request->method)->firstOrFail();
            $deposits = $deposits->where('method_code',$method->code);
        }
        if ($scope == 'pending') {
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 2);
        }elseif($scope == 'approved'){
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 1);
        }elseif($scope == 'rejected'){
            $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 3);
        }
        $deposits = $deposits->orderBy('id','desc')->paginate(getPaginate());
        $pageTitle = ' Payments Log';
        $emptyMessage= 'No Payments Found';
        $dateSearch = $search;
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits','dateSearch','scope'));
    }

    public function details($id)
    {
        $general = GeneralSetting::first();
        $deposit = Deposit::where('id', $id)->where('method_code','!=',0)->with($this->depositRelations)->firstOrFail();
        $pageTitle = $deposit->user->username.' requested ' . showAmount($deposit->amount) . ' '.$general->cur_text;
        $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        return view('admin.deposit.detail', compact('pageTitle', 'deposit','details'));
    }


    public function approve(Request $request)
    {

        $request->validate(['id' => 'required|integer']);
        $deposit = Deposit::where('id',$request->id)->where('status',2)->firstOrFail();
        $deposit->status = 1;
        $deposit->save();

        $order = Order::where('id', $deposit->order_id)->first();
        $order->payment_status = 1;
        $order->save();


        $user = User::find($deposit->user_id);

        $transaction = new Transaction();
        $transaction->user_id = $deposit->user_id;
        $transaction->amount = $deposit->amount;
        $transaction->charge = $deposit->charge;
        $transaction->trx_type = '+';
        $transaction->details = 'Payment Via ' . $deposit->gatewayCurrency()->name;
        $transaction->trx =  $deposit->trx;
        $transaction->save();

        $general = GeneralSetting::first();
        notify($user, 'DEPOSIT_APPROVE', [
            'method_name' => $deposit->gatewayCurrency()->name,
            'method_currency' => $deposit->method_currency,
            'method_amount' => showAmount($deposit->final_amo),
            'amount' => showAmount($deposit->amount),
            'charge' => showAmount($deposit->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($deposit->rate),
            'trx' => $deposit->trx,
        ]);
        $notify[] = ['success', 'Payment has been approved.'];

        return redirect()->route('admin.deposit.pending')->withNotify($notify);
    }

    public function reject(Request $request)
    {

        $request->validate([
            'id' => 'required|integer',
            'message' => 'required|max:250'
        ]);
        $deposit = Deposit::where('id',$request->id)->where('status',2)->firstOrFail();

        $deposit->admin_feedback = $request->message;
        $deposit->status = 3;
        $deposit->save();

        $general = GeneralSetting::first();
        notify($deposit->user, 'DEPOSIT_REJECT', [
            'method_name' => $deposit->gatewayCurrency()->name,
            'method_currency' => $deposit->method_currency,
            'method_amount' => showAmount($deposit->final_amo),
            'amount' => showAmount($deposit->amount),
            'charge' => showAmount($deposit->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($deposit->rate),
            'trx' => $deposit->trx,
            'rejection_message' => $request->message
        ]);

        $notify[] = ['success', 'Payments request has been rejected.'];
        return  redirect()->route('admin.deposit.pending')->withNotify($notify);

    }
}

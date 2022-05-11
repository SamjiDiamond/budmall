<?php

namespace App\Http\Controllers\Admin;

use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\WithdrawMethod;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function pending()
    {
        $pageTitle = 'Pending Withdrawals';
        $withdrawals = Withdrawal::pending()->with(['seller','method'])->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No withdrawal found';
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'emptyMessage'));
    }

    public function approved()
    {
        $pageTitle = 'Approved Withdrawals';
        $withdrawals = Withdrawal::approved()->with(['seller','method'])->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No withdrawal found';
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'emptyMessage'));
    }

    public function rejected()
    {
        $pageTitle = 'Rejected Withdrawals';
        $withdrawals = Withdrawal::rejected()->with(['seller','method'])->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No withdrawal found';
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'emptyMessage'));
    }

    public function log()
    {
        $pageTitle = 'Withdraw Log';
        $withdrawals = Withdrawal::where('status', '!=', 0)->with(['seller','method'])->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No Payout history';
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'emptyMessage'));
    }


    public function logViaMethod($methodId,$type = null){
        $method = WithdrawMethod::findOrFail($methodId);
        if ($type == 'approved') {
            $pageTitle = 'Approved Withdrawals Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 1)->with(['seller','method'])->where('method_id',$method->id)->orderBy('id','desc')->paginate(getPaginate());
        }elseif($type == 'rejected'){
            $pageTitle = 'Rejected Withdrawals Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 3)->with(['seller','method'])->where('method_id',$method->id)->orderBy('id','desc')->paginate(getPaginate());

        }elseif($type == 'pending'){
            $pageTitle = 'Pending Withdrawals Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 2)->with(['seller','method'])->where('method_id',$method->id)->orderBy('id','desc')->paginate(getPaginate());
        }else{
            $pageTitle = 'Withdraw Via '.$method->name;
            $withdrawals = Withdrawal::where('status', '!=', 0)->with(['seller','method'])->where('method_id',$method->id)->orderBy('id','desc')->paginate(getPaginate());
        }
        $emptyMessage = 'No Withdraw found';
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'emptyMessage','method'));
    }


    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $emptyMessage = 'No search result found.';

        $withdrawals = Withdrawal::with(['seller', 'method'])->where('status','!=',0)->where(function ($q) use ($search) {
            $q->where('trx', 'like',"%$search%")->orWhereHas('seller', function ($seller) use ($search) {
                $seller->where('username', 'like',"%$search%");
            });
        });

        if ($scope == 'pending') {
            $pageTitle = 'Pending Withdraw Search';
            $withdrawals = $withdrawals->where('status', 2);
        }elseif($scope == 'approved'){
            $pageTitle = 'Approved Withdraw Search';
            $withdrawals = $withdrawals->where('status', 1);
        }elseif($scope == 'rejected'){
            $pageTitle = 'Rejected Withdraw Search';
            $withdrawals = $withdrawals->where('status', 3);
        }else{
            $pageTitle = 'Withdraw History Search';
        }

        $withdrawals = $withdrawals->paginate(getPaginate());
        $pageTitle .= ' - ' . $search;

        return view('admin.withdraw.withdrawals', compact('pageTitle', 'emptyMessage', 'search', 'scope', 'withdrawals'));
    }

    public function dateSearch(Request $request,$scope){
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
            return redirect()->route('admin.withdrawals.log')->withNotify($notify);
        }
        if ($end && !preg_match($pattern,$end)) {
            $notify[] = ['error','Invalid date format'];
            return redirect()->route('admin.withdrawals.log')->withNotify($notify);
        }

        if ($start) {
            $withdrawals = Withdrawal::where('status','!=',0)->whereDate('created_at',Carbon::parse($start));
        }
        if($end){
            $withdrawals = Withdrawal::where('status','!=',0)->whereDate('created_at','>=',Carbon::parse($start))->whereDate('created_at','<=',Carbon::parse($end));
        }
        if ($request->method) {
            $method = WithdrawMethod::findOrFail($request->method);
            $withdrawals = $withdrawals->where('method_id',$method->id);
        }

        if ($scope == 'pending') {
            $withdrawals = $withdrawals->where('status', 2);
        }elseif($scope == 'approved'){
            $withdrawals = $withdrawals->where('status', 1);
        }elseif($scope == 'rejected') {
            $withdrawals = $withdrawals->where('status', 3);
        }

        $withdrawals = $withdrawals->with(['seller', 'method'])->paginate(getPaginate());
        $pageTitle = 'Withdraw Log';
        $emptyMessage = 'No Withdrawals Found';
        $dateSearch = $search;
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'emptyMessage', 'dateSearch', 'withdrawals','scope'));


    }

    public function details($id)
    {
        $general = GeneralSetting::first();
        $withdrawal = Withdrawal::where('id',$id)->where('status', '!=', 0)->with(['seller','method'])->firstOrFail();
        $pageTitle = $withdrawal->seller->username.' Withdraw Requested ' . showAmount($withdrawal->amount) . ' '.$general->cur_text;
        $details = $withdrawal->withdraw_information ? json_encode($withdrawal->withdraw_information) : null;
        $methodImage =  getImage(imagePath()['withdraw']['method']['path'].'/'. $withdrawal->method->image,'800x800');
        return view('admin.withdraw.detail', compact('pageTitle', 'withdrawal','details','methodImage'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $withdraw = Withdrawal::where('id',$request->id)->where('status',2)->with('seller')->firstOrFail();
        $withdraw->status = 1;
        $withdraw->admin_feedback = $request->details;
        $withdraw->save();

        $general = GeneralSetting::first();
        notify($withdraw->seller, 'WITHDRAW_APPROVE', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount),
            'amount' => showAmount($withdraw->amount),
            'charge' => showAmount($withdraw->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($withdraw->rate),
            'trx' => $withdraw->trx,
            'admin_details' => $request->details
        ]);

        $notify[] = ['success', 'Withdraw marked as approved.'];
        return redirect()->route('admin.withdrawals.pending')->withNotify($notify);
    }


    public function reject(Request $request)
    {
        $general    = GeneralSetting::first();
        $request->validate(['id' => 'required|integer']);
        $withdraw   = Withdrawal::where('id',$request->id)->where('status',2)->with('seller')->firstOrFail();

        $withdraw->status           = 3;
        $withdraw->admin_feedback   = $request->details;
        $withdraw->save();

        $seller                     = $withdraw->seller;
        $seller->balance            += $withdraw->amount;
        $seller->save();

        $transaction                = new Transaction();
        $transaction->seller_id     = $withdraw->seller_id;
        $transaction->amount        = $withdraw->amount;
        $transaction->post_balance  = $seller->balance;
        $transaction->charge        = 0;
        $transaction->trx_type      = '+';
        $transaction->details       = showAmount($withdraw->amount) . ' ' . $general->cur_text . ' Refunded from withdrawal rejection';
        $transaction->trx = $withdraw->trx;
        $transaction->save();

        notify($seller, 'WITHDRAW_REJECT', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount),
            'amount' => showAmount($withdraw->amount),
            'charge' => showAmount($withdraw->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($withdraw->rate),
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($seller->balance),
            'admin_details' => $request->details
        ]);

        $notify[] = ['success', 'Withdraw has been rejected.'];
        return redirect()->route('admin.withdrawals.pending')->withNotify($notify);
    }

}

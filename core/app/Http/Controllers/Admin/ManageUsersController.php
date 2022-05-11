<?php
namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\EmailLog;
use App\Models\Userwallet;
use App\Models\Withdrawal;
use App\Models\Stafftransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\WithdrawMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class ManageUsersController extends Controller
{
    public function allDispatch()
    {
        $pageTitle = 'All Dispatch Staff';
        $emptyMessage = 'No Dispatch Staff found';
        $type = 2;
        $users = Admin::whereType(2)->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.staff.staff', compact('type','pageTitle', 'emptyMessage', 'users'));
    }
    public function allStaff()
    {
        $pageTitle = 'All Sub-Admin Staff';

        $type = 1;
        $emptyMessage = 'No Subadmin Staff found';
        $users = Admin::whereType(1)->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.staff.staff', compact('type','pageTitle', 'emptyMessage', 'users'));
    }


    public function addstaff(Request $request)
    {
    $request->validate([
            'state'   => 'required|integer',
            'type'   => 'required|integer',
            'name'         => 'required|max:50',
            'password'         => 'required|max:50',
            'username'          => 'required|max:50',
            'email'             => 'required|max:90',

        ]);
        $admin = Auth::guard('admin')->user();

        $email = Admin::whereEmail($request->email)->first();
        if(isset($email))
        {
        $notify[]=['error', 'Sorry, there is a staff with this email address'];
        return redirect()->back()->withNotify($notify);
        }

        $username = Admin::whereUsername($request->username)->first();
        if(isset($username))
        {
         $notify[]=['error', 'Sorry, there is a staff with this username'];
        return redirect()->back()->withNotify($notify);
        }


        $staff = new Admin;
        $staff->name        = $request->name;
        $staff->status        = 1;
        $staff->username        = $request->username;
        $staff->email        = $request->email;
        $staff->state        = $request->state;
        $staff->type        = $request->type;
        $staff->wcode        = getTrx();
        $staff->password        = bcrypt($request->password);;
        $staff->created_by  = $admin->id;
        $staff->save();

        $notify[] = ['success', 'Staff Account created successfully.'];
        return back()->withNotify($notify);

    }


    public function deletestaff($id)
    {
        $user = Admin::findOrFail($id);
        if($id < 2)
        {
        $notify[]=['error', 'Sorry, Action not allowed'];
        return redirect()->back()->withNotify($notify);
        }
        $user->delete();
        $notify[] = ['success', 'Staff Account deleted successfully.'];
        return back()->withNotify($notify);
    }



    public function editstaff($id)
    {
        $user = Admin::findOrFail($id);
        $pageTitle = 'Staff Details';
        //return $user->wcode;
         $wallet = UserWallet::whereWcode($user->wcode)->first();
        if($user->type == 2)
         {

        if(!$wallet)
        {
        $wcode = getTrx();
        $newwallet = new Userwallet;
        $newwallet->wcode  = $wcode;
        $newwallet->balance  = 0;
        $newwallet->save();
        $user->wcode = $wcode;
        $user->save();

        }
           }

        $totalcredit = Stafftransaction::whereStaffId($id)->whereTrxType("+")->sum('amount');
        $totalOrders = Order::whereDispatcher($id)->count();
        $dispatchedOrders = Order::whereDispatcher($id)->whereStatus(3)->count();

        return view('admin.staff.staffdetails', compact('dispatchedOrders','totalOrders','totalcredit','pageTitle', 'user','wallet'));


    }

    public function editstaffpost(Request $request, $id)
    {
        $staff = Admin::findOrFail($id);
        $request->validate([
            'state'   => 'required|integer',
            'name'         => 'required|max:50',
            'username'          => 'required|max:50',
            'email'             => 'required|max:90',

        ]);

        $staff->name        = $request->name;
        $staff->status        = $request->status;
        $staff->username        = $request->username;
        $staff->email        = $request->email;
        $staff->state        = $request->state;
        if(isset($request->password))
        {
        $staff->password    = bcrypt($request->password);;
        }
        $staff->save();

        $notify[] = ['success', 'Staff Account updated successfully.'];
        return back()->withNotify($notify);

    }

     public function staffshowEmailSingleForm($id)
    {
        $user = Admin::findOrFail($id);
        $pageTitle = 'Send Email To: ' . $user->username;
        return view('admin.staff.email_single', compact('pageTitle', 'user'));
    }

    public function staffsendEmailSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        $user = Admin::findOrFail($id);
        sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        $notify[] = ['success', $user->username . ' will receive an email shortly.'];
        return back()->withNotify($notify);
    }

     public function staffemailLog($id){
        $user = Admin::findOrFail($id);
        $pageTitle = 'Email log of '.$user->username;
        $logs = EmailLog::where('user_id',$id)->with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No email log found';
        return view('admin.staff.email_log', compact('pageTitle','logs','emptyMessage','user'));
    }

    public function staffemailDetails($id){
        $email = EmailLog::findOrFail($id);
        $pageTitle = 'Email details';
        return view('admin.staff.email_details', compact('pageTitle','email'));
    }

    public function creditstaff(Request $request, $id)
    {
        $staff = Admin::findOrFail($id);
        $request->validate([
            'amount'   => 'required|integer',
            'narration'   => 'required'

        ]);

        $wallet = Userwallet::whereWcode($staff->wcode)->first();
        $wallet->balance += $request->amount;
        $trx = new Stafftransaction;
        $trx->pre_balance        = $wallet->balance;
        $wallet->save();

        $admin = Auth::guard('admin')->user();

        $trx->staff_id        = $id;
        $trx->admin_id        = $admin->id;
        $trx->amount        = $request->amount;
        $trx->post_balance        = $wallet->balance;
        $trx->trx_type        = "+" ;
        $trx->trx        = getTrx();
        $trx->details        = $request->narration;
        $trx->save();

        $notify[] = ['success', 'Staff Account credited successfully.'];
        return back()->withNotify($notify);

    }

    public function debitstaff(Request $request, $id)
    {
        $staff = Admin::findOrFail($id);
        $request->validate([
            'amount'   => 'required|integer',
            'narration'   => 'required'

        ]);

        $wallet = Userwallet::whereWcode($staff->wcode)->first();
        if($wallet->balance < $request->amount)
        {
         $notify[] = ['error', 'Amount greater than wallet balance.'];
        return back()->withNotify($notify);
        }
        $wallet->balance -= $request->amount;
        $trx = new Stafftransaction;
        $trx->pre_balance        = $wallet->balance;
        $wallet->save();

        $admin = Auth::guard('admin')->user();
        $trx = new Stafftransaction;
        $trx->staff_id        = $id;
        $trx->admin_id        = $admin->id;
        $trx->amount        = $request->amount;
        $trx->post_balance        = $wallet->balance;
        $trx->trx_type        = "-" ;
        $trx->trx        = getTrx();
        $trx->details        = $request->narration;
        $trx->save();

        $notify[] = ['success', 'Staff Account credited successfully.'];
        return back()->withNotify($notify);

    }

     public function creditstafflog($id){
        $user = Admin::findOrFail($id);
        $pageTitle = 'Credit Log '.$user->username;
        $logs = Stafftransaction::where('staff_id',$id)->whereTrxType('+')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transaction log found';
        return view('admin.staff.trx_log', compact('pageTitle','logs','emptyMessage','user'));
    }


     public function debitstafflog($id){
        $user = Admin::findOrFail($id);
        $pageTitle = 'Debit Log '.$user->username;
        $logs = Stafftransaction::where('staff_id',$id)->whereTrxType('-')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transaction log found';
        return view('admin.staff.trx_log', compact('pageTitle','logs','emptyMessage','user'));
    }
    
     public function mycreditlog(){
       
        $admin = Auth::guard('admin')->user();
        $user = Admin::findOrFail($admin->id);
        $pageTitle = 'Credit Log '.$user->username;
        $logs = Stafftransaction::where('staff_id',$id)->whereTrxType('+')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transaction log found';
        return view('admin.staff.trx_log', compact('pageTitle','logs','emptyMessage','user'));
    }


     public function mydebitlog(){
        
        $admin = Auth::guard('admin')->user();
        $user = Admin::findOrFail($admin->id);
        $pageTitle = 'Debit Log '.$user->username;
        $logs = Stafftransaction::where('staff_id',$id)->whereTrxType('-')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transaction log found';
        return view('admin.staff.trx_log', compact('pageTitle','logs','emptyMessage','user'));
    }



    
    public function allconsultant()
        {
    
            $pageTitle = 'Sales Consultant';
            $emptyMessage = 'No customer found';
            $users = User::orderBy('id','desc')->whereSc(1)->paginate(getPaginate());
            return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
        }



    public function allUsers()
    {

        $pageTitle = 'All Customers';
        $emptyMessage = 'No customer found';
        $users = User::orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function activeUsers()
    {
        $pageTitle = 'Active Customers';
        $emptyMessage = 'No active customer found';
        $users = User::active()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function bannedUsers()
    {
        $pageTitle = 'Banned Customers';
        $emptyMessage = 'No banned customer found';
        $users = User::banned()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function emailUnverifiedUsers()
    {
        $pageTitle = 'Email Unverified Customers';
        $emptyMessage = 'No email unverified customer found';
        $users = User::emailUnverified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function emailVerifiedUsers()
    {
        $pageTitle = 'Email Verified Customers';
        $emptyMessage = 'No email verified customer found';
        $users = User::emailVerified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function smsUnverifiedUsers()
    {
        $pageTitle = 'SMS Unverified Customers';
        $emptyMessage = 'No sms unverified customer found';
        $users = User::smsUnverified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function smsVerifiedUsers()
    {
        $pageTitle = 'SMS Verified Customers';
        $emptyMessage = 'No sms verified customer found';
        $users = User::smsVerified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }


    public function search(Request $request, $scope)
    {
        $search     = $request->search;
        $users      = User::where(function ($user) use ($search) {
                        $user->where('username', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });

        if ($scope == 'active') {
            $pageTitle = 'Active ';
            $users = $users->where('status', 1);
        }elseif($scope == 'banned'){
            $pageTitle = 'Banned';
            $users = $users->where('status', 0);
        }elseif($scope == 'emailUnverified'){
            $pageTitle = 'Email Unverified ';
            $users = $users->where('ev', 0);
        }elseif($scope == 'smsUnverified'){
            $pageTitle = 'SMS Unverified ';
            $users = $users->where('sv', 0);
        }else{
            $pageTitle = '';
        }

        $users = $users->paginate(getPaginate());
        $pageTitle .= 'Customer Search - ' . $search;
        $emptyMessage = 'No search result found';
        return view('admin.users.list', compact('pageTitle', 'search', 'scope', 'emptyMessage', 'users'));
    }


    public function detail($id)
    {
        $pageTitle          = 'Customer\'s Detail';
        $user               = User::findOrFail($id);
        $totalDeposit       = Deposit::where('user_id', $user->id)->where('status',1)->sum('amount');
        $totalTransaction   = Transaction::where('user_id', $user->id)->count();
        $totalOrders        = Order::where('user_id', $user->id)->where('payment_status', '!=', 0)->count();
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('admin.users.detail', compact('pageTitle', 'user','totalDeposit','totalTransaction','countries' ,'totalOrders'));

    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);


        $request->validate([
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
        ]);

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->address = [
                            'address' => $request->address,
                            'city' => $request->city,
                            'state' => $request->state,
                            'zip' => $request->zip,
                            'country' => @$user->address->country,
                        ];
        $user->status   = $request->status ? 1 : 0;
        $user->ev       = $request->ev ? 1 : 0;
        $user->sv       = $request->sv ? 1 : 0;
        $user->ts       = $request->ts ? 1 : 0;
        $user->tv       = $request->tv ? 1 : 0;
        $user->save();

        $notify[] = ['success', 'Customer detail has been updated'];
        return redirect()->back()->withNotify($notify);
    }

    public function userLoginHistory($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'Customer Login History - ' . $user->username;
        $emptyMessage = 'No users login found.';
        $loginLogs = $user->loginLogs()->orderBy('id','desc')->with('user')->paginate(getPaginate());
        return view('admin.users.logins', compact('pageTitle', 'emptyMessage', 'loginLogs'));
    }

    public function showEmailSingleForm($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'Send Email To: ' . $user->username;
        return view('admin.users.email_single', compact('pageTitle', 'user'));
    }

    public function sendEmailSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        $user = User::findOrFail($id);
        sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        $notify[] = ['success', $user->username . ' will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function transactions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search customer Transactions : ' . $user->username;
            $transactions = $user->transactions()->where('trx', $search)->with('user')->orderBy('id','desc')->paginate(getPaginate());
            $emptyMessage = 'No transactions';
            return view('admin.reports.transactions', compact('pageTitle', 'search', 'user', 'transactions', 'emptyMessage'));
        }
        $pageTitle = 'Customer Transactions : ' . $user->username;
        $transactions = $user->transactions()->with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transactions';
        return view('admin.reports.transactions', compact('pageTitle', 'user', 'transactions', 'emptyMessage'));
    }

    public function deposits(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $userId = $user->id;
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search customer payments : ' . $user->username;
            $deposits = $user->deposits()->where('trx', $search)->orderBy('id','desc')->paginate(getPaginate());
            $emptyMessage = 'No payments';
            return view('admin.deposit.log', compact('pageTitle', 'search', 'user', 'deposits', 'emptyMessage','userId'));
        }

        $pageTitle = 'Customer Payment : ' . $user->username;
        $deposits = $user->deposits()->orderBy('id','desc')->with(['gateway','user'])->paginate(getPaginate());
        $successful = $user->deposits()->orderBy('id','desc')->where('status',1)->sum('amount');
        $pending = $user->deposits()->orderBy('id','desc')->where('status',2)->sum('amount');
        $rejected = $user->deposits()->orderBy('id','desc')->where('status',3)->sum('amount');
        $emptyMessage = 'No payments';
        $scope = 'all';
        return view('admin.deposit.log', compact('pageTitle', 'user', 'deposits', 'emptyMessage','userId','scope','successful','pending','rejected'));
    }


    public function depViaMethod($method,$type = null,$userId){
        $method = Gateway::where('alias',$method)->firstOrFail();
        $user = User::findOrFail($userId);
        if ($type == 'approved') {
            $pageTitle = 'Approved Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('user_id',$user->id)->where('method_code',$method->code)->where('status', 1)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }elseif($type == 'rejected'){
            $pageTitle = 'Rejected Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('user_id',$user->id)->where('method_code',$method->code)->where('status', 3)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }elseif($type == 'successful'){
            $pageTitle = 'Successful Payment Via '.$method->name;
            $deposits = Deposit::where('status', 1)->where('user_id',$user->id)->where('method_code',$method->code)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }elseif($type == 'pending'){
            $pageTitle = 'Pending Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('user_id',$user->id)->where('method_code',$method->code)->where('status', 2)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }else{
            $pageTitle = 'Payment Via '.$method->name;
            $deposits = Deposit::where('status','!=',0)->where('user_id',$user->id)->where('method_code',$method->code)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }
        $pageTitle = 'Payment History: '.$user->username.' Via '.$method->name;
        $methodAlias = $method->alias;
        $emptyMessage = 'Nop payment history found';
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits','methodAlias','userId'));
    }

    public function withdrawals(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search customer Withdrawals : ' . $user->username;
            $withdrawals = $user->withdrawals()->where('trx', 'like',"%$search%")->orderBy('id','desc')->paginate(getPaginate());
            $emptyMessage = 'No withdrawals';
            return view('admin.withdraw.withdrawals', compact('pageTitle', 'user', 'search', 'withdrawals', 'emptyMessage'));
        }
        $pageTitle = 'User Withdrawals : ' . $user->username;
        $withdrawals = $user->withdrawals()->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No withdrawals';
        $userId = $user->id;
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'user', 'withdrawals', 'emptyMessage','userId'));
    }

    public  function withdrawalsViaMethod($method,$type,$userId){
        $method = WithdrawMethod::findOrFail($method);
        $user = User::findOrFail($userId);
        if ($type == 'approved') {
            $pageTitle = 'Approved Withdrawal of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 1)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());
        }elseif($type == 'rejected'){
            $pageTitle = 'Rejected Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 3)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());

        }elseif($type == 'pending'){
            $pageTitle = 'Pending Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 2)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());
        }else{
            $pageTitle = 'Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', '!=', 0)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());
        }
        $emptyMessage = 'Withdraw Log Not Found';
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'emptyMessage','method'));
    }

    public function showEmailAllForm()
    {
        $pageTitle = 'Send Email To All Customers';
        return view('admin.users.email_all', compact('pageTitle'));
    }

    public function sendEmailAll(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        foreach (User::where('status', 1)->cursor() as $user) {
            sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        }

        $notify[] = ['success', 'All customers will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function login($id){
        $user = User::findOrFail($id);
        Auth::login($user);
        return redirect()->route('user.home');
    }

    public function emailLog($id){
        $user = User::findOrFail($id);
        $pageTitle = 'Email log of '.$user->username;
        $logs = EmailLog::where('user_id',$id)->with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No email log found';
        return view('admin.users.email_log', compact('pageTitle','logs','emptyMessage','user'));
    }

    public function emailDetails($id){
        $email = EmailLog::findOrFail($id);
        $pageTitle = 'Email details';
        return view('admin.users.email_details', compact('pageTitle','email'));
    }

}

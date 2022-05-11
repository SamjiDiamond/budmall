<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shop;
use App\Models\Seller;
use App\Models\Product;
use App\Models\SellLog;
use App\Models\EmailLog;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\WithdrawMethod;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;

class ManageSellerController extends Controller
{
    public function allSeller()
    {
        $pageTitle      = 'All Sellers';
        $emptyMessage   = 'No seller found';
        $sellers          = Seller::orderBy('id','desc')->with('products')->paginate(getPaginate());
        return view('admin.seller.list', compact('pageTitle', 'emptyMessage', 'sellers'));
    }

    public function activeSeller()
    {
        $pageTitle      = 'Active Sellers';
        $emptyMessage   = 'No active seller found';
        $sellers          = Seller::active()->with('products')->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.seller.list', compact('pageTitle', 'emptyMessage', 'sellers'));
    }

    public function bannedSeller()
    {
        $pageTitle      = 'Banned Sellers';
        $emptyMessage   = 'No banned seller found';
        $sellers          = Seller::banned()->with('products')->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.seller.list', compact('pageTitle', 'emptyMessage', 'sellers'));
    }

    public function emailUnverifiedSeller()
    {
        $pageTitle      = 'Email Unverified Seller';
        $emptyMessage   = 'No email unverified seller found';
        $sellers          = Seller::emailUnverified()->with('products')->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.seller.list', compact('pageTitle', 'emptyMessage', 'sellers'));
    }

    public function emailVerifiedSeller()
    {
        $pageTitle      = 'Email Verified Seller';
        $emptyMessage   = 'No email verified seller found';
        $sellers          = Seller::emailVerified()->with('products')->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.seller.list', compact('pageTitle', 'emptyMessage', 'sellers'));
    }

    public function smsUnverifiedSeller()
    {
        $pageTitle      = 'SMS Unverified Seller';
        $emptyMessage   = 'No sms unverified seller found';
        $sellers          = Seller::smsUnverified()->with('products')->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.seller.list', compact('pageTitle', 'emptyMessage', 'sellers'));
    }

    public function smsVerifiedSeller()
    {
        $pageTitle      = 'SMS Verified Seller';
        $emptyMessage   = 'No sms verified seller found';
        $sellers          = Seller::smsVerified()->with('products')->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.seller.list', compact('pageTitle', 'emptyMessage', 'sellers'));
    }


    public function search(Request $request, $scope)
    {
        $search     = $request->search;
        $sellers      = Seller::where(function ($user) use ($search) {
                        $user->where('username', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });

        if ($scope == 'active') {
            $pageTitle  = 'Active ';
            $sellers    = $sellers->where('status', 1);
        }elseif($scope == 'banned'){
            $pageTitle  = 'Banned';
            $sellers    = $sellers->where('status', 0);
        }elseif($scope == 'emailUnverified'){
            $pageTitle  = 'Email Unverified ';
            $sellers    = $sellers->where('ev', 0);
        }elseif($scope == 'smsUnverified'){
            $pageTitle  = 'SMS Unverified ';
            $sellers    = $sellers->where('sv', 0);
        }elseif($scope == 'withBalance'){
            $pageTitle  = 'With Balance ';
            $sellers    = $sellers->where('balance','!=',0);
        }else{
            $pageTitle  = '';
        }

        $sellers          = $sellers->paginate(getPaginate());
        $pageTitle      .= 'Seller Search - ' . $search;
        $emptyMessage   = 'No search result found';
        return view('admin.seller.list', compact('pageTitle', 'search', 'scope', 'emptyMessage', 'sellers'));
    }

    public function detail($id)
    {
        $pageTitle = 'Seller Details';
        $seller = Seller::findOrFail($id);
        $totalWithdraw = Withdrawal::where('seller_id',$seller->id)->where('status',1)->sum('amount');
        $totalTransaction = Transaction::where('seller_id',$seller->id)->count();
        $totalProducts = Product::where('seller_id',$seller->id)->count();
        $totalSold = SellLog::where('seller_id',$seller->id)->sum('after_commission');
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('admin.seller.detail', compact('pageTitle', 'seller','totalWithdraw','totalTransaction','countries','totalProducts','totalSold'));
    }

    public function sellLogs($id)
    {
        $seller = Seller::findOrFail($id);
        $pageTitle = "Sell logs of : $seller->username";
        $logs = SellLog::where('seller_id',$seller->id)->paginate(getPaginate());
        return view('admin.seller.sales_log', compact('pageTitle','logs','seller'));
    }
    public function sellerProducts($id)
    {
        $seller     = Seller::findOrFail($id);
        $pageTitle  = "Products of : $seller->username";
        $products   = Product::where('seller_id',$seller->id)->paginate(getPaginate());
        return view('admin.products.index', compact('pageTitle','products','seller'));
    }


    public function update(Request $request, $id)
    {
        $seller = Seller::findOrFail($id);

        $request->validate([
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
        ]);

        $seller->firstname  = $request->firstname;
        $seller->lastname   = $request->lastname;
        $seller->address    = [
                                'address' => $request->address,
                                'city' => $request->city,
                                'state' => $request->state,
                                'zip' => $request->zip,
                                'country' => @$seller->address->country,
                            ];
        $seller->status = $request->status ? 1 : 0;
        $seller->ev = $request->ev ? 1 : 0;
        $seller->sv = $request->sv ? 1 : 0;
        $seller->ts = $request->ts ? 1 : 0;
        $seller->tv = $request->tv ? 1 : 0;
        $seller->save();

        $notify[] = ['success', 'Seller detail has been updated'];
        return redirect()->back()->withNotify($notify);
    }

    public function sellerLoginHistory($id)
    {
        $user = Seller::findOrFail($id);
        $pageTitle = 'Seller Login History - ' . $user->username;
        $emptyMessage = 'No seller login found.';
        $loginLogs = $user->loginLogs()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.seller.logins', compact('pageTitle', 'emptyMessage', 'loginLogs'));
    }

    public function showEmailSingleForm($id)
    {
        $user = Seller::findOrFail($id);
        $pageTitle = 'Send Email To: ' . $user->username;
        return view('admin.seller.email_single', compact('pageTitle', 'user'));
    }

    public function sendEmailSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        $user = Seller::findOrFail($id);
        sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        $notify[] = ['success', $user->username . ' will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function transactions(Request $request, $id)
    {
        $user = Seller::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search Seller Transactions : ' . $user->username;
            $transactions = $user->transactions()->where('trx', $search)->with('seller')->orderBy('id','desc')->paginate(getPaginate());
            $emptyMessage = 'No transactions';
            return view('admin.reports.seller_transactions', compact('pageTitle', 'search', 'user', 'transactions', 'emptyMessage'));
        }
        $pageTitle = 'Seller Transactions : ' . $user->username;
        $transactions = $user->transactions()->with('seller')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transactions';
        return view('admin.reports.seller_transactions', compact('pageTitle', 'user', 'transactions', 'emptyMessage'));
    }

    public function withdrawals(Request $request, $id)
    {
        $user = Seller::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search Seller Withdrawals : ' . $user->username;
            $withdrawals = $user->withdrawals()->where('trx', 'like',"%$search%")->orderBy('id','desc')->paginate(getPaginate());
            $emptyMessage = 'No withdrawals';
            return view('admin.withdraw.withdrawals', compact('pageTitle', 'user', 'search', 'withdrawals', 'emptyMessage'));
        }
        $pageTitle = 'Seller Withdrawals : ' . $user->username;
        $withdrawals = $user->withdrawals()->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No withdrawals';
        $userId = $user->id;
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'user', 'withdrawals', 'emptyMessage','userId'));
    }

    public  function withdrawalsViaMethod($method,$type,$userId){
        $method = WithdrawMethod::findOrFail($method);
        $user = Seller::findOrFail($userId);
        if ($type == 'approved') {
            $pageTitle = 'Approved Withdrawal of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 1)->where('seller_id',$user->id)->with(['seller','method'])->orderBy('id','desc')->paginate(getPaginate());
        }elseif($type == 'rejected'){
            $pageTitle = 'Rejected Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 3)->where('seller_id',$user->id)->with(['seller','method'])->orderBy('id','desc')->paginate(getPaginate());

        }elseif($type == 'pending'){
            $pageTitle = 'Pending Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 2)->where('seller_id',$user->id)->with(['seller','method'])->orderBy('id','desc')->paginate(getPaginate());
        }else{
            $pageTitle = 'Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', '!=', 0)->where('seller_id',$user->id)->with(['seller','method'])->orderBy('id','desc')->paginate(getPaginate());
        }
        $emptyMessage = 'Withdraw Log Not Found';
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'emptyMessage','method'));
    }

    public function showEmailAllForm()
    {
        $pageTitle = 'Send Email To All Seller ';
        return view('admin.seller.email_all', compact('pageTitle'));
    }

    public function sendEmailAll(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        foreach (Seller::where('status', 1)->cursor() as $user) {
            sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        }

        $notify[] = ['success', 'All seller  will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function login($id){
        $user = Seller::findOrFail($id);
        auth()->guard('seller')->login($user);
        return redirect()->route('seller.home');
    }

    public function emailLog($id){
        $seller      = Seller::findOrFail($id);
        $pageTitle   = 'Email log of '.$seller->username;
        $logs        = EmailLog::where('seller_id',$id)->with('seller')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage= 'No data found';
        return view('admin.seller.email_log', compact('pageTitle','logs','emptyMessage','seller'));
    }

    public function emailDetails($id){
        $email = EmailLog::findOrFail($id);
        $pageTitle = 'Email details';
        return view('admin.seller.email_details', compact('pageTitle','email'));
    }

    public function shopDetails($sellerID)
    {
        $seller = Seller::findOrFail($sellerID);
        $pageTitle = "Shop Details of : $seller->username";
        if($seller->shop){
            $shop = $seller->shop;
        }else{
            $notify[]=['error','Shop not found!'];
            return back()->withNotify($notify);
        }

        return view('admin.seller.shop_details',compact('shop','pageTitle','seller'));
    }

    public function shopUpdate(Request $request)
    {
        $seller         = Seller::findOrFail($request->seller_id);
        $shop           = Shop::where('seller_id', $seller->id)->first();
        $logoValidation = $coverValidation = 'required';

        if($shop){
            $logoValidation     = $shop->logo?'nullable':'required';
            $coverValidation    = $shop->cover?'nullable':'required';
        }

        $request->validate([
            'name'                  => 'required|string|max:40',
            'phone'                 => 'required|string|max:40',
            'address'               => 'required|string|max:600',
            'opening_time'          => 'nullable|date_format:H:i',
            'closing_time'          => 'nullable|date_format:H:i',
            'meta_title'            => 'nullable|string|max:191',
            'meta_description'      => 'nullable|string|max:191',
            'meta_keywords'         => 'nullable|array',
            'meta_keywords.array.*' => 'string',
            'social_links'          => 'nullable|array',
            'social_links.*.name'   => 'required_with:social_links|string',
            'social_links.*.icon'   => 'required_with:social_links|string',
            'social_links.*.link'   => 'required_with:social_links|string',

            'image'                 => [$logoValidation, 'image',new FileTypeValidate(['jpg','jpeg','png'])],
            'cover_image'           => [$coverValidation, 'image',new FileTypeValidate(['jpg','jpeg','png'])]
        ],[
            'firstname.required'                  => 'First name field is required',
            'lastname.required'                   => 'Last name field is required',
            'social_links.*.name.required_with'   => 'All specification name is required',
            'social_links.*.icon.required_with'   => 'All specification icon is required',
            'social_links.*.link.required_with'   => 'All specification link is required',
            'image.required'                      => 'Logo is required',
            'cover_image.required'                => 'Cover is required'
        ]);

        if(!$shop) $shop = new Shop();


        if ($request->hasFile('image')) {
            $location       = imagePath()['seller']['shop_logo']['path'];
            $size           = imagePath()['seller']['shop_logo']['size'];
            $shop->logo     = uploadImage($request->image, $location, $size, @$shop->logo);
        }

        if($request->hasFile('cover_image')){
            $location       = imagePath()['seller']['shop_cover']['path'];
            $size           = imagePath()['seller']['shop_cover']['size'];
            $shop->cover    = uploadImage($request->cover_image, $location, $size, @$seller->cover_image);
        }

        $shop->name              = $request->name;
        $shop->seller_id         = $seller->id;
        $shop->phone             = $request->phone;
        $shop->address           = $request->address;
        $shop->opens_at          = $request->opening_time;
        $shop->closed_at         = $request->closing_time;
        $shop->meta_title        = $request->meta_title;
        $shop->meta_description  = $request->meta_description;
        $shop->meta_keywords     = $request->meta_keywords??null;
        $shop->social_links      = $request->social_links??null;
        $shop->save();

        $notify[]=['success','Updated successfully'];
        return back()->withNotify($notify);

    }

    public function featureStatus($id)
    {
        $seller = Seller::findOrFail($id);
        if($seller->featured == 1){
            $seller->featured = 0;
            $msg = "Seller removed from featured list";
        } else{
            $seller->featured = 1;
            $msg = "Seller removed from featured list";
        }
        $seller->save();
        $notify[]=['success',$msg];
        return back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|gt:0']);

        $user = Seller::findOrFail($id);
        $amount = getAmount($request->amount);
        $general = GeneralSetting::first(['cur_text','cur_sym']);
        $trx = getTrx();

        if ($request->act) {
            $user->balance += $amount;
            $user->save();
            $notify[] = ['success', $general->cur_sym . $amount . ' has been added to ' . $user->username . ' balance'];


            $transaction = new Transaction();
            $transaction->seller_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = getAmount($user->balance);
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Added Balance Via Admin';
            $transaction->trx =  $trx;
            $transaction->save();


            notify($user, 'BAL_ADD', [
                'trx' => $trx,
                'amount' => $amount,
                'currency' => $general->cur_text,
                'post_balance' => getAmount($user->balance),
            ]);

        } else {
            if ($amount > $user->balance) {
                $notify[] = ['error', $user->username . ' has insufficient balance.'];
                return back()->withNotify($notify);
            }
            $user->balance -= $user->balance;
            $user->save();



            $transaction = new Transaction();
            $transaction->seller_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = getAmount($user->balance);
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Subtract Balance Via Admin';
            $transaction->trx =  $trx;
            $transaction->save();


            notify($user, 'BAL_SUB', [
                'trx' => $trx,
                'amount' => $amount,
                'currency' => $general->cur_text,
                'post_balance' => getAmount($user->balance)
            ]);
            $notify[] = ['success', $general->cur_sym . $amount . ' has been subtracted from ' . $user->username . ' balance'];
        }
        return back()->withNotify($notify);
    }
}

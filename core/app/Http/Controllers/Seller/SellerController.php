<?php

namespace App\Http\Controllers\Seller;

use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Product;
use App\Models\SellLog;
use App\Models\Withdrawal;
use App\Models\OrderDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\WithdrawMethod;
use App\Rules\FileTypeValidate;
use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SellerController extends Controller
{

    public function home()
    {
        $pageTitle = 'Dashboard';
        $seller    = seller();

        $order['pending']       = OrderDetail::pendingOrder()->where('seller_id',$seller->id)->count();
        $order['processing']    = OrderDetail::processingOrder()->where('seller_id',$seller->id)->count();
        $order['dispatched']    = OrderDetail::dispatchedOrder()->where('seller_id',$seller->id)->count();
        $order['delivered']     = OrderDetail::deliveredOrder()->where('seller_id',$seller->id)->count();
        $order['cancelled']     = OrderDetail::cancelledOrder()->where('seller_id',$seller->id)->count();
        $order['cod']           = OrderDetail::cod()->where('seller_id',$seller->id)->count();

        $product['approved']    = Product::active()->where('seller_id',$seller->id)->count();
        $product['pending']     = Product::pending()->where('seller_id',$seller->id)->count();
        $product['total_sold']  = Product::active()->where('seller_id',$seller->id)->sum('sold');


        $sale['last_seven_days']          = SellLog::where('seller_id',$seller->id)->where('created_at', '>=', Carbon::today()->subDays(7))->sum('after_commission');

        $sale['last_fifteen_days']        = SellLog::where('seller_id',$seller->id)->where('created_at', '>=', Carbon::today()->subDays(15))->sum('after_commission');

        $sale['last_thirty_days']         = SellLog::where('seller_id',$seller->id)->where('created_at', '>=', Carbon::today()->subDays(30))->sum('after_commission');


        $report['months'] = collect([]);
        $report['withdraw_month_amount'] = collect([]);

        $withdrawalMonth = Withdrawal::where('seller_id',$seller->id)->where('created_at', '>=', Carbon::now()->subYear())->where('status', 1)
            ->selectRaw("SUM( CASE WHEN status = 1 THEN amount END) as withdrawAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')->get();
        $withdrawalMonth->map(function ($withdrawData) use ($report){
            if (!in_array($withdrawData->months,$report['months']->toArray())) {
                $report['months']->push($withdrawData->months);
            }
            $report['withdraw_month_amount']->push(showAmount($withdrawData->withdrawAmount));
        });

        $months = $report['months'];

        for($i = 0; $i < $months->count(); ++$i) {
            $monthVal      = Carbon::parse($months[$i]);
            if(isset($months[$i+1])){
                $monthValNext = Carbon::parse($months[$i+1]);
                if($monthValNext < $monthVal){
                    $temp = $months[$i];
                    $months[$i]   = Carbon::parse($months[$i+1])->format('F-Y');
                    $months[$i+1] = Carbon::parse($temp)->format('F-Y');
                }else{
                    $months[$i]   = Carbon::parse($months[$i])->format('F-Y');
                }
            }
        }

        $withdraw['total'] = Withdrawal::where('seller_id',$seller->id)->where('status',1)->sum('amount');
        $withdraw['pending'] = Withdrawal::where('seller_id',$seller->id)->where('status',2)->count();
        $withdraw['approved'] = Withdrawal::where('seller_id',$seller->id)->where('status',1)->count();

        $latestOrders = OrderDetail::pendingOrder()->where('seller_id',seller()->id)
                        ->with(['order','order.user','order.deposit.gateway'])
                        ->orderBy('id', 'DESC')->take(7)->get();



        return view('seller.dashboard', compact('pageTitle','order','sale','months','report','withdrawalMonth','withdraw','product','latestOrders'));
    }

    public function sellLogs()
    {
        $pageTitle     = "Sales Log";
        $emptyMessage  = 'No sales log found';
        $logs          = SellLog::when(request()->search,function($q){
                            return $q->where('order_id',request()->search);
                        })->where('seller_id',seller()->id)->latest()->paginate(getPaginate());
        return view('seller.sales.index', compact('pageTitle','emptyMessage','logs'));
    }

    public function shop()
    {
        $pageTitle  = 'Manage Shop';
        $seller     = seller();
        $shop       = $seller->shop;
        return view('seller.shop', compact('pageTitle', 'seller', 'shop'));
    }

    public function shopUpdate(Request $request)
    {
        $seller         = seller();
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
            'firstname.required'=>'First name field is required',
            'lastname.required' =>'Last name field is required',
            'social_links.*.name.required_with'   => 'All social name is required',
            'social_links.*.icon.required_with'   => 'All social icon is required',
            'social_links.*.link.required_with'   => 'All social link is required',
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

    public function profile()
    {
        $pageTitle  = "Profile Setting";
        $seller     = seller();
        return view('seller.profile', compact('pageTitle','seller'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname'  => 'required|string|max:40',
            'address'   => 'nullable|string|max:600',
            'state'     => 'nullable|string|max:40',
            'zip'       => 'nullable|string|max:40',
            'city'      => 'nullable|string|max:50',
            'image'     => ['image',new FileTypeValidate(['jpg','jpeg','png'])]
        ],[
            'firstname.required'=>'First name field is required',
            'lastname.required' =>'Last name field is required'
        ]);

        $seller = seller();

        $address = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$seller->address->country,
            'city' => $request->city,
        ];

        $seller->firstname  = $request->firstname;
        $seller->lastname   = $request->lastname;
        $seller->address    = $address;

        if ($request->hasFile('image')) {
            $location       = imagePath()['seller']['profile']['path'];
            $size           = imagePath()['seller']['profile']['size'];
            $filename       = uploadImage($request->image, $location, $size, $seller->image);
            $seller->image  = $filename;
        }

        $seller->save();

        $notify[] = ['success', 'Profile updated successfully.'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle  = 'Change password';
        $seller     = seller();
        return view('seller.password', compact('pageTitle', 'seller'));
    }

    public function submitPassword(Request $request)
    {
        $password_validation    = Password::min(6);
        $general                = GeneralSetting::first();

        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password'  => 'required',
            'password'          => ['required','confirmed',$password_validation]
        ]);

        try {
            $seller     = seller();
            if (Hash::check($request->current_password, $seller->password)) {
                $password = Hash::make($request->password);
                $seller->password = $password;
                $seller->save();
                $notify[] = ['success', 'Password changes successfully.'];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', 'The password doesn\'t match!'];
                return back()->withNotify($notify);
            }
        } catch (\PDOException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }


    public function withdrawMoney()
    {
        $withdrawMethod = WithdrawMethod::where('status',1)->get();
        $pageTitle = 'Withdraw Money';
        return view('seller.withdraw.methods', compact('pageTitle','withdrawMethod'));
    }

    public function withdrawStore(Request $request)
    {
        $this->validate($request, [
            'method_code' => 'required',
            'amount' => 'required|numeric|gt:0'
        ]);

        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->firstOrFail();
        $seller = seller();

        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your requested amount is smaller than minimum amount.'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your requested amount is larger than maximum amount.'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $seller->balance) {
            $notify[] = ['error', 'You do not have sufficient balance for withdraw.'];
            return back()->withNotify($notify);
        }


        $charge         = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge    = $request->amount - $charge;
        $finalAmount    = $afterCharge * $method->rate;

        $withdraw = new Withdrawal();
        $withdraw->method_id    = $method->id; // wallet method ID
        $withdraw->seller_id    = $seller->id;
        $withdraw->amount       = $request->amount;
        $withdraw->currency     = $method->currency;
        $withdraw->rate         = $method->rate;
        $withdraw->charge       = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx          = getTrx();
        $withdraw->save();
        session()->put('wtrx', $withdraw->trx);
        return redirect()->route('seller.withdraw.preview');
    }

    public function withdrawPreview()
    {
        $withdraw = Withdrawal::with('method','seller')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id','desc')->firstOrFail();
        $pageTitle = 'Withdrawal Preview';
        return view('seller.withdraw.preview', compact('pageTitle','withdraw'));
    }


    public function withdrawSubmit(Request $request)
    {
        $general    = GeneralSetting::first();
        $withdraw   = Withdrawal::with('method','seller')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id','desc')->firstOrFail();

        $rules      = [];
        $inputField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($withdraw->method->user_data as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg','jpeg','png']));
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        $this->validate($request, $rules);

        $seller = seller();

        if ($seller->ts) {
            $response       = verifyG2fa($seller,$request->authenticator_code);
            if (!$response) {
                $notify[]   = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }


        if ($withdraw->amount > $seller->balance) {
            $notify[] = ['error', 'Your request amount is larger then your current balance.'];
            return back()->withNotify($notify);
        }

        $directory  = date("Y")."/".date("m")."/".date("d");
        $path       = imagePath()['verify']['withdraw']['path'].'/'.$directory;
        $collection = collect($request);
        $reqField   = [];

        if ($withdraw->method->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($withdraw->method->user_data as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory.'/'.uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $request[$inKey]];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $withdraw['withdraw_information'] = $reqField;
        } else {
            $withdraw['withdraw_information'] = null;
        }


        $withdraw->status = 2;
        $withdraw->save();
        $seller->balance  -=  $withdraw->amount;
        $seller->save();


        $transaction                = new Transaction();
        $transaction->seller_id     = $withdraw->seller_id;
        $transaction->amount        = $withdraw->amount;
        $transaction->post_balance  = $seller->balance;
        $transaction->charge        = $withdraw->charge;
        $transaction->trx_type      = '-';
        $transaction->details       = showAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name;
        $transaction->trx           =  $withdraw->trx;
        $transaction->save();

        $adminNotification              = new AdminNotification();
        $adminNotification->seller_id   = $seller->id;
        $adminNotification->title       = 'New withdraw request from '.$seller->username;
        $adminNotification->click_url   = urlPath('admin.withdrawals.details',$withdraw->id);
        $adminNotification->save();

        notify($seller, 'WITHDRAW_REQUEST', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount),
            'amount' => showAmount($withdraw->amount),
            'charge' => showAmount($withdraw->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($withdraw->rate),
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($seller->balance),
            'delay' => $withdraw->method->delay
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        return redirect()->route('seller.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog()
    {
        $pageTitle = "Withdrawal History";
        $emptyMessage  = "No data found";
        $withdraws = Withdrawal::when(request()->search, function($q){
            return $q->where('trx',request()->search);
        })->where('seller_id', seller()->id)->where('status', '!=', 0)->with('method')->orderBy('id','desc')->paginate(getPaginate());
        return view('seller.withdraw.log', compact('pageTitle','withdraws','emptyMessage'));
    }

    public function trxLogs()
    {
        $pageTitle      = "Transaction Logs";
        $emptyMessage   = "No data found";
        $transactions   = Transaction::when(request()->search, function($q){
            return $q->where('trx',request()->search);
        })->where('seller_id', seller()->id)->with('seller')->orderBy('id','desc')->paginate(getPaginate());

        return view('seller.transactions', compact('pageTitle','transactions','emptyMessage'));
    }



    public function show2faForm()
    {
        $general    = GeneralSetting::first();
        $ga         = new GoogleAuthenticator();
        $seller     = seller();
        $secret     = $ga->createSecret();
        $qrCodeUrl  = $ga->getQRCodeGoogleUrl($seller->username . '@' . $general->sitename, $secret);
        $pageTitle  = 'Two Factor Security';
        return view('seller.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $seller = seller();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);

        $response = verifyG2fa($seller,$request->code,$request->key);
        if ($response) {
            $seller->tsc = $request->key;
            $seller->ts = 1;
            $seller->save();
            $sellerAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($seller, '2FA_ENABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$sellerAgent['ip'],
                'time' => @$sellerAgent['time']
            ]);
            $notify[] = ['success', 'Google authenticator enabled successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $seller       = seller();
        $response   = verifyG2fa($seller,$request->code);

        if ($response) {
            $seller->tsc = null;
            $seller->ts = 0;
            $seller->save();
            $sellerAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($seller, '2FA_DISABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$sellerAgent['ip'],
                'time' => @$sellerAgent['time']
            ]);
            $notify[] = ['success', 'Two factor authenticator disable successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

}


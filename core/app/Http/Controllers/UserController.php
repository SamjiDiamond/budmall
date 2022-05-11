<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\GeneralSetting;
use App\Models\Userwallet;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\FileTypeValidate;
use App\Lib\GoogleAuthenticator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function home()
    {
        $pageTitle  = 'Dashboard';
        $user       = Auth::user();
        $orders     = $user->orders()->whereIn('payment_status', [1,2])->get();

        $wallet = Userwallet::whereWcode($user->wcode)->first();
        if(!$wallet)
        {
        $wcode = getTrx();
        $newwallet = new UserWallet;
        $newwallet->wcode  = $wcode;
        $newwallet->balance  = 0;
        $newwallet->save();

        $user->wcode = $wcode;
        $user->save();
        }
        $wallet = Userwallet::whereWcode($user->wcode)->first();
        
        
        
         if($user->ref_code == null)
        {
            $uname = substr($user->username,0,4);
            $refname = strtoupper($uname);
            $refcode = $refname.rand(1234,9999);
            //return $refcode;
             
            $user->ref_code = $refcode;
            $user->save();
        }
        $rrwallet = Userwallet::whereWcode($user->ref_code)->first();
        if(!$rrwallet)
        {
        $newwallet = new UserWallet;
        $newwallet->wcode  = $user->ref_code;
        $newwallet->balance  = 0;
        $newwallet->save();

        }
        $rwallet = Userwallet::whereWcode($user->ref_code)->first();

        return view($this->activeTemplate . 'user.dashboard', compact('rwallet','wallet','pageTitle',  'orders'));
    }
    
    

    public function referral()
    {
        $pageTitle = "Referral Setting";
        $user = Auth::user();
         if($user->ref_code == null)
        {
            $uname = substr($user->username,0,4);
            $refname = strtoupper($uname);
            $refcode = $refname.rand(1234,9999);
            //return $refcode;
             
            $user->ref_code = $refcode;
            $user->save();
        }
        $wallet = Userwallet::whereWcode($user->ref_code)->first();
        if(!$wallet)
        {
        $newwallet = new UserWallet;
        $newwallet->wcode  = $user->ref_code;
        $newwallet->balance  = 0;
        $newwallet->save();

        }
        $wallet = Userwallet::whereWcode($user->ref_code)->first();
        $ref = User::whereRef($user->ref_code)->get();
        $earning = Transaction::whereRef(1)->whereUserId($user->id)->latest()->get();
        $sum = Transaction::whereRef(1)->whereUserId($user->id)->sum('usd_amount');
        
        return view($this->activeTemplate. 'user.referral', compact('sum','wallet','earning','pageTitle','user','ref'));
    }
    
     public function withdrawreferral()
    {
        $user = Auth::user();
        $wallet = Userwallet::whereWcode($user->ref_code)->first();
        if(!$wallet)
        {
        $notify[] = ['error', 'Invalid wallet.'];
        return back()->withNotify($notify);
        }
        
        if($wallet->balance < 1)
        {
        $notify[] = ['error', 'Insufficient balance. You need to have earned up to 1USD before your can withdraw from wallet.'];
        return back()->withNotify($notify);
        }
        
        $bwallet = Userwallet::whereWcode($user->wcode)->first();
         if(!$bwallet)
        {
        $notify[] = ['error', 'Invalid remittance wallet.'];
        return back()->withNotify($notify);
        }
        
        $bwallet->balance += $bwallet->balance;
        $bwallet->save();
        
        $wallet->balance = 0;
        $wallet->save();
        $notify[] = ['success', 'Fund remitted successfully.'];
        return back()->withNotify($notify);
         
    }




    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = Auth::user();
        return view($this->activeTemplate. 'user.profile_setting', compact('pageTitle','user'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|max:40',
            'city' => 'sometimes|required|max:50',
            'image' => ['image',new FileTypeValidate(['jpg','jpeg','png'])]
        ],[
            'firstname.required'=>'First name field is required',
            'lastname.required'=>'Last name field is required'
        ]);

        $user = Auth::user();

        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$user->address->country,
            'city' => $request->city,
        ];


        if ($request->hasFile('image')) {
            $location = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $filename = uploadImage($request->image, $location, $size, $user->image);
            $in['image'] = $filename;
        }
        $user->fill($in)->save();
        $notify[] = ['success', 'Profile updated successfully.'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change password';
        return view($this->activeTemplate . 'user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required','confirmed',$password_validation]
        ]);


        try {
            $user = auth()->user();
            if (Hash::check($request->current_password, $user->password)) {
                $password = Hash::make($request->password);
                $user->password = $password;
                $user->save();
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

    /*
     * Deposit History
     */
    public function depositHistory()
    {
        $pageTitle = 'Payment History';
        $emptyMessage = 'No payment history found.';
        $user = auth()->user();
        $logs = Transaction::whereUserId($user->id)->orderBy('id','desc')->paginate(getPaginate());
        return view($this->activeTemplate.'user.payments', compact('pageTitle', 'emptyMessage', 'logs'));
    }


    public function show2faForm()
    {
        $general = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->sitename, $secret);
        $pageTitle = 'Two Factor';
        return view($this->activeTemplate.'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code,$request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_ENABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
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

        $user = auth()->user();
        $response = verifyG2fa($user,$request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_DISABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);
            $notify[] = ['success', 'Two factor authenticator disable successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }


    public function productsReview()
    {
        $perpage = 16;

        $data = auth()->user()->orders()
        ->where('status', 3)
        ->join('order_details', function($join){
            $join->on('orders.id', '=', 'order_details.order_id');
          })
        ->distinct('product_id')
        ->get(['order_number','product_id'])
        ->pluck('product_id');

        $products  =  Product::whereIn('id', $data)->with('userReview')->get();

        $pageTitle = 'Review Products';
        $emptyMessage = 'You didn\'t receive any of our product yet';

        return view(activeTemplate() . 'user.orders.products_for_review', compact('pageTitle', 'emptyMessage', 'products'));
    }

    public function addReview(Request $request)
    {
        $this->validate($request, [
            'pid'       => 'required|string',
            'review'    => 'required|string',
            'rating'    => 'required|numeric',
        ]);

        $product_id = $request->pid;
        $check_user = ProductReview::where('user_id', auth()->user()->id)->where('product_id', $product_id);
        if($check_user->count() == 0){
            $add_review = ProductReview::create([
                'user_id'       => auth()->user()->id,
                'product_id'    => $product_id,
                'review'        => $request->review,
                'rating'        => $request->rating,
            ]);
            if ($add_review) {
                $notify[] = ['success', 'Review Added'];
            } else {
                $notify[] = ['error', 'ERROR: Something went wrong!!'];
            }
        }else{
            $notify[] = ['error', 'You have already reviewd for this product'];
        }
        return redirect()->back()->withNotify($notify);
    }


}

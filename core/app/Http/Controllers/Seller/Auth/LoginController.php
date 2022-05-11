<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\SellerLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $username;

    public function __construct(){
        $this->activeTemplate = activeTemplate();

        $this->middleware('seller.guest')->except('logout');
        $this->username = $this->findUsername();
    }

    protected function guard()
    {
        return Auth::guard('seller');
    }


    public function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function showLoginForm()
    {
        $pageTitle = "Sign In";
        return view($this->activeTemplate . 'seller.auth.login', compact('pageTitle'));
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if(isset($request->captcha)){
            if(!captchaVerify($request->captcha, $request->captcha_secret)){
                $notify[] = ['error',"Invalid captcha"];
                return back()->withNotify($notify)->withInput();
            }
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }



        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);


        return $this->sendFailedLoginResponse($request);
    }


    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }


    protected function validateLogin(Request $request)
    {
        $customRecaptcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();

        $validation_rule = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        if ($customRecaptcha) {
            $validation_rule['captcha'] = 'required';
        }

        $request->validate($validation_rule);
    }

    public function username()
    {
        return $this->username;
    }

    public function authenticated(Request $request, $seller)
    {
        if ($seller->status == 0) {
            $this->guard()->logout();
            $notify[] = ['error','Your account has been deactivated.'];
            return redirect()->route('seller.login')->withNotify($notify);
        }

        $seller->save();

        $ip     = $_SERVER["REMOTE_ADDR"];
        $exist  = SellerLogin::where('seller_ip',$ip)->first();

        $sellerLogin = new SellerLogin();

        if ($exist) {
            $sellerLogin->longitude     = $exist->longitude;
            $sellerLogin->latitude      = $exist->latitude;
            $sellerLogin->city          = $exist->city;
            $sellerLogin->country_code  = $exist->country_code;
            $sellerLogin->country       = $exist->country;
        }else{
            $info                       = json_decode(json_encode(getIpInfo()), true);
            $sellerLogin->longitude     = @implode(',',$info['long']);
            $sellerLogin->latitude      = @implode(',',$info['lat']);
            $sellerLogin->city          = @implode(',',$info['city']);
            $sellerLogin->country_code  = @implode(',',$info['code']);
            $sellerLogin->country       = @implode(',', $info['country']);
        }

        $sellerAgent             = osBrowser();
        $sellerLogin->seller_id  = $seller->id;
        $sellerLogin->seller_ip  = $ip;
        $sellerLogin->browser    = @$sellerAgent['browser'];
        $sellerLogin->os         = @$sellerAgent['os_platform'];
        $sellerLogin->save();

        return redirect()->route('seller.home');
    }


    public function logout()
    {
        $this->guard()->logout();

        request()->session()->invalidate();

        $notify[] = ['success', 'You have been logged out.'];
        return redirect()->route('seller.login')->withNotify($notify);
    }
}

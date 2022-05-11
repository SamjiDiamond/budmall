<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Seller;
use App\Models\SellerLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    public function __construct()
    {
        $this->middleware('seller.guest');
        $this->middleware('regStatus')->except('registrationNotAllowed');

        $this->activeTemplate = activeTemplate();
    }

    protected function guard()
    {
        return Auth::guard('seller');
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Sign Up as Seller";
        $info = json_decode(json_encode(getIpInfo()), true);
        $mobile_code = @implode(',', $info['code']);
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view($this->activeTemplate . 'seller.auth.register', compact('pageTitle','mobile_code','countries'));
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        $exist = Seller::where('mobile',$request->mobile_code.$request->mobile)->first();

        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return back()->withNotify($notify)->withInput();
        }

        if (isset($request->captcha)) {
            if (!captchaVerify($request->captcha, $request->captcha_secret)) {
                $notify[] = ['error', "Invalid captcha"];
                return back()->withNotify($notify)->withInput();
            }
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }


    protected function validator(array $data)
    {
        $general = GeneralSetting::first();
        $password_validation = Password::min(6);
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        if ($general->agree) {
            $agree = 'required';
        }

        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',',array_column($countryData, 'dial_code'));
        $countries = implode(',',array_column($countryData, 'country'));

        $validate = Validator::make($data, [
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'required|string|email|max:90|unique:users',
            'mobile' => 'required|string|max:50|unique:users',
            'password' => ['required','confirmed',$password_validation],
            'username' => 'required|alpha_num|unique:users|min:6',
            'captcha' => 'sometimes|required',
            'mobile_code' => 'required|in:'.$mobileCodes,
            'country_code' => 'required|in:'.$countryCodes,
            'country' => 'required|in:'.$countries,
            'agree' => $agree
        ]);
        return $validate;
    }


    protected function create(array $data)
    {

        $general = GeneralSetting::first();

        //User Create
        $seller = new Seller();
        $seller->firstname    = isset($data['firstname']) ? $data['firstname'] : null;
        $seller->lastname     = isset($data['lastname']) ? $data['lastname'] : null;
        $seller->email        = strtolower(trim($data['email']));
        $seller->password     = Hash::make($data['password']);
        $seller->username     = trim($data['username']);
        $seller->country_code = $data['country_code'];
        $seller->mobile       = $data['mobile_code'].$data['mobile'];

        $seller->address = [
            'address'   => '',
            'state'     => '',
            'zip'       => '',
            'country'   => isset($data['country']) ? $data['country'] : null,
            'city'      => ''
        ];

        $seller->status = 1;
        $seller->ev = $general->ev ? 0 : 1;
        $seller->sv = $general->sv ? 0 : 1;
        $seller->save();

        $adminNotification = new AdminNotification();
        $adminNotification->seller_id = $seller->id;
        $adminNotification->title = 'New seller registered';
        $adminNotification->click_url = urlPath('admin.sellers.detail',$seller->id);
        // $adminNotification->save();

        //Login Log Create
        $ip = $_SERVER["REMOTE_ADDR"];
        $exist = SellerLogin::where('seller_ip',$ip)->first();
        $sellerLogin = new SellerLogin();

        //Check exist or not
        if ($exist) {
            $sellerLogin->longitude =  $exist->longitude;
            $sellerLogin->latitude =  $exist->latitude;
            $sellerLogin->city =  $exist->city;
            $sellerLogin->country_code = $exist->country_code;
            $sellerLogin->country =  $exist->country;
        }else{
            $info = json_decode(json_encode(getIpInfo()), true);
            $sellerLogin->longitude =  @implode(',',$info['long']);
            $sellerLogin->latitude =  @implode(',',$info['lat']);
            $sellerLogin->city =  @implode(',',$info['city']);
            $sellerLogin->country_code = @implode(',',$info['code']);
            $sellerLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $sellerLogin->seller_id = $seller->id;
        $sellerLogin->seller_ip =  $ip;

        $sellerLogin->browser = @$userAgent['browser'];
        $sellerLogin->os = @$userAgent['os_platform'];
        $sellerLogin->save();


        return $seller;
    }


    public function checkSeller(Request $request){
        $exist['data'] = null;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = Seller::where('email',$request->email)->first();
            $exist['type'] = 'email';
        }
        if ($request->mobile) {
            $exist['data'] = Seller::where('mobile',$request->mobile)->first();

            $exist['type'] = 'mobile';
        }
        if ($request->username) {
            $exist['data'] = Seller::where('username',$request->username)->first();
            $exist['type'] = 'username';
        }

        return response($exist);
    }

    public function registered()
    {
        return redirect()->route('seller.home');
    }

}

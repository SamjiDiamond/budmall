<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\CodeRequest;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('regStatus')->except('registrationNotAllowed');
    }


    public function countrys(){
        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'Fetched successfully',
            'data'=>$countryData
        ]);

    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $general = GeneralSetting::first();
        $password_validation = Password::min(6);
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
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
            'password' => ['required',$password_validation],
            'username' => 'required|alpha_num|unique:users|min:5',
            'mobile_code' => 'required|in:'.$mobileCodes,
            'country_code' => 'required|in:'.$countryCodes,
            'country' => 'required|in:'.$countries,
        ]);
        return $validate;
    }


    public function register(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }

        $exist = User::where('mobile',$request->mobile_code.$request->mobile)->first();
        if ($exist) {
            return response()->json([
                'code'=>409,
                'status'=>false,
                'message'=>'The mobile number already exists',
            ]);
        }


        $user = $this->create($request->all());

        $response['access_token'] =  $user->createToken('auth_token')->plainTextToken;
        $response['user'] = $user;
        $response['token_type'] = 'Bearer';
        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'Registration successful',
            'data'=>$response
        ]);

    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     */
    protected function create(array $data)
    {

        $general = GeneralSetting::first();

        //User Create
        $user = new User();
        $user->firstname = isset($data['firstname']) ? $data['firstname'] : null;
        $user->lastname = isset($data['lastname']) ? $data['lastname'] : null;
        $user->email = strtolower(trim($data['email']));
        $user->password = Hash::make($data['password']);
        $user->username = trim($data['username']);
        $user->country_code = $data['country_code'];
        $user->mobile = $data['mobile_code'].$data['mobile'];
        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => isset($data['country']) ? $data['country'] : null,
            'city' => ''
        ];
        $user->status = 1;
        $user->ev = 1;
        $user->sv = 1;
        $user->ts = 0;
        $user->tv = 1;
        $user->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail',$user->id);
        $adminNotification->save();


        //Login Log Create
        $ip = $_SERVER["REMOTE_ADDR"];
//        $exist = UserLogin::where('user_ip',$ip)->first();
//        $userLogin = new UserLogin();
//
//        //Check exist or not
//        if ($exist) {
//            $userLogin->longitude =  $exist->longitude;
//            $userLogin->latitude =  $exist->latitude;
//            $userLogin->city =  $exist->city;
//            $userLogin->country_code = $exist->country_code;
//            $userLogin->country =  $exist->country;
//        }else{
//            $info = json_decode(json_encode(getIpInfo()), true);
//            $userLogin->longitude =  @implode(',',$info['long']);
//            $userLogin->latitude =  @implode(',',$info['lat']);
//            $userLogin->city =  @implode(',',$info['city']);
//            $userLogin->country_code = @implode(',',$info['code']);
//            $userLogin->country =  @implode(',', $info['country']);
//        }
//
//        $userAgent = osBrowser();
//        $userLogin->user_id = $user->id;
//        $userLogin->user_ip =  $ip;
//
//        $userLogin->browser = @$userAgent['browser'];
//        $userLogin->os = @$userAgent['os_platform'];
//        $userLogin->save();


        return $user;
    }

    public function sendcode(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }

        $em = User::where("email", $input['email'])->first();

        if ($em) {
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>'Email already exist',
            ]);
        }

        $code = substr(rand(), 0, 4);

        CodeRequest::create([
            'mobile' => trim($input['email']),
            'code' => $code,
            'status' => 0,
            'type' => "register"
        ]);

//        Mail::to($input['emailphone'])->send(new EmailVerificationMail($code));

        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'Code sent successfully',
            'data' => $code
        ]);
    }

}

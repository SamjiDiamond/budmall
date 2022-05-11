<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EmailReset;
use App\Mail\EmailVerificationMail;
use App\Message;
use App\Models\CodeRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return JsonResponse
     */

    //Login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:200',
            'password' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Check your inputs and try again']);
        }

        $user = User::where('email', $request->username)->orWhere('phone', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Incorrect credentials']);
        }

        $token = $user->createToken("app")->plainTextToken;
        return response()->json(['success' => true, 'message' => 'Login successfully', 'token' => $token, 'data' => $user->makeHidden(["id"]), 'settings' => ['paystack_pubkey' => env('PAYSTACK_PUBLIC_KEY')]], 200);
    }

    //Registration
    public function register(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'code' => 'required|string|max:4',
            'phone' => 'nullable|numeric|min:11',
            'bvn' => 'required|string|min:11|max:11',
            'pin' => 'required|string|min:4',
            'password' => 'required|string|min:8',
        ]);

        $type = "register";

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Check your inputs and try again', 'errors' => $validator->errors()]);
        }

        if (isset($input['email'])) {
            $em = User::where("email", $input['email'])->first();

            if ($em) {
                return response()->json(['success' => false, 'message' => 'Email already exist']);
            }


            $reg = CodeRequest::where([['mobile', $request->email], ['status', 0], ['type', $type]])->latest()->first();

            if ($reg == null) {
                return response()->json(['status' => 0, 'message' => 'Kindly request for OTP']);
            }

            if ($reg->code != $request->code) {
                return response()->json(['status' => 0, 'message' => 'Verification code did not match']);
            }
        }

        if (isset($input['phone'])) {
            $ph = User::where("phone", $input['phone'])->first();

            if ($ph) {
                return response()->json(['success' => false, 'message' => 'Phone number already exist']);
            }

            $reg = CodeRequest::where([['mobile', $request->phone], ['status', 0], ['type', $type]])->latest()->first();

            if ($reg == null) {
                return response()->json(['success' => true, 'message' => 'Kindly request for OTP']);
            }

            if ($reg->code != $request->code) {
                return response()->json(['success' => true, 'message' => 'Verification code did not match']);
            }
        }

        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'bvn' => $request->bvn,
            'pin' => $request->pin,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'email_verified' => 1,
            'email_code' => $request->code,
        ]);

        return response()->json(['success' => true, 'message' => 'Your Registration is Successful']);
    }

    //Forgot Password
    public function reset_password_request(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'emailphone' => 'required|string|max:255',
            'type' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Check your inputs and try again']);
        }

        $user = User::Where('phone', $request['emailphone'])->orWhere('email', $request['emailphone'])->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Kindly provide registered email address or phone number']);
        }


        $code = substr(rand(), 0, 4);

        CodeRequest::create([
            'mobile' => trim($input['emailphone']),
            'code' => $code,
            'status' => 0,
            'type' => "recover"
        ]);

        if ($input['type'] == "email") {
            Mail::to($input['emailphone'])->send(new EmailReset($code));
        } else {
            $message = "Your " . env("APP_NAME") . " password reset code is " . $code . ". Valid for 1hour, One-time use only.";

            $this->send_smsroute9ja($input['emailphone'], $message);
        }

        return response()->json(['success' => true, 'message' => 'Code has been sent', 'data' => $code]);
    }

    //Password Reset
    public function reset_password_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emailphone' => 'required|string|max:255',
            'code' => 'required',
            'password' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Check your inputs and try again']);
        }

        $type = "recover";

        $reg = CodeRequest::where([['mobile', $request->emailphone], ['status', 0], ['type', $type]])->latest()->first();

        if ($reg == null) {
            return response()->json(['status' => 0, 'message' => 'Kindly request for OTP']);
        }

        if ($reg->code != $request->code) {
            return response()->json(['status' => 0, 'message' => 'Verification code did not match']);
        }

        User::where(['phone' => $request->emailphone])->orWhere(['email' => $request->emailphone])->update([
            'password' => Hash::make($request->password)
        ]);

        $reg->status = 1;
        $reg->save();

        return response()->json(['success' => true, 'message' => 'Password reset successfully.']);
    }

    public function sendcode(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'emailphone' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Check your inputs and try again', 'errors' => $validator->errors()]);
        }

        $type = $input['type'];

        if ($type == "email") {
            $em = User::where("email", $input['emailphone'])->first();

            if ($em) {
                return response()->json(['success' => false, 'message' => 'Email already exist']);
            }
        } else {
            $ph = User::where("phone", $input['emailphone'])->first();

            if ($ph) {
                return response()->json(['success' => false, 'message' => 'Phone number already exist']);
            }
        }

        $code = substr(rand(), 0, 4);

        CodeRequest::create([
            'mobile' => trim($input['emailphone']),
            'code' => $code,
            'status' => 0,
            'type' => "register"
        ]);


        if ($type == "email") {
            Mail::to($input['emailphone'])->send(new EmailVerificationMail($code));
        } else {
            $message = "Your " . env("APP_NAME") . " verification code is " . $code . ". Valid for 1hour, One-time use only.";

            $this->send_smsroute9ja($input['emailphone'], $message);
        }

        return response()->json(['success' => true, 'message' => 'Code sent successfully', 'data' => $code]);
    }

    public function biometric()
    {

        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }
        $user->tokens()->delete();
        $token = $user->createToken("app")->plainTextToken;
        return response()->json(['success' => true, 'message' => 'Login successfully', 'token' => $token, 'data' => $user->makeHidden(["id"]), 'settings' => ['paystack_pubkey' => env('PAYSTACK_PUBLIC_KEY')]]);
    }

    function send_smsroute9ja($number, $body)
    {
        $url = "https://smsroute9ja.com.ng/components/com_spc/smsapi.php?";
        $np = 'username=' . env("SMS9JA_USER") . '&password=' . env("SMS9JA_PASS") . '&sender=' . env("APP_NAME") . '&recipient=' . $number . '&message=' . urlencode($body);
        file_get_contents($url . $np);
    }

    public function updatepin(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'oldpin' => 'required',
            'newpin' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            $user = User::find(Auth::id());

            if ($user->pin != $input['oldpin']) {
                return response()->json(['success' => false, 'message' => 'Current pin did not match']);
            }

            $user->pin = $input['newpin'];
            $user->save();

            return response()->json(['success' => true, 'message' => 'Pin set successfully']);


        } else {
            return response()->json(['success' => false, 'message' => 'Incomplete request', 'error' => $validator->errors()]);
        }
    }

    public function updatepassword(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'oldpass' => 'required',
            'newpass' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            $user = User::find(Auth::id());

            if (!Hash::check($input['oldpass'], $user->password)) {
                return response()->json(['success' => false, 'message' => 'Current password did not match']);
            }

            $user->password = Hash::make($input['newpass']);
            $user->save();

            return response()->json(['success' => true, 'message' => 'Password changed successfully']);


        } else {
            return response()->json(['success' => false, 'message' => 'Incomplete request', 'error' => $validator->errors()]);
        }
    }
}

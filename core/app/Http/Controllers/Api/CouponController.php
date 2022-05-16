<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Coupon;
use App\Models\GeneralSetting;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'          => 'required|string',
            'subtotal'      => 'required|numeric|gt:0'
        ]);

        if($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }

        $general = GeneralSetting::first();

        $now = Carbon::now();

       // $coupon = Coupon::where('coupon_code', $request->code)->with('categories')->where('start_date', '<=', $now)->where('end_date', '>=', $now)->where('status', 1)->with(['appliedCoupons', 'categories', 'products'])->first();
       $coupon = Coupon::where('coupon_code', $request->code)->with('categories')->where('status', 1)->first();

        if($coupon){

            // Check Expiry

             if($now < $coupon->start_date)
             {
                 return response()->json([
                     'code'=>200,
                     'status'=>false,
                     'message'=>'Sorry, coupon is not available for use yet',
                 ]);
             }

             if($now > $coupon->end_date)
             {
                 return response()->json([
                     'code'=>200,
                     'status'=>false,
                     'message'=>'Sorry, this coupon has expired',
                 ]);
             }



            // Check Minimum Subtotal
            if($request->subtotal < $coupon->minimum_spend)
            {
                return response()->json([
                    'code'=>200,
                    'status'=>false,
                    'message'=>"Sorry your have to order minimum amount of $coupon->minimum_spend $general->cur_text",
                ]);
            }

            // Check Maximum Subtotal
            if($coupon->maximum_spend !=null && $request->subtotal > $coupon->maximum_spend){
                return response()->json([
                    'code'=>200,
                    'status'=>false,
                    'message'=>"Sorry your have to order maximum amount of $coupon->maximum_spend $general->cur_text",
                ]);
            }

            //Check Limit Per Coupon
            if($coupon->appliedCoupons->count() >= $coupon->usage_limit_per_coupon){
                return response()->json([
                    'code'=>200,
                    'status'=>false,
                    'message'=>"Sorry your Coupon has exceeded the maximum Limit For Usage",
                ]);
            }

            //Check Limit Per User
            if($coupon->appliedCoupons->where('user_id', auth()->id())->count() >= $coupon->usage_limit_per_user){
                return response()->json([
                    'code'=>200,
                    'status'=>false,
                    'message'=>"Sorry you have already reached the maximum usage limit for this coupon",
                ]);
            }

           /* $coupon_categories  = $coupon->categories->pluck('id')->toArray();
            $coupon_products    = $coupon->products->pluck('id')->toArray();

            //Check all of the products in cart with coupon's products
            if(empty(array_intersect($coupon_products, $request->products))){
                //Check all of the products in cart with coupon's products
                foreach($request->categories as $cateogires){
                    if(empty(array_intersect($cateogires, $coupon_categories))){
                        return response()->json(['error' => 'The coupon is not available for some products on your cart.']);
                    }
                }
            }*/


            if($coupon->discount_type == 1){
                $amount = $coupon->coupon_amount;
            }else{
                $amount = $request->subtotal * $coupon->coupon_amount / 100;
            }

            // Check in session

            if(session()->has('coupon') && session('coupon')['code'] == $request->code){
                return response()->json([
                    'code'=>200,
                    'status'=>false,
                    'message'=>'The coupon has already been applied',
                ]);
            }

            return response()->json([
                'code'=>200,
                'status'=>true,
                'message'=>'Coupon validated successfully',
                'data'=>$amount
            ]);
        }else{
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>'Invalid Coupon Code.',
            ]);
        }
    }
}

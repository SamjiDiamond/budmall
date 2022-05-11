<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignProductAttribute;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function dashboard(){

        $datas['topSellingProducts'] = Product::topSales(9);
        $datas['featuredProducts']   = Product::active()->featured()->where('status', 1)->inRandomOrder()->take(6)->get();
        $datas['latestProducts']     = Product::active()->latest()->where('status', 1)->inRandomOrder()->take(12)->get();
        $datas['featuredSeller']     = Seller::active()->featured()->whereHas('shop')->with('shop')->inRandomOrder()->take(16)->get();
        $datas['topBrands']          = Brand::top()->inRandomOrder()->take(16)->get();
        $datas['pageTitle']          = 'Store Front';
        $datas['catego'] = Category::latest()->where('id','!=',27)->get();
        $datas['superpack'] = Category::latest()->whereId(27)->get();

        $datas['offers'] = Offer::where('status', 1)->where('end_date', '>', now())
            ->with(['products'=> function($q){ return $q->whereHas('categories')->whereHas('brand');},
                'products.reviews'
            ])->get();

        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'Fetched Successfully',
            'data'=>$datas
        ]);

    }

    public function cart(){

        $datas=Cart::where("user_id", Auth::id())->get();

        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'Fetched Successfully',
            'data'=>$datas
        ]);
    }

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity'  => 'required|numeric|gt:0'
        ]);


        if($validator->fails())
        {
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }


        $product = Product::findOrFail($request->product_id);
        $user_id = auth()->user()->id;


        $attributes= AssignProductAttribute::where('product_id', $request->product_id)->distinct('product_attribute_id')->with('productAttribute')->get(['product_attribute_id']);

        //return $attributes;
        if ($attributes->count() > 0) {
            $count = $attributes->count();
            $validator = Validator::make($request->all(), [
                'attributes' => "required|array|min:$count"
            ],[
                'attributes.required' => 'Product variants must be selected',
                'attributes.min' => 'All product variants must be selected'
            ]);
        }

        if($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }

        $selected_attr = [];


        $s_id = session()->get('session_id');

        if ($s_id == null) {
            session()->put('session_id', uniqid());
            $s_id = session()->get('session_id');
        }

        $selected_attr = $request['attributes']??null;

        if($selected_attr != null){
            sort($selected_attr);
            $selected_attr = (json_encode($selected_attr));
        }

        if($user_id != null){
            $cart = Cart::where('user_id', $user_id)->where('product_id', $request->product_id)->where('attributes', $selected_attr)->first();
        }else{
            $cart = Cart::where('session_id', $s_id)->where('product_id', $request->product_id)->where('attributes', $selected_attr)->first();
        }

        //Check Stock Status
        if($product->in_stock < 1)
        {
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>'Product Is Not In Stock',
            ]);
        }

        if($cart) {
            $cart->quantity  += $request->quantity;
            if(isset($stock_qty) && $cart->quantity > $stock_qty){
                return response()->json([
                    'code'=>200,
                    'status'=>false,
                    'message'=>'Sorry, You have already added maximum amount of stock',
                ]);
            }

            $cart->save();
        }else {
            $cart = new Cart();
            $cart->user_id    = auth()->user()->id??null;
            $cart->session_id = $s_id;
            $cart->attributes = json_decode($selected_attr);
            $cart->product_id = $request->product_id;
            $cart->quantity   = $request->quantity;
            $cart->save();
        }
        //return ;

        return response()->json([
            'code'=>200,
            'status'=>false,
            'message'=>'Added to Cart',
        ]);
    }


}

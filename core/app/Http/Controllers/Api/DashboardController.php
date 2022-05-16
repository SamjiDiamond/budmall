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
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function categorys(){

        $datas = Category::latest()->get();

        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'Fetched Successfully',
            'data'=>$datas
        ]);
    }

    public function productDetails($id, $order_id =null)
    {
        $product = Product::where('id', $id)->where('status', 1)->firstOrFail();

        $review_available = false;

        if($order_id){
            $order = Order::where('order_number', $order_id)->where('user_id', auth()->id())->first();
            if($order){
                $od = OrderDetail::where('order_id', $order->id)->where('product_id', $id)->first();
                if($od){
                    $review_available = true;
                }
            }
        }

        $images = $product->productPreviewImages;

        if($images->count() == 0){
            $images = $product->productVariantImages;
        }
        if(optional($product->offer)->activeOffer){
            $discount = calculateDiscount($product->offer->activeOffer->amount, $product->offer->activeOffer->discount_type, $product->base_price);
        }else $discount = 0;

        $rProducts = $product->categories()->with(
            [
                'products' => function($q){
                    return $q->whereHas('categories')->whereHas('brand');
                },
                'products.reviews' ,'products.offer', 'products.offer.activeOffer'
            ]
        )
            ->get()->map(function($item) use($id){
                return $item->products->where('id', '!=', $id)->take(5);
            });

        $related_products = [];

        foreach ($rProducts as $childArray){
            foreach ($childArray as $value){
                $related_products[] = $value;
            }
        }

        $attributes     = AssignProductAttribute::where('status',1)->with('productAttribute')->where('product_id', $id)->distinct('product_attribute_id')->get(['product_attribute_id']);

        $imageData      = imagePath()['product'];
        $seoContents    = getSeoContents($product, $imageData, 'main_image');



        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'Fetched Successfully',
            'data'=>compact('product', 'review_available', 'related_products', 'discount', 'attributes', 'images', 'seoContents')
        ]);

    }

    public function createPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|integer|min:4',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }

        $user=User::find(Auth::id());

        $user->pin=Hash::make($request->pin);
        $user->save();

        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'Pin created successfully',
        ]);
    }

    public function validatePin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|integer|min:4',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>$validator->errors()->all(),
            ]);
        }

        if(!Hash::check($request->pin, Auth::user()->pin)){
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>'Invalid Pin',
            ]);
        }

        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'Pin matched successfully',
        ]);
    }


}

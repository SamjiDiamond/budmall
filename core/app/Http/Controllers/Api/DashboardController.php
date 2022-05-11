<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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


}

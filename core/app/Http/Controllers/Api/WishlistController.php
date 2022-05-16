<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    //WISH LIST
    public function addToWishList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',

        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user_id = auth()->user()->id??null;

        $s_id = session()->get('session_id');
        if ($s_id == null) {
            session()->put('session_id', uniqid());
            $s_id = session()->get('session_id');
        }

        if($user_id != null){
            $wishlist = Wishlist::where('user_id', $user_id)
            ->where('product_id', $request->product_id)
            ->first();
        }else{

            $wishlist = Wishlist::where('session_id', $s_id)
            ->where('product_id', $request->product_id)
            ->first();
        }

        if($wishlist) {
            return response()->json([
                'code'=>200,
                'status'=>false,
                'message'=>'Already in the wish list',
            ]);
        }else {
            $wishlist = new Wishlist();
            $wishlist->user_id    = auth()->user()->id??null;
            $wishlist->session_id = $s_id;
            $wishlist->product_id = $request->product_id;
            $wishlist->save();
        }
        $wishlist = session()->get('wishlist');

        $wishlist[$request->product_id] = [
            "id" => $request->product_id,
        ];

        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'Added to Wishlist',
        ]);

    }


    public function getWsihList()
    {
        $w_data     = [];
        $user_id    = auth()->user()->id??null;
        if($user_id != null){
            $w_data = Wishlist::where('user_id', $user_id)
            ->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->orderBy('id', 'desc')
            ->get();

        }else{
            $s_id       = session()->get('session_id');
            $w_data     = Wishlist::where('session_id', $s_id)
            ->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->orderBy('id', 'desc')
            ->get();

        }

        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>"Fetched successfully",
            'data' => $w_data
        ]);

    }

    public function wishList()
    {

        $user_id    = auth()->user()->id??null;
        $notify[] = [];

        if($user_id != null){
           /* $wishlist_data = Wishlist::where('user_id', $user_id)
             ->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->get();*/
             $wishlist_data = Wishlist::where('user_id', $user_id)

            ->get();
        }else{
            $s_id       = session()->get('session_id');
            if(!$s_id){
                return redirect(route('home'))->withNotify($notify);
            }
           /* $wishlist_data = Wishlist::where('session_id', $s_id)
             ->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->get();*/
            $wishlist_data = Wishlist::where('session_id', $s_id)

            ->get();
        }

        $pageTitle     = 'Wishlist';
        $emptyMessage  = 'No product in your wishlist';
        return view(activeTemplate() . 'wishlist', compact('pageTitle', 'wishlist_data', 'emptyMessage'));
    }

    public function removeFromwishList($id)
    {
        if($id==0){
            $user_id    = auth()->user()->id??null;
            if($user_id != null){
                $wishlist = Wishlist::where('user_id', $user_id);
            }else{
                $s_id       = session()->get('session_id');
                if(!$s_id){
                    abort(404);
                }
                $wishlist = Wishlist::where('session_id', $s_id);
            }

        }else{
            $wishlist   = Wishlist::find($id);

            if(!$wishlist){
                return response()->json([
                    'code'=>200,
                    'status'=>false,
                    'message'=>'This product isn\'t available in your Wishlist',
                ]);
            }

            $product_id = $wishlist->product_id;
            $wl         = session()->get('wishlist');
            unset($wl[$product_id]);
            session()->put('wishlist', $wl);
        }

        if($wishlist) {
            $wishlist->delete();
            return response()->json([
                'code'=>200,
                'status'=>true,
                'message'=>'Product removed From Wishlist',
            ]);
        }

        return response()->json([
            'code'=>200,
            'status'=>false,
            'message'=>'This product isn\'t available in your Wishlist',
        ]);
    }
}

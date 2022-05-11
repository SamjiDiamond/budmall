<?php

namespace App\Http\Controllers;
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
            return response()->json(['error' => 'Already in the wish list']);
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

        session()->put('wishlist', $wishlist);
        return response()->json(['success' => 'Added to Wishlist']);
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
            if($w_data->count() >3){
                $latest = $w_data->sortByDesc('id')->take(3);
            }else{
                $latest = $w_data;
            }

        }else{
            $s_id       = session()->get('session_id');
            $w_data     = Wishlist::where('session_id', $s_id)
            ->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->orderBy('id', 'desc')
            ->get();

            if($w_data->count() >3){
                $latest = $w_data->sortByDesc('id')->take(5);
            }else{
                $latest = $w_data;
            }
        }
        $more = $w_data->count() - count($latest);
        $emptyMessage = 'No product in your wishlist';

        return view(activeTemplate() . 'partials.wishlist_items', ['data' => $latest, 'emptyMessage'=> $emptyMessage, 'more'=>$more]);
    }

    public function getWsihListTotal(){
        $w_data     = [];
        $user_id    = auth()->user()->id??null;
        if($user_id != null){
           /* $w_data = Wishlist::where('user_id', $user_id)
            ->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->get();*/
              $w_data = Wishlist::where('user_id', $user_id)

            ->get();

        }else{
            $s_id       = session()->get('session_id');
          /*  $w_data     = Wishlist::where('session_id', $s_id)
            ->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->get();*/
             $w_data     = Wishlist::where('session_id', $s_id)

            ->get();
        }

        return response($w_data->count());
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
            $wishlist   = Wishlist::findorFail($id);
            $product_id = $wishlist->product_id;
            $wl         = session()->get('wishlist');
            unset($wl[$product_id]);
            session()->put('wishlist', $wl);
        }
        Artisan::call('cache:clear');
        if($wishlist) {
            $wishlist->delete();
            return response()->json(['success' => 'Deleted From Wishlist']);
        }

        return response()->json(['error' => 'This product isn\'t available in your wishlsit']);
    }
}

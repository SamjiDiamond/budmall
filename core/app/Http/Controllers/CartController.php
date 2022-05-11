<?php

namespace App\Http\Controllers;

use App\Models\AssignProductAttribute;
use App\Models\Cart;
use App\Models\Deliverysetup;
use App\Models\GatewayCurrency;
use App\Models\ShippingMethod;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\OrderDetail;
use App\Models\ProductStock;
use App\Models\GeneralSetting;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity'  => 'required|numeric|gt:0'
        ]);


        if($validator->fails()) 
        {
            return response()->json($validator->errors());
        }
        session()->forget('coupon');

        $product = Product::findOrFail($request->product_id);
        $user_id = auth()->user()->id??null;
        

        $attributes     = AssignProductAttribute::where('product_id', $request->product_id)->distinct('product_attribute_id')->with('productAttribute')->get(['product_attribute_id']);

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
            return response()->json($validator->errors());
        }

        $selected_attr = [];
        $user_id = auth()->user()->id??null;
        
        /*if($user_id == null)
        {
            return response()->json(['error' => 'Please  Login To Add Product To Cart']);
        }*/

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
            
                return response()->json(['error' => 'Product Is Not In Stock']);
            
        }

        if($cart) {
            $cart->quantity  += $request->quantity;
            if(isset($stock_qty) && $cart->quantity > $stock_qty){
                return response()->json(['error' => 'Sorry, You have already added maximum amount of stock']);
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

        return response()->json(['success' => 'Added to Cart']);

    }

    public function getCart()
    {
        $subtotal = 0;
        $user_id    = auth()->user()->id??null;

        if($user_id != null){
           /* $total_cart = Cart::where('user_id', $user_id)
            ->with(['product','product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->orderBy('id', 'desc')
            ->get();*/

            $s_id       = session()->get('session_id');
            $total_cart = Cart::where('user_id', $user_id)
            ->Orwhere('session_id', $s_id)

            ->orderBy('id', 'desc')
            ->get();

            if($total_cart->count() > 3){
                $latest = $total_cart->sortByDesc('id')->take(3);
            }else{
                $latest = $total_cart;
            }

        }else{
            $s_id       = session()->get('session_id');
            $total_cart = Cart::where('session_id', $s_id)

            ->orderBy('id', 'desc')
            ->get();

            if($total_cart->count() >3){
                $latest = $total_cart->sortByDesc('id')->take(3);
            }else{
                $latest = $total_cart;
            }
        }

        if($total_cart->count() > 0){

            foreach($total_cart as $tc){

                if($tc->attributes != null){
                    $s_price = AssignProductAttribute::priceAfterAttribute($tc->product, $tc->attributes);
                }else{
                    if($tc->product->offer && $tc->product->offer->activeOffer){
                        $s_price = $tc->product->base_price - calculateDiscount($tc->product->offer->activeOffer->amount, $tc->product->offer->activeOffer->discount_type, $tc->product->base_price);
                    }else{
                        $s_price = $tc->product->base_price;
                    }
                }
                $subtotal += $s_price * $tc->quantity;
            }
        }

        $more           = $total_cart->count() - count($latest);
        $emptyMessage  = 'No product in your cart';
        $coupon         = null;

        if(session()->has('coupon')){
           $coupon = session('coupon');
        }

        return view(activeTemplate() . 'partials.cart_items', ['data' => $latest, 'subtotal' => $subtotal, 'emptyMessage'=>$emptyMessage, 'more'=>$more, 'coupon'=>$coupon]);
    }

    public function getCartTotal()
    {
        $subtotal = 0;
        $user_id    = auth()->user()->id??null;
        if($user_id != null){
           /* $total_cart = Cart::where('user_id', $user_id)
            ->with(['product','product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->get();*/
            $total_cart = Cart::where('user_id', $user_id)

            ->get();

        }else{
            $s_id       = session()->get('session_id');
           /* $total_cart = Cart::where('session_id', $s_id)
            ->with(['product','product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->get();*/
            $total_cart = Cart::where('session_id', $s_id)

            ->get();
        }
        return $total_cart->count();
    }

    public function shoppingCart()
    {
        $user_id    = auth()->user()->id??null;
        if($user_id != null){
          /*  $data = Cart::where('user_id', $user_id)->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->orderBy('id', 'desc')
            ->get();*/

             $data = Cart::where('user_id', $user_id)
            ->get();


        }else{
            $s_id       = session()->get('session_id');
            /*$data = Cart::where('session_id', $s_id)
            ->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->orderBy('id', 'desc')
            ->get();*/
            $data = Cart::where('session_id', $s_id)

            ->get();
        }
        $pageTitle     = 'My Cart';
        
        session()->forget('order_number');
        session()->forget('Track');
        session()->forget('delicalc');
        
        $emptyMessage  = 'Your Shopping Cart Is Empty. Try checking our range of products to add product to cart';
        return view(activeTemplate() . 'cart', compact('pageTitle', 'data', 'emptyMessage'));
    }

    public function updateCartItem(Request $request, $id)
    {
        if(session()->has('coupon')){
            return response()->json(['error' => 'You have applied a coupon on your cart. If you want to delete any item form your cart please remove the coupon first.']);
        }

        $cart_item = Cart::findorFail($id);

        $attributes = $cart_item->attributes??null;
        if($attributes !==null){
            sort($attributes);
            $attributes = json_encode($attributes);
        }
        if($cart_item->product->show_in_frontend && $cart_item->product->track_inventory){
            $stock_qty  = ProductStock::showAvailableStock($cart_item->product_id, $attributes);

            //if($request->quantity > $stock_qty){
              //  return response()->json(['error' => 'Sorry! your requested amount of quantity is not available in our stock', 'qty'=>$stock_qty]);
            //}
        }

        if($request->quantity == 0){
            return response()->json(['error' => 'Quantity must be greater than  0']);
        }
        $cart_item->quantity = $request->quantity;
        $cart_item->save();
        return response()->json(['success' => 'Quantity updated']);
    }

    public function removeCartItem($id){

        if(session()->has('coupon')){
            return response()->json(['error' => 'You have applied a coupon on your cart. If you want to delete any item form your cart please remove the coupon first.']);
        }

        $cart_item = Cart::findorFail($id);
        $cart_item->delete();
        return response()->json(['success' => 'Cart Item Deleted Successfully']);
    }

    public function checkout()
    {
        $user_id    = auth()->user()->id ?? null;

        $general = GeneralSetting::first();
        
         $user_id = auth()->user()->id??null;
        
        if($user_id == null)
        {
        $notify[]=['error', 'Please login to account before proceeding to checkout'];
        return redirect()->back()->withNotify($notify);
        }


        if($user_id){
            $data = Cart::where('user_id', $user_id)->get();
        }else{
            $data = Cart::where('session_id', session('session_id'))->get();
        }
        if($data->count() == 0){
            $notify[] = ['success', 'No product in your cart'];
            return back()->withNotify($notify);
        }
        $pay = 0;
        foreach($data as $prod)
        {
        $amount = Product::where('id', $prod->product_id)->first()->base_price;
        $cost = $amount*$prod->quantity;
        $pay += $cost;
        }
        $subtotal = $pay;
        //return $subtotal;

        $gateway = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();


        $state = State::OrderBy('state','ASC')->get();
        $shipping_methods = ShippingMethod::where('status', 1)->get();
        $pageTitle = 'Checkout';
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        
         return view(activeTemplate() . 'checkout', compact('subtotal','gateway','state','pageTitle', 'shipping_methods', 'countries'));
    }

      public function calculatedelivery(Request $request)
    {
        $general = GeneralSetting::first();

        $request->validate([
            'shipping_method'   => 'required|integer',
            'firstname'         => 'required|max:50',
            'lastname'          => 'required|max:50',
            'mobile'            => 'required|max:50',
            'email'             => 'required|max:90',
            'location'          => 'required',
            'state'             => 'required|max:50'
        ]);


        $general = GeneralSetting::first();
        $address = $request->location;

        $apiKey  = $general->mapkey;
        $address = urlencode( $address );
        $url     = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&key=".$apiKey;
        $resp    = json_decode( file_get_contents( $url ), true );
        // Latitude and Longitude (PHP 7 syntax)
        $latitudeTo    = $resp['results'][0]['geometry']['location']['lat'] ?? '';
        $longitudeTo   = $resp['results'][0]['geometry']['location']['lng'] ?? '';


        $status = $resp['status'];
        if(!isset($status))
        {
        $notify[]=['error', 'Sorry, Destination Address cannot be determined at the moment. Please try again later'];
        return redirect()->back()->withNotify($notify);
        }

        if($status != "OK")
        {
        $notify[]=['error', 'Sorry, Destination Address cannot be determined at the moment. Please try again later'];
        return redirect()->back()->withNotify($notify);
        }

        $state = State::whereId($request->state)->first();

        if(!$state)
        {
        $notify[]=['error', 'Sorry, Destination State cannot be determined at the moment. Please try again later'];
        return redirect()->back()->withNotify($notify);
        }

        $latitudeFrom = $state->lati;
        $longitudeFrom = $state->longi;

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$latitudeFrom.",".$longitudeFrom."&destinations=".$latitudeTo.",".$longitudeTo."&mode=driving&language=en-EN&departure_time=now&key=AIzaSyAGrHdhUTvfj1Fyl9Dx7_e7RstThaE1uHo";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $km = $response_a['rows'][0]['elements'][0]['distance']['text'];
	    $meters = $response_a['rows'][0]['elements'][0]['distance']['value'];
        $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
	    $value = $response_a['rows'][0]['elements'][0]['duration']['value'];

        //return array('distance KM' => $km,'distance M' => $meters, 'time' => $time, 'value' => $value);

        //Calculate distance from latitude and longitude
        //$theta = $longitudeFrom - $longitudeTo;
        //$dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        //$dist = acos($dist);
        //$dist = rad2deg($dist);
        //$miles = $dist * 60 * 1.1515;

        //$distance = ($miles * 1.609344);

        $distance = $meters/1000;
        //return $distance;


        $carts_data = Cart::where('session_id', session('session_id'))->orWhere('user_id', auth()->user()->id??null)->get();

        $rate = $general->usdrate;

        $distance_amount = 0;
        foreach ($carts_data as $cart)
        {

            $product = Product::whereId($cart->product_id)->first();
            $tier = $product->delivery_category;

            $get = json_decode($tier,true);

            if($get)
            {
            $ppk = 0;
            foreach($get as $dat)
             {
            $set = Deliverysetup::whereId($dat)->first();
            $dppk = $set->ppk ?? 60;
            $ppk += $dppk;
             }

            }
            else
            {
            $ppk = 10;
            }

            //$sum = $cart->id;
            $sum = ($ppk * $distance) * $cart->quantity;
            $distance_amount += $sum;


        }
        //return $distance_amount;
        if(!$distance_amount)
        {
        $notify[]=['error', 'Sorry,Delivery amount cannot be determined at the moment. Please try again later'];
        return redirect()->back()->withNotify($notify);
        }
         if($distance_amount < 500)
        {
        $distance_amount = 500;    
        }
        if($distance_amount > 5000)
        {
        $distance_amount = 5000;    
        }
        
        //return $distance;
        $mobile = $request->mobile;
        session()->put('bfname', $request->firstname);
        session()->put('blname', $request->lastname);
        session()->put('bphone', $mobile);
        session()->put('bemail', $request->email);
        session()->put('bcity',  $request->location);
        session()->put('bstate', $request->state);
        session()->put('bstaten', $state->state);
        session()->put('delicalc', $distance_amount);
        session()->put('shipmeth', $request->shipping_method);
        return redirect()->back();

    }

}

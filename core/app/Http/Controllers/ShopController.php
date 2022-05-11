<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\OrderDetail;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\AssignProductAttribute;
use App\Models\Seller;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function __construct(){
        $this->activeTemplate = activeTemplate();
    }

    public function categories()
    {
        $data['all_categories'] = Category::latest()->paginate(20);
        $data['pageTitle']      = 'Categories';
        $data['emptyMessage']   = 'No Category Found';

        return view($this->activeTemplate.'categories', $data);
    }

    public function brands()
    {
        $data['brands']         = Brand::latest()->paginate(30);
        $data['pageTitle']     = 'Brands';
        $data['emptyMessage']  = 'No Brand Found';

        return view($this->activeTemplate.'brands', $data);
    }

    public function products(Request $request)
    {
        $brands       = Brand::latest()->get();
        $categories   = Category::where('parent_id', null)->latest()->get();
        $pageTitle    = 'Products';
        $brand        = $request->brand?$request->brand:['0'];
        $category_id  = $request->category_id??0;
        $min          = $request->min;
        $max          = $request->max;

        $perpage = getPaginate(15);

        if(request()->perpage){
            $perpage = request()->perpage;
        }
        

        if($category_id !=0){
            $all_products       = Product::whereCategory($category_id)->Orwhere('subcategory',$category_id)->where('status', 1)
                                    ->latest()
                                    ->get();;
        }else{
            $all_products       = Product::where('status', 1)
                                    ->where('base_price','!=',0)
                                    ->latest()
                                    ->get();
        }

        //$min_price              = $all_products->min('base_price')??0;
        //$max_price              = $all_products->max('base_price')??0;

        $min_price              = 1;
        $max_price              = 99999999999999;

        if (in_array("0", $brand)){
            $productCollection  = $all_products;
        }else{
            $productCollection  = $all_products->whereIn('brand.id', $brand);
        }

        if($min && $max){
            $productCollection = $productCollection->where('base_price', '>=', $min)->where('base_price', '<=', $max);
        }elseif($min){
            $productCollection = $productCollection->where('base_price', '>=', $min);
        }elseif($max){
            $productCollection = $productCollection->where('base_price', '<=', $max);
        }
        $products           =  paginate($productCollection, $perpage);


        if($request->ajax()){
            $view = 'partials.products_filter';
        }else{
            $view = 'products';
        }

        $emptyMessage ="Sorry! No Product Found.";

        return view($this->activeTemplate . $view, compact('products', 'perpage', 'brand', 'min_price', 'max_price', 'pageTitle' ,'brands','min', 'max', 'category_id', 'emptyMessage'));
    }

    public function productSearch(Request $request)
    {
        $pageTitle     = 'Product Search';
        $emptyMessage  = 'No product found';
        $searchKey     = $request->search_key;
        $category_id   = $request->category_id;
        $perpage       = getPaginate(15);

        if(request()->perpage){
            $perpage = request()->perpage;
        }

        if($category_id == 0){
          /*  $products   = Product::with(['categories', 'offer', 'offer.activeOffer', 'reviews', 'brand'])
                            ->where('status', 1)
                            ->whereHas('categories')
                            ->whereHas('brand')
                            ->orderBy('id', 'ASC')
                            ->where(function ($query) use ($searchKey) {
                                return $query->where('name', 'like', "%{$searchKey}%")->orWhere('summary', 'like', "%{$searchKey}%")->orWhere('description', 'like', "%{$searchKey}%");
                            })
                            ->whereHas('brand')

                            ->paginate($perpage);*/
                            
                         /*   $products   = Product::
                            where('status', 1)
                             ->where(function ($query) use ($searchKey) {
                                return $query->where('name', 'like', "%{$searchKey}%")->orWhere('summary', 'like', "%{$searchKey}%")->orWhere('description', 'like', "%{$searchKey}%");
                            })
                            ->get();*/

                         $products   = Product::
                            where('status', 1)
                             ->where(function ($query) use ($searchKey) {
                                return $query->where('name', 'like', "%{$searchKey}%");
                            })
                            ->get();
        }else{
            
            $products   = Category::where('id', $category_id)->firstOrFail()
                            ->products()
                            ->where('status', 1)
                            ->where('base_price','>', 1)
                            ->with('categories', 'offer', 'offer.activeOffer', 'reviews', 'brand')
                            ->whereHas('categories')
                            ->whereHas('brand')
                            ->orderBy('id', 'ASC')
                            ->where(function($query) use ($searchKey){
                                return $query->where('name', 'like', "%{$searchKey}%");
                                })
/*
                            ->where(function($query) use ($searchKey){
                                return $query->where('name', 'like', "%{$searchKey}%")
                                    ->orWhere('summary', 'like', "%{$searchKey}%")
                                    ->orWhere('description', 'like', "%{$searchKey}%");
                                })*/

                            ->get();
                            /*Product::
                            where('status', 1)
                             ->where(function ($query) use ($searchKey) {
                                return $query->where('name', 'like', "%{$searchKey}%")->orWhere('summary', 'like', "%{$searchKey}%")->orWhere('description', 'like', "%{$searchKey}%");
                            }) 
                            ->paginate($perpage);*/
        }

        if($request->ajax()){
            $view = 'partials.products_search_filter';
        }else{
            $view = 'products_search';
        }

        return view($this->activeTemplate . $view, compact('pageTitle', 'products', 'emptyMessage', 'searchKey','category_id','perpage'));
    }


  /*  public function productsByCategory(Request $request, $id)
    {
        $category   = Category::whereId($id)->firstOrFail();
        $pageTitle  = 'Products by Category - '.$category->name;
        $categories = Category::where('parent_id', null)->latest()->get();
        $brand      = $request->brand?$request->brand:['0'];
        $min        = $request->min;
        $max        = $request->max;
        $perpage    = getPaginate(15);


        if(request()->perpage){
            $perpage = request()->perpage;
        }

        //$all_products   = Product::whereCategory($id)->whereStatus(1)->get();

        $procat = ProductCategory::whereCategoryId($id)->Orwhere('category_id',$id)->Orwhere('category_id',$id)->get();
        return $procat;
         $all_products  = $category->products()
                                    ->where('status', 1)
                                    //->with('categories', 'offer', 'offer.activeOffer','brand', 'reviews')
                                    //->whereHas('categories')
                                    //->whereHas('brand')
                                    ->get();
        //return $all_products;

        $min_price       = $all_products->min('base_price')??0;
        $max_price       = $all_products->max('base_price')??0;
        $brands          = $all_products->unique('brand')->pluck('brand');

        if(in_array("0", $brand)){
            $productCollection  = $all_products;
        }else{
            $productCollection  = $all_products->whereIn('brand.id', $brand);
        }

        if($min && $max){
            $productCollection = $productCollection->where('base_price', '>=', $min)->where('base_price', '<=', $max);
        }elseif($min){
            $productCollection = $productCollection->where('base_price', '>=', $min);
        }elseif($max){
            $productCollection = $productCollection->where('base_price', '<=', $max);
        }else{
            $productCollection = $productCollection;
        }

        $products           = paginate($productCollection, $perpage, $page = null, $options = []);

        $emptyMessage       = "Sorry! No Product Found";
        $imageData          = imagePath()['category'];
        $seoContents        = getSeoContents($category, $imageData, 'image');
        $view               = 'products_by_category';

        if($request->ajax()){
            $view = 'partials.products_filter';
        }

        return view($this->activeTemplate . $view, compact('products', 'perpage', 'brand' ,'min_price', 'max_price', 'pageTitle', 'emptyMessage','min', 'max', 'category', 'brands', 'seoContents'));
    }*/
     public function productsByCategory(Request $request, $id)
    {
        $cat   = Category::whereId($id)->firstOrFail();
        $category   = Category::whereId($id)->Orwhere('sub_id',$id)->firstOrFail();
       
        $pageTitle  = 'Products by Category - '.$category->name;
        $categories = Category::where('parent_id', null)->latest()->get();
        $brand      = $request->brand?$request->brand:['0'];
        $min        = $request->min;
        $max        = $request->max;
        $perpage    = getPaginate(15);

        if(request()->perpage){
            $perpage = request()->perpage;
        }
        

        $all_products   = $category->products()
                                    ->where('base_price','>', 1)
                                    ->where('status', 1)
                                    ->with('categories', 'offer', 'offer.activeOffer','brand', 'reviews')
                                    ->whereHas('categories') 
                                    ->get();
        $count = count($all_products);
        if($count < 1)
        {
        $category   = Category::where('sub_id',$category->sub_id)->firstOrFail();
        //return $category;
        $pageTitle  = 'Products by Category - '.$category->name;
        $categories = Category::where('parent_id', null)->latest()->get();
        $brand      = $request->brand?$request->brand:['0'];
        $min        = $request->min;
        $max        = $request->max;
        $perpage    = getPaginate(15);

        if(request()->perpage)
        {
            $perpage = request()->perpage;
        }
        

        $all_products   = $category->products2()
                                    ->where('base_price','>', 1)
                                    ->where('status', 1)
                                    ->with('categories', 'offer', 'offer.activeOffer','brand', 'reviews')
                                    ->whereHas('categories') 
                                    ->get();
                                
        }
        
        

        $products1   = $category->products2()
                                    ->where('base_price','>', 1)
                                    ->where('status', 1)
                                    ->with('categories', 'offer', 'offer.activeOffer','brand', 'reviews')
                                    ->whereHas('categories') 
                                    ->get();
       if($cat->parent_id > 0)
                                    {
       $all_products   = Product::where('subcategory',$cat->sub_id)->where('status', 1 )->where('base_price', '>', 0 )->where('category','>',0)->where('subcategory','>',0)
                                    ->get();  
                                    
                                    
                                    }
                                    
        else
        {
             $all_products   = Product::whereCategory($id)->where('status', 1 )->where('base_price', '>', 0 )->where('category','>',0)->where('subcategory','>',0)
                                     
                                    //->Orwhere('subcategory',$cat->sub_id)->Orwhere('subcategory',$id)
                                    ->get();
        }
          
        
                                    
        
        //$all_products = array_merge($products2, $products1);


        $min_price       = $all_products->min('base_price')??0;
        $max_price       = $all_products->max('base_price')??0;
        $brands          = $all_products->unique('brand')->pluck('brand');

        if(in_array("0", $brand)){
            $productCollection  = $all_products;
        }else{
            $productCollection  = $all_products->whereIn('brand.id', $brand);
        }

        if($min && $max){
            $productCollection = $productCollection->where('base_price', '>=', $min)->where('base_price', '<=', $max);
        }elseif($min){
            $productCollection = $productCollection->where('base_price', '>=', $min);
        }elseif($max){
            $productCollection = $productCollection->where('base_price', '<=', $max);
        }else{
            $productCollection = $productCollection;
        }

        $products           = paginate($productCollection, $perpage, $page = null, $options = []);

        $emptyMessage       = "Sorry! No Product Found";
        $imageData          = imagePath()['category'];
        $seoContents        = getSeoContents($category, $imageData, 'image');
        $view               = 'products_by_category';

        if($request->ajax()){
            $view = 'partials.products_filter';
        }

        return view($this->activeTemplate . $view, compact('products', 'perpage', 'brand' ,'min_price', 'max_price', 'pageTitle', 'emptyMessage','min', 'max', 'category', 'brands', 'seoContents'));
    }


    public function productsByBrand(Request $request, $id)
    {
        $brand                  = Brand::whereId($id)->firstOrFail();
        $pageTitle              = 'Products by Brand - '.$brand->name;
        $categories             = Category::where('parent_id', null)->latest()->get();
        $category_id            = $request->category_id??0;
        $min                    = $request->min;
        $max                    = $request->max;
        $perpage = getPaginate(15);

        if(request()->perpage){
            $perpage = request()->perpage;
        }
        if($category_id !=0){
            $all_products       = Category::where('id', $category_id)->firstOrFail()
                                    ->products()
                                    ->where('base_price','>', 1)
                                    ->where('status', 1)
                                    ->where('brand_id', $id)->with('categories', 'offer', 'offer.activeOffer','brand', 'reviews')
                                    ->whereHas('categories')
                                    ->whereHas('brand')
                                    ->get();
        }else{
            $all_products       = $brand->products()
                                    ->where('base_price','>', 1)
                                    ->where('status', 1)
                                    ->where('base_price','>', 1)
                                    ->where('brand_id', $id)
                                    ->with('categories', 'offer', 'offer.activeOffer','brand', 'reviews')
                                    ->whereHas('categories')
                                    ->whereHas('brand')
                                    ->get();
                                    
        }

        $productCollection = $all_products;

        if($min && $max){
            $productCollection = $productCollection->where('base_price', '>=', $min)->where('base_price', '<=', $max);
        }elseif($min){
            $productCollection = $productCollection->where('base_price', '>=', $min);
        }elseif($max){
            $productCollection = $productCollection->where('base_price', '<=', $max);
        }


        $min_price              = $all_products->min('base_price')??0;
        $max_price              = $all_products->max('base_price')??0;

        $products           =  paginate($productCollection, $perpage, $page = null, $options = []);
        $view = 'products_by_brand';

        if($request->ajax()){
            $view = 'partials.products_filter';
        }

        $emptyMessage   = "Sorry! No Product Found.";

        $imageData      = imagePath()['brand'];
        $seoContents    = getSeoContents($brand, $imageData, 'logo');


        return view($this->activeTemplate . $view, compact('products','categories', 'perpage', 'brand' ,'min_price', 'max_price', 'pageTitle', 'emptyMessage','min', 'max', 'category_id', 'seoContents'));

    }

    public function quickView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|gt:0',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $id         = $request->id;

        $review_available = false;

       /* $product = Product::where('id', $id)
                    ->where('status', 1)
                    ->with('categories', 'offer', 'offer.activeOffer', 'reviews', 'productImages', 'brand')
                    ->whereHas('categories')
                    ->whereHas('brand')
                    ->firstOrFail();*/

         $product = Product::where('id', $id)
                    ->where('status', 1)

                    ->firstOrFail();


        if(optional($product->offer)->activeOffer){
            $discount = calculateDiscount($product->offer->activeOffer->amount, $product->offer->activeOffer->discount_type, $product->base_price);
        }else $discount = 0;


        $rProducts = $product->categories()
                    ->with('products', 'products.offer')
                    ->get()
                    ->map(function($item) use($id){
                        return $item->products->where('id', '!=', $id)->take(5);
                    });

        $attributes     = AssignProductAttribute::where('status',1)->where('product_id', $id)->distinct('product_attribute_id')->with('productAttribute', 'product')->get(['product_attribute_id']);

        $pageTitle = 'Product Details';
        return view($this->activeTemplate . 'partials.quick_view', compact('product', 'pageTitle', 'review_available', 'discount', 'attributes'));
    }

    public function productDetails($id, $order_id =null)
    {
        $product = Product::where('id', $id)->where('status', 1)->firstOrFail();

        /*$product = Product::where('id', $id)->where('status', 1)
                    ->with('categories','assignAttributes','offer', 'offer.activeOffer', 'reviews', 'productImages', 'stocks')
                    ->whereHas('categories')
                    ->whereHas('brand')
                    ->firstOrFail();
                    */

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

        $pageTitle = 'Product Details';
        return view($this->activeTemplate . 'product_details', compact('product', 'pageTitle', 'review_available', 'related_products', 'discount', 'attributes', 'images', 'seoContents'));
    }

    public function addToCompare(Request $request)
    {
        $id         = $request->product_id;
        $product    = Product::where('id', $id)->with('categories')->first();
        $compare    = session()->get('compare');

        if($compare){
            $reset_compare  = reset($compare);
            $prev_product   = Product::where('id', $reset_compare['id'])->with('categories')->first();

            $not_same       = empty(array_intersect($product->categories->pluck('id')->toArray(), $prev_product->categories->pluck('id')->toArray()));

            if($not_same){
                return response()->json(['error' => 'A different type of product is already on your comparison list']);
            }
            if(count($compare) > 2){
                return response()->json(['error' => 'You can\'t add more than 3 product in comparison list']);
            }
        }

        if(!$compare) {

            $compare = [
                $id => [
                    "id" => $product->id
                ]
            ];
            session()->put('compare', $compare);
            return response()->json(['success' => 'Added to comparison list']);
        }

        // if compare list is not empty then check if this product exist
        if(isset($compare[$id])) {
            return response()->json(['error' => 'Already in the comparison list']);
        }
        $compare[$id] = [
            "id" => $product->id
        ];

        session()->put('compare', $compare);
        return response()->json(['success' => 'Added to comparison list']);
    }

    public function compare()
    {
        $data       = session()->get('compare');

        $products   = [];

        if($data){
            foreach($data as $key=>$val){
                array_push($products, $key);
            }
        }

        $compare_data   = Product::with('categories', 'offer', 'offer.activeOffer', 'reviews')
                            ->whereHas('categories')
                            ->whereHas('brand')
                            ->whereIn('id',$products)->get();

        $compare_items = $compare_data->take(4);

        $pageTitle = 'Product Comparison';
        $emptyMessage = 'Comparison list is empty';
        return view($this->activeTemplate . 'compare', compact('pageTitle', 'compare_items', 'emptyMessage'));
    }

    public function getCompare()
    {
        $data       = session()->get('compare');
        if(!$data){
            return response(['total'=> 0]);
        }

        $products   = [];

        foreach($data as $key=>$val){
            $products[] = $key;
        }

        $compare_data   = Product::with('categories', 'offer', 'offer.activeOffer', 'reviews')
                            ->where('status', 1)
                            ->whereHas('categories')
                            ->whereHas('brand')
                            ->whereIn('id',$products)->get();
        return response(['total'=> count($compare_data)]);
    }

    public function getStockByVariant(Request $request)
    {
        $pid    = $request->product_id;
        $attr   = json_decode($request->attr_id);
        sort($attr);
        $attr = json_encode($attr);

        $stock  = ProductStock::showAvailableStock($pid, $attr);

        return response()->json(['sku'=> $stock->sku??'Not Available', 'quantity' => $stock->quantity??0]);
    }

    public function getImageByVariant(Request $request)
    {
        $variant = AssignProductAttribute::whereId($request->id)->with('productImages')->firstOrFail();
        $images         = $variant->productImages;

        if($images->count() > 0){
            return view($this->activeTemplate . 'partials.variant_images', compact('images'));
        }else{
            return response()->json(['error'=>true]);
        }
    }

    public function removeFromCompare($id)
    {
        $compare = session()->get('compare');

        if(isset($compare[$id])) {
            unset($compare[$id]);
            session()->put('compare', $compare);
            $notify[] = ['success', 'Deleted from compare list'];
            return response()->json(['message' => 'Removed']);
        }

        return response()->json(['error' => 'Something went wrong']);
    }

    public function loadMore(Request $request)
    {
        $reviews = ProductReview::where('product_id', $request->pid)->latest()->paginate(5);
        return view($this->activeTemplate . 'partials.product_review', compact('reviews'));
    }

    public function allSellers()
    {
        $pageTitle  = "Our sellers";
        $sellers    = Seller::active()->emailVerified()->smsVerified()->whereHas('shop')->with('shop')->paginate(getPaginate());
        return view($this->activeTemplate.'all_sellers',compact('pageTitle','sellers'));
    }

    public function sellerDetails($id,$slug)
    {
        $pageTitle      = "Seller Details";
        $seller         = Seller::active()->emailVerified()->smsVerified()->where('id',$id)->whereHas('shop')->with('shop')->firstOrFail();
        $imageData      = imagePath()['seller']['shop_cover'];
        $seoContents    = getSeoContents($seller->shop, $imageData, 'cover');
        $products       = Product::active()->where('seller_id',$seller->id)->latest()->paginate(getPaginate(20));
        return view($this->activeTemplate.'seller_details',compact('pageTitle','seller','products', 'seoContents'));
    }

}

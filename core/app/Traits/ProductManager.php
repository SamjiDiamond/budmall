<?php
namespace App\Traits;

use App\Models\Brand;
use App\Models\Deliverysetup;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Productlocation;
use App\Models\ProductsCategory;
use App\Models\ProductReview;
use App\Models\ProductStock;
use App\Models\Producttier;
use App\Models\State;
use App\Rules\FileTypeValidate;
use Illuminate\Support\Facades\Auth;

trait ProductManager
{
    protected function pageTitle($isTrashed, $searchKey)
    {
        if($isTrashed)
        $title  =  "All Trashed Products";
        else
        $title  =  "All Products";
        if($searchKey)
        $title  ="Product Search : '$searchKey'";

        return $title;
    }

    public function products($sellerId = 0, $isTrashed = false)
    {
        $search = trim(strtolower(request()->search));
        $query  = Product::query();
        if($sellerId)
        $query  = $query->sellers();
        //$query  = $query->with(['categories','brand', 'stocks'])->whereHas('categories')->whereHas('brand');
        if($isTrashed)
        $query  = $query->onlyTrashed();
        if($search)
        $query  = $query->where('name', 'like', "%$search%");

        $data['products']       = $query->orderByDesc('id')->paginate(getPaginate());
        $data['pageTitle']      = $this->pageTitle($isTrashed, $search);
        $data['emptyMessage']   = "No product found";
        return $data;
    }

    public function pendingProducts($sellerId = 0, $isTrashed = false)
    {
        $search = trim(strtolower(request()->search));

        $query  = Product::query();
        if($sellerId){
        $query  = $query->sellers();
        $query  = $query->with(['categories','brand', 'stocks'])->whereHas('categories')->whereHas('brand');
        }
        if($isTrashed)
        $query  = $query->onlyTrashed();
        if($search)
        $query  = $query->where('name', 'like', "%$search%");

        $data['products']       = $query->where('status',0)->orderByDesc('id')->paginate(getPaginate());
        $data['pageTitle']      = 'Pending Products';
        $data['emptyMessage']   = "No product found";
        return $data;
    }
    public function productByVendor($admin = true, $isTrashed = false)
    {
        $search = trim(strtolower(request()->search));

        $query  = Product::query();
        if($isTrashed)
        $query  = $query->onlyTrashed();
        if($search)
        $query  = $query->where('name', 'like', "%$search%");
        if($admin){
            $data['pageTitle']      = 'Products By Admin';
            $query = $query->where('seller_id',0);
        }else{
            $data['pageTitle']      = 'Products By Seller';
            $query = $query->where('seller_id','!=',0);
        }

        $data['products']       = $query->orderByDesc('id')->paginate(getPaginate());
        $data['emptyMessage']   = "No product found";
        return $data;
    }

    public function productCreate()
    {
        $data['categories'] = Category::with('allSubcategories')->where('parent_id', null)->get();
        $data['tier'] = Deliverysetup::whereStatus(1)->get();
        $data['state'] = State::whereStatus(1)->get();

        $data['brands']     = Brand::orderBy('name')->get();
        $data['pageTitle']  = "Add New Product";
        return $data;
    }

    public function editProduct($id, $sellerId =0)
    {
        if($sellerId)
        $data['product']        = Product::where('seller_id', $sellerId)->where('id', $id)->firstOrFail();
        else
        $data['product']        = Product::whereId($id)->first();

        $data['categories']     = Category::with('allSubcategories')->where('parent_id', null)->get();
        $data['tier'] = Deliverysetup::whereStatus(1)->get();
        $data['state'] = State::whereStatus(1)->get();

        $data['brands']         = Brand::orderBy('name')->get();
        $data['images']         = [];

        if($data['product'])
           {
        foreach($data['product']->productPreviewImages as $key=>$image)
        {
            $img['id'] = $image->id;
            $img['src'] = getImage(imagePath()['product']['path']. '/' . $image->image);
            $data['images'][] = $img;
        }
           }

        $data['pageTitle']      = "Edit Product";
        return $data;
    }


public function storeProduct($request, $id, $sellerId = 0)
    {
        $validation_rule = $this->getProductValidationRule($id);
        $request->validate($validation_rule, [
            'specification.*.name.required'   =>  'All specification name is required',
            'specification.*.value'           =>  'All specification value is required',
        ]);

        //Check if the sku is already taken
        if($request->sku && $this->checkSKU($request->sku, $id)){
            $notify[]=['error','This SKU has already been taken'];
            return $notify;
        }

        $product = new Product();

        if($id){
            $product                = Product::findOrFail($id);
            $prev_track_inventory   = $product->track_inventory;
            $prev_has_variants      = $product->has_variants;

            if($sellerId && $product->seller_id != $sellerId){
                $notify[]=['error','This product doesn\'t belong to this seller'];
                return $notify;
            }

            $sellerId = $product->seller_id;
            $product->status        = 1 ;
        }else{
            $product->status        = $sellerId == 0 ? 1 : 0;
        }

        if ($request->hasFile('main_image'))
        {
            try {
                $request->merge(['image' => uploadImage($request->main_image, imagePath()['product']['path'], imagePath()['product']['size'], @$product->main_image ,imagePath()['product']['thumb'])]);
            } catch (\Exception $exp) {
                $notify[]=['error','Could not upload the main image'];
                return $notify;
            }
        }else
        {
            $request->merge(['image'=>$product->main_image]);
        }

        $product->seller_id         = $sellerId;
        $product->brand_id          = $request->brand_id;
        $product->sku               = $request->has_variants?null:$request->sku;
        $product->name              = $request->name;
        $product->model             = $request->model;
        $product->has_variants      = $request->has_variants??0;
        $product->track_inventory   = $request->track_inventory??0;
        $product->show_in_frontend  = $request->show_in_frontend??0;
        $product->main_image        = $request->image;
        $product->video_link        = $request->video_link;
        $product->description       = $request->description;
        $product->summary           = $request->summary;
        $product->specification     = $request->specification??null;
        $product->extra_descriptions= $request->extra??null;
        $product->base_price        = $request->base_price;
        $product->meta_title        = $request->meta_title;
        $product->category          = $request->category;
        $product->subcategory       = $request->subcategory;
        $product->meta_description  = $request->meta_description;
        $product->meta_keywords     = $request->meta_keywords??null;

        $location = json_encode($request->location);
        $product->delivery_location = $location??null;

        //$tier = array($request->tier);
        $tier = json_encode($request->tier);
        $product->delivery_category = $tier??null;
        $product->save();

        /*$prod = Product::all();
        ###Polpulate Product Delcalc######
        foreach($prod as $upd)
        {
        $fixtier = Producttier::whereCode($upd->sku)->get();
        foreach($fixtier as $fix)
        {
        $upd->delivery_category = $fix->category??null;
        $upd->save();
        }
        }
        ###Polpulate Product delcalc######
        */


        /*

        ###Polpulate Product Category######
        $prod = Product::all();
         foreach($prod as $upd)
        {
        $productc = new ProductsCategory();
        $productc->product_id = $upd->id??null;
        $productc->save();
        ###Polpulate Product Category######
        }*/


        //Check for location and delivery category
        $admin = Auth::guard('admin')->user();
        
        //Check Old Images
        $previous_images = $product->productPreviewImages->pluck('id')->toArray();
        $image_to_remove = array_values(array_diff($previous_images, $request->old??[]));


        foreach($image_to_remove as $item){
            $productImage   = ProductImage::find($item);
            $location       = imagePath()['product']['path'];

            removeFile($location . '/' . $productImage->image);
            $productImage->delete();
        }

        if ($request->hasFile('photos')) {
            foreach($request->photos as $image){
                try {
                    $product_img = uploadImage($image, imagePath()['product']['path'], imagePath()['product']['size'],null, imagePath()['product']['thumb']);
                }catch (\Exception $exp) {
                    $notify[]=['error','Could not upload additional images'];
                    return $notify;
                }
                $productImage = new ProductImage();
                $productImage->product_id   = $product->id;
                $productImage->image        = $product_img;
                $productImage->save();
            }
        }

        $type = 'Added';

        if($id){
            $product->categories()->sync($request->category);
            $type = 'Updated';

            //If the value of track_inventory or has_variants is changed then delete the prev inventory
            if(($prev_has_variants != $product->has_variants) || $prev_track_inventory != $product->track_inventory){
                $product_stocks = $product->stocks();
                foreach($product_stocks->get() as $st){
                    $st->stockLogs()->delete();
                }
                $product_stocks->delete();
            }

            // Check stock table to update the sku in stock table
            if($product->sku){
                $stock = ProductStock::where('id', $product->id)->first();
                if($stock){
                    $stock->sku = $product->sku;
                    $stock->save();
                }
            }
        }
        else $product->categories()->attach($request->subcategory);

        $message = "Product $type Successfully";
        $notify[]=['success', $message];
        return $notify;
    }

    
    public function storeProductOLD($request, $id, $sellerId = 0)
    {
        $validation_rule = $this->getProductValidationRule($id);
        $request->validate($validation_rule, [
            'specification.*.name.required'   =>  'All specification name is required',
            'specification.*.value'           =>  'All specification value is required',
        ]);

        //Check if the sku is already taken
        if($request->sku && $this->checkSKU($request->sku, $id)){
            $notify[]=['error','This SKU has already been taken'];
            return $notify;
        }

        $product = new Product();

        if($id){
            $product                = Product::findOrFail($id);
            $prev_track_inventory   = $product->track_inventory;
            $prev_has_variants      = $product->has_variants;

            if($sellerId && $product->seller_id != $sellerId){
                $notify[]=['error','This product doesn\'t belong to this seller'];
                return $notify;
            }

            $sellerId = $product->seller_id;
            $product->status        = 1 ;
        }else{
            $product->status        = $sellerId == 0 ? 1 : 0;
        }

        if ($request->hasFile('main_image'))
        {
            try {
                $request->merge(['image' => uploadImage($request->main_image, imagePath()['product']['path'], imagePath()['product']['size'], @$product->main_image ,imagePath()['product']['thumb'])]);
            } catch (\Exception $exp) {
                $notify[]=['error','Could not upload the main image'];
                return $notify;
            }
        }else
        {
            $request->merge(['image'=>$product->main_image]);
        }

        $product->seller_id         = $sellerId;
        $product->brand_id          = $request->brand_id;
        $product->sku               = $request->has_variants?null:$request->sku;
        $product->name              = $request->name;
        $product->model             = $request->model;
        $product->has_variants      = $request->has_variants??0;
        $product->track_inventory   = $request->track_inventory??0;
        $product->show_in_frontend  = $request->show_in_frontend??0;
        $product->main_image        = $request->image;
        $product->video_link        = $request->video_link;
        $product->description       = $request->description;
        $product->summary           = $request->summary;
        $product->specification     = $request->specification??null;
        $product->extra_descriptions= $request->extra??null;
        $product->base_price        = $request->base_price;
        $product->meta_title        = $request->meta_title;
        $product->meta_description  = $request->meta_description;
        $product->meta_keywords     = $request->meta_keywords??null;

        $location = json_encode($request->location);
        $product->delivery_location = $location??null;

        //$tier = array($request->tier);
        $tier = json_encode($request->tier);
        $product->delivery_category = $tier??null;
        $product->save();

        /*$prod = Product::all();
        ###Polpulate Product Delcalc######
        foreach($prod as $upd)
        {
        $fixtier = Producttier::whereCode($upd->sku)->get();
        foreach($fixtier as $fix)
        {
        $upd->delivery_category = $fix->category??null;
        $upd->save();
        }
        }
        ###Polpulate Product delcalc######
        */


        /*

        ###Polpulate Product Category######
        $prod = Product::all();
         foreach($prod as $upd)
        {
        $productc = new ProductsCategory();
        $productc->product_id = $upd->id??null;
        $productc->save();
        ###Polpulate Product Category######
        }*/


        //Check for location and delivery category
        $admin = Auth::guard('admin')->user();
        if($id)
         {

        //$tier = Producttier::whereCode($product->sku)->whereStatus(1)->delete();
        //$dloc = Productlocation::whereCode($product->sku)->whereStatus(1)->delete();

      /* $locid = $request->location;
       foreach($request->location as $r)
       {
        $delocate = new Productlocation();
        $delocate->code = $product->sku;
        $delocate->location = $r;
        $delocate->registeredby = $admin->id;
        $delocate->status = 1;
        $delocate->save();
       }


          foreach($request->tier as $tier)
         {
        $deltier = new Producttier();
        $deltier->code = $product->sku;
        $deltier->category = $tier;
        $deltier->registeredby = $admin->id;
        $deltier->status = 1;
        $deltier->save();

         }*/

         }




        //Check Old Images
        $previous_images = $product->productPreviewImages->pluck('id')->toArray();
        $image_to_remove = array_values(array_diff($previous_images, $request->old??[]));


        foreach($image_to_remove as $item){
            $productImage   = ProductImage::find($item);
            $location       = imagePath()['product']['path'];

            removeFile($location . '/' . $productImage->image);
            $productImage->delete();
        }

        if ($request->hasFile('photos')) {
            foreach($request->photos as $image){
                try {
                    $product_img = uploadImage($image, imagePath()['product']['path'], imagePath()['product']['size'],null, imagePath()['product']['thumb']);
                }catch (\Exception $exp) {
                    $notify[]=['error','Could not upload additional images'];
                    return $notify;
                }
                $productImage = new ProductImage();
                $productImage->product_id   = $product->id;
                $productImage->image        = $product_img;
                $productImage->save();
            }
        }

        $type = 'Added';

        if($id){
            $product->categories()->sync($request->categories);
            $type = 'Updated';

            //If the value of track_inventory or has_variants is changed then delete the prev inventory
            if(($prev_has_variants != $product->has_variants) || $prev_track_inventory != $product->track_inventory){
                $product_stocks = $product->stocks();
                foreach($product_stocks->get() as $st){
                    $st->stockLogs()->delete();
                }
                $product_stocks->delete();
            }

            // Check stock table to update the sku in stock table
            if($product->sku){
                $stock = ProductStock::where('id', $product->id)->first();
                if($stock){
                    $stock->sku = $product->sku;
                    $stock->save();
                }
            }
        }else $product->categories()->attach($request->categories);

        $message = "Product $type Successfully";
        $notify[]=['success', $message];
        return $notify;
    }

    public function deleteProduct($id, $sellerId = 0)
    {
        $query    = Product::where('id', $id);
        if($sellerId)
        $query    = $query->where('seller_id', $sellerId);
        $product  = $query->withTrashed()->firstOrFail();
        $type     = 'Deleted';

        if ($product->trashed()){
            $product->restore();
            $type =  'Restored';
        }else $product->delete();

        $notify[] = ['success', "Product $type Successfully"];
        return $notify;
    }

    protected function getProductValidationRule($id)
    {
        $validation_rule =  [
            'name'                  => 'required|string|max:191',
            'tier'                  => 'required|array|min:1',
            'location'                  => 'required|array|min:1',
            'model'                 => 'nullable|string|max:100',
            'brand_id'              => 'required|integer',
            'base_price'            => 'required|numeric',
            'category'            => 'required|int|min:1',
            'subcategory'            => 'required|int|min:1',
            'has_variants'          => 'sometimes|required|numeric|min:1|max:1',
            'track_inventory'       => 'sometimes|required|numeric|min:1|max:1',
            'show_in_frontend'      => 'sometimes|required|numeric|min:1|max:1',
            'description'           => 'nullable|string',
            'summary'               => 'nullable|string|max:360',
            'sku'                   => 'nullable|required_without:has_variants|string|max:100',
            'extra'                 => 'sometimes|required|array',
            'extra.*.key'           => 'required_with:extra',
            'extra.*.value'         => 'required_with:extra',
            'specification'         => 'sometimes|required|array',
            'specification.*.name'  => 'required_with:specification',
            'specification.*.value' => 'required_with:specification',
            'meta_title'            => 'nullable|string|max:191',
            'meta_description'      => 'nullable|string|max:191',
            'meta_keywords'         => 'nullable|array',
            'meta_keywords.array.*' => 'nullable|string',
            'video_link'            => 'nullable|string',
            'photos'                => 'required_if:id,0|array|min:1',
            'photos.*'              => ['image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
        ];

        if($id ==0){
            $validation_rule['main_image']  = ['required', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
        }else{
            $validation_rule['main_image']  = ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
        }

        return $validation_rule;
    }

    protected function checkSKU($sku, $id)
    {
        Product::where('sku', $sku)->where('id', '!=', $id)->with('stocks')->orWhereHas('stocks', function($q)use($sku, $id){
            $q->where('sku', $sku)->where('product_id', '!=', $id);
        })->first();
    }


    public function productReviews($sellerId=0)
    {
        $query = ProductReview::with(['product','user']);

        if($sellerId){
            $query = $query->whereHas('product',function($q) use($sellerId){
                $q->where('seller_id', $sellerId);
            });
        }else{
            $query = $query->whereHas('product');
        }

        $data['reviews'] = $query->whereHas('user')->latest()->paginate(getPaginate());

        $data['pageTitle']      = "Product Reviews";
        $data['emptyMessage']   = "No review yet";

        return $data;
    }

    public function productReviewSearch($key, $sellerId = 0)
    {
        $query = ProductReview::query();

        if($sellerId){
            $query = $query->whereHas('product', function($q) use($sellerId){
                $q->where('seller_id', $sellerId);
            });
        }else{
            $query->whereHas('product');
        }

        $query = $query->with('product', 'user');

        $query = $query->where(function($q) use($key){
            $q->where('review', 'like', "%$key%")
            ->orWhere('rating', 'like', "%$key%")

            ->orWhereHas('user', function ($user) use ($key) {
                $user->where('username', 'like', "%$key%");
            })->orWhereHas('product', function ($product) use ($key) {
                $product->where('name', 'like', "%$key%");
            });
        });

        $data['reviews']        = $query->whereHas('user')->latest()->paginate(getPaginate());
        $data['pageTitle']      = "Search of Reviews $key";
        $data['emptyMessage']   = "No Review Yet";

        return $data;

    }

}

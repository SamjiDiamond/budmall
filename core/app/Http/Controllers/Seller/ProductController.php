<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Traits\ProductManager;
use App\Http\Controllers\Controller;
use App\Traits\ProductVariantManager;

class ProductController extends Controller
{
    use ProductManager, ProductVariantManager;

    protected function seller()
    {
        return auth()->guard('seller')->user();
    }

    protected function id()
    {
        return $this->seller()->id;
    }

    /*
    ==================Product Manager TRAIT==================
    */

    public function index()
    {
        return view('seller.products.index', $this->products($this->id()));
    }
    public function pending()
    {
        return view('seller.products.index', $this->pendingProducts($this->id()));
    }

    public function trashed()
    {
        return view('seller.products.index', $this->products($this->id(), true));
    }

    public function create()
    {
        return view('seller.products.create', $this->productCreate());
    }

    public function edit($id)
    {
        return view('seller.products.create', $this->editProduct($id, $this->id()));
    }

    public function store(Request $request, $id){
        return back()->withNotify(
            $this->storeProduct($request, $id, $this->id())
        );
    }

    public function delete($id)
    {
        return back()->withNotify(
            $this->deleteProduct($id, $this->id())
        );
    }

    /*
    ==================ProductVariantManager TRAIT==================
    */

    public function addVariant($product_id)
    {
        return view('seller.products.variant.create', $this->addProductVariant($product_id, $this->id()));
    }

    public function storeVariant(Request $request, $id)
    {
        return back()->withNotify(
            $this->storeProductVariant($request, $id, $this->id())
        );
    }

    public function updateVariant(Request $request, $id)
    {
        return back()->withNotify(
            $this->updateProductVariant($request, $id, $this->id())
        );
    }

    public function deleteVariant($id)
    {
        return back()->withNotify(
            $this->deleteProductVariant($id, $this->id())
        );
    }

    public function reviews()
    {
        return view('seller.products.reviews', $this->productReviews($this->id()));
    }

    public function reviewSearch(Request $request)
    {
        if($request->key != null) {
            return view('seller.products.reviews', $this->productReviewSearch($request->key, $this->id()));
        }else{
            return redirect()->route('seller.product.reviews');
        }
    }


    public function addVariantImages($id)
    {
        return view('seller.products.variant.images', $this->addProductVariantImages($id, $this->id()));
    }

    public function saveVariantImages(Request $request, $id)
    {
        $storeImages = $this->saveProductVariantImages($request, $id, $this->id());

        return redirect()->back()->withNotify($storeImages);
    }


}

<?php
namespace App\Traits;

use App\Models\AssignProductAttribute;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductStock;
use App\Rules\FileTypeValidate;

trait ProductVariantManager {

    public function addProductVariant($productId, $sellerId = 0)
    {
        if($sellerId)
        $product = Product::where('id', $productId)->where('seller_id', $sellerId)->firstOrFail();
        else
        $product = Product::where('id', $productId)->firstOrFail();

        if(!$product->has_variants){
            abort('403');
        }

        $data['productId']          = $productId;
        $data['productName']        = $product->name;
        $data['currentAttributes']  = AssignProductAttribute::where('product_id', $productId)
                                    ->with('productAttribute')
                                    ->get()
                                    ->groupBy('product_attribute_id');

        $data['attributes']         = ProductAttribute::all();
        $data['pageTitle']          = "Add Product Variants";
        $data['emptyMessage']       = "No variant added yet";
        return $data;
    }

    public function storeProductVariant($request, $id, $sellerId = 0)
    {
        if($sellerId) $this->checkProductOwner($id, $sellerId);
        $request->validate(...$this->getValidationRule($request));

        $data = [];

        if($request->attr_type == 1){
            $data = $request->text;
        }else if($request->attr_type == 2){
            $data = $request->color;
        }else if($request->attr_type == 3){
            foreach ($request->img as $key=>$item) {
                $data[$key]['name'] = $item['name'];
                $data[$key]['price'] =$item['price'];
                if(is_file($item['value'])) {
                    try {
                        $data[$key]['value'] = uploadImage($item['value'], imagePath()['attribute']['path'], imagePath()['attribute']['size']);
                    } catch (\Exception $exp) {
                        $notify[] = ['error', 'Couldn\'t upload the image.'];
                        return $notify;
                    }
                }
            }
        }

        $exist = AssignProductAttribute::where('product_id',$id)->where('product_attribute_id',$request->attr_id)->count();

        if(!$exist){
            $stocks = ProductStock::where('product_id', $id)->cursor();
            foreach($stocks as $stock){
                $stock->delete();
            }
        }

        foreach($data as $attr){
            $assign_attr = new AssignProductAttribute();
            $assign_attr->product_attribute_id  = $request->attr_id;
            $assign_attr->product_id            = $id;
            $assign_attr->name                  = $attr['name'];
            $assign_attr->value                 = $attr['value']??'';
            $assign_attr->extra_price           = $attr['price'];
            $assign_attr->save();
        }

        $notify[] = ['success', 'New variant added successfully'];
        return $notify;
    }

    public function addProductVariantImages($id, $sellerId = 0)
    {
        $variant        = AssignProductAttribute::whereId($id)->with('product', 'productImages','productAttribute')->firstOrFail();
        if($sellerId && $variant->product->seller_id != $sellerId){
            abort(403);
        }

        $product_name   = $variant->product->name;
        $images         = [];

        foreach($variant->productImages as $key=>$image){
            $img['id'] = $image->id;
            $img['src'] = getImage(imagePath()['product']['path']. '/' . $image->image);
            $images[] = $img;
        }

        $data['images']         = $images;
        $data['product_name']   = $product_name;
        $data['variant']        = $variant;
        $data['pageTitle']      = "Add Variant Images";

        return $data;
    }

    public function saveProductVariantImages($request, $id, $sellerId = 0)
    {
        $variant = AssignProductAttribute::whereId($id)->with('product', 'productImages')->firstOrFail();

        if($sellerId && $variant->product->seller_id != $sellerId){
            abort(403);
        }

        $validation_rule = [
            'photos'                =>  'required_if:id,0|array|min:1',
            'photos.*'              =>  ['image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
        ];

        $request->validate($validation_rule);

        //Check Old Images
        $previous_images = $variant->productImages->pluck('id')->toArray();

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
                    $notify[] = ['error', 'Could not upload the Image.'];
                    return back()->withNotify($notify);
                }
                $productImage = new ProductImage();
                $productImage->product_id                       = $variant->product->id;
                $productImage->assign_product_attribute_id      = $id;
                $productImage->image                            = $product_img;
                $productImage->save();
            }
        }
        $notify[] = ['success', 'Images Added Successfully'];
        return $notify;
    }


    public function updateProductVariant($request, $id, $sellerId = 0){

        $variant        = AssignProductAttribute::findOrFail($id);

        if($sellerId){
            // Check Product Owner
            $this->checkProductOwner($variant->product_id, $sellerId);
        }

        $validationRule = $this->getUpdateValidationRule($variant);

        $request->validate($validationRule);

        if($variant->productAttribute->type == 1 || $variant->productAttribute->type == 2){

        }elseif($variant->productAttribute->type == 3){
            $old_img =(isset($variant->value))? $variant->value :'';

            if($request->hasFile('image')) {
                try {
                    $request->merge(['value' => uploadImage($request->image, imagePath()['attribute']['path'], imagePath()['attribute']['size'], $old_img)]);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload the Image.'];
                    return $notify;
                }
            }
        }
        $variant->name   = $request->name;
        $variant->value  = $request->value??'';
        $variant->extra_price  = $request->price;
        $variant->save();
        $notify[] = ['success', 'Product variant updated successfully'];
        return $notify;
    }

    public function deleteProductVariant($id, $sellerId = 0)
    {
        $variant = AssignProductAttribute::findOrFail($id);
        // Check Product Owner
        if($sellerId) $this->checkProductOwner($variant->product_id, $sellerId);

        if($variant->productAttribute->type == 3){
            $location = imagePath()['attribute']['path'];
            removeFile($location . '/' . $variant->value);
        }
        $variant->delete();

        $notify[] = ['success', 'Variant deleted successfully'];
        return $notify;
    }

    private function getUpdateValidationRule($variant)
    {
        $validationRule['name']     = 'required|string|max:50';
        $validationRule['price']    = 'required|numeric';
        if($variant->productAttribute->type == 1 || $variant->productAttribute->type == 2)
            $validationRule['value']='required';
        else
            $validationRule = ['image'   => ['required','image', new FileTypeValidate(['jpeg', 'jpg', 'png'])]];

        return $validationRule;
    }

    private function getValidationRule()
    {
        return [
            [
                'attr_type'     =>'required|integer|in:1,2,3',
                'attr_id'       =>'required',

                'text'          => 'required_if:attr_type,1|array|min:1',
                'text.*.name'   =>'required_with:text|max:50',
                'text.*.value'  =>'required_with:text|max:191',
                'text.*.price'  =>'required_with:text',

                'color'         => 'required_if:attr_type,2|array|min:1',
                'color.*.name'  =>'required_with:color|max:50',
                'color.*.value' =>'required_with:color|max:191',
                'color.*.price' =>'required_with:color',

                'img'           => 'required_if:attr_type,3|array|min:1',
                'img.*.name'    => 'required_with:img|max:50',
                'img.*.price'   => 'required_with:img',
                'img.*.value'   => ['required_with:img', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])]

            ],

            [
                'attr_id.required'      => 'Type field is required',
            ],[
                'attr_type.required'    => 'Type Field',
                'text.*.name'           => 'Name Field',
                'text.*.price'          => 'Price Field',

                'color.*.name'          => 'Name Field',
                'color.*.value'         => 'Value Field',
                'color.*.price'         => 'Price Field',

                'img.*.name'            => 'Name Field',
                'img.*.price'           => 'Price Field',
                'img.*.value'           => 'Image Field'
            ]

        ];
    }


    private function checkProductOwner($productId, $sellerId)
    {
        $product = Product::where('id', $productId)->first();
        if(!$product || $product->seller_id != $sellerId){
            abort(403);
        }
    }

}

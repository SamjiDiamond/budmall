<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function index()
    {
        $pageTitle     = "All Attrubute Types";
        $emptyMessage  = "No attribute yet";
        $query          = ProductAttribute::latest();
        $key            = request()->search??null;
        if($key){
            $query->where('name', 'LIKE' , '%'.$key.'%')->orWhere('name_for_user', 'LIKE' , '%'.$key.'%');
        }
        $attributes     = $query->paginate(getPaginate());
        return view('admin.products.attributes.index', compact('pageTitle', 'emptyMessage', 'attributes'));
    }

    public function store(Request $request, $id)
    {
        $validation_rule = [
            'name'          => 'required|max:100',
            'name_for_user' => 'required|max:100',
            'type'          => 'required|integer'
        ];

        $request->validate($validation_rule);

        if($id ==0){
            $product_attrubute = new ProductAttribute();
            $notify[] = ['success', 'Attribute Type Created Successfully'];
        }else{
            $product_attrubute = ProductAttribute::findOrFail($id);
            $notify[] = ['success', 'Attribute Type Updated Successfully'];
        }
        $product_attrubute->name            = $request->name;
        $product_attrubute->name_for_user   = $request->name_for_user;
        $product_attrubute->type            = $request->type;
        $product_attrubute->save();

        return redirect()->back()->withNotify($notify);
    }

    public function delete($id)
    {
        $product_attrubute = ProductAttribute::where('id', $id)->withTrashed()->first();

        $product_attrubute->delete();
        $notify[] = ['success', 'Product Attribute Deleted Successfully'];
        return redirect()->back()->withNotify($notify);

    }
}

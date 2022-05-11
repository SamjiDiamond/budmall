<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $pageTitle     = "All Brands";
        $emptyMessage  = "No brand yet";
        $brands         = Brand::with('products')
        ->orderBy('id', 'desc')
        ->paginate(getPaginate());
        return view('admin.brands.index', compact('pageTitle', 'emptyMessage', 'brands'));
    }

    public function trashed()
    {
        $pageTitle     = "Trashed Brands";
        $emptyMessage  = "No brand yet";
        $brands         = Brand::with('products')
        ->onlyTrashed()
        ->orderBy('id', 'desc')
        ->paginate(getPaginate());
        return view('admin.brands.index', compact('pageTitle', 'emptyMessage', 'brands'));
    }


    public function brandSearch(Request $request)
    {
        if ($request->search != null) {
            $emptyMessage  = 'No brand found';
            $search         = trim(strtolower($request->search));
            $brands         = Brand::with('products')
            ->where('name', 'like', "%$search%")
            ->orderByDesc('id', 'desc')
            ->paginate(getPaginate());
            $pageTitle     = 'Brand Search - ' . $search;
            return view('admin.brands.index', compact('pageTitle', 'emptyMessage', 'brands'));
        } else {
            return redirect()->route('admin.brand.all');
        }

    }

    public function brandTrashedSearch(Request $request)
    {
        if ($request->search != null) {
            $emptyMessage  = 'No brand found';
            $search         = trim(strtolower($request->search));
            $brands         = Brand::with('products')
            ->onlyTrashed()
            ->orderByDesc('id')
            ->where('name', 'like', "%$search%")->paginate(getPaginate());

            $pageTitle     = 'Trashed Brand Search - ' . $search;
            return view('admin.brands.index', compact('pageTitle', 'emptyMessage', 'brands'));
        } else {
            return redirect()->route('admin.brand.all');
        }

    }

    public function store(Request $request, $id)
    {
        $validation_rule = [
            'name'                      => 'required|string|max:50|unique:brands,name,'.$id,
            'meta_title'                => 'nullable|string|max:191',
            'meta_description'          => 'nullable|string|max:191',
            'meta_keywords'             => 'nullable|array',
            'meta_keywords.array.*'     => 'nullable|string',
        ];

        if($id ==0){
            $brand = new Brand();
            $validation_rule['image_input']  = ['required', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
            $notify[] = ['success', 'Brand Created Successfully'];
        }else{
            $brand = Brand::findOrFail($id);
            $validation_rule['image_input']  = ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
            $notify[] = ['success', 'Brand Updated Successfully'];
        }
        $request->validate($validation_rule,[
            'meta_keywords.array.*'     => 'All keywords',
            'image_input.required'      => 'Logo field is required'
        ]);

        if ($request->hasFile('image_input')) {

            try {
                $request->merge(['image' => $this->store_image($request->key, $request->image_input, $brand->logo)]);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Could not upload the Image.'];
                return back()->withNotify($notify);
            }
        }else{
            $request->merge(['image'=>$brand->logo]);
        }

        $brand->name             = $request->name;
        $brand->meta_title       = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        $brand->meta_keywords    = $request->meta_keywords;
        $brand->logo             = $request->image;
        $brand->save();

        return redirect()->back()->withNotify($notify);
    }

    public function delete($id)
    {
        $category = Brand::where('id', $id)->withTrashed()->first();

        if ($category->trashed()){
            $category->restore();
            $notify[] = ['success', 'Brand Restored Successfully'];
            return redirect()->back()->withNotify($notify);
        }else{
            $category->delete();
            $notify[] = ['success', 'Brand Deleted Successfully'];
            return redirect()->back()->withNotify($notify);
        }
    }

    public function setTop(Request $request)
    {
        $brand = Brand::findOrFail($request->id);
        if ($brand) {
            if ($brand->top == 1) {
                $brand->top = 0;
                $msg = 'Excluded from top brands';
            } else {
                $brand->top = 1;
                $msg = 'Included in top brands';
            }
            $brand->save();
            return response()->json(['success' => $msg]);
        }
    }

    protected function store_image($key, $image, $old_image = null)
    {
        $path = imagePath()['brand']['path'];
        $size = imagePath()['brand']['size'];
        return uploadImage($image, $path, $size, $old_image);
    }
}

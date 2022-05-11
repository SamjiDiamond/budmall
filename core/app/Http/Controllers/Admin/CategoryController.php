<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $pageTitle     = "All Categories";
        $emptyMessage  = "No category yet";
        $categories     = Category::with(['allSubcategories'])->where('parent_id', null)->get();
        return view('admin.categories.index', compact('pageTitle', 'emptyMessage', 'categories'));
    }

    public function trashed()
    {
        $pageTitle     = "Trashed Categories";
        $emptyMessage  = "No category yet";
        $categories    = Category::onlyTrashed()->with(['allSubcategories'])->paginate(getPaginate());
        return view('admin.categories.trashed', compact('pageTitle', 'emptyMessage', 'categories'));
    }

    public function categoryTrashedSearch(Request $request)
    {
        if ($request->search != null) {
            $emptyMessage  = 'No category found';
            $search         = trim(strtolower($request->search));
            $categories       = Category::onlyTrashed()->where('name', 'like', "%$search%")
            ->with(['subcategories'])
            ->orderByDesc('id')
            ->paginate(getPaginate());
            $pageTitle     = 'Trashed Category Search - ' . $search;
            return view('admin.categories.trashed', compact('pageTitle', 'emptyMessage', 'categories'));
        } else {
            return redirect()->route('admin.categories.trashed');
        }
    }

    public function store(Request $request, $id)
    {
        $validation_rule = [
            'parent_id'                 => 'nullable|integer|gt:0',
            'name'                      => 'required|string|max:50',
            'icon'                      => 'required|string|max:100',
            'meta_title'                => 'nullable|string|max:191',
            'meta_description'          => 'nullable|string|max:191',
            'meta_keywords'             => 'nullable|array',
            'meta_keywords.array.*'     => 'nullable|string',
            'top_category'              => 'nullable|integer|between:0,1',
            'special_category'          => 'nullable|integer|between:0,1',
            'filter_menu'               => 'nullable|integer|between:0,1'
        ];


        if($id ==0){
            $category = Category::where('name', $request->name)->where('parent_id', $request->parent_id)->first();

            if($category){
                return response()->json(['status'=>'error', 'message'=>'The Name Has Already Been Taken']);
            }

            $category = new Category();
            $validation_rule['image_input']  = ['required', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];

        }else{
            $category = Category::where('name', $request->name)->where('parent_id', $request->parent_id)->where('id', '!=', $id)->first();

            if($category){
                return response()->json(['status'=>'error', 'message'=>'The Name Has Already Been Taken']);
            }

            $category = Category::findOrFail($id);

            $validation_rule['image_input']  = ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
        }
        $validator = Validator::make($request->all(), $validation_rule,[
            'meta_keywords.array.*' => 'All keywords',
            'image_input.required'   => 'Image field is required'
        ]);


        if($validator->fails()) {
            return response()->json(['status'=>'error', 'message'=>$validator->errors()]);
        }

        if ($request->hasFile('image_input')) {
            try {
                $request->merge(['image' => $this->storeImage($request->image_input, $category->image)]);
            } catch (\Exception $exp) {
                $notify = ['error', ''];
                return response()->json(['status'=>'error', 'message'=>'Could not upload the image']);
            }
        }else{
            $request->merge(['image'=>$category->image]);
        }

        $category->name             = $request->name;
        $category->parent_id        = $request->parent_id;
        $category->icon             = $request->icon;
        $category->meta_title       = $request->meta_title;
        $category->meta_description = $request->meta_description;
        $category->meta_keywords    = $request->meta_keywords;
        $category->image            = $request->image;
        $category->is_top           = $request->top_category??0;
        $category->is_special       = $request->special_category??0;
        $category->in_filter_menu   = $request->filter_menu??0;
        $category->save();

        if($id ==0){
            $message = 'Category Added Successfully';
            $reload  = true;
        }else{
            $message = 'Category Updated Successfully';
            $reload  = false;
        }

        return response()->json(['status'=>'success', 'message'=>$message, 'reload' => $reload]);
    }

    public function delete($id)
    {
        $category = Category::where('id', $id)->withTrashed()->first();

        if ($category->trashed()){
            $category->restore();
            $notify[] = ['success', 'Category Restored Successfully'];
            return redirect()->back()->withNotify($notify);
        }else{
            $category->delete();
            $notify[] = ['success', 'Category Deleted Successfully'];
            return redirect()->back()->withNotify($notify);
        }
    }

    protected function storeImage($image, $old_image = null)
    {
        $path = imagePath()['category']['path'];
        $size = imagePath()['category']['size'];
        return uploadImage($image, $path, $size, $old_image);
    }

}

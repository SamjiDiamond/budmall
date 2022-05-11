<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ManageOffersController extends Controller
{
    public function index()
    {
        $pageTitle     = "All Offers";
        $emptyMessage  = "No offer yet";
        $offers         = Offer::paginate(getPaginate());
        return view('admin.offers.index', compact('pageTitle', 'emptyMessage', 'offers'));
    }

    public function create()
    {
        $pageTitle     = "Create New Offer";
        return view('admin.offers.create', compact('pageTitle'));
    }

    public function save(Request $request, $id)
    {
        $validation_rule = [
            "offer_name"    => 'required|string|max:50',
            "discount_type" => 'required|integer|between:1,2',
            "amount"        => 'required|numeric',
            "start_date"    => 'required|date|date_format:Y-m-d',
            "end_date"      => 'required|date|date_format:Y-m-d'
        ];

        $request->validate($validation_rule);

        if($id ==0){
            $offer = new Offer();
            $notify[] = ['success', 'Offer Created Successfully'];
        }else{
            $offer = Offer::findOrFail($id);
            $notify[] = ['success', 'Offer Updated Successfully'];
        }

        $offer->name                = $request->offer_name;
        $offer->discount_type       = $request->discount_type;
        $offer->amount              = $request->amount;
        $offer->start_date          = $request->start_date;
        $offer->end_date            = $request->end_date;

        $offer->save();

        if($id != 0){
            $offer->products()->sync($request->products);
        }else{
            $offer->products()->attach($request->products);
        }

        return redirect()->back()->withNotify($notify);
    }

    public function edit($id)
    {
        $offer         = Offer::whereId($id)->with(['products'=> function($q){
            return $q->whereHas('categories')->whereHas('brand');
        }])->firstOrFail();
        $pageTitle     = "Edit Offer";
        return view('admin.offers.create', compact('pageTitle', 'offer'));
    }

    public function delete($id)
    {
        $offer = Offer::where('id', $id)->delete();

        $notify[] = ['success', 'Offer Deleted Successfully'];
        return redirect()->back()->withNotify($notify);
    }

    public function prordutsForOffer(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }
        $products = Product::select('id','name')->where('name', 'like', "%$request->search%")->whereHas('categories')->whereHas('brand')->doesntHave('offers')->paginate($request->rows??5);

        $response = [];

        foreach($products as $product){
            $response[] = [
                "id"    => $product->id,
                "text"  => $product->name
            ];
        }

        return response()->json($response);

    }

    public function changeStatus (Request $request)
    {
        $offer = Offer::findOrFail($request->id);
        if ($offer) {
            if ($offer->status == 1) {
                $offer->status = 0;
                $msg = 'Offer deactivated successfully';
            } else {
                $offer->status = 1;
                $msg = 'Offer activated successfully';
            }
            $offer->save();
            return response()->json(['success' => $msg]);
        }
    }

}

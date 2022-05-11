<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deliverysetup;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodsController extends Controller
{

    public function calcindex()
    {
        $pageTitle         = 'Shipping Calculator Manager';
        $emptyMessage      = 'No shipping calculation created yet';
        $shipping_methods   = Deliverysetup::OrderBy('name','ASC')->paginate(getPaginate());

        return view('admin.shipping_method.calc', compact('pageTitle', 'emptyMessage', 'shipping_methods'));
    }

      public function createcalc()
    {
        $pageTitle = 'Create New Shipping Calculator';

        return view('admin.shipping_method.createcalc', compact('pageTitle'));
    }

     public function calcedit(Deliverysetup $id)
    {
        $shipping_method =  $id;
        $pageTitle = 'Edit Shipping Calculator';

        return view('admin.shipping_method.createcalc', compact('pageTitle', 'shipping_method'));
    }

     public function calcstore(Request $request, $id)
    {
        $validation_rule = [
            'name'          => 'required|string|max:191',
            'ppk'        => 'required|numeric',
            'weight'   => 'required|string|',
        ];
        $request->validate($validation_rule);

        if($id ==0){
            $sm = new Deliverysetup();
            $notify[] = ['success', 'Shipping Calculator Created Successfully'];
        }else{
            $sm = Deliverysetup::findOrFail($id);
            $notify[] = ['success', 'Shipping Calculator Updated Successfully'];
        }

        $sm->name         = $request->name;
        $sm->ppk       = $request->ppk;
        $sm->weight= $request->weight;
        $sm->save();

        return redirect()->back()->withNotify($notify);
    }

    public function calcdelete(Deliverysetup $id)
    {
        $id->delete();
        $notify[] = ['success', 'Shipping Calculator Deleted Successfully'];
        return redirect()->back()->withNotify($notify);
    }

    public function calcchangeStatus(Request $request)
    {
        $method = Deliverysetup::findOrFail($request->id);
        if ($method) {
            if ($method->status == 1) {
                $method->status = 0;
                $msg = 'Shipping Calculator has been deactivated';
            } else {
                $method->status = 1;
                $msg = 'Shipping Calculator has been activated';
            }
            $method->save();
            return response()->json(['success' => true, 'message' => $msg]);
        }
    }






    public function index()
    {
        $pageTitle         = 'Shipping Method Manager';
        $emptyMessage      = 'No shipping mehods created yet';
        $shipping_methods   = ShippingMethod::latest()->paginate(getPaginate());

        return view('admin.shipping_method.index', compact('pageTitle', 'emptyMessage', 'shipping_methods'));
    }

    public function create()
    {
        $pageTitle = 'Create New Shipping Method';

        return view('admin.shipping_method.create', compact('pageTitle'));
    }

    public function edit(ShippingMethod $id)
    {
        $shipping_method =  $id;
        $pageTitle = 'Edit Shipping Method';

        return view('admin.shipping_method.create', compact('pageTitle', 'shipping_method'));
    }

    public function store(Request $request, $id)
    {
        $validation_rule = [
            'name'          => 'required|string|max:191',
            'charge'        => 'required|numeric',
            'description'   => 'nullable|string|',
        ];
        $request->validate($validation_rule);

        if($id ==0){
            $sm = new ShippingMethod();
            $notify[] = ['success', 'Shipping Method Created Successfully'];
        }else{
            $sm = ShippingMethod::findOrFail($id);
            $notify[] = ['success', 'Shipping Method Updated Successfully'];
        }

        $sm->name         = $request->name;
        $sm->charge       = $request->charge;
        $sm->shipping_time= $request->deliver_in;
        $sm->description  = $request->description;
        $sm->save();

        return redirect()->back()->withNotify($notify);
    }

    public function delete(ShippingMethod $id)
    {
        $id->delete();
        $notify[] = ['success', 'Shipping Method Deleted Successfully'];
        return redirect()->back()->withNotify($notify);
    }

    public function changeStatus(Request $request)
    {
        $method = ShippingMethod::findOrFail($request->id);
        if ($method) {
            if ($method->status == 1) {
                $method->status = 0;
                $msg = 'Shipping method has been deactivated';
            } else {
                $method->status = 1;
                $msg = 'Shipping method has been activated';
            }
            $method->save();
            return response()->json(['success' => true, 'message' => $msg]);
        }
    }

}

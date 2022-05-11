@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <form action="{{ route('admin.shipping.calculator.store', isset($shipping_method)?$shipping_method->id:0) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card-body">

                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label class="font-weight-bold">@lang('Name')</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" placeholder="@lang('Enter Shipping Method Name')" value="{{@$shipping_method->name}}" name="name"/>
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label class="font-weight-bold">@lang('Weight')</label>
                                </div>
                                <div class="input-group col-md-10">
                                   <input type="text" class="form-control numeric-validation" placeholder="@lang('Enter Weight Category')" value="{{@$shipping_method->weight}}" name="weight"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">KG</span>
                                      </div>
                                </div>
                            </div>

                             <div class="form-group row">
                                <div class="col-md-2">
                                    <label class="font-weight-bold">@lang('Price/KM')</label>
                                </div>
                                <div class="input-group col-md-10">
                                    <input type="number" class="form-control numeric-validation" placeholder="@lang('Enter Amount of Charge')" value="{{@$shipping_method->ppk}}" name="ppk"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">{{$general->cur_sym}}</span>
                                      </div>
                                </div>
                            </div>


                </div>
                <div class="card-footer">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-block btn--primary mr-2">{{isset($shipping_method)?trans('Update'):trans('Add')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.shipping.calculator') }}" class="btn btn-sm btn--primary box--shadow1 text--small">
    <i class="la la-fw la-backward"></i> @lang('Go Back')
</a>
@endpush



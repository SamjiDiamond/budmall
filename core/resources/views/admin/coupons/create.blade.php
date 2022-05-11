@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <form action="{{ route('admin.coupon.store', $coupon->id??0) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('General Information')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold" for="coupon_name">@lang('Coupon Name') </label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <input type="text" class="form-control" id="coupon_name" name="coupon_name" value="{{ isset($coupon)?$coupon->coupon_name:old('coupon_name') }}" placeholder="@lang('Type Here')..." />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold" for="coupon_code">@lang('Coupon Code')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <input type="text" class="form-control" id="coupon_code" name="coupon_code" value="{{ isset($coupon)?$coupon->coupon_code:old('coupon_code') }}" placeholder="@lang('Type Here')...">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold" for="discount_type">@lang('Discount Type')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <select class="form-control" id="discount_type" name="discount_type">
                                        <option selected value="">@lang('Select One')</option>
                                        <option value="1">@lang('Fixed')</option>
                                        <option value="2">@lang('Percentage')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold" for="amount">@lang('Amount')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <div class="input-group">
                                        <input type="number" class="form-control numeric-validation" id="amount" name="amount" value="{{ isset($coupon)?$coupon->coupon_amount:old('amount') }}" placeholder="@lang('Type Here')...">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2">@if(isset($coupon)) {{ $coupon->type==1?$general->cur_sym:'%' }} @else {{ $general->cur_text }} @endif</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold" for="start_date">@lang('Start Date')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <input type="text" name="start_date" id="start_date" class="datepicker-here form-control" data-language='en' data-date-format="yyyy-mm-dd" data-position='bottom left' value="{{ isset($coupon)?$coupon->start_date:old('start_date') }}" placeholder="@lang('Select Date')" autocomplete="off" >

                                    <small class="form-text text-muted"> <i class="fa fa-info-circle" aria-hidden="true"></i> @lang('Year-Month-Date')</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold" for="end_date">@lang('End Date')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <input type="text" name="end_date" id="end_date" class="datepicker-here form-control" data-language='en' data-date-format="yyyy-mm-dd" data-position='bottom left' value="{{ isset($coupon)?$coupon->end_date:old('end_date') }}" placeholder="@lang('Select Date')" autocomplete="off">
                                    <small class="form-text text-muted"> <i class="fa fa-info-circle" aria-hidden="true"></i> @lang('Year-Month-Date')</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold" for="description">@lang('Description')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <textarea class="form-control" name="description" id="description" rows="3">{{ isset($coupon)?$coupon->description:old('$coupon->description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card my-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Usage Restrictions')</h5>
                </div>
                <div class="card-body has-select2">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row  ">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold" for="minimum_spend">@lang('Minimum Spend')</label>
                                </div>

                                <div class="col-lg-10 col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2"> {{ $general->cur_sym }}</span>
                                        </div>
                                        <input type="number" class="form-control numeric-validation" id="minimum_spend" name="minimum_spend" value="{{ isset($coupon)?$coupon->minimum_spend:old('minimum_spend') }}" placeholder="@lang('Type Here')..." />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold" for="maximum_spend">@lang('Maximum Spend')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2"> {{ $general->cur_sym }}</span>
                                        </div>
                                        <input type="number" class="form-control numeric-validation" id="maximum_spend" name="maximum_spend" value="{{ isset($coupon)?$coupon->maximum_spend:old('maximum_spend') }}" placeholder="@lang('Type Here')...">
                                    </div>
                                </div>
                            </div>
                           <!-- <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold" for="categories">@lang('Categories')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <select class="select2-multi-select form-control" name="categories[]" id="categories" multiple>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">@lang($category->name)</option>
                                            @php
                                                $prefix = '--'
                                            @endphp
                                            @foreach ($category->allSubcategories as $subcategory)
                                                @include('admin.partials.subcategories', ['subcategory' => $subcategory, 'prefix'=>$prefix])
                                                <option value="{{ $subcategory->id }}">
                                                    {{ $prefix }}@lang($subcategory->name)
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label class="font-weight-bold">@lang('Select Product')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">

                                    <select class="form-control" id="products" name="products[]" multiple>
                                        @if(request()->routeIs('admin.coupon.edit'))
                                            @foreach($coupon->products as $product)
                                            <option value="{{ $product->id }}">{{ __($product->name) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>-->
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label for="usage_limit_per_coupon" class="font-weight-bold">@lang('Usage Limit Per Coupon')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <input name="usage_limit_per_coupon" class="form-control integer-validation" id="usage_limit_per_coupon" value="{{ isset($coupon)?$coupon->usage_limit_per_coupon:old('usage_limit_per_coupon') }}" type="number" placeholder="@lang('Type Here')...">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-2 col-md-3">
                                    <label for="usage_limit_per_customer" class="font-weight-bold">@lang('Usage Limit Per Customer')</label>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <input name="usage_limit_per_customer" class="form-control numeric-validation" id="usage_limit_per_customer" value="{{ isset($coupon)?$coupon->usage_limit_per_user:old('usage_limit_per_customer') }}" type="number" placeholder="@lang('Type Here')...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-block btn--primary">@lang('Add')</button>
        </form>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.coupon.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-backward"></i>@lang('Go Back')</a>
@endpush

@push('script')
    <script>
        'use strict';
        (function($){
            var dropdownParent = $('.has-select2');
            $('.select2-basic, .select2-multi-select').select2({
                dropdownParent: dropdownParent,
                closeOnSelect: false
            });

            $('#discount_type').on('change', function () {
                var val = this.value;
                if(val == 1) {
                    $('#basic-addon2').text(`{{ $general->cur_sym }}`);
                }else{
                    $('#basic-addon2').text(`%`);
                }
            });

            @if(request()->routeIs('admin.coupon.edit'))
                var categories = @json($coupon->categories->pluck('id'));

                var products = @json($coupon->products->pluck('id'));
                $('#products').val(products);

                $('#discount_type').val({{ $coupon->discount_type }});
                if($('#discount_type').val() == 1) {
                    $('#basic-addon2').text(`{{ $general->cur_sym }}`);
                }else{
                    $('#basic-addon2').text(`%`);
                }
                $('#categories').val(categories);
                $('.select2-multi-select').select2({
                    dropdownParent: dropdownParent,
                    closeOnSelect: false
                });

                $('#start_date').datepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    autoApply: true,
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                }).data('datepicker').selectDate(new Date($('#start_date').val()));

                $('#end_date').datepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    autoApply: true,
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                }).data('datepicker').selectDate(new Date($('#end_date').val()));
            @else
            $('.datepicker-here').datepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoApply: true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            @endif

            $('#products').select2({
                ajax: {
                    url: '{{ route("admin.products_for_coupon") }}',
                    type: "get",
                    dataType: 'json',
                    delay: 1000,
                    data: function (params) {

                        return {
                            search: params.term,
                            page : params.page,// Page number, page breaks
                            rows : 5// How many rows are displayed per page
                        };
                    },
                    processResults: function (response, params) {
                        params.page = params.page || 1;
                        return {
                            results: response,
                            pagination : {
                                more : params.page < response.length
                            }
                        };
                    },
                    cache: false
                },
                dropdownParent: dropdownParent
            });
        })(jQuery)

    </script>
@endpush

@push('script-lib')
<script type="text/javascript" src="{{ asset('assets/dashboard/js/vendor/datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/dashboard/js/vendor/datepicker.en.js') }}"></script>
@endpush

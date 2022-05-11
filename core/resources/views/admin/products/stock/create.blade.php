@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12 mb-4">
        <div class="card-body bg--10 py-2">
            <h4 class="text-white">@lang('Product Name') : {{ __($product->name) }}</h4>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="card">
                <div class="card-body p-0">
                        @if($data && $product->has_variants)
                            <div class="table-responsive--md table-responsive">
                                <table class="table table--light">

                                    <thead>
                                        <tr>
                                            <th>@lang('S.N.')</th>
                                            <th>@lang('Variant')</th>
                                            <th>@lang('SKU')</th>
                                            <th>@lang('Quantity')</th>
                                            <th class="text-center">@lang('Action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($data as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ __($item['combination']) }}</td>
                                            <td>{{ @$item['sku'] }}</td>
                                            <td class="text-center">
                                                <span class="text--small badge @if($item['quantity'] ==0) badge--danger @elseif($item['quantity'] < 10) badge--warning @else badge--success @endif font-weight-normal">{{ $item['quantity']??0 }}</span>
                                            </td>

                                            <td class="text-center">
                                                <a href="javascript:void(0)" data-sku="{{ $item['sku'] }}" data-attributes="{{ $item['attributes'] }}" class="icon-btn btn--primary mr-1 editBtn" data-toggle="tooltip" data-title="@lang('Update Inventory')">
                                                    <i class="la la-pencil-alt"></i>
                                                </a>

                                                <a href="{{route('admin.products.stock.log', $item['stock_id']??0)}}" class="icon-btn btn--info" data-toggle="tooltip" data-title="@lang('See Logs')"><i class="fas fa-list"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif(!$data && $product->has_variants)
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light">
                                <tr>
                                    <td colspan="100%" class="text-center"><h3 class="text--danger">@lang('You did\'t add any variant for this product yet. ')</h3>
                                        <a class="btn btn--dark mt-3" href="{{ route('admin.products.variant.store', [$product->id]) }}">@lang('Add Variant')</a>
                                    </td>
                                </tr>

                            </table>
                        </div>
                        @else
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light">
                                <tr>
                                    <th>@lang('SKU')</th>
                                    <th>@lang('Quantity')</th>
                                    <th class="text-center">@lang('Action')</th>
                                </tr>
                                <tr>
                                    <td data-label="@lang('SKU')">{{$product->sku}}</td>
                                    <td data-label="@lang('Quantity')">

                                        {{ sprintf('%02d', \App\Models\ProductStock::showAvailableStock($product->id, $attr_val=null))}}
                                    </td>

                                    <td class="text-center">
                                        <a href="javascript:void(0)" data-sku="{{ $product->sku }}" data-attributes="0" class="icon-btn btn--primary mr-1 editBtn" data-toggle="tooltip" data-title="@lang('Update Inventory')">
                                            <i class="la la-pencil-alt"></i>
                                        </a>

                                        <a href="{{route('admin.products.stock.log', $product->stocks[0]??0)}}" class="icon-btn btn--info" data-toggle="tooltip" data-title="@lang('See Logs')"><i class="fas fa-list"></i></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="editModal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Update Inventory')</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>

                    <form action="{{route('admin.products.stock.add', $product->id)}}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="attr" value="">
                            <div class="form-group sku">
                                <label for="">@lang('SKU')</label>
                                <input type="text" name="sku" id="" class="form-control" placeholder="@lang('Type Here').."/>

                                <span class="text--small text--info"> <i class="fas fa-info-circle"> @lang('If you want to update only SKU keep the Quantity field 0')</i></span>
                            </div>

                            <label>@lang('Quantity')</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select class="btn btn--dark group-select-left" name="type">
                                    <option value="1">+</option>
                                    <option value="2">-</option>
                                    </select>
                                </div>
                                <input type="text" class="form-control integer-validation" name="quantity" placeholder="@lang('Type Here')...">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn--primary">@lang('Save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('breadcrumb-plugins')
<a href="{{ route('admin.products.all') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-backward"></i>@lang('Go Back')</a>
@endpush

@push('script')
<script>
    'use strict';
    (function($){

        $('.editBtn').on('click', function(){
            var modal       = $('#editModal');
            var attrArray   = $(this).data('attributes');
            modal.find('input[name=sku]').val($(this).data('sku'));
            if(attrArray != 0){
                modal.find('input[name=attr]').val(JSON.stringify(attrArray));
            }else{
                modal.find('.sku').hide();
                modal.find('input[name=attr]').remove();

            }
            modal.modal('show');
        });
    })(jQuery)

</script>
@endpush

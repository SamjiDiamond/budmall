@extends($activeTemplate .'layouts.frontend')

@section('content')

<!--section start-->
<div class="cart-section padding-bottom padding-top">
    <div class="container">
        <table class="cart-table section-bg  mb-0">
            <thead>
            <tr class="table-head">
                <th scope="col">@lang('Product')</th>
                <th scope="col">@lang('Availability')</th>
                <th scope="col">@lang('Price')</th>
                <th scope="col">@lang('Action')</th>
            </tr>
            </thead>

            <tbody class="cart-table-body">
            @forelse ($wishlist_data as $item)
                @php
                    if($item->activeOffer)
                        $discount = calculateDiscount($item->product->activeOffer->amount, $item->product->activeOffer->discount_type, $item->product->base_price);
                    else $discount = 0;

                    $price = $item->product->base_price - $discount;

                    $stock_qty  = $item->product->stocks->sum('quantity')
                @endphp
                <tr>
                    <td data-label="@lang('Product')">
                        <a href="{{route('product.detail', ['id'=>$item->product->id, 'slug'=>slug($item->product->name)])}}" class="cart-item mw-100">
                            <div class="cart-img">
                                <img src="{{ getImage(imagePath()['product']['path'].'/'.@$item->product->main_image, imagePath()['product']['size']) }}" alt="@lang('cart')">
                            </div>
                            <div class="cart-cont">
                                <h6 class="title">{{ __($item->product->name) }}</h6>
                            </div>
                        </a>
                    </td>

                    <td data-label="@lang('Availability')">

                        @if($item->product->track_inventory)
                            @if($stock_qty > 0)
                            <i class="fas fa-check text-success"></i>
                            @else
                            <i class="fas fa-times text-danger"></i>
                            @endif
                        @else
                            <i class="fas fa-check text-success"></i>
                        @endif

                    </td>

                    <td data-label="@lang('Price')">
                        {{$general->cur_sym}}{{getAmount($price, 2)}}
                    </td>

                    <td data-label="@lang('Action')">
                        <span class="edit remove-wishlist" data-id="{{$item->id}}" data-page="1">
                            <i class="las la-trash"></i>
                        </span>

                        <a href="javascript:void(0)" data-product="{{$item->product_id}}"  data-id="{{$item->id}}" class="quick-view-btn">
                            <span class="edit add-cart">
                                <i class="las la-cart-plus"></i>
                            </span>
                        </a>
                    </td>
                </tr>

                @empty

                <tr>
                    <td colspan="100%">
                        {{__($emptyMessage)}}
                    </td>
                </tr>
            </tbody>
            @endforelse

        </table>

        <div class="cart-total section-bg rounded--5">

            <div class="d-flex flex-wrap align-items-center">
                <div class="apply-coupon-code">
                    @if($wishlist_data->count()>1)
                        <a href="javascript:void(0)" class="btn btn-danger remove-all-btn" data-label="@lang('Are you sure to remove all product?')" data-id="0" data-toggle="modal" data-target="#deleteModal">@lang('Remove All')</a>
                    @endif
                </div>

                <div class="checkout ml-auto">
                    <a href="{{route('home')}}" class="theme cmn--btn">@lang('Continue Shopping')</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--section end-->

{{-- REMOVE METHOD MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered d-flex justify-content-center"" role="document">

        <div class="modal-content">
            <div class="modal-header bg-3">
                <h5 class="modal-title text-white" id="deleteModalLabel"></h5>
            </div>
            <div class="modal-body text-center text-danger">
                <i class="far fa-times-circle fa-4x animated rotateIn"></i>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm bg-3 text-white h-unset" data-dismiss="modal">@lang('No')</button>
                <button type="button" class="btn btn-sm btn-danger remove-wishlist h-unset" data-id="0" data-dismiss="modal">@lang('Yes')</button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <li><a href="{{route('home')}}">@lang('Home')</a></li>
@endpush

@push('meta-tags')
    @include('partials.seo')
@endpush

@push('script')
    <script>
        'use strict';
        (function($){
            $('#deleteModal').on('show.bs.modal', function (e) {
                var label = $(e.relatedTarget).data('label');
            $('.modal-title').text(label);
                var id = $(e.relatedTarget).data('id');
            });

        })(jQuery)
    </script>
@endpush

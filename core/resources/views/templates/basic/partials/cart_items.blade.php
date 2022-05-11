
<h4 class="title">@lang('Your Cart')</h4>
@forelse ($data as $item)

<div class="single-product-item">
    <div class="thumb">
        <img src="{{ getImage(imagePath()['product']['path'].'/'.@$item->product->main_image, imagePath()['product']['size']) }}" alt="@lang('shop')">
    </div>
    <div class="content">
        <h4 class="title"><a class="cl-white" href="{{route('product.detail', ['id'=>$item->product->id, 'slug'=>slug($item->product->name)])}}">{{ shortDescription(__($item->product->name), 35) }}</a></h4>
        <div class="price">
            <span class="pprice">
                {{systemCurrency()['symbol']}}@php
                    if($item->attributes != null){
                        $s_price = App\Models\AssignProductAttribute::priceAfterAttribute($item->product, $item->attributes);
                        echo getAmount($s_price/systemCurrency()['rate'], 2);
                    }else{
                        if($item->product->offer && $item->product->offer->activeOffer){
                            $s_price = $item->product->base_price - calculateDiscount($item->product->offer->activeOffer->amount, $item->product->offer->activeOffer->discount_type, $item->product->base_price);
                        }else{
                            $s_price = $item->product->base_price;
                        }
                        echo getAmount($s_price/systemCurrency()['rate'], 2);
                    }
                @endphp
                x {{ $item->quantity }}
            </span>
        </div>
        <div class="text-white">
            @if($item->attributes != null)
                @php echo App\Models\AssignProductAttribute::cartAttributesShow($item->attributes) @endphp
            @endif
        </div>

        <a href="javascript:void(0)" class="remove-item-button remove-cart-item" data-id="{{$item->id}}" data-pid="{{$item->product->id}}"><i class="la la-times"></i></a>
    </div>
</div>
@empty
<div class="single-product-item no_data">
    <div class="no_data-thumb w-50 ml-auto mr-auto mb-4 text-white">
        <i class="la la-shopping-basket la-10x"></i>
    </div>
    <h6 class="cl-white">{{__($emptyMessage)}}</h6>
</div>
@endforelse

@if($data->count()>0)
<div class="btn-wrapper text-center">
    <a href="{{route('shopping-cart')}}" class="qv-btn bg btn-block">@if($more> 0)
        @lang('And') {{$more}} @lang('More')
    @else
        @lang('View Cart')
    @endif</a>
</div>
@endif

@if($subtotal > 0)
    <div class="d-flex justify-content-between mt-3 text-white">
        <span class="text-white"> @lang('Subtotal') </span>
        <span class="text-white">{{ systemCurrency()['symbol'] }}{{ getAmount($subtotal/systemCurrency()['rate'], 2) }}</span>

    </div>
    @if($coupon)

    <div class="coupon-wrapper">
        <div class="d-flex mt-1 text-white">
            <span class="mr-2 text-danger remove-coupon"><i class="la la-times-circle"></i></span>
            <span>@lang('Coupon') (<b class="couponCode1">{{$coupon['code']}}</b>) </span>
            <div class="ml-auto">
                <span class="amount">{{systemCurrency()['symbol']}}<span class="couponAmount"> {{ getAmount($coupon['amount']/systemCurrency()['rate'], 2) }}</span> </span>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-1 text-white border-top-1">
            <span class="text-white"> @lang('Total Amount') </span>
            <span class="text-white">{{ systemCurrency()['symbol'] }}{{ getAmount($subtotal/systemCurrency()['rate'] - $coupon['amount']/systemCurrency()['rate'], 2) }}</span>
        </div>
    </div>

    @endif
@endif



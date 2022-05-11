<div class="row mb-30-none page-main-content" id="grid-view">
    @foreach ($products as $item)
    @php
        if($item->offer && $item->offer->activeOffer){
            $discount = calculateDiscount($item->offer->activeOffer->amount, $item->offer->activeOffer->discount_type, $item->base_price);
        }else $discount = 0;
        $wCk = checkWishList($item->id);
        $cCk = checkCompareList($item->id);
    @endphp


    <div class="col-lg-4 col-sm-6 grid-control col-6 mb-30">
        <div class="product-item-2 m-0">
            <div class="product-item-2-inner wish-buttons-in">
                <div class="product-thumb">
                    <ul class="wish-react">
                        <li>
                            <a href="javascript:void(0)" title="@lang('Add To Wishlist')" class="add-to-wish-list {{$wCk?'active':''}}" data-id="{{$item->id}}"><i class="lar la-heart"></i></a>
                        </li>
                        <li>

                            <a href="javascript:void(0)" title="@lang('Compare')" class="add-to-compare {{$cCk?'active':''}}" data-id="{{$item->id}}"><i class="las la-sync-alt"></i></a>
                        </li>
                    </ul>
                    <a href="{{route('product.detail', ['id'=>$item->id, 'slug'=>slug($item->name)])}}">
                        <img src="{{ getImage(imagePath()['product']['path'].'/'.@$item->main_image, imagePath()['product']['size']) }}" alt="@lang('flash')">
                    </a>
                </div>
                <div class="product-content">
                    <div class="product-before-content">
                        <h6 class="title">
                            <a href="{{route('product.detail', ['id'=>$item->id, 'slug'=>slug($item->name)])}}">{{ $item->name }}</a>
                        </h6>
                        <div class="single_content">
                            <p>@php echo __($item->summary) @endphp</p>
                        </div>
                        <div class="ratings-area justify-content-between">
                            <div class="ratings">
                                @php echo displayAvgRating($item->reviews) @endphp
                            </div>
                            <span class="ml-2 mr-auto">({{ $item->reviews->count() }})</span>
                            <div class="price">
                                @if($discount > 0)
                                    {{systemCurrency()['symbol'] }}{{ getAmount($item->base_price - $discount/systemCurrency()['rate'], 2) }}
                                    <del>{{ getAmount($item->base_price, 2) }}</del>
                                @else
                                    {{systemCurrency()['symbol'] }}{{ getAmount($item->base_price/systemCurrency()['rate'], 2) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="product-after-content">
                        <button data-product="{{$item->id}}" class="qv-btn btn-sm quick-view-btn">
                            @lang('View')
                        </button>
                        <div class="price">
                            @if($discount > 0)
                            {{systemCurrency()['symbol'] }}{{ getAmount($item->base_price - $discount/systemCurrency()['rate'], 2) }}
                            <del>{{ getAmount($item->base_price, 2) }}</del>
                            @else
                            {{systemCurrency()['symbol'] }}{{ getAmount($item->base_price/systemCurrency()['rate'], 2) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if($products->count() == 0)
    <div class="col-lg-12 mb-30">
        @include($activeTemplate.'partials.empty_page', ['message' => __($emptyMessage)])
    </div>
    @endif
</div>

{{$products->appends(['perpage'=>@$perpage, 'brand'=>@$brand, 'category_id'=>@$category_id, 'min'=>@$min, 'max'=>@$max])->links()}}


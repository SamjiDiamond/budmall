<!-- Featured Section Starts Here -->
<div class="featured-section padding-bottom-half padding-top-half oh">
    <div class="container">
        <div class="section-header-2">
            <h3 class="title">@lang('Our Latest Products')</h3>
        </div>
        <div class="row g-4">
            @foreach ($latestProducts as $item)
            @php
                if($item->offer && $item->offer->activeOffer){
                    $discount = calculateDiscount($item->offer->activeOffer->amount, $item->offer->activeOffer->discount_type, $item->base_price);
                }else $discount = 0;
                $wCk = checkWishList($item->id);
                $cCk = checkCompareList($item->id);
            @endphp
            <div class="col-sm-6 col-xl-3 col-6">
                <div class="product-item-2 m-0">
                    <div class="product-item-2-inner wish-buttons-in">
                        <ul class="wish-react">
                            <li>
                                <a href="javascript:void(0)" title="@lang('Add To Wishlist')" class="add-to-wish-list {{$wCk?'active':''}}" data-id="{{$item->id}}"><i class="lar la-heart"></i></a>
                            </li>
                            <li>

                                <a href="javascript:void(0)" title=" @lang('Compare')" class="add-to-compare {{$cCk?'active':''}}" data-id="{{$item->id}}"><i class="las la-sync-alt"></i></a>
                            </li>
                        </ul>
                        <div class="product-thumb">
                            <a href="{{route('product.detail', ['id'=>$item->id, 'slug'=>slug($item->name)])}}">
                                <img src="{{ getImage(imagePath()['product']['path'].'/'.@$item->main_image, imagePath()['product']['size']) }}" alt="@lang('flash')">
                            </a>
                        </div>
                        <div class="product-content">
                            <div class="product-before-content">
                                <h6 class="title">
                                    <a href="{{route('product.detail', ['id'=>$item->id, 'slug'=>slug($item->name)])}}">{{ __($item->name) }}</a>
                                </h6>
                                <div class="ratings-area justify-content-between">
                                    <div class="ratings">
                                        @php echo displayAvgRating($item->reviews) @endphp
                                    </div>

                                    <span class="ml-2 mr-auto">({{ $item->reviews->count() }})</span>
                                    <div class="price">
                                        @if($discount > 0)
                                        {{ systemCurrency()['symbol'] }}{{ getAmount($item->base_price - $discount/systemCurrency()['rate'], 2) }}
                                        <del>{{ getAmount($item->base_price, 2) }}</del>
                                        @else
                                        {{ systemCurrency()['symbol'] }}{{ getAmount($item->base_price/systemCurrency()['rate'], 2) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="product-after-content">
                                <button data-product="{{$item->id}}" class="cmn-btn btn-sm quick-view-btn">
                                    @lang('View')
                                </button>
                                <div class="price">
                                    @if($discount > 0)
                                    {{ systemCurrency()['symbol'] }}{{ $item->base_price - $discount/systemCurrency()['rate'] }}
                                    <del>{{ getAmount($item->base_price, 2) }}</del>
                                    @else
                                    {{ systemCurrency()['symbol'] }}{{ getAmount($item->base_price/systemCurrency()['rate'], 2) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</div>
<!-- Featured Section Ends Here -->

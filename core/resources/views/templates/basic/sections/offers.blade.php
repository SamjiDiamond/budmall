
@foreach ($offers as $offer)

    <section class="hot-deal-section padding-bottom-half padding-top-half overflow-hidden border-bottom">
        <div class="container">
            <div class="section-header-2 mb-3">
                <h4 class="title">{{ __($offer->name) }}</h4>
                <span class="btn--base btn-sm"> @lang('Ends') {{ diffForHumans($offer->end_date) }}</span>
            </div>
            <div class="row g-2">
                @foreach ($offer->products as $item)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xxl-8-item  col-6">
                    <a class="product__item" href="{{route('product.detail', ['id'=>$item->id, 'slug'=>slug($item->name)])}}">
                        <div class="product__thumb">
                            <img src="{{ getImage(imagePath()['product']['path'].'/'.@$item->main_image, imagePath()['product']['size']) }}" alt="products">
                        </div>
                        <div class="product__content">
                            <h6 class="d-price">{{ systemCurrency()['symbol'] }}{{calculateDiscount($offer->amount,$offer->discount_type,$item->base_price/systemCurrency()['rate'])}}</h6>
                            <del class="m-price">{{ systemCurrency()['symbol'] }}{{getAmount($item->base_price/systemCurrency()['rate'])}}</del>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
@endforeach

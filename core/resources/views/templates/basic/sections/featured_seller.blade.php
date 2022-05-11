<!-- Single Product Section Starts Here -->
<section class="single-product-section padding-bottom-half padding-top-half overflow-hidden border-bottom">
    <div class="container">
        <div class="section-header-2">
            <h4 class="title">@lang('Our Seller')</h4>
            <a class="btn--base btn-sm" href="{{route('all.sellers')}}">@lang('View All')</a>
        </div>
        <div class="row g-2">
            @foreach ($featuredSeller as $seller)
            <div class="col-6 col-sm-4 col-md-3 col-xxl-8-item">
                <a class="d-block shop-item" href="{{route('seller.details',[$seller->id,slug($seller->shop->name)])}}">
                    <div class="thumb mb-10 oh rounded">
                        <img src="{{ getImage(imagePath()['seller']['shop_logo']['path'].'/'.@$seller->shop->logo, imagePath()['seller']['shop_logo']['size']) }}" alt="products">
                    </div>
                    <h6 class="line-limitation-2 text-center">{{$seller->shop->name}}</h6>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Single Product Section Ends Here -->

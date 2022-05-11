<section class="shop-section padding-bottom-half padding-top-half overflow-hidden border-bottom">
    <div class="container">
        <div class="section-header-2">
            <h4 class="title">@lang('Top Brands')</h4>

            <span class="">
                <a class="btn--base btn-sm" href="{{route('brands')}}">@lang('View All')</a>
            </span>

        </div>

        <div class="row g-2">
            @foreach ($topBrands as $brand)
            <div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-2 col-xxl-8-item">
                <a href="{{route('products.brand',[$brand->id,slug($brand->name)])}}" class="d-block shop-item">
                    <div class="thumb mb-10 oh rounded">
                        <img src="{{getImage(imagePath()['brand']['path'].'/'.$brand->logo,imagePath()['brand']['size'])}}" class="w-100" alt="brand-logo">
                    </div>
                    <span class="line-limitation-2 text-center">{{$brand->name}}</span>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

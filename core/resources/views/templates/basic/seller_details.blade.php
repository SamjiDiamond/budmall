
@extends($activeTemplate .'layouts.frontend')


@section('content')
    <!-- Vendor Sections Here -->
    <section class="vendor-profile padding-bottom-half">
        <div class="container">
            <div class="vendor__single__item">
                <div class="vendor__single__item-thumb">
                    <img src="{{getImage(imagePath()['seller']['shop_cover']['path'].'/'.$seller->shop->cover,imagePath()['seller']['shop_cover']['size'])}}" alt="vendor">
                </div>
                <div class="vendor__single__item-content">
                    <div class="vendor__single__author">
                        <div class="thumb">
                            <img src="{{getImage(imagePath()['seller']['shop_logo']['path'].'/'.$seller->shop->logo,imagePath()['seller']['shop_logo']['size'])}}" alt="vendor">
                        </div>
                        <div class="content">
                            <div class="title__area">
                                <h4 class="title">{{$seller->shop->name}}</h4>
                                @if (!empty($seller->shop->social_links))
                                @php
                                    $socials = json_decode(json_encode($seller->shop->social_links));
                                @endphp
                                <ul class="social__icons">
                                    @foreach ($socials as $item)
                                    <li>
                                        <a target="_blank" href="{{$item->link}}">@php echo $item->icon; @endphp</a>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            <div class="content-area">
                                <ul>
                                    <li>
                                        <i class="las la-map-marker-alt">{{$seller->shop->address}}</i>
                                    </li>
                                    <li>
                                      <i class="las la-phone"></i> {{$seller->shop->phone}}
                                    </li>
                                    <li>
                                       <i class="las la-envelope"></i>{{$seller->email}}
                                    </li>
                                    <li>
                                       <i class="las la-door-open"></i>@lang('Opens at :'){{showDateTime($seller->shop->opens_at,'h:i a')}}
                                    </li>
                                    <li>
                                        <i class="las la-door-closed"></i>@lang('Closed at :'){{showDateTime($seller->shop->closed_at,'h:i a')}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Vendor Sections Here -->


    <!-- Vendor Products Sections Here -->
    <section class="vendor-products padding-bottom">
        <div class="container">
            <div class="section-header-2">
                <h4 class="title mr-auto">@lang('Seller Products')</h4>
            </div>
            <div class="row g-2 justify-content-center">
                @forelse ($products as $item)
                <div class="col-lg-3 col-sm-6 grid-control mb-30">
                    <div class="product-item-2 m-0">
                        <div class="product-item-2-inner wish-buttons-in">
                            <div class="product-thumb">

                                <a href="{{route('product.detail', ['id'=>$item->id, 'slug'=>slug($item->name)])}}">
                                    <img src="{{ getImage(imagePath()['product']['path'].'/thumb_'.@$item->main_image, imagePath()['product']['size']) }}" alt="@lang('flash')">
                                </a>
                            </div>
                            <div class="product-content">
                                <div class="product-before-content">
                                    <h6 class="title">
                                        <a href="{{route('product.detail', ['id'=>$item->id, 'slug'=>slug($item->name)])}}">{{ __($item->name) }}</a>
                                    </h6>
                                    <h6 class="title mt-1">
                                        @lang('Brand') : {{ __($item->brand->name) }}
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
                                            {{ $general->cur_sym }}{{ showAmount($item->base_price) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="product-after-content">
                                    <button data-product="{{$item->id}}" class="cmn-btn btn-sm quick-view-btn">
                                        @lang('View')
                                    </button>
                                    <div class="price">
                                        {{$general->cur_sym }}{{ getAmount($item->base_price) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 col-sm-4 col-md-3 col-lg-2 col-xxl-8-item text-center">
                    <h6>@lang('No Product Yet')</h6>
                </div>
                @endforelse

            </div>
            <div class="row justify-content-center">
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xxl-8-item">
                    {{$products->appends(request()->all())->links()}}
                </div>
            </div>
        </div>
    </section>
    <!-- Vendor Products Sections Here -->
@endsection

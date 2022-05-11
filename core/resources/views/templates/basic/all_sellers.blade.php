
@extends($activeTemplate .'layouts.frontend')


@section('content')
        <!-- Hero Sections Here -->

        <!-- Hero Sections Here -->

          <!-- Vendor Sections Here -->
    <section class="vendor-section padding-top padding-bottom">
        <div class="container">
            <div class="row g-4 justify-content-center">
                @foreach ($sellers as $seller)
                <div class="col-md-6 col-lg-4">
                    <div class="vendor__item">
                        <div class="vendor__top">
                            <h5 class="title">
                                <a href="{{route('seller.details',[$seller->id,slug($seller->shop->name)])}}">{{$seller->shop->name}}</a>
                            </h5>
                            <hr>
                            <ul class="vendor__info">
                                <li>
                                    <i class="las la-map-marker-alt">{{$seller->shop->address}}</i>
                                </li>
                                <li>
                                    <a href="Tel:{{$seller->shop->phone}}"><i class="las la-phone"></i> {{$seller->shop->phone}}</a>
                                </li>
                                <li>
                                    <a href="Mailto:{{$seller->email}}"><i class="las la-envelope"></i>{{$seller->email}}</a>
                                </li>
                                <li class="my-2">
                                    <p><i class="las la-door-open"></i>@lang('Opens at :'){{showDateTime($seller->shop->opens_at,'h:i a')}}</p>
                                </li>
                                <li class="my-2">
                                    <p> <i class="las la-door-closed"></i>@lang('Closed at :'){{showDateTime($seller->shop->closed_at,'h:i a')}}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="vendor__bottom">
                            <a href="{{route('seller.details',[$seller->id,slug($seller->shop->name)])}}" class="read-more">@lang('See Details')</a>
                            <a href="{{route('seller.details',[$seller->id,slug($seller->shop->name)])}}" class="vendor-author">
                                <img src="{{getImage(imagePath()['seller']['shop_logo']['path'].'/'.$seller->shop->logo,imagePath()['seller']['shop_logo']['size'])}}" alt="vendor">
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Vendor Sections Here -->
@endsection
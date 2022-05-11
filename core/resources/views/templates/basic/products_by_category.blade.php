@extends($activeTemplate .'layouts.frontend')

@section('content')
<!-- Category Section Starts Here -->
<div class="category-section padding-bottom padding-top">
    <div class="container">
        @if($products->count() == 0)
            <div class="col-lg-12 mb-30">
                @include($activeTemplate.'partials.empty_page', ['message' => __($emptyMessage)])
            </div>
        @else
            <div class="row">
                {{--<div class="col-xl-3">
                    <aside class="category-sidebar">
                        <div class="widget d-xl-none">
                            <div class="d-flex justify-content-between">
                                <h5 class="title border-0 pb-0 mb-0">@lang('Filter')</h5>
                                <div class="close-sidebar"><i class="las la-times"></i></div>
                            </div>
                        </div>

                        <div class="widget">
                            <h5 class="title">@lang('Filter by Price')</h5>
                            <div class="widget-body">
                                <div id="slider-range"></div>
                                <div class="price-range">
                                    <label for="amount">@lang('Price') :</label>
                                    <input type="text" id="amount" readonly>
                                    <input type="hidden" name="min_price" value="{{$min_price}}">
                                    <input type="hidden" name="max_price" value="{{$max_price}}">
                                </div>
                            </div>
                        </div>


                        @if(isset($brands) && $brands->count()>0)

                        <div class="widget">
                            <h5 class="title">@lang('Filter by Brand')</h5>

                            <div class="widget-body">
                                <div class="widget-check-group">
                                    <input type="checkbox" value="0" name="brand" id="all-brand" @if (in_array(0, $brand)) checked @endif>
                                    <label for="all-brand">@lang('All Brand')</label>
                                </div>

                                @foreach ($brands as $key=>$item)
                                    <div class="widget-check-group brand-filter">
                                        <input type="checkbox" value="{{$item->id}}" name="brand" id="brand-{{$loop->iteration}}" @if (in_array($item->id, $brand)) checked @endif>
                                        <label for="brand-{{$loop->iteration}}" >{{__($item->name)}}</label>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        @endif



                    </aside>
                </div>--}}
                <div class="col-xl-12">

                    <div class="filter-category-header">

                        <div class="fileter-select-item">

                            <div class="select-item product-page-per-view">
                                <select class="select-bar">
                                    <option value="">@lang('Products Per Page')</option>
                                    <option value="5" {{@$perpage == 5?'selected':''}}>@lang('5 Items Per Page') </option>
                                    <option value="15" {{@$perpage == 15?'selected':''}}>@lang('15 Items Per Page') </option>
                                    <option value="30" {{@$perpage == 30?'selected':''}}>@lang('30 Items Per Page') </option>
                                    <option value="50" {{@$perpage == 50?'selected':''}}>@lang('50 Items Per Page') </option>
                                    <option value="100" {{@$perpage == 100?'selected':''}}>@lang('100 Items Per Page') </option>
                                    <option value="200" {{@$perpage == 200?'selected':''}}>@lang('200 Items Per Page') </option>
                                </select>
                            </div>
                        </div>

                        <div class="fileter-select-item d-none d-lg-block ml-auto align-self-end">

                            <ul class="view-number">
                                <li class="change-grid-to-6">
                                    <span class="bar"></span>
                                    <span class="bar"></span>
                                </li>
                                <li class="change-grid-to-4 active">
                                    <span class="bar"></span>
                                    <span class="bar"></span>
                                    <span class="bar"></span>
                                </li>
                                <li class="change-grid-to-3">
                                    <span class="bar"></span>
                                    <span class="bar"></span>
                                    <span class="bar"></span>
                                    <span class="bar"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="fileter-select-item ml-auto ml-lg-0 align-self-end">
                            <ul class="view-style d-flex">
                                <li>
                                    <a href="javascript:void(0)" class="active view-grid-style"><i class="las la-border-all"></i></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="view-list-style"><i class="las la-list-ul"></i></a>
                                </li>
                            </ul>
                        </div>
                        <div class="fileter-select-item align-self-end d-xl-none pl-0">
                            <div class="filter-in">
                                <i class="las la-filter"></i>
                            </div>
                        </div>
                    </div>

                    <div class="position-relative">
                        <div id="overlay" >
                            <div class="cv-spinner">
                                <span class="spinner"></span>
                            </div>
                        </div>
                        <div class="overlay-2" id="overlay2"></div>
                        <div class="page-main-content">
                            <div class="row mb-30-none page-main-content" id="grid-view">

                                @foreach ($products as $item)
                                @php
                                    if($item->offer && $item->offer->activeOffer){
                                        $discount = calculateDiscount($item->offer->activeOffer->amount, $item->offer->activeOffer->discount_type, $item->base_price);
                                    }else $discount = 0;
                                    $wCk = checkWishList($item->id);
                                    $cCk = checkCompareList($item->id);
                                @endphp

                                <div class="col-lg-4 col-sm-6 col-6 grid-control mb-30">
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
                                                    <img  src="{{ getImage(imagePath()['product']['path'].'/'.@$item->main_image, imagePath()['product']['size']) }}" alt="@lang('flash')">
                                                </a>
                                            </div>
                                            <div class="product-content">
                                                <div class="product-before-content">
                                                    <h6 class="title">
                                                        <a href="{{route('product.detail', ['id'=>$item->id, 'slug'=>slug($item->name)])}}">{{ __($item->name) }}</a>
                                                    </h6>
                                                     
                                                    <div class="single_content">
                                                        <p>@php echo __($item->summary) @endphp</p>
                                                    </div>
                                                    <div class="ratings-area justify-content-between">
                                                        {{--<div class="ratings">
                                                            @php echo displayAvgRating($item->reviews) @endphp
                                                        </div>
                                                        <span class="ml-2 mr-auto">({{ __($item->reviews->count()) }})</span>--}}
                                                        <div class="price">
                                                            @if($discount > 0)
                                                                {{ systemCurrency()['symbol'] }}{{ getAmount($item->base_price - $discount/systemCurrency()['rate'], 2) }}
                                                                <del>{{ getAmount($item->base_price/systemCurrency()['rate'], 2) }}</del>
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
                                                        {{ systemCurrency()['symbol'] }}{{ getAmount($item->base_price - $discount/systemCurrency()['rate'], 2) }}
                                                        <del>{{ getAmount($item->base_price/systemCurrency()['rate'], 2) }}</del>
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
                            </div>
                            {{$products->appends(['perpage'=>@$perpage, 'brand'=>@$brand, 'min'=>@$min, 'max'=>@$max])->links()}}
                        </div>
                    </div>

                </div>
            </div>
        @endif
    </div>
</div>
<!-- Category Section Ends Here -->

@endsection

@push('breadcrumb-plugins')
    <li><a href="{{route('home')}}">@lang('Home')</a></li>
@endpush


@push('script')
    <script>
        (function($){

            $('select[name=per_page]').val(`{{ $perpage }}`);

            $("input[type='checkbox'][name='brand']").on('click',function(){
                var brand = [];
                var min = $('input[name="min_price"]').val();
                var max = $('input[name="max_price"]').val();

                if($('#all-brand').is(':checked')){
                    $("input[type='checkbox'][name='brand']").not(this).prop('checked', false);
                }
                $('.brand-filter input:checked').each(function() {
                    brand.push(parseInt($(this).attr('value')));
                });

                var perpage = $('select[name=per_page]').val();
                var category_id = `{{ $category->id }}`;

                getFilteredData(brand, min, max, category_id, perpage);

            });



            function getFilteredData(brand, min=null, max=null, category_id=`{{ $category->id }}`, perpage=`{{ $perpage }}`){
                $("#overlay, #overlay2").fadeIn(300);
                $.ajax({
                    url: "{{ route('category.filter', [$category->id, slug($category->name)]) }}",
                    method: "get",
                    data: {'brand':brand, 'perpage':perpage, 'min':min,  'max':max, 'category_id': category_id},
                    success: function(result){
                        $('.ajax-preloader').addClass('d-none');
                        $('.page-main-content').html(result);

                    }
                }).done(function() {
                    setTimeout(function(){
                        $("#overlay, #overlay2").fadeOut(300);
                    },500);
                });
            }


            $(document).on('change', '.product-page-per-view select', function(){
                var perpage = $(this).val();
                var brand = [];

                var min = $('input[name="min_price"]').val();
                var max = $('input[name="max_price"]').val();

                $('.brand-filter input:checked').each(function() {
                    brand.push(parseInt($(this).attr('value')));
                });
                var category_id = `{{ $category->id }}`;

                getFilteredData(brand, min, max, category_id, perpage);
            });

            $( "#slider-range" ).slider({
                range: true,
                min: {{$min_price}},
                max: {{$max_price}},
                values: [ {{$min_price}}, {{$max_price}} ],
                slide: function( event, ui ) {
                    $( "#amount" ).val( "{{$general->cur_sym}}" + ui.values[ 0 ] + " - {{$general->cur_sym}}" + ui.values[ 1 ] );
                    $('input[name=min_price]').val(ui.values[ 0 ]);
                    $('input[name=max_price]').val(ui.values[ 1 ]);
                },

                change: function(){
                    var brand = [];
                    var min = $('input[name="min_price"]').val();
                    var max = $('input[name="max_price"]').val();

                    $('.brand-filter input:checked').each(function() {
                        brand.push(parseInt($(this).attr('value')));
                    });

                    var perpage = $('select[name=per_page]').val();
                    var category_id = `{{ $category->id }}`;
                    getFilteredData(brand, min, max, category_id);
                }

            });

            $( "#amount" ).val( "{{$general->cur_sym}}" + $( "#slider-range" ).slider( "values", 0 ) + " - {{$general->cur_sym}}" + $( "#slider-range" ).slider( "values", 1 ) );

    })(jQuery)
    </script>
@endpush

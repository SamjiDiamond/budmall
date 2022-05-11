@extends($activeTemplate.'layouts.frontend')

@section('content')

    <!-- Order Track Section Starts Here -->
    <div class="order-track-section padding-bottom padding-top">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9 col-xl-6">
                    <form class="order-track-form mb-4 mb-md-5">
                        @csrf
                        <div class="order-track-form-group">
                            <input type="text" name="order_number" class="bg--white" placeholder="@lang('Enter Your Order ID')" value="{{old('order_number', @$order_number)}}">
                            <button type="button" class="track-btn">@lang('Track Now')</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="order-track-wrapper d-flex flex-wrap justify-content-center">


                        <div class="confirm-state order-track-item">
                            <div class="thumb">
                                <i class="las la-check-square"></i>
                            </div>
                            <div class="content">
                                <h6 class="title">@lang('Confirmed')</h6>
                            </div>
                        </div>

                        <div class="order-track-item processing-state">
                            <div class="thumb">
                                <i class="las la-sync-alt"></i>
                            </div>
                            <div class="content">
                                <h6 class="title">@lang('On Processing')</h6>
                            </div>
                        </div>

                        <div class="order-track-item dispatched-state">
                            <div class="thumb">
                                <i class="las la-truck-pickup"></i>
                            </div>
                            <div class="content">
                                <h6 class="title">@lang('Dispatched')</h6>
                            </div>
                        </div>

                        <div class="order-track-item delivered-state">
                            <div class="thumb">
                                <i class="las la-map-signs"></i>
                            </div>
                            <div class="content">
                                <h6 class="title">@lang('Delivered')</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Order Track Section Ends Here -->

@endsection

@push('breadcrumb-plugins')
    <li><a href="{{route('home')}}">@lang('Home')</a></li>
@endpush


@push('script')
 <script>
    'use strict';
    (function($){
        $(document).on('click', '.track-btn' ,function (e) {
            var order_number = $('input[name=order_number]').val();
            var url = '{{route('order-track')}}';
            $.ajax({
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                url: url,
                data: {
                    'order_number': order_number
                },
                method:"POST",
                success: function(response){
                    if(response.success) {
                        if(response.status == 4){
                                $('.confirm-state').removeClass('active');
                                $('.processing-state').removeClass('active');
                                $('.dispatched-state').removeClass('active');
                                $('.delivered-state').removeClass('active');
                                notify('error', 'This order is canceled by admin');
                        }
                        if(response.payment_status != 0 && response.status != 4){
                            if(response.status >= 0){
                                $('.confirm-state').addClass('active');
                            }else{
                                $('.confirm-state').removeClass('active');
                            }

                            if(response.status >= 1){
                                $('.processing-state').addClass('active');
                            }else{
                                $('.processing-state').removeClass('active');
                            }

                            if(response.status >= 2){
                                $('.dispatched-state').addClass('active');
                            }else{
                                $('.dispatched-state').removeClass('active');
                            }

                            if(response.status >= 3){
                                $('.delivered-state').addClass('active');
                            }else{
                                $('.delivered-state').removeClass('active');
                            }


                        }
                    }else{
                        notify('error', response.message);
                    }
                }
            });

        });
    })(jQuery)
 </script>
@endpush

@push('meta-tags')
    @include('partials.seo')
@endpush

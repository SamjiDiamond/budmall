@extends(activeTemplate() .'layouts.frontend')

@section('content')
@php $curr = systemCurrency()['symbol']; @endphp
@php $rate = systemCurrency()['rate'];
$vat = $subtotal/100*$general->vat;
 if(session()->has('coupon'))
 {
 $aftercoup = $subtotal - session('coupon')['amount'];
 $total =  $aftercoup  + $vat + session()->get('delicalc');

 }
 else
 {
 $aftercoup = $subtotal;
 $total =  $aftercoup + $vat + session()->get('delicalc');
 }
@endphp
@php




@endphp
    <!-- Checkout Section Starts Here -->
    <div class="checkout-section padding-bottom padding-top">
        <div class="container">
            <div class="checkout-area section-bg">
                <div class="row flex-wrap-reverse">

                <div class="col-md-6 col-lg-5 col-xl-4">
                        <div class="payment-details">
                            <h4 class="title text-center">@lang('Payment Details')</h4>
                            <ul>
                                <li>
                                    <span class="subtitle">@lang('Cart Total')</span>
                                    <span class="text-success" id="cartSubtotal">{{systemCurrency()['symbol']}}{{number_format($subtotal/$rate,2)}}</span>
                                </li>
                                 @if(session()->has('coupon'))
                                    <li>
                                        <span class="subtitle">@lang('Coupon') ({{session('coupon')['code']}})</span>
                                        <span class="text-success" id="couponAmount">{{systemCurrency()['symbol']}}{{ number_format(session('coupon')['amount']/systemCurrency()['rate'], 2)}}</span>
                                    </li>

                                    <li>
                                        <span class="subtitle">Sub Total</span>
                                        <span class="text-suzccess" id="afterCouponAmount">{{systemCurrency()['symbol']}}{{number_format($aftercoup/systemCurrency()['rate'],2)}} </span>
                                    </li>
                                @endif

                                 <li>
                                    <span class="subtitle">@lang('VAT') <small><b>{{$general->vat}}%</b></small></span>
                                    <span class="text-suczcess" id="cartVat">{{systemCurrency()['symbol']}} {{number_format($vat/$rate,2)}}</span>
                                </li>
                               
                                <li>
                                    <span class="subtitle">@lang('Delivery Cost')</span>
                                    <span class="text-daznger" id="shippingCharge">{{systemCurrency()['symbol']}}{{number_format(session()->get('delicalc') /systemCurrency()['rate'],2) ?? 0}}</span>
                                </li>
                                <li class="border-0">
                                    <span class="subtitle bold">@lang('Total')</span>
                                    <span class="cl-title" id="cartTotal">{{systemCurrency()['symbol']}}{{number_format($total/systemCurrency()['rate'],2)}}</span>
                                </li>
                            </ul>
                            <p id="shipping-details">

                            </p>

                             @if(session()->get('delicalc'))
                             <form action="{{route('user.checkout-to-payment', 2)}}" method="post">
                             @csrf



                                                <small><b>@lang('Select Payment Method')</b></small>
                                                <div class="billing-select">
                                                    <select name="payment" required>
                                                        <option selected disabled>@lang('Select One')</option>
                                                        <option value="0">@lang('Pay With Wallet')</option>
                                                        @foreach($gateway as $data)
                                                        <option value="{{$data->id}}">@lang($data->name)</option>
                                                        @endforeach
                                                        @if ($general->cod)
                                                         <option value="cod">@lang('Cash On Delivery')</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <br>
                                                <small><b>@lang('Notify Beneficiary') ?</b></small><br>
                                                 <small class="text-primary">is this a surprise delivery?</small>
                                               
                                                   
                                                <div class="billing-select">
                                                        <select name="notify" required>
                                                        <option value="0">@lang('NO')</option>

                                                         <option value="1">@lang('YES')</option>
                                                       
                                                    </select>
                                                </div>

                             <input hidden class="form-control custom--style" value="{{session()->get('shipmeth')}}" type="text" name="shipping_method"  required>
                             <input hidden class="form-control custom--style" value="{{session()->get('bfname')}}" type="text" name="firstname" required>
                             <input hidden class="form-control custom--style" value="{{session()->get('blname')}}" name="lastname" type="text" required>
                             <input hidden class="form-control custom--style" value="{{session()->get('bphone')}}" name="mobile" type="text" required>
                             <input hidden class="form-control custom--style" id="e-mail" value="{{session()->get('bemail')}}"  name="email" type="text" required>
                             <input hidden type="text" class="form-control custom--style" value="{{session()->get('bcity')}}"  name="location"   placeholder="Beneficiary's Address" required autocomplete="off">
                              <input hidden  type="text" class="form-control custom--style" value="{{session()->get('bstate')}}"  name="state" placeholder="Beneficiary's State" required autocomplete="off">
                             <br>
                             <button type="submit" class="btn btn-success btn-sm w-100">@lang('Proceed To Checkout')</button>
                             </form>
                            @endif

                        </div>
                    </div>


                    <div class="col-md-6 col-lg-7 col-xl-8">
                    <div class="col-12 card">

                        <div class="checkout-wrapper">
                        <br>
                            <h4 class="title text-center">Beneficiary's Details</h4>
                            <ul class="nav-tabs nav justify-content-center">
                               <!-- <li>
                                    <a href="#self" data-toggle="tab" class="active">@lang('For Yourself')</a>
                                </li>

                                <li>
                                    <a href="#guest" data-toggle="tab" class="active">@lang('Order As Gift')</a>
                                </li>
                                  -->
                            </ul>
                            <div class="tab-content">

                                <div class="tab-pane fade show active" id="guest">
                                    <form action="{{route('user.calculatedelivery')}}" method="post" class="guest-form mb--20">
                                        @csrf

                                        <div class="row">
                                            <div class="col-lg-12 mb-20">
                                                <label for="shipping-method-2" class="billing-label">@lang('Shipping Method')</label>
                                                <div class="billing-select">
                                                    <select name="shipping_method" id="shipping-method-2" required>
                                                        @foreach ($shipping_methods as $sm)
                                                            <option selected data-shipping="{{$sm->description}}" data-charge="{{$sm->charge}}" value="{{$sm->id}}">{{$sm->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 mb-20">
                                                <label for="firstname" class="billing-label">Beneficiary's @lang('First Name')</label>
                                                <input class="form-control custom--style" value="{{session()->get('bfname')}}" id="firstname" type="text" name="firstname" value="{{ old('firstname') }}" required>
                                            </div>
                                            <div class="col-lg-6 mb-20">
                                                <label for="lastname" class="billing-label">Beneficiary's @lang('Last Name')</label>
                                                <input class="form-control custom--style" value="{{session()->get('blname')}}"  id="lastname" name="lastname" type="text" value="{{ old('lastname')}}" required>
                                            </div>

 
                                            
                                            <div class="col-lg-6 mb-20">
                                                <label for="mobile" class="billing-label">Beneficiary's @lang('Mobile')</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text mobile-code">+234</span>
                                                    
                                                </div>
                                                <input type="text" name="mobile" id="mobile" value="{{session()->get('bphone')}}" value="{{ old('mobile')}}" required class="form-control custom--style">
                                                <small class="text--danger mobileExist d-block"></small>
                                            </div>
                                            </div>

                                            <div class="col-lg-6 mb-20">
                                                <label for="e-mail" class="billing-label">Beneficiary's @lang('Email')</label>
                                                <input class="form-control custom--style" id="e-mail" value="{{session()->get('bemail')}}"  name="email" type="text" required>
                                            </div>

                                          <!--  <div class="col-lg-6 mb-20">
                                                <label for="country-2" class="billing-label">@lang('Country')</label>
                                                <div class="billing-select">
                                                    <select name="country" id="country-2" class="select-bar" required>
                                                        @foreach($countries as $key => $country)
                                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">{{ __($country->country) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>-->

                                            <div class="col-12 mb-20">
                                                <label for="city-2" class="billing-label">Beneficiary's @lang('Address')</label>

                                                <input type="text" class="form-control custom--style" value="{{session()->get('bcity')}}"  name="location" id="location" placeholder="Beneficiary's Address" value="" required autocomplete="off">
                                            </div>

                                            <div class="col-12 mb-20">
                                                <label for="state-2" class="billing-label">@lang('State')</label>

                                                <select name="state" class="select2-basic">
                                                @if(session()->get('bstate') )
                                                
                                                <option selected>{{@App\Models\State::whereId(session()->get('bstate'))->first()->name }}</option>
                                                @else
                                                  <option selected disabled>Please Select State</option>
                                              
                                                @endif
                                                @foreach($state as $data)
                                                <option value="{{$data->id}}">{{$data->state}}</option>
                                                @endforeach
                                                </select>
                                            </div>


                                        </div>

                                        <div class="row">

                                            <div class="col-lg-6 mb-20">
                                                <button type="submit" class="bill-button w-100">@lang('Calculate Delivery Rate')</button>

                                            </div>
                                            </form>


                                            <br>
                                        </div>

                                </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Checkout Section Ends Here -->

@endsection

@push('script')
 <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{$general->mapkey}}&libraries=places"></script>


      <script>
            function init() {
                var input = document.getElementById('location');
                var autocomplete = new google.maps.places.Autocomplete(input);
            }

            google.maps.event.addDomListener(window, 'load', init);


        </script>

@endpush

@push('breadcrumb-plugins')
    <li><a href="{{route('home')}}">@lang('Home')</a></li>
    <li><a href="{{route('products')}}">@lang('Products')</a></li>
    <li><a href="{{route('shopping-cart')}}">@lang('Cart')</a></li>
@endpush


@push('meta-tags')
    @include('partials.seo')
@endpush

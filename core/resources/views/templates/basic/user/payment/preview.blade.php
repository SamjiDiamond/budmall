@extends($activeTemplate.'layouts.frontend')
@section('content')
<div class="checkout-section padding-bottom padding-top">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card border-0 shadow-md">
                    <div class="card-header bg-transparent d-flex justify-content-between">
                        <img src="{{ $data->gatewayCurrency()->methodImage() }}" class="card-img-top w-25" @lang('gateway-image')">
                        <h3 class="align-self-center cl-1">
                            @lang('Payment Preview')
                        </h3>
                    </div>
                    <div class="card-body">

                        <ul class="list-group list-group-flush text-center ">
                            <li class="list-group-item d-flex justify-content-between align-items-center">@lang('Amount'): <strong>{{showAmount($data->amount)}} {{$general->cur_text}}</strong></li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Charge'):
                                <span><strong>{{showAmount($data->charge)}}</strong> {{$general->cur_text}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Payable'): <strong>{{showAmount($data->amount + $data->charge)}} {{$general->cur_text}}</strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Conversion Rate'): <strong>1 {{$general->cur_text}} = {{showAmount($data->rate)}}  {{$data->baseCurrency()}}</strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('In') {{$data->baseCurrency()}}:
                                <strong>{{showAmount($data->final_amo)}}</strong>
                            </li>

                            @if($data->gateway->crypto==1)
                                <li class="list-group-item">@lang("Conversion with $data->method_currency and final value will Show on next step")
                                </li>
                            @endif
                            @if( 1000 >$data->method_code)
                            <li class="list-group-item p-0">
                                <a href="{{route('user.deposit.confirm')}}" class="cmn-btn btn-block">@lang('Pay Now')</a>
                            </li>
                            @else
                                <li class="list-group-item p-0">
                                    <a href="{{route('user.deposit.manual.confirm')}}" class="cmn-btn btn-block">@lang('Pay Now')</a>
                                </li>
                            @endif
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <li><a href="{{route('home')}}">@lang('Home')</a></li>
    <li><a href="{{route('products')}}">@lang('Products')</a></li>
    <li><a href="{{route('shopping-cart')}}">@lang('Cart')</a></li>
    <li><a href="{{route('user.checkout')}}">@lang('Checkout')</a></li>
    <li><a href="{{route('user.deposit')}}">@lang('Payment')</a></li>
@endpush


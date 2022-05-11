@extends($activeTemplate.'layouts.frontend')
@section('content')
<div class="container padding-bottom padding-top">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-6 col-xl-4">
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{$deposit->gatewayCurrency()->methodImage()}}"   alt="@lang('Image')" class="w-100 mb-4">
                    <div>
                        <h5>@lang('Please Pay') {{showAmount($deposit->final_amo)}} {{__($deposit->method_currency)}}</h5>
                        <h5 class="my-3">@lang('To Get') {{showAmount($deposit->amount)}}  {{__($general->cur_text)}}</h5>
                        <button type="button" class="btn--base mt-4 d-block  w-100" id="btn-confirm" onClick="payWithRave()">@lang('Pay Now')</button>
                    </div>
                    <form action="{{ route('ipn.'.$deposit->gateway->alias) }}" method="POST" class="text-center">
                        <script
                        src="//js.paystack.co/v1/inline.js"
                        data-key="{{ $data->key }}"
                        data-email="{{ $data->email }}"
                        data-amount="{{$data->amount}}"
                        data-currency="{{$data->currency}}"
                        data-ref="{{ $data->ref }}"
                        data-custom-button="btn-confirm"
                        >
                       </script>
                   </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

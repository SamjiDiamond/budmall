@extends($activeTemplate.'layouts.frontend')

@section('content')
<div class="checkout-section padding-bottom padding-top">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card border-0 shadow-md">
                    <div class="card-header bg-transparent d-flex justify-content-between">
                      <!--  <img src="{{ $datas->gatewayCurrency()->methodImage() }}" class="card-img-top w-25" @lang('gateway-image')">-->
                        <h3 class="align-self-center cl-1">
                            @lang('Payment Preview')
                        </h3>
                    </div>
                    <div class="card-body">

                        <ul class="list-group list-group-flush text-center ">
                            <li class="list-group-item d-flex justify-content-between align-items-center">@lang('Amount'): <strong>{{showAmount($datas->amount)}} USD</strong></li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Charge'):
                                <span><strong>{{showAmount($datas->charge)}}</strong> USD</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Payable'): <strong>{{showAmount($datas->amount + $datas->charge)}} USD</strong>
                            </li>

                           <!-- <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Conversion Rate'): <strong>1 {{$general->cur_text}} = {{showAmount($datas->rate)}}  {{$datas->baseCurrency()}}</strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('In') {{$datas->baseCurrency()}}:
                                <strong>{{showAmount($datas->final_amo)}}</strong>
                            </li>

                            @if($datas->gateway->crypto==1)
                                <li class="list-group-item">@lang("Conversion with $datas->method_currency and final value will Show on next step")
                                </li>
                            @endif-->
                            <li class="list-group-item p-0">
                                <button type="button" class="btn--base mt-4 d-block  w-100" id="btn-confirm" onClick="payWithRave()">@lang('Pay Now')</button>
                            </li>

                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@push('script')
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script>
        "use strict"
        var btn = document.querySelector("#btn-confirm");
        btn.setAttribute("type", "button");
        const API_publicKey = "{{$data->API_publicKey}}";

        function payWithRave() {
            var x = getpaidSetup({
                PBFPubKey: API_publicKey,
                customer_email: "{{$data->customer_email}}",
                amount: "{{$data->amount }}",
                customer_phone: "{{$data->customer_phone}}",
                currency: "{{$data->currency}}",
                txref: "{{$data->txref}}",
                onclose: function () {
                },
                callback: function (response) {
                    var txref = response.tx.txRef;
                    var status = response.tx.status;
                    var chargeResponse = response.tx.chargeResponseCode;
                    if (chargeResponse == "00" || chargeResponse == "0") {
                        window.location = '{{ url('ipn/deposit/flutterwave') }}/' + txref + '/' + status;
                    } else {
                        window.location = '{{ url('ipn/deposit/flutterwave') }}/' + txref + '/' + status;
                    }
                        // x.close(); // use this to close the modal immediately after payment.
                    }
                });
        }
    </script>
@endpush

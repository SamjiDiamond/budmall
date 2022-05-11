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
                    <form action="{{$data->url}}" method="{{$data->method}}">
                        <input type="hidden" custom="{{$data->custom}}" name="hidden">
                        <script src="{{$data->checkout_js}}"
                                @foreach($data->val as $key=>$value)
                                data-{{$key}}="{{$value}}"
                            @endforeach >
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
   
@endsection


@push('script')
    <script>
        (function ($) {
            "use strict";
            $('input[type="submit"]').addClass("mt-4 btn--base text-center");
        })(jQuery);
    </script>
@endpush

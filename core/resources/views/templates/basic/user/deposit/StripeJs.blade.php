@extends($activeTemplate.'layouts.frontend')
@section('content')
<div class="container padding-bottom padding-top">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-6 col-xl-4">
            <div class="card text-center">
                <div class="card-body">
                    <div>
                    <h5>@lang('Amount') {{showAmount($deposit->amount)}} {{__($deposit->method_currency)}}</h5> 
                    <form action="{{$data->url}}" method="{{$data->method}}">
                        <script src="{{$data->src}}"
                            class="stripe-button"
                            @foreach($data->val as $key=> $value)
                            data-{{$key}}="{{$value}}"
                            @endforeach
                        >
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
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        (function ($) {
            "use strict";
            $('button[type="submit"]').addClass("btn--base btn-block text-center");
            $('button[type="submit"]').children().remove();
            $('button[type="submit"]').text('@lang('Pay Now')')
        })(jQuery);
    </script>
@endpush

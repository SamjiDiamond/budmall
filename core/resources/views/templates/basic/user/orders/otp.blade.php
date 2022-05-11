@extends($activeTemplate.'layouts.frontend')
@section('content')

@php
    $content  = getContent('code_verify_page.content', true);
@endphp

<div class="account-section padding-bottom padding-top">
    <div class="contact-thumb d-none d-lg-block">
        <img src="{{ getImage('assets/images/frontend/code_verify_page/'. @$content->data_values->image, '600x600') }}" alt="@lang('login-bg')">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-7">

                <div class="section-header left-style">
                    <h3 class="title">Enter OTP</h3>
                    <p class="text-danger">You are about to make payment of <b>{{number_format($order->total_amount_usd,2)}} USD</b> for an order from your Wallet</p>
                    <br>
                    <p>Please enter the One Time Password sent to your registered email address on {{$general->sitename}} and click on the <b>PAY</b> button to complete this transaction</p>
                </div>
                @php
                $attempts = 3-$order->otp_try;
                @endphp
                
                @if($order->otp_fail < 1)
                @if($order->otp_try > 0)
                <b class="text-danger">You have {{$attempts}} @if($attempts > 1) attempts @else attempt @endif left</b>
                @endif
                 <br>
                @endif
               
                @if($order->otp_fail > 0)
                <b class="text-danger">You have entered wrong order OTP 3 times. Please enter the Code sent to your email to continue this transaction</b>
                @endif

                <form action="" method="POST" class="contact-form mb-30-none">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                     <div class="contact-group">
                        <input type="number" name="otp" placeholder="********" id="otpcode" class="form-control">
                    </div>

                     <div class="contact-group">
                        <button type="submit" class="cmn--btn m-0 ml-auto text-white"><li class="fa fa-lock"></li> @lang('Pay')</button>
                    </div>


                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script>
    (function($){
        "use strict";
          $('#code').on('input change', function () {
          var xx = document.getElementById('code').value;
          $(this).val(function (index, value) {
             value = value.substr(0,7);
              return value.replace(/\W/gi, '').replace(/(.{3})/g, '$1 ');
          });
      });
    })(jQuery)
</script>
@endpush

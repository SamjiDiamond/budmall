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
                   <center> <h3 class="title">Payment Successful!!!!</h3></center>
                    <p class="text-success">You have successfully paid the sum of <b>{{@number_format($order->total_amount_usd ,2) ?? "0.00"}} USD for your order with transaction number {{@$order->order_number}} </b></p>
                    <br>
                    <b>Your order is on its way to you. <br>Thank you for choosing BudMall</b>
                </div>
                  
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

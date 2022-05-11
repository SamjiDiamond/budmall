@extends($activeTemplate .'layouts.frontend')
@section('content')

@php
    $authorize_content  = getContent('authorize_sms_page.content', true);
@endphp

<div class="account-section padding-bottom padding-top">

    <div class="contact-thumb d-none d-lg-block">
        <img src="{{ getImage('assets/images/frontend/authorize_sms_page/'. @$authorize_content->data_values->image, '600x840') }}" alt="@lang('login-bg')">
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="section-header left-style">
                    <h3 class="title">{{ __('Google authentication') }}</h3>
                        <p>{{ __('Please verify your 2FA') }}</p>
                </div>
      

                <form action="{{route('user.go2fa.verify')}}" method="POST">
                    
                    @csrf
                    
                        <p class="text-center">@lang('Current Time'): {{\Carbon\Carbon::now()}}</p>
                   
    
                    <div class="contact-group">
                      <label>@lang('Verification code')</label>
                      <input type="text" name="code" placeholder="@lang('Code')" id="code">
                    </div>
                   
                    <div class="contact-group">
                        <button type="submit" class="cmn--btn m-0 ml-auto text-white">@lang('Submit')</button>
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

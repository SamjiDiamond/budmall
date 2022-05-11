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
                    <h3 class="title">{{ __($authorize_content->data_values->title) }}</h3>
                        <p>{{ __($authorize_content->data_values->description) }}</p>
                </div>
                <form action="{{route('user.verify.sms')}}" method="POST" class="contact-form mb-30-none">
                    @csrf

                    <div class="contact-group">
                        <label for="code">@lang('Verification Code')</label>
                        <input type="text" id="code" name="sms_verified_code">
                    </div>

                    <div class="contact-group">
                        <button type="submit" class="cmn--btn m-0 ml-auto text-white">@lang('Submit')</button>
                    </div>

                    <div class="form-group">
                        <p>@lang('If you don\'t get any code'), <a href="{{route('user.send.verify.code')}}?type=phone" class="forget-pass"> @lang('Try again')</a></p>
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

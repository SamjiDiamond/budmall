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
                    <h3 class="title">{{ __(@$content->data_values->title) }}</h3>
                    <p>{{ __(@$content->data_values->description) }}</p>
                </div>

                <form action="{{ route('seller.password.verify.code') }}" method="POST" class="contact-form mb-30-none">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                     <div class="contact-group">
                        <label>@lang('Verification Code')</label>
                        <input type="text" name="code" id="code" class="form-control">
                    </div>

                     <div class="contact-group">
                        <button type="submit" class="cmn--btn m-0 ml-auto text-white">@lang('Verify Code')</button>
                    </div>

                    <div class="contact-group justify-content-end">
                        @lang('Please check including your Junk/Spam Folder. If not found') <a href="{{ route('user.password.request') }}" class="ml-1">@lang('Try to send again')</a>
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

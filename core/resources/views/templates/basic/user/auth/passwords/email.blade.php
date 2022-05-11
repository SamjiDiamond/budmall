@extends($activeTemplate.'layouts.frontend')

@section('content')
@php
    $content        = getContent('forgot_password_page.content', true);
@endphp


<div class="account-section padding-bottom padding-top">

    <div class="contact-thumb d-none d-lg-block">
        <img src="{{ getImage('assets/images/frontend/forgot_password_page/'. @$content->data_values->image, '600x600') }}" alt="@lang('login-bg')">
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="section-header left-style">
                    <h3 class="title">{{ __($content->data_values->title) }}</h3>
                    <p>{{ __($content->data_values->description) }}</p>
                </div>

                <form method="POST" action="{{ route('user.password.email') }}" class="contact-form mb-30-none">
                    @csrf
                    <div class="contact-group">
                        <label>@lang('Select One')</label>

                        <div class="select-item">
                            <select class="select-bar" name="type">
                                <option value="email">@lang('E-Mail Address')</option>
                                <option value="username">@lang('Username')</option>
                            </select>
                        </div>
                    </div>

                    <div class="contact-group">
                        <label class="my_value"></label>
                        <input type="text" class=" @error('value') is-invalid @enderror" name="value" value="{{ old('value') }}" required autofocus="off">

                        @error('value')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="contact-group">
                        <button type="submit" class="cmn--btn m-0 ml-auto text-white">
                            {{ __('Submit') }}
                        </button>
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

        myVal();
        $('select[name=type]').on('change',function(){
            myVal();
        });
        function myVal(){
            $('.my_value').text($('select[name=type] :selected').text());
        }
    })(jQuery)
</script>
@endpush

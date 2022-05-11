@extends($activeTemplate.'layouts.frontend')
@section('content')
@php
    $login_content        = getContent('login_page.content', true);
@endphp

<section class="account-section padding-bottom padding-top">
    <div class="contact-thumb d-none d-lg-block">
        <img src="{{ getImage('assets/images/frontend/login_page/'. @$login_content->data_values->image, '600x840') }}" alt="@lang('login-bg')">
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-7 card">
                <br>
                <div class="section-header left-style">
                    <h3 class="title">{{ __(@$login_content->data_values->title) }}</h3>
                    <p>{{ __(@$login_content->data_values->description) }}</p>
                </div>
                <form method="POST" action="{{ route('user.login')}}" class="contact-form mb-30-none">
                    @csrf

                    <div class="form-group">
                        <label for="username">@lang('Username')</label>
                        <input id="username" type="text" name="username" placeholder="@lang('Enter Your Username')" value="{{ old('username') }}">
                    </div>

                    <div class="form-group">
                        <label for="password">@lang('Password')</label>
                        <input id="password" type="password" name="password" placeholder="@lang('Enter Your Password')" required autocomplete="current-password">
                    </div>

                        @include($activeTemplate.'partials.custom_captcha')

                        <div class="contact-group">
                            <div class="d-flex flex-wrap align-items-center w-100 justify-content-end ">
                                    <button type="submit" id="recaptcha" class="cmn--btn m-0 text-white">@lang('Login')</button>
                            </div>
                        </div>
                        <div class="contact-group">
                            <div class="w-100">
                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    @if (Route::has('user.register'))
                                    <span class="acc">@lang('Don\'t have an account')? <a href="{{route('user.register')}}">@lang('Create An Account')</a></span>
                                    @endif
                                    @if (Route::has('user.password.request'))
                                    <span class="account-alt">
                                        <a href="{{route('user.password.request')}}">
                                            {{ __('Forgot Password') }}?
                                        </a>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection

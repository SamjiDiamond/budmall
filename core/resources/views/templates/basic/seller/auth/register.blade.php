@extends($activeTemplate.'layouts.frontend')
@section('content')
@php
    $register_content = getContent('seller_register_page.content', true);
    $pages = getContent('pages.element', false,'',1);
@endphp

<section class="account-section padding-bottom padding-top">
    <div class="contact-thumb rev-side d-none d-lg-block">
        <img src="{{ getImage('assets/images/frontend/seller_register_page/'. @$register_content->data_values->image, '650x980') }}" alt="register-bg">
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-7 offset-lg-5">

                <div class="section-header left-style">
                    <h3 class="title">{{ __(@$register_content->data_values->title) }}</h3>
                    <p>{{ __(@$register_content->data_values->description) }}</p>
                </div>

                <form action="{{ route('seller.register') }}" method="POST" onsubmit="return submitUserForm();">
                    @csrf

                    <div class="contact-group">
                        <label for="firstname">@lang('First Name')</label>
                        <input id="firstname" type="text" name="firstname" value="{{ old('firstname') }}" required>
                    </div>

                    <div class="contact-group">
                        <label for="lastname">@lang('Last Name')</label>
                        <input id="lastname" type="text" name="lastname" value="{{ old('lastname') }}" required>
                    </div>

                    <div class="contact-group">
                        <label for="country">@lang('Country')</label>
                        <div class="select-item">
                            <select name="country" id="country" class="select-bar checkUser">
                                @foreach($countries as $key => $country)
                                    <option data-mobile_code="{{ $country->dial_code }}"
                                        value="{{ $country->country }}" data-code="{{ $key }}">
                                        {{ __($country->country) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="contact-group">
                        <label for="mobile">@lang('Mobile')</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text mobile-code"></span>
                                <input type="hidden" name="mobile_code">
                                <input type="hidden" name="country_code">
                            </div>
                            <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}" class="form-control checkUser" placeholder="@lang('Your Phone Number')">
                            <small class="text--danger mobileExist d-block"></small>
                        </div>
                    </div>

                    <div class="contact-group">
                        <label for="email">@lang('Email')</label>
                        <input id="email" type="email" class="form-control checkUser" name="email" value="{{ old('email') }}" required>
                    </div>



                    <div class="contact-group">
                        <label for="username">@lang('Username')</label>
                        <div class="multi-group">
                            <input id="username" type="text" class="checkUser w-100" name="username" value="{{ old('username') }}" required>
                            <small class="text--danger usernameExist d-block"></small>
                        </div>
                    </div>


                    <div class="contact-group hover-input-popup">
                        <label for="password">@lang('Password')</label>
                        <div class="multi-group">
                            <input id="password" type="password" name="password" required class="w-100">
                            @if($general->secure_password)
                                <div class="input-popup">
                                    <p class="error lower">@lang('1 small letter minimum')</p>
                                    <p class="error capital">@lang('1 capital letter minimum')</p>
                                    <p class="error number">@lang('1 number minimum')</p>
                                    <p class="error special">@lang('1 special character minimum')</p>
                                    <p class="error minimum">@lang('6 character password')</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="contact-group">
                        <label for="password-confirm">@lang('Confirm Password')</label>
                        <div class="multi-group">
                            <input id="password-confirm" class="w-100" type="password" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>



                    @include($activeTemplate.'partials.custom_captcha')

                    @if($general->agree)

                    <div class="contact-group">
                        <div class="multi-group">
                            <div class="form-check form--check w-100">
                                <input type="checkbox" class="form-check-input" name="agree" id="agree" value="checkedValue">
                                <label for="agree" class="form-check-label">
                                    @lang('I agree with')
                                     @foreach ($pages as $item)
                                       
                                        <span>
                                            <a href="{{route('page.details',[$item->id,slug($item->data_values->pageTitle)])}}" class="text--base">{{__($item->data_values->pageTitle)}}</a> @if (!$loop->last)
                                            ,
                                            @endif
                                        </span>
                                    @endforeach
                                </label>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="contact-group">
                        <div class="m--10 d-flex flex-wrap align-items-center w-100 justify-content-between">
                            <span class="account-alt">@lang('Already have an account?') <a href="{{ route('seller.login') }}">@lang('Login')</a></span>

                            <button type="submit" id="recaptcha" class="cmn--btn text-white">@lang('Sign Up')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6 class="text-center">@lang('You already have an account please Sign in ')</h6>
            </div>
            <div class="modal-footer">
                <a type="button" class="btn btn--dark h-auto text-white" data-dismiss="modal">@lang('Close')</button>
                <a href="{{ route('seller.login') }}" class="btn btn--base h-auto">@lang('Login')</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
    <style>

        .hover-input-popup {
            position: relative;
        }

        .hover-input-popup input:focus~.input-popup {
            opacity: 1;
            visibility: visible;
        }

        .input-popup {
            position: absolute;
            bottom: 130%;
            left: 50%;
            width: 280px;
            background-color: #1a1a1a;
            color: #fff;
            padding: 20px;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
            -webkit-transform: translateX(-50%);
            -ms-transform: translateX(-50%);
            transform: translateX(-50%);
            opacity: 0;
            visibility: hidden;
            -webkit-transition: all 0.3s;
            -o-transition: all 0.3s;
            transition: all 0.3s;
        }

        .input-popup::after {
            position: absolute;
            content: '';
            bottom: -19px;
            left: 50%;
            margin-left: -5px;
            border-width: 10px 10px 10px 10px;
            border-style: solid;
            border-color: transparent transparent #1a1a1a transparent;
            -webkit-transform: rotate(180deg);
            -ms-transform: rotate(180deg);
            transform: rotate(180deg);
        }

        .input-popup p {
            padding-left: 20px;
            position: relative;
        }

        .input-popup p::before {
            position: absolute;
            content: '';
            font-family: 'Line Awesome Free';
            font-weight: 900;
            left: 0;
            top: 4px;
            line-height: 1;
            font-size: 18px;
        }

        .input-popup p.error {
            text-decoration: line-through;
        }

        .input-popup p.error::before {
            content: "\f057";
            color: #ea5455;
        }

        .input-popup p.success::before {
            content: "\f058";
            color: #28c76f;
        }

    </style>
@endpush


@push('script-lib')
    <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush
@push('script')
    <script>
      "use strict";
        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML = '<span class="text-danger">@lang("Captcha field is required.")</span>';
                return false;
            }
            return true;
        }
        (function ($) {
            @if($mobile_code)
            $(`option[data-code={{ $mobile_code }}]`).attr('selected','');
            @endif
            $('select[name=country]').change(function(){
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+'+$('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+'+$('select[name=country] :selected').data('mobile_code'));
            @if($general->secure_password)
                $('input[name=password]').on('input',function(){
                    secure_password($(this));
                });
            @endif

            $('.checkUser').on('focusout',function(e){
                var url = `{{ route('seller.checkSeller') }}`;
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {mobile:mobile,_token:token}
                }

                if($(this).attr('name') == 'email') {
                    var data = {email:value,_token:token}
                }
                if($(this).attr('name') == 'username') {
                    var data = {username:value,_token:token}
                }

                $.post(url,data,function(response) {
                    console.log(response);
                  if (response['data'] && response['type'] == 'email') {
                    $('#existModalCenter').modal('show');
                  }else if(response['data'] != null){
                    $(`.${response['type']}Exist`).text(`${response['type']} already exist`);
                  }else{
                    $(`.${response['type']}Exist`).text('');
                  }
                });
            });
        })(jQuery);
    </script>
@endpush

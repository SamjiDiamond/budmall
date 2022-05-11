@extends($activeTemplate.'layouts.frontend')

@section('content')
@php
    $register_content = getContent('register_page.content', true);
    $pages = getContent('pages.element', false,'',1);
@endphp

@push('style')
 
@endpush
 <section class="our-webcoderskull padding-lg card">
  <div class="container">
    <br>
    
           <center> <h4 class="text-black">Register As A Sales Consultant</h4><br>
           
           <b>Register as a BudMall Sales Consultant and earn {{$general->scref}}% of purchases processed using your Referral Link. <a href="https://budmall.ng/pages/66/general-terms-and-conditions">Terms & Condition</a> apply</b></center>
            
             <div class="col-12 card ">
             <br>
             <form action="{{ route('user.register') }}" method="POST" onsubmit="return submitUserForm();">
                    @csrf

                    <div class="form-group">
                        <label for="firstname">@lang('First Name')</label>
                        <input id="firstname" type="text" name="firstname" value="{{ old('firstname') }}" required>
                    </div>

                    <div class="form-group">
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

                    <div class="form-group">
                        <label for="mobile">@lang('Mobile')</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text mobile-code"></span>
                                <input type="hidden" name="mobile_code">
                                <input type="hidden" name="country_code">
                            </div>
                            <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}" class="form-control form-lg checkUser" placeholder="@lang('Your Phone Number')">
                            <small class="text--danger mobileExist d-block"></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">@lang('Email')</label>
                        <input id="email" type="email" class=" checkUser" name="email" value="{{ old('email') }}" required>
                    </div>


                    <div class="form-group">
                        <label for="username">@lang('Username')</label>
                        <div class="multi-group">
                            <input id="username" type="text" class="checkUser w-100" name="username" value="{{ old('username') }}" required>
                            <small class="text--danger usernameExist d-block"></small>
                        </div>
                    </div>
                    <input name="sc" value="1" hidden>



                    <div class="form-group hover-input-popup">
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

                    <div class="form-group">
                        <label for="password-confirm">@lang('Confirm Password')</label>
                        <div class="multi-group">
                            <input id="password-confirm" class="w-100" type="password" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div> 

                    @include($activeTemplate.'partials.custom_captcha')
 
                    <div class="contact-group">
                        <div class="m--10 d-flex flex-wrap align-items-center w-100 justify-content-between">
                            <button type="submit" id="recaptcha" class="cmn--btn text-white">@lang('Register')</button>
                        </div>
                    </div>
                </form>
                </div>
  </div>
</section>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/secure_password.css') }}">
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
                var url = `{{ route('user.checkUser') }}`;
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

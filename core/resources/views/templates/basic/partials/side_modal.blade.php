    <!-- ===========Cart=========== -->
    <div class="cart-sidebar-area" id="cart-sidebar-area">
        @include($activeTemplate.'partials.side_modal_logo')
        <div class="bottom-content">
            <div class="cart-products cart--products">

            </div>
        </div>
    </div>
    <!-- ===========Cart End=========== -->

    <!-- ===========Wishlist=========== -->
    <div class="cart-sidebar-area" id="wish-sidebar-area">
        @include($activeTemplate.'partials.side_modal_logo')

        <div class="bottom-content">
            <div class="cart-products wish-products">

            </div>
        </div>
    </div>
    <!-- ===========Wishlist End=========== -->


    <!-- Header Section Ends Here -->
    <div class="dashboard-menu before-login-menu d-flex flex-wrap justify-content-center flex-column" id="account-sidebar-area">
        <span class="side-sidebar-close-btn"><i class="las la-times"></i></span>
        @guest
            <div class="login-wrapper py-5 px-4">
                <h4 class="subtitle cl-white">@lang('My Account')</h4>
                <form method="POST" action="{{ route('user.login')}}" class="sign-in-form">
                    @csrf
                    <div class="form-group">
                        <label for="login-username">@lang('Username')</label>
                        <input type="text" class="form-control" name="username" id="login-username" value="{{ old('email') }}" placeholder="@lang('Username')">
                    </div>

                    <div class="form-group">
                        <label for="login-pass">@lang('Password')</label>
                        <input type="password" class="form-control" name="password" id="login-pass" placeholder="********">
                    </div>


                    @php $captcha = loadCustomCaptcha(46, '100%') @endphp
                    
                    @if($captcha)
                        <div class="form-group">
                        <label for="password">@lang('Captcha')</label>
                            @php echo $captcha @endphp
                            <input type="text" class="mt-3" name="captcha" autocomplete="off" placeholder="@lang('Verify Captcha')">
                        </div>
                    @endif

                    <div class="form-group text-right pt-2">
                        <button type="submit" class="login-button">@lang('Login')</button>
                    </div>

                    <div class="pt-2 mb-0">
                        <p class="create-accounts">
                            <a href="{{route('user.password.request')}}" class="mb-2">@lang('Forgot Password')?</a>
                        </p>
                        <p class="create-accounts">
                            <span>@lang('Don\'t have an account')?
                                 <a href="{{route('user.register')}}" class="btn btn--white text--dark btn-sm mt-2">@lang('Create An Account')</a> 
                            </span>
                        </p>
                    </div>
                </form>
            </div>
        @endguest

        @auth
        @include($activeTemplate.'user.partials.dp')
        <ul class="cl-white">
            @include($activeTemplate.'user.partials.sidebar')
        </ul>
        @endauth

    </div>

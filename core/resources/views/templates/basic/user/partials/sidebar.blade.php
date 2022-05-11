
<div class="user">
    <span class="side-sidebar-close-btn"><i class="las la-times"></i></span>
   <br><br><br><br>
    <div class="thumb">
        <a href="{{ route('user.profile.setting') }}">
            <img src="{{ getAvatar(imagePath()['profile']['user']['path'].'/'.auth()->user()->image) }}" alt="@lang('user')">
        </a>
    </div>
    <div class="content">
        <h6 class="title">
        
            <a class="text--base" href="{{ auth()->user()->fullname }}" class="cl-white">
                {{ auth()->user()->fullname }}</a></h6>
    </div>
</div>

<li>
    <a href="{{ route('user.home') }}" class="{{ menuActive('user.home') }}"> <i class="las la-home"></i>@lang('Dashboard')</a>
</li>

<li>
    <a href="{{ route('user.fundwallet') }}" class="{{ menuActive('user.fundwallet') }}"> <i class="las la-wallet"></i>@lang('Deposit')</a>
</li>

<li>
    <a href="{{ route('user.airtime') }}" class="{{ menuActive('user.airtime') }}"><i class="las la-phone"></i>@lang('Buy Airtime')</a>
</li>

<li>
    <a href="{{ route('user.internet') }}" class="{{ menuActive('user.internet') }}"><i class="las la-wifi"></i>@lang('Internet Data')</a>
</li>

<li>
    <a href="{{ route('user.cabletv') }}" class="{{ menuActive('user.cabletv') }}"><i class="las la-desktop"></i>@lang('Cable TV')</a>
</li>

<li>
    <a href="{{ route('user.utility') }}" class="{{ menuActive('user.utility') }}"><i class="las la-bolt"></i>@lang('Utility Bills')</a>
</li>


<li>
    <a href="{{ route('user.profile.setting') }}" class="{{ menuActive('user.profile.setting') }}"><i class="las la-user-alt"></i>@lang('Profile')</a>
</li>

<li>
    <a href="{{ route('user.profile.referral') }}" class="{{ menuActive('user.profile.referral') }}"><i class="las la-users"></i>@lang('Referral')</a>
</li>

<li>
    <a href="{{ route('user.deposit.history') }}" class="{{ menuActive('user.deposit.history') }}"><i class="las la-money-bill-wave"></i>@lang('Payment Log')</a>
</li>

<li>
    <a href="{{route('user.orders', 'all')}}" class="{{ menuActive('user.orders') }}"><i class="las la-list"></i>@lang('Order Log')</a>
</li>

<li>
    <a href="{{route('user.product.review')}}" class="{{ menuActive('user.product.review') }}"><i class="la la-star"></i> @lang('Review Products')</a>
</li>

<li>
    <a href="{{route('ticket')}}" class="{{ menuActive('ticket*') }}"><i class="la la-ticket"></i> @lang('Support Tickets')</a>
</li>

<li>
    <a href="{{route('user.password.change')}}" class="{{ menuActive('user.password.change') }}"><i class="la la-key"></i> @lang('Account Password')</a>
</li>
<li>
    <a href="{{route('user.twofactor')}}" class="{{ menuActive('user.twofactor') }}"><i class="la la-lock"></i> @lang('2FA Security')</a>
</li>

<li>
    <a href="{{ route('user.logout') }}"><i class="la la-sign-out"></i>@lang('Sign Out')</a>
</li>

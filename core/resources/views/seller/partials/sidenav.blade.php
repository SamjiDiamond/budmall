@php
    $sidebar = sellerSidebarVariation();
@endphp

<div class="sidebar {{ $sidebar['selector'] }} {{ $sidebar['sidebar'] }} {{ @$sidebar['overlay'] }} {{ @$sidebar['opacity'] }}" data-background="{{getImage('assets/dashboard/images/sidebar/4.jpg')}}">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{route('admin.dashboard')}}" class="sidebar__main-logo">
                <img src="{{getImage(imagePath()['logoIcon']['path'] .'/logo_2.png')}}" alt="@lang('image')">
            </a>
            <a href="{{route('admin.dashboard')}}" class="sidebar__logo-shape">
                <img src="{{getImage(imagePath()['logoIcon']['path'] .'/favicon.png')}}" alt="@lang('image')">
            </a>
            <button type="button" class="navbar__expand"></button>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">

                <li class="sidebar-menu-item bg--white mb-2">
                    <span class="menu-title nav-link font-weight-bold @if(seller()->balance> 0)text--success @else text--danger @endif">@lang('Current Balance'): {{ $general->cur_sym.showAmount(seller()->balance) }}</span>
                </li>

                <li class="sidebar-menu-item {{menuActive('seller.home')}}">
                    <a href="{{route('seller.home')}}" class="nav-link ">
                        <i class="menu-icon la la-dashboard"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{menuActive('seller.shop*')}}">
                    <a href="{{route('seller.shop')}}" class="nav-link ">
                        <i class="menu-icon las la-store-alt"></i>
                        <span class="menu-title">@lang('My Shop')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('seller.products*', 3)}}">
                        <i class="la la-product-hunt menu-icon"></i>
                        <span class="menu-title">@lang('Manage Product')</span>
                    </a>

                    <div class="sidebar-submenu {{menuActive('seller.products*', 2)}} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('seller.products.all') }}">
                                <a class="nav-link" href="{{ route('seller.products.all') }}">
                                    <i class="menu-icon las la-tshirt"></i>
                                    <span class="menu-title">@lang('All Products')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('seller.products.pending') }}">
                                <a class="nav-link" href="{{ route('seller.products.pending') }}">
                                    <i class="menu-icon las la-hourglass"></i>
                                    <span class="menu-title">@lang('Pending Products')</span>

                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('seller.products.reviews') }}">
                                <a class="nav-link" href="{{ route('seller.products.reviews') }}">
                                    <i class="menu-icon las la-star"></i>
                                    <span class="menu-title">@lang('Product Reviews')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('seller.order*',3)}}">
                        <i class="las la-money-bill menu-icon"></i>
                        <span class="menu-title">@lang('Orders')</span>
                        @if($pending_orders_count > 0 || $processing_orders_count || $dispatched_orders_count > 0)
                        <span class="menu-badge pill bg--primary ml-auto">
                            <i class="las la-bell"></i>
                        </span>
                        @endif
                    </a>

                    <div class="sidebar-submenu {{menuActive('seller.order*',2)}} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('seller.order.index') }}">
                                <a class="nav-link" href="{{ route('seller.order.index') }}">
                                    <i class="menu-icon las la-list-ol"></i>
                                    <span class="menu-title">@lang('All Orders')</span>

                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('seller.order.to_deliver')}}">
                                <a class="nav-link" href="{{ route('seller.order.to_deliver') }}">
                                    <i class="menu-icon las la-pause-circle"></i>
                                    <span class="menu-title">@lang('Pending Orders')</span>
                                    @if($pending_orders_count > 0)
                                     <span class="badge bg--primary badge-pill ml-2"><i class="fas fa-exclamation"></i></span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('seller.order.on_processing') }}">
                                <a class="nav-link" href="{{ route('seller.order.on_processing') }}">
                                    <i class="menu-icon las la-spinner"></i>
                                    <span class="menu-title">@lang('Processing Orders')</span>
                                    @if($processing_orders_count > 0)
                                    <span class="badge bg--primary badge-pill ml-2"><i class="fas fa-exclamation"></i></span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('seller.order.dispatched') }}">
                                <a class="nav-link" href="{{ route('seller.order.dispatched') }}">
                                    <i class="menu-icon las la-shopping-basket"></i>
                                    <span class="menu-title">@lang('Dispatched Orders')</span>
                                    @if($dispatched_orders_count > 0)
                                    <span class="badge bg--primary badge-pill ml-2"><i class="fas fa-exclamation"></i></span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('seller.order.delivered') }}">
                                <a class="nav-link" href="{{ route('seller.order.delivered') }}">
                                    <i class="menu-icon las la-check-circle"></i>
                                    <span class="menu-title">@lang('Delivered Orders') </span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('seller.order.canceled') }}">
                                <a class="nav-link" href="{{ route('seller.order.canceled') }}">
                                    <i class="menu-icon las la-times-circle"></i>
                                    <span class="menu-title">@lang('Canceled Orders')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('seller.order.cod') }}">
                                <a class="nav-link" href="{{ route('seller.order.cod') }}">
                                    <i class="menu-icon las la-hand-holding-usd"></i>
                                    <span class="menu-title"><abbr data-toggle="tooltip" title="@lang('Cash On Delivery')">{{ @$deposit->gateway->name??trans('COD') }}</abbr> @lang('Orders')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item {{ menuActive('seller.sell.log') }}">
                    <a class="nav-link" href="{{ route('seller.sell.log') }}">
                        <i class="menu-icon las la-file-invoice-dollar"></i>
                        <span class="menu-title">@lang('Sales Log')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('seller.trx.log') }}">
                    <a class="nav-link" href="{{ route('seller.trx.log') }}">
                        <i class="menu-icon las la-exchange-alt"></i>
                        <span class="menu-title">@lang('Transaction Log')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('seller.withdraw*', 3)}}">
                        <i class="menu-icon la la-money"></i>
                        <span class="menu-title">@lang('Withdraw')</span>
                    </a>
                    <div class="sidebar-submenu {{menuActive('seller.withdraw*', 2)}} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive(['seller.withdraw.money','seller.withdraw.preview']) }}">
                                <a class="nav-link" href="{{ route('seller.withdraw.money') }}">
                                    <i class="menu-icon la la-money"></i>
                                    <span class="menu-title">@lang('Withdraw Money')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('seller.withdraw.history') }}">
                                <a class="nav-link" href="{{ route('seller.withdraw.history') }}">
                                    <i class="menu-icon las la-history"></i>
                                    <span class="menu-title">@lang('Withdrawal History')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item {{ menuActive('seller.twofactor') }}">
                    <a class="nav-link" href="{{ route('seller.twofactor') }}">
                        <i class="menu-icon la la-shield"></i>
                        <span class="menu-title">@lang('2FA Security')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive(['seller.ticket*']) }}">
                    <a class="nav-link" href="{{ route('seller.ticket.index') }}">
                        <i class="menu-icon las la-headset"></i>
                        <span class="menu-title">@lang('Support')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a class="nav-link" href="{{ route('seller.logout') }}">
                        <i class="menu-icon las la-sign-out-alt"></i>
                        <span class="menu-title">@lang('Log Out')</span>
                    </a>
                </li>
            </ul>


            <div class="text-center mb-3 text-uppercase">
                <span class="text--primary">{{__(systemDetails()['name'])}}</span>
                <span class="text--success">@lang('V'){{systemDetails()['version']}} </span>
            </div>
        </div>
    </div>
</div>
<!-- sidebar end -->


@push('style')
    <style>
        .sidebar[class*="bg--"] .sidebar__menu .sidebar-submenu .sidebar-menu-item:hover a .menu-title {
            color: inherit;
        }

        .sidebar[class*="overlay--white"] .sidebar__menu .sidebar-menu-item.active > a {
            background-color: rgb(209 209 209);
        }
    </style>
@endpush

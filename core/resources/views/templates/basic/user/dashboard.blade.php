@extends($activeTemplate.'layouts.frontend')
@section('content')


<div class="dashboard-section padding-bottom padding-top">
    <div class="container">
        <div class="row">
            <div class="col-xl-3">
                <div class="dashboard-menu">
                    @include($activeTemplate.'user.partials.dp')
                    <ul>
                        @include($activeTemplate.'user.partials.sidebar')
                    </ul>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="row justify-content-center mb-30-none">
<div class="col-sm-12 col-lg-12">
                        <div class="dashboard-item">
                            <a href="{{ route('user.profile.referral') }}" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-sitemap"></i>
                                </span>
                                <div class="cont">

                                    <div class="dashboard-header">
                                        <h3 class="title">${{number_format($rwallet->balance,2)}}
                                        </h3>
                                     </div>
                                    @lang('Referral Balance')
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.fundwallet')}}" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-wallet"></i>
                                </span>
                                <div class="cont">

                                    <div class="dashboard-header">
                                        <h3 class="title">${{number_format($wallet->balance,2)}}
                                        </h3>
                                       {{-- <small>{{$general->cur_sym}}{{ number_format($wallet->balance,2) }}</small>--}}
                                    </div>
                                    @lang('Wallet Balance')
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'all')}}" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-list-ol"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('All Orders')
                                </div>
                            </a>
                        </div>
                    </div>



                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'pending')}}">
                                <span class="dashboard-icon">
                                    <i class="las la-clipboard-list"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->where('status', 0)->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('Pending Orders')
                                </div>
                            </a>
                        </div>
                    </div>


                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'processing')}}" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-list-ul"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->where('status', 1)->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('Processing Orders')
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'dispatched')}}" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-th-list"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->where('status',2)->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('Dispatched Orders')
                                </div>
                            </a>
                        </div>
                    </div>


                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'completed')}}" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-list-alt"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->where('status', 3)->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('Order Completed')
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'canceled')}}">
                                <span class="dashboard-icon">
                                    <i class="las la-times"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->where('status', 4)->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('Canceled Orders')
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

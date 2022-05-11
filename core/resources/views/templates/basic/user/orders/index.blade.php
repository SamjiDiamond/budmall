@extends($activeTemplate.'layouts.frontend')
@section('content')
<div class="payment-history-section padding-bottom padding-top">
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
                <table class="payment-table section-bg">
                    <thead>
                        <tr>
                            <th>@lang('S.N.')</th>
                            <th>@lang('Order ID')</th>
                            <th class="text-center">@lang('Products')</th>
                            <th class="text-center">@lang('Payment')</th>
                            <th class="text-center">@lang('Order')</th>
                            <th class="text-center">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                        <tr>
                            <td data-label="@lang('#')">{{$loop->iteration}}</td>
                            <td data-label="@lang('Order ID')">
                                <a href="{{route('user.order', $order->order_number)}}" class="text-info">{{$order->order_number}}</a>
                            </td>
                            <td data-label="@lang('Total Products')" class="text-center">{{$order->orderDetail->sum('quantity')}}</td>


                            <td data-label="@lang('Payment')">
                                <span class="text-white badge badge-capsule
                                    @if($order->payment_status==0)
                                        badge--danger
                                    @elseif($order->payment_status==2)
                                        badge--primary
                                    @else
                                        badge--success
                                    @endif
                                    ">
                                    @if($order->payment_status==0)
                                        @lang('Incomplete')
                                    @elseif($order->payment_status==2)
                                        <abbr data-toggle="tooltip" title="@lang('Cash On Delivery')">{{ @$deposit->gateway->name??trans('COD') }}</abbr>
                                    @else
                                        @lang('Paid')
                                    @endif
                                </span>
                            </td>

                            <td data-label="@lang('Order Status')">
                                <span class="text-white badge badge-capsule
                                    @if($order->payment_status==0)
                                        badge--danger
                                    @else
                                        @if ($order->status == 0)
                                            badge--primary
                                            @elseif ($order->status == 1)
                                            badge--primary
                                        @elseif ($order->status == 2)
                                            badge--info
                                            @elseif ($order->status == 3)
                                            badge--success
                                        @elseif($order->status == 4)
                                            badge--danger
                                        @endif
                                    @endif
                                    ">
                                    @if($order->payment_status==0)
                                    @lang('Incomplete')
                                    @else
                                        @if($order->status == 0)
                                            @lang('Pending')
                                        @elseif ($order->status == 1)
                                            @lang('Processing')...
                                        @elseif ($order->status == 2)
                                            @lang('Dispatched')
                                        @elseif ($order->status == 3)
                                            @lang('Received')
                                            @elseif($order->status == 4)
                                            @lang('Cancelled')
                                        @endif
                                    @endif

                                </span>
                            </td>
                            <td data-label="@lang('Action')" class="text-center">
                                <a href="{{route('user.order', $order->order_number)}}" class="btn-normal-2 btn-sm"> <i class="fas fa-desktop"></i></a>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $orders->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


@push('breadcrumb-plugins')
    <li><a href="{{route('user.home')}}">@lang('Dashboard')</a></li>
@endpush

@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body">
                <div class="row justify-content-end mb-2">
                    <div class="col-lg-3">
                        <form action="
                            @if(request()->routeIs('admin.report.order') ||request()->routeIs('admin.report.order.search'))
                                {{ route('admin.report.order.search') }}
                            @else
                                {{ route('admin.report.order.user_search', $user->id) }}
                            @endif
                            " method="GET">
                                <div class="input-group has_append">
                                    <input type="text" name="search" class="form-control" placeholder="Order ID" value="{{ $key ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn--primary box--shadow1" id="search-btn" type="submit"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                        </form>

                    </div>
                    <div class="col-lg-3">

                        <form action="
                            @if(request()->routeIs('admin.report.order') ||request()->routeIs('admin.report.order.search'))
                            {{ route('admin.report.order.search') }}
                            @else
                                {{ route('admin.report.order.user_search', $user->id) }}
                            @endif
                            " method="GET"
                        >
                            <div class="input-group has_append ">
                                <input name="date" type="text" data-range="true" data-multiple-dates-separator=" to " data-language="en" class="datepicker-here form-control" data-position='bottom right' placeholder="Date to Date Search" autocomplete="off" value="" data-date-format="dd-mm-yyyy">
                                <div class="input-group-append">
                                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col">@lang('Date')</th>
                                <th scope="col">@lang('Order ID')</th>
                                <th scope="col">@lang('Customer')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td data-label="@lang('Date')">{{ showDateTime($order->created_at) }}</td>
                                <td data-label="@lang('Order ID')" class="font-weight-bold">
                                    <a href="{{ route('admin.order.details', $order->id) }}">{{ strtoupper($order->order_number) }}</a>
                                </td>

                                <td data-label="@lang('Customer')">
                                    <a href="{{ route('admin.users.detail', $order->user->id) }}">{{ $order->user->username }} </a>
                                </td>

                                <td scope="row" data-label="@lang('Status')" >
                                    <span class="text--small badge font-weight-normal
                                        @if($order->status == 0)
                                            {{'badge--warning'}}
                                        @elseif($order->status == 1)
                                            {{'badge--primary'}}

                                        @elseif($order->status == 2)
                                            {{'badge--dark'}}
                                        @elseif($order->status == 3)
                                            {{'badge--success'}}
                                        @elseif($order->status == 4)
                                            {{'badge--danger'}}
                                        @endif
                                        ">
                                        @if($order->status == 0)
                                        {{__('Pending')}}
                                        @elseif($order->status == 1)
                                        {{__('Processing')}}...
                                        @elseif($order->status == 2)
                                            {{__('Dispatched')}}
                                        @elseif($order->status == 3)
                                            {{__('Delivered')}}
                                        @elseif($order->status == 4)
                                            {{__('Canceled')}}
                                        @endif
                                    </span>
                                </td>

                                <td data-label="@lang('Amount')" class="budget">{{ $general->cur_sym }}{{ getAmount($order->total_amount) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($orders->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($orders) }}
                </div>
            @endif
        </div>
    </div>
</div>


@endsection

@push('script')

<script src="{{asset('assets/dashboard/js/vendor/datepicker.min.js')}}"></script>
<script src="{{asset('assets/dashboard/js/vendor/datepicker.en.js')}}"></script>
<script>
    // date picker

    'use strict';
    (function($){
        $('.datepicker-here').datepicker();
    })(jQuery)
</script>
@endpush



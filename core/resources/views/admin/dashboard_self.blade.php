@extends('admin.layouts.app')

@section('panel')

<div class="row mb-none-30">
    <div class="col-xl-4 col-md-6 mb-30">
        <div class="widget bb--3 border--10 b-radius--10 bg--white p-4 box--shadow2 has--link">
            <div class="widget__icon b-radius--rounded bg--10"><i class="las la-cart-arrow-down"></i></div>
            <div class="widget__content">
                <p class="text-uppercase text-muted">@lang('All Orders')</p>
                <h1 class="text--10 font-weight-bold">
                  {{$order['all']}}
                </h1>
                <p class="mt-10 text-right">
                    <a class="btn btn-sm text--white bg--10" href="{{route('admin.order.index')}}">@lang('View All')
                    </a>
                </p>
            </div>
        </div><!-- widget end -->
    </div>
    <div class="col-xl-4 col-md-6 mb-30">
        <div class="widget bb--3 border--warning b-radius--10 bg--white p-4 box--shadow2 has--link">
            <div class="widget__icon b-radius--rounded bg--warning"><i class="las la-cart-arrow-down"></i></div>
            <div class="widget__content">
                <p class="text-uppercase text-muted">@lang('Pending Orders')</p>
                <h1 class="text--dark font-weight-bold">
                  {{$order['pending']}}
                </h1>
                <p class="mt-10 text-right">
                    <a class="btn btn-sm btn--dark" href="{{route('admin.order.to_deliver')}}">@lang('View All')
                    </a>
                </p>
            </div>
        </div><!-- widget end -->
    </div>

    <div class="col-xl-4 col-md-6 mb-30">
        <div class="widget bb--3 border--teal b-radius--10 bg--white p-4 box--shadow2 has--link">
            <div class="widget__icon b-radius--rounded bg--teal">
                <i class="las la-cart-arrow-down"></i>
            </div>
            <div class="widget__content">
                <p class="text-uppercase text-muted">@lang('Proccessing Orders')</p>

                <h1 class="text--teal font-weight-bold">
                   {{$order['processing']}}
                </h1>
                <p class="mt-10 text-right">
                    <a class="btn btn-sm bg--teal text-white" href="{{route('admin.order.on_processing')}}">@lang('View All')
                    </a>
                </p>
            </div>
        </div><!-- widget end -->
    </div>

    <div class="col-xl-4 col-md-6 mb-30">
        <div class="widget bb--3 border--light-blue b-radius--10 bg--white p-4 box--shadow2 has--link">
            <div class="widget__icon b-radius--rounded bg--light-blue">
                <i class="las la-cart-arrow-down"></i>
            </div>
            <div class="widget__content">
                <p class="text-uppercase text-muted">@lang('Dispatched Orders')</p>
                <h1 class="text--light-blue font-weight-bold">
                    {{$order['dispatched']}}
                </h1>
                <p class="mt-10 text-right">
                    <a class="btn btn-sm bg--light-blue text-white" href="{{route('admin.order.dispatched')}}">@lang('View All')
                    </a>
                </p>
            </div>
        </div><!-- widget end -->
    </div>

    <div class="col-xl-4 col-md-6 mb-30">
        <div class="widget bb--3 border--success b-radius--10 bg--white p-4 box--shadow2 has--link">
            <div class="widget__icon b-radius--rounded bg--success">
                <i class="las la-cart-arrow-down"></i>
            </div>

            <div class="widget__content">
                <p class="text-uppercase text-muted">@lang('Delivered Orders')</p>
                <h1 class="text--success font-weight-bold">
                    {{$order['dispatched']}}
                </h1>

                <p class="mt-10 text-right">
                    <a class="btn btn-sm bg--success text--white" href="{{route('admin.order.delivered')}}">
                        @lang('View All')
                    </a>
                </p>
            </div>
        </div><!-- widget-two end -->
    </div>


    <div class="col-xl-4 col-md-6 mb-30">
        <div class="widget bb--3 border--danger b-radius--10 bg--white p-4 box--shadow2 has--link">
            <div class="widget__icon b-radius--rounded bg--danger">
                <i class="las la-comment-slash"></i>
            </div>
            <div class="widget__content">
                <p class="text-uppercase text-muted">@lang('Cancelled Orders')</p>
                <h1 class="text--danger font-weight-bold">
                    {{$order['cancelled']}}
                </h1>
                <p class="mt-10 text-right">
                    <a class="btn btn-sm btn--danger" href="{{route('admin.order.canceled')}}">@lang('View All')
                    </a>
                </p>
            </div>
        </div><!-- widget end -->
    </div>

   <!-- <div class="col-xl-4 col-md-6 mb-30">
        <div class="widget bb--3 border--deep-purple b-radius--10 bg--white p-4 box--shadow2 has--link">
            <div class="widget__icon b-radius--rounded bg--deep-purple">
                <i class="las la-shipping-fast"></i>
            </div>
            <div class="widget__content">
                <p class="text-uppercase text-muted">@lang('COD Orders')</p>
                <h1 class="text--deep-purple font-weight-bold">
                    {{$order['cod']}}
                </h1>
                <p class="mt-10 text-right">
                    <a class="btn btn-sm bg--deep-purple text--white" href="{{route('admin.order.cod')}}">@lang('View All')
                    </a>
                </p>
            </div>
        </div><!-- widget end 
    </div>-->
    <div class="col-xl-4 col-md-6 mb-30">
        <div class="widget bb--3 border--indigo b-radius--10 bg--white p-4 box--shadow2 has--link">
            <div class="widget__icon b-radius--rounded bg--indigo">
                <i class="las la-tshirt"></i>
            </div>
            <div class="widget__content">
                <p class="text-uppercase text-muted">@lang('Total Products')</p>
                <h1 class="text--indigo font-weight-bold">
                    {{$product['total']}}
                </h1>
                <p class="mt-10 text-right">
                    <a class="btn btn-sm bg--indigo text--white" href="{{route('admin.products.all')}}">@lang('View All')
                    </a>
                </p>
            </div>
        </div><!-- widget end -->
    </div>

    <div class="col-xl-4 col-md-6 mb-30">
        <div class="widget bb--3 border--black b-radius--10 bg--white p-4 box--shadow2 has--link">
            <div class="widget__icon b-radius--rounded bg--black">
                <i class="las la-file-invoice-dollar"></i>
            </div>
            <div class="widget__content">
                <p class="text-uppercase text-muted">@lang('Total Sold')</p>
                <h1 class="text--black font-weight-bold">
                    {{$product['total_sold']}}
                </h1>
                <p class="mt-10 text-right">
                    <a class="btn btn-sm bg--black text--white" href="{{route('admin.order.sells.log.admin')}}">@lang('View All')
                    </a>
                </p>
            </div>
        </div><!-- widget end -->
    </div>

</div><!-- row end-->

<div class="row mt-5 mb-none-30">
    <div class="col-md-12 mb-3">
        <h4>@lang('Sales Log')</h4>
    </div>
    <div class="col-xl-4 mb-30">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
          <i class="icon-7 overlay-icon text text--11"></i>
          <div class="widget-two__icon b-radius--5 bg--11">
            <i class="las la-money-bill"></i>
          </div>
          <div class="widget-two__content">
            <h2>{{$general->cur_sym}}{{getAmount($sale['last_seven_days'])}}</h2>
            <p>@lang('Sale Amount In Last 7 Days')</p>
          </div>
        </div><!-- widget-two end -->
    </div>

    <div class="col-xl-4 mb-30">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
          <i class="icon-15 overlay-icon text text--dark"></i>
          <div class="widget-two__icon b-radius--5 bg--15">
            <i class="las la-money-bill"></i>
          </div>
          <div class="widget-two__content">
            <h2>{{$general->cur_sym}}{{getAmount($sale['last_fifteen_days'])}}</h2>
            <p>@lang('Sale Amount In Last 15 Days')</p>
          </div>
        </div><!-- widget-two end -->
    </div>

    <div class="col-xl-4 mb-30">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
          <i class="icon-30 overlay-icon text text--danger"></i>
          <div class="widget-two__icon b-radius--5 bg--5">
            <i class="las la-money-bill"></i>
          </div>
          <div class="widget-two__content">
            <h2>{{$general->cur_sym}}{{getAmount($sale['last_thirty_days'])}}</h2>
            <p>@lang('Sale Amount In Last 30 Days')</p>
          </div>
        </div><!-- widget-two end -->
    </div>
</div>

<div class="row mt-50 mb-none-30">
    <div class="col-xl-6 mb-30">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">@lang('Monthly Sales Report')</h5>
                <div id="apex-bar-chart"> </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-12 mb-30">
        <div class="card min-height-500">
            <div class="card-body">
                <h5 class="card-title">@lang('Top Selling Products')</h5>
                @forelse($product['top_selling_products']->where('seller_id',0) as $item)
                    @php
                        if($item->offer && $item->offer->activeOffer){
                            $discount = calculateDiscount($item->offer->activeOffer->amount, $item->offer->activeOffer->discount_type, $item->base_price);
                        }else $discount = 0;
                    @endphp

                    <div class="d-flex flex-wrap single-product mt-30">
                        <a href="{{ route('product.details', [$item->id, slug($item->name)]) }}" data-toggle="tooltip" data-placement="bottom" title="@lang('View As Customer')" class="col-md-2 text-center"><img src="{{ getImage(imagePath()['product']['path']. '/thumb_'. @$item->main_image, imagePath()['product']['size']) }}" alt="image"></a>

                        <div class="col-md-10 mt-md-0 mt-3">
                            <a href="{{ route('admin.products.edit', [$item->id, slug($item->name)]) }}" data-toggle="tooltip" data-placement="top" title="@lang('Edit')" class="text--blue font-weight-bold d-inline-block mb-2">{{ __($item->name) }}</a>
                            <p class="float-right">{{ $item->total }} @lang('sales')</p>
                            <p>{{ __(shortDescription($item->summary, 100)) }}</p>
                            <p class="font-weight-bold">

                                @if($discount > 0)
                                    <del>{{ $general->cur_sym }}{{ getAmount($item->base_price, 2) }}</del>
                                    <span class="ml-2">{{ $general->cur_sym }}{{ getAmount($item->base_price - $discount, 2) }}</span>
                                @else
                                    <span class="ml-2">{{ $general->cur_sym }}{{ getAmount($item->base_price, 2) }}</span>
                                @endif
                            </p>
                        </div>
                    </div><!-- media end-->
                @empty
                    <h3 class="mt-5 text-center">@lang('No Sale Yet')</h3>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row mt-5 mb-30">
    <div class="col-md-12">
        <div class="card b-radius--10 ">
            <div class="card-header">
                <h4>@lang('Latest Orders')</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th class="text-left">@lang('Order Date')</th>
                                <th class="text-left">@lang('Customer')</th>
                                <th class="text-left">@lang('Order ID')</th>
                                <th class="text-right">@lang('Amount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($latestOrders as $item)
                            <tr>
                                <td data-label="@lang('Order Date')" class="text-left">
                                    {{ showDateTime($item->created_at, 'd M, Y') }}
                                </td>

                                <td data-label="@lang('Customer')" class="text-left">
                                    @if ($item->order->user)
                                     {{ $item->order->user->username }}
                                    @endif
                                </td>
                                <td data-label="@lang('Order ID')" class="text-left">
                                    {{ @$item->order->order_number }}
                                </td>



                                <td data-label="@lang('Amount')" class="text-right">
                                    <b>{{ $general->cur_sym.(getAmount($item->total_price)) }}</b>
                                </td>

                                <td data-label="@lang('Action')">
                                    <span class="badge
                                        @if($item->order->status == 0)
                                            {{'badge--warning'}}
                                        @elseif($item->order->status == 1)
                                            {{'badge--primary'}}

                                        @elseif($item->order->status == 2)
                                            {{'badge--dark'}}
                                        @elseif($item->order->status == 3)
                                            {{'badge--success'}}
                                        @elseif($item->order->status == 4)
                                            {{'badge--danger'}}
                                        @endif
                                            ">

                                        @if($item->order->status == 0)
                                        {{'Pending'}}
                                        @elseif($item->order->status == 1)
                                        {{'Processing...'}}
                                        @elseif($item->order->status == 2)
                                            {{'Dispatched'}}
                                        @elseif($item->order->status == 3)
                                            {{'Delivered'}}
                                        @elseif($item->order->status == 4)
                                            {{'Canceled'}}

                                        @endif
                                    </span>
                                </td>
                                <td data-label="Action">

                                    <a href="{{ route('admin.order.details', $item->order->id) }}" data-toggle="tooltip" title="@lang('View')" class="icon-btn btn--dark mr-1">
                                        <i class="la la-desktop"></i>
                                    </a>

                                    @if(!request()->routeIs('admin.order.notpaid'))
                                    <button type="button" class="approveBtn icon-btn btn--success {{$item->status >= 3?'disabled':''}} text-white" data-toggle="tooltip" data-action="{{ $item->status+1 }}" data-id='{{$item->id}}'
                                    title="
                                    @if($item->status == 0)
                                        {{ __('Mark as Processing') }}
                                    @elseif($item->status == 1)
                                        {{ __('Mark as Dispatched') }}
                                    @elseif($item->status == 2)
                                            {{ __('Mark as  Delivered') }}
                                    @endif
                                    ">
                                        <i class="la la-check"></i>
                                    </button>

                                    <button type="button" class="{{ ($item->status==0 || $item->status==4)?'approveBtn':'' }} icon-btn btn--{{$item->status==4?'warning':'danger'}} {{ ($item->status==0 || $item->status==4)?'':'disabled' }}" data-toggle="tooltip" data-action="{{ ($item->status==4)?0:4 }}" data-id='{{$item->id}}' title="{{$item->status==4?__('Retake'):__('Cancel')}}">
                                        <i class="la la-{{$item->status==4?'reply':'ban'}}"></i>
                                    </button>

                                    @else

                                    <button type="button" class="icon-btn btn--danger" data-toggle="modal" data-target="#deleteModal" data-id='{{$item->id}}'>
                                        <i class="la la-trash"></i>
                                    </button>

                                    @endif

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">@lang('No order found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form action="{{ route('admin.order.status') }}" method="POST" id="deliverPostForm">
            @csrf
            <input type="hidden" name="id" id="oid">
            <input type="hidden" name="action" id="action">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">@lang('Confirmation Alert')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-bold">

                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--success">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script src="{{asset('assets/dashboard/js/vendor/apexcharts.min.js')}}"></script>
     <script>
            'use strict';
        // apex-bar-chart js
        var options = {
            series: [{
                name: 'Total Sold',
                data: [
                  @foreach($months as $month)
                    {{ getAmount(@$sellMonth->where('months',$month)->first()->totalAmount) }},
                  @endforeach
                ]
            }],
            chart: {
                type: 'bar',
                height: 410,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json($months),
            },
            yaxis: {
                title: {
                    text: "{{__($general->cur_sym)}}",
                    style: {
                        color: '#7c97bb'
                    }
                }
            },
            grid: {
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "{{__($general->cur_sym)}}" + val + " "
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#apex-bar-chart"), options);
        chart.render();

    </script>


    <script>
    $('.approveBtn').on('click', function () {
        var modal = $('#approveModal');
        $('#oid').val($(this).data('id'));
        var action = $(this).data('action');

        $('#action').val(action);

        if(action == 1){
            $('.text-bold').text("@lang('Are you sure to mark the order as processing?')");
        }else if(action ==2){
            $('.text-bold').text("@lang('Are you sure to mark the order as dispatched?')");
        }else if(action ==3){
            $('.text-bold').text("@lang('Are you sure to mark the order as delivered?')");
        }else if(action ==4){
            $('.text-bold').text("@lang('Are you sure to cancel this order?')");
        }else{
            $('.text-bold').text("@lang('Are you sure to retake this order?')");
        }

        modal.modal('show');
    });
    </script>
@endpush


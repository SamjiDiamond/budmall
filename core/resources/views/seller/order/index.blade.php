@extends('seller.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">

                <div class="row justify-content-end">
                    <div class="col-xl-3 mb-3">
                        <form action="" method="GET" class="pt-3 px-3">
                            <div class="input-group has_append">
                                <input type="text" name="search" class="form-control" placeholder="Order ID" value="{{ request()->search }}">
                                <div class="input-group-append">
                                    <button class="btn btn--primary box--shadow1" id="search-btn" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th class="text-left">@lang('Order ID') | @lang('Time')</th>
                                <th class="text-left">@lang('Customer')</th>
                                @if(!request()->routeIs('seller.order.cod'))
                                 <th class="text-left">@lang('Payment Via')</th>
                                @endif
                                 <th class="text-right">@lang('Amount')</th>
                                @if(request()->routeIs('seller.order.index'))
                                    <th>@lang('Status')</th>
                                @endif
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($orders as $item)
                            <tr>
                                <td data-label="@lang('Order ID') | @lang('Time')" class="text-left">
                                    <span class="font-weight-bold d-block text--primary">{{ @$item->order->order_number }}</span>
                                    {{ showDateTime($item->created_at) }}
                                </td>

                                <td data-label="@lang('Customer')" class="text-left">
                                    @if ($item->order->user){{ $item->order->user->username }}@endif
                                </td>

                                @if(!request()->routeIs('seller.order.cod'))
                                <td data-label="@lang('Payment Via')" class="text-left">
                                    @if($item->order->payment_status==2)
                                    <strong class="text-warning"><abbr data-toggle="tooltip" title="@lang('Cash On Delivery')">{{ @$deposit->gateway->name??trans('COD') }}</abbr></strong>
                                    @elseif($item->order->deposit)
                                        <strong class="text-primary">{{ $item->order->deposit->gateway->name }}</strong>
                                    @endif
                                </td>
                                @endif

                                <td data-label="@lang('Amount')" class="text-right">
                                    <b>{{ $general->cur_sym.(getAmount($item->total_price)) }}</b>
                                </td>
                                @if(request()->routeIs('seller.order.index'))

                                <td data-label="@lang('Status')" class="text-center">
                                    @php echo $item->order->statusBadge(); @endphp
                                </td>
                                @endif
                                <td>
                                    <a href="{{route('seller.order.details',$item->order_id)}}" class="icon-btn" data-toggle="tooltip" data-title="@lang('Order Details')"><i class="las la-desktop"></i></a>
                                </td>
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

{{-- DELIVERY METHOD MODAL --}}
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form action="{{ route('seller.order.status') }}" method="POST" id="deliverPostForm">
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
<script>

    'use strict';
    (function($){

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
    })(jQuery)

</script>
@endpush



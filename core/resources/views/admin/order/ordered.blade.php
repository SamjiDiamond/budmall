@extends('admin.layouts.app')

@section('panel')

<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                
                
                <div class="row justify-content-end">
                    
                     <div class="col-xl-3 mb-3">
                        <form action="" method="GET" class="pt-3 px-3">
                            <div class="input-group has_append">
                                <input type="text" name="search" class="form-control" placeholder="Order ID" value="{{ request()->search ?? '' }}">
                                <div class="input-group-append">
                                    <button class="btn btn--primary box--shadow1" id="search-btn" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                  <form action="" method="get" >
                @csrf
                <center>

                <div class="row justify-content-center">
                                  

                    <div class="col-4">
                        <div class="form-group">
                            <label>From Date</label>
                                <input type="datetime-local" name="from" requuired class="form-control" placeholder="Order ID" value="{{ request()->search ?? '' }}">
                                 
                        </div>
                        
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                                                        <label>To Date</label>

                                <input type="datetime-local" name="to" requuired class="form-control" placeholder="Order ID" value="{{ request()->search ?? '' }}">
                                 
                        </div>
                        
                    </div> 
                    
                     <div class="col-4">
  <br>
                                <div class="input-group has_append">
                                 <div class="input-group-append">
                                    <button class="btn btn--primary box--shadow1"   type="submit"><i class="fa fa-eye"></i> Filter</button>
                                </div>
                                  
                        </div>
                        
                    </div> 

                    </div>
                    </center>
                </form>

                @include('admin.partials.datatable')

                <div class="table-responsive--md  table-responsive">
                    <table  id="example" class="table table-light style-two">
                        <thead>
                            <tr>
                                <th>@lang('Order ID')</th>
                                <th>@lang('Time')</th>
                                <th>@lang('Customer')</th>
                                @if(!request()->routeIs('admin.order.cod'))
                                    <th>@lang('Payment Method')</th>
                                @endif
                                @if(request()->routeIs('admin.order.dispatched'))
                                    <th>@lang('State Champion')</th>
                                @endif
                                @if(request()->routeIs('admin.order.delivered'))
                                <th>@lang('State Champion')</th>
                                @endif
                                <th class="text-right">@lang('Amount')</th>
                                <th class="text-right">@lang('Amount USD')</th>
                                @if(request()->routeIs('admin.order.index'))
                                    <th>@lang('Status')</th>
                                @endif
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($orders as $item)
                            <tr>
                                  <td data-label="@lang('Order ID')">
                                    <span class="font-weight-bold d-block text--primary">{{ @$item->order_number }}</span>
                                </td>
                                <td data-label="@lang('Time')">
                                    <span class="font-weight-bold d-block text--primary"> </span>
                                    {{ showDateTime($item->created_at) }}
                                </td>

                                <td data-label="@lang('Customer')">
                                    <a href="{{ route('admin.users.detail', $item->user->id) }}">{{ $item->user->username }}</a>
                                </td>

                                @if(!request()->routeIs('admin.order.cod'))
                                    <td data-label="@lang('Payment Method')">
                                        @if($item->payment_status==2)
                                            <strong class="text-warning"><abbr data-toggle="tooltip" title="@lang('Cash On Delivery')">
                                                Cash On Delivery</abbr>
                                            </strong>
                                        @elseif($item->deposit)
                                            <strong class="text-primary">@if($item->method_code == 0) Deposit Wallet @else {{ App\Models\Gateway::whereCode($item->method_code)->first()->name ?? "Unknown" }} @endif</strong>
                                        @endif
                                    </td>
                                @endif
                                 @if(request()->routeIs('admin.order.dispatched'))
                                    <td data-label="@lang('State Champion')">
                                         
                                            <strong class="text-info"> 
                                                @php 
                                                $dispatcher = App\Models\Admin::whereId($item->dispatcher)->first();
                                                if($dispatcher)
                                                {
                                                $state = App\Models\State::whereId($dispatcher->state)->first();
                                                }
                                                @endphp
                                                {{@$dispatcher->name ?? "N/A"}}<br>
                                                <small> {{@$dispatcher->email }}</small>
                                                
                                            </strong>
                                            <br>
                                            @if(@$state)
                                            <small> <strong class="text-primary">{{ @$state->state ?? "Unknown"}}</strong></small>
                                            @endif
                                        
                                    </td>
                                @endif
                                 @if(request()->routeIs('admin.order.delivered'))
                                 <td data-label="@lang('Payment Via')">
                                         
                                            <strong class="text-info"> 
                                                @php 
                                                $dispatcher = App\Models\Admin::whereId($item->dispatcher)->first();
                                                if($dispatcher)
                                                {
                                                $state = App\Models\State::whereId($dispatcher->state)->first();
                                                }
                                                @endphp
                                                {{@$dispatcher->name}}<br>
                                                <small> {{@$dispatcher->email}}</small>
                                                
                                            </strong>
                                            <br>
                                            @if(@$state)
                                            <small> <strong class="text-primary">{{ @$state->state ?? "Unknown"}}</strong></small>
                                            @endif
                                        
                                    </td>
                                 
                                 @endif

                                <td data-label="@lang('Amount')" class="text-right">
                                    <b>{{ $general->cur_sym.($item->total_amount) }}</b>
                                </td>
                                <td data-label="@lang('Amount')" class="text-right">
                                    <b>${{ ($item->total_amount_usd) }} <small>USD</small></b>
                                </td>
                                @if(request()->routeIs('admin.order.index'))

                                <td data-label="@lang('Status')" class="text-center">
                                    @php echo $item->statusBadge(); @endphp
                                </td>
                                @endif

                                <td data-label="@lang('Action')">

                                    <a href="{{ route('admin.order.details', $item->id) }}" data-toggle="tooltip" title="@lang('View')" class="icon-btn btn--dark mr-1">
                                        <i class="la la-desktop"></i>
                                    </a>

                                    @if(!request()->routeIs('admin.order.notpaid'))
                                    <button type="button" class="approveBtn icon-btn btn--success {{$item->status >= 3?'disabled':''}} text-white" data-toggle="tooltip" data-action="{{ $item->status+1 }}" data-id='{{$item->id}}'
                                    title="@if($item->status == 0) @lang('Mark as Processing')@elseif($item->status == 1)@lang('Mark as Dispatched')
                                    @elseif($item->status == 2) @lang('Mark as Delivered')@endif">
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
                    <p class="text-bold"></p>
                    
                    <div id="dispatch"></div>
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
                var dispatch = "<hr><label>elect Dispatcher</label><br><select name='dispatcher' class='form-control'>@foreach($staff as $data)@php $state = App\Models\State::whereId($data->state)->first(); @endphp<option selected disabled>Select State Champion</option><option value='{{$data->id}}'>{{$data->username}} ( {{$state->state ?? 'Unknown'}} State)</option>@endforeach</select>";
                $('.text-bold').text("@lang('Are you sure to mark the order as dispatched?')");
                document.getElementById("dispatch").innerHTML = dispatch;
               
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



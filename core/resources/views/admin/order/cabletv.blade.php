@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            
             <form action="" method="get" >
                 <br><br>
                @csrf
                <center>

                                   

                    <div class="col-12">
                        <div class="form-group">
                            <label>From Date</label>
                                <input type="date" name="from" requuired class="form-control" placeholder="Order ID" value="{{ request()->search ?? '' }}">
                                 
                        </div>
                        
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                                                        <label>To Date</label>

                                <input type="date" name="to" requuired class="form-control" placeholder="Order ID" value="{{ request()->search ?? '' }}">
                                 
                        </div>
                        
                    </div> 
                    
                     <div class="col-12">
  <br>
                                <div class="input-group has_append">
                                 <div class="input-group-append">
                                    <button class="btn btn--primary box--shadow1"   type="submit"><i class="fa fa-calendar"></i> Filter By Date</button>
                                </div>
                                  
                        </div>
                        
                    </div> 

                     </center>
                </form>
                @include('admin.partials.datatable')


                <div class="table-responsive--md  table-responsive">
                    <table id="example" class="table table--light style--two">
                        <thead>
                            <tr>

                                <th>@lang('Order ID')</th>
                                <th>@lang('Time')</th>                                <th>@lang('Customer')</th>
                                <th>@lang('Decoder/Plan')</th>
                                <th>@lang('Decoder Nuumber')</th>
                                <th class="text-right">@lang('Amount')</th>
                                <th class="text-right">@lang('Amount USD')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($bills as $item)
                            <tr>

                                <td data-label="@lang('Order ID') ">
                                    <span class="font-weight-bold d-block text--primary">{{ @$item->trx }}</span>
                                 </td>
                                <td data-label=" @lang('Time')">
                                     {{ showDateTime($item->created_at) }}
                                </td>
                                <td data-label="@lang('Customer')">
                                    <a href="{{ route('admin.users.detail', $item->user_id) }}">{{ App\Models\User::whereId($item->user_id)->first()->username ?? "N/A" }}</a>
                                </td>

                                <td data-label="@lang('Amount')" class="text-right">
                                <center> <b>{{strtoupper($item->network)}}<br>
                                {{$item->plan}}<br>
                                  
                                    </b>
                                    </center>
                                </td>

                                <td data-label="@lang('Amount')" class="text-right">
                                    <b>{{strtoupper($item->phone)}}<br>
                                    {{strtoupper($item->accountname)}}
                                    </b>
                                </td>

                                <td data-label="@lang('Amount')" class="text-right">
                                    <b>{{ $general->cur_sym.($item->amount) }}</b>
                                </td>
                                <td data-label="@lang('Amount')" class="text-right">
                                    <b>${{ ($item->usd) }} <small>USD</small></b>
                                </td>
                              

                                <td data-label="@lang('Action')">
 
                                    <button type="button" class="icon-btn btn--primary" data-toggle="modal" data-target="#detailsModal">
                                        <i class="la la-desktop"></i>
                                    </button>

                                </td>
                            </tr>

                            {{-- DELIVERY METHOD MODAL --}}
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
         
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">@lang('API Response Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-bold"></p>
                    @php
                    $api = json_decode($item->api,true);
                   
                    @endphp
                    
                    
                    @foreach(json_decode($item->api,) as $k => $val)
                    <li><b>{{inputTitle($k) ?? ""}}:</b> {{$val ?? ""}}</li>
                    @endforeach
                    
                    <div id="dispatch"></div>
                 </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                </div>
            
        </div>
    </div>
</div>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            
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

            
            modal.modal('show');
        });
    })(jQuery)

</script>
@endpush



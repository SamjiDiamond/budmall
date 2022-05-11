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
             <div class="row mb-30-none">
            @foreach($gatewayCurrency as $data)
                <div class="col-lg-3 col-12 col-md-3 mb-4">
                    <div class="card">
                       <!-- <h5 class="card-header p-1">
                            <img src="{{$data->methodImage()}}" class="card-img-top" width="50" alt="{{__($data->name)}}">
                        </h5> -->
                        <div class="card-bodys p-1s">
                         <button type="button" data-toggle="modal" data-target="#myModal{{$data->id}}" class="cmn-btn btn-block"> Deposit With {{$data->name}}</button>
                        </div>
                    </div>
                </div>

<!-- The Modal -->
<div class="modal" id="myModal{{$data->id}}">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">{{$data->name}}</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
       <form action="{{route('user.fundwallet.insert')}} " method="POST">
       @csrf
      <div class="modal-body">
        <label>Please Enter Amount</label>
        <input name="amount" type="tel" placeholder="0.00 {{$data->currency}}" class="form-control">
        <input type="hidden" name="currency" class="edit-currency" value="{{$data->currency}}">
        <input type="hidden" name="method_code" class="edit-method-code" value="{{$data->method_code}}">

      </div>

      <!-- Modal footer -->
      <div class="modal-footer"> 
        <button type="submit" class="cmn-btn btn-block">@lang('Proceed')</button>
      </div>
       </form>

    </div>
  </div>
</div>
<!-- The Modal -->

            @endforeach
        </div>
        <hr>
        <b><h4>Deposit Logs</h4></b>

        <table class="payment-table section-bg">
                        <thead class="bg--base">
                            <tr>
                                <th class="text-white">@lang('Transaction ID')</th>
                                <th class="text-white">@lang('Gateway')</th>
                                <th class="text-white">@lang('Amount')</th>
                                <th class="text-white">@lang('Status')</th>
                                <th class="text-white">@lang('Time')</th>
                                <th class="text-white"> @lang('MORE')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($logs) >0)
                            @foreach($logs as $k=>$data)
                                <tr>
                                    <td data-label="#@lang('Trx')">{{$data->trx}}</td>
                                    <td data-label="@lang('Gateway')">{{ __(@$data->gateway->name ??'Offline')  }}</td>
                                    <td data-label="@lang('Amount')">
                                        <strong>{{showAmount($data->amount)}} USD</strong>
                                    </td>
                                    <td>
                                        @if($data->status == 1)
                                            <span class="badge badge--success badge-capsule">@lang('Complete')</span>
                                        @elseif($data->status == 2)
                                            <span class="badge badge--warning badge-capsule">@lang('Pending')</span>
                                        @elseif($data->status == 3)
                                            <span class="badge badge--danger badge-capsule">@lang('Cancel')</span>
                                        @endif

                                        @if($data->admin_feedback != null)
                                            <button class="btn-info btn-rounded  badge detailBtn" data-admin_feedback="{{$data->admin_feedback}}"><i class="fa fa-info"></i></button>
                                        @endif

                                    </td>
                                    <td data-label="@lang('Time')">
                                        {{showDateTime($data->created_at, 'd M, Y h:i A')}}
                                    </td>

                                    @php
                                        $details = ($data->detail != null) ? json_encode($data->detail) : null;
                                    @endphp

                                    <td data-label="@lang('Details')">
                                        <a href="javascript:void(0)" class="qv-btn moreViewBtn"
                                        data-info="{{ $details }}"
                                        data-metod_code = {{ $data->method_code }}
                                        data-id="{{ $data->id }}"
                                        data-amount="{{ showAmount($data->amount)}} {{ __($data->method_currency) }}"
                                        data-charge="{{ showAmount($data->charge)}} {{ __($data->method_currency) }}"
                                        data-after_charge="{{ showAmount($data->amount + $data->charge)}} {{ __($data->method_currency) }}"
                                        data-rate="{{ showAmount($data->rate)}} {{ __($data->method_currency) }}"
                                        data-payable="{{ showAmount($data->final_amo)}} {{ __($data->method_currency) }}">
                                            <i class="la la-list"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="100%">There is no deposit log at the moment</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    {{$logs->appends(request()->all())->links()}}
    </div>

</div>
</div>
</div>
</div>
 {{-- APPROVE MODAL --}}
    <div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item dark-bg">@lang('Amount') : <span class="withdraw-amount "></span></li>
                        <li class="list-group-item dark-bg">@lang('Charge') : <span class="withdraw-charge "></span></li>
                        <li class="list-group-item dark-bg">@lang('After Charge') : <span class="withdraw-after_charge"></span></li>
                        <li class="list-group-item dark-bg">@lang('Payable Amount') : <span class="withdraw-payable"></span></li>
                    </ul>
                    <ul class="list-group withdraw-detail mt-1">
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="withdraw-detail"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.moreViewBtn').on('click', function() {
                var modal = $('#approveModal');
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.find('.withdraw-charge').text($(this).data('charge'));
                modal.find('.withdraw-after_charge').text($(this).data('after_charge'));
                modal.find('.withdraw-rate').text($(this).data('rate'));
                modal.find('.withdraw-payable').text($(this).data('payable'));

                if($(this).data('method_code') >999){

                    var list = [];
                    var details =  Object.entries($(this).data('info'));

                    var ImgPath = "{{asset(imagePath()['verify']['deposit']['path'])}}/";
                    var singleInfo = '';

                    for (var i = 0; i < details.length; i++) {
                        if (details[i][1].type == 'file') {
                            singleInfo += `<li class="list-group-item">
                                                <span class="font-weight-bold "> ${details[i][0].replaceAll('_', " ")} </span> : <img src="${ImgPath}/${details[i][1].field_name}" alt="@lang('Image')" class="w-100">
                                            </li>`;
                        }else{
                            singleInfo += `<li class="list-group-item">
                                                <span class="font-weight-bold "> ${details[i][0].replaceAll('_', " ")} </span> : <span class="font-weight-bold ml-3">${details[i][1].field_name}</span>
                                            </li>`;
                        }
                    }

                    if (singleInfo)
                    {
                        modal.find('.withdraw-detail').html(`<br><strong class="my-3">@lang('Payment Information')</strong>  ${singleInfo}`);
                    }else{
                        modal.find('.withdraw-detail').html(`${singleInfo}`);
                    }
                }


                modal.modal('show');
            });

            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var feedback = $(this).data('admin_feedback');
                modal.find('.withdraw-detail').html(`<p> ${feedback} </p>`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush


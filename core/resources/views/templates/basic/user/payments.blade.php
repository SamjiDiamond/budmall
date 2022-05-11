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
                        <thead class="bg--base">
                            <tr>
                                <th class="text-white">@lang('Transaction ID')</th>
                                <th class="text-white">@lang('Action')</th>
                                <th class="text-white">@lang('Details')</th>
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
                                    <td data-label="@lang('Action')">{{ __(@$data->action)  }}</td>
                                    <td data-label="@lang('Amount')">
                                        <strong>{{showAmount($data->usd_amount)}} USD</strong>
                                    </td>
                                    <td>
                                        @if($data->trx_type == "+")
                                            <span class="badge badge--success badge-capsule">@lang('Credit')</span>
                                        @else
                                            <span class="badge badge--danger badge-capsule">@lang('Debit')</span>
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
                                        data-details="{{ $data->details }}"
                                        data-amount="{{ showAmount($data->usd_amount)}} USD"
                                        data-charge="{{ showAmount($data->charge)}} USD"
                                        data-after_charge="{{ showAmount($data->amount + $data->charge)}} {{ __($general->cur_text) }}"
                                        data-rate="{{ showAmount($data->rate)}} {{ __($data->method_currency) }}"
                                        data-payable="{{ showAmount($data->final_amo)}} {{ __($data->method_currency) }}">
                                            <i class="la la-list"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    {{$logs->appends(request()->all())->links()}}
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
                        {{-- <li class="list-group-item dark-bg">@lang('Charge') : <span class="withdraw-charge "></span></li>--}}
                        <li class="list-group-item dark-bg">@lang('Details') : <span class="withdraw-details"></span></li>
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
                modal.find('.withdraw-details').text($(this).data('details'));
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


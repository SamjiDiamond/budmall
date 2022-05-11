@extends('seller.layouts.app')

@section('panel')

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="row justify-content-end">
                        <div class="col-xl-3 mb-3">
                            <form action="" method="GET" class="pt-3 px-3">
                                <div class="input-group has_append">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('Search by Transaction ID')..."
                                        value="{{request()->search ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn--primary" id="search-btn" type="submit"><i class="la la-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Transaction No.') | @lang('Time')</th>
                                    <th>@lang('Gateway') | @lang('Rate')</th>
                                    <th>@lang('Amount') | @lang('Charge')</th>
                                    <th>@lang('Including Charge')</th>
                                    <th>@lang('Receivable')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdraws as $data)
                                    <tr>
                                        <td data-label="@lang('Transaction No.') | @lang('Time')">
                                            <span class="d-block font-weight-bold text--primary">
                                                {{$data->trx}}
                                            </span>
                                            <span class="text--small">
                                                {{showDateTime($data->created_at)}}
                                            </span>
                                        </td>

                                        <td data-label="@lang('Gateway') | @lang('Rate')">
                                            <span class="d-block text--info font-weight-bold">{{ __($data->method->name) }}</span>
                                            <span class="text--small">
                                                1 {{__($general->cur_text)}} = {{showAmount($data->rate)}} {{__($data->currency)}}
                                            </span>
                                        </td>

                                        <td data-label="@lang('Amount')">
                                            <span class="d-block">{{showAmount($data->amount)}} {{__($general->cur_text)}}</span>
                                            <span class="text--danger">
                                                {{showAmount($data->charge)}} {{__($general->cur_text)}}
                                            </span>
                                        </td>

                                        <td data-label="@lang('Including Charge')">
                                            {{showAmount($data->after_charge)}} {{__($general->cur_text)}}
                                        </td>

                                        <td data-label="@lang('Receivable')" class="text--success">
                                            <span class="d-block font-weight-bold">{{showAmount($data->final_amount)}} {{__($data->currency)}}</span>
                                        </td>
                                        <td data-label="@lang('Status')">
                                            @if($data->status == 2)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($data->status == 1)
                                                <span class="badge badge--success">@lang('Completed')
                                                    <button class="admin-feedback-btn bg--primary approveBtn" data-admin_feedback="{{$data->admin_feedback}}"></button>
                                                </span>
                                            @elseif($data->status == 3)
                                                <span class="badge badge--danger">@lang('Rejected')
                                                    <button class="admin-feedback-btn bg--primary approveBtn" data-admin_feedback="{{$data->admin_feedback}}"></button>
                                                </span>
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
            
                @if($withdraws->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($withdraws) }}
                    </div>
                @endif
            </div>
        </div>
    </div>


    {{-- Detail MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Admin\'s Feedback')</h5>
                </div>
                <div class="modal-body">

                    <div class="withdraw-detail"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($){
            "use strict";
            $('.approveBtn').on('click', function() {
                var modal = $('#detailModal');
                var feedback = $(this).data('admin_feedback');
                modal.find('.withdraw-detail').html(`<p> ${feedback} </p>`);
                modal.modal('show');
            });
        })(jQuery);

    </script>
@endpush

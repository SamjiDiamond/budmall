@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Facilitator')</th>
                                <th>@lang('Pre Balance')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Post Balance')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('TRX ID')</th>
                                <th>@lang('Narration')</th>
                                <th>@lang('TRX Date')</th>
                            </tr>
                            </thead>
                            <tbody>

                                @forelse($logs as $log)
                                    <tr>

                                        <td data-label="@lang('Admin')">
                                            <span class="font-weight-bold">{{ App\Models\Admin::whereId($log->admin_id)->first()->username ?? "N/A" }}</span>

                                        </td>
                                        <td data-label="@lang('Pre Balance')">
                                           {{__($general->cur_sym)}}{{number_format($log->pre_balance,2)}}
                                        </td>
                                        <td data-label="@lang('Amount')">
                                            {{__($general->cur_sym)}}{{number_format($log->amount,2)}}
                                        </td>
                                        <td data-label="@lang('Post Balance')"> {{__($general->cur_sym)}}{{number_format($log->post_balance,2)}}</td>
                                        <td data-label="@lang('Type')">
                                            @if($log->trx_type == "+")
                                            <label class="badge bg-success">Credit</label>
                                            @else
                                            <label class="badge bg-danger">Debit</label>
                                            @endif

                                        </td>
                                         <td>{{$log->trx}}</td>
                                         <td>{{$log->details}}</td>
                                        <td data-label="@lang('Date')">
                                            {{showDateTime($log->created_at,'d M, Y h:i A')}}
                                            <br>
                                            {{ $log->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($logs) }}
                </div>
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.staff.editstaff', $user->id) }}" class="btn btn--info"><i class="las la-user"></i> {{ $user->name }}</a>
@endpush

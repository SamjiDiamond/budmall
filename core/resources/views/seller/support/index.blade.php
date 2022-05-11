@extends('seller.layouts.app')

@section('panel')
        <div class="row justify-content-center mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive--md  table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Ticket ID')</th>
                                        <th>@lang('Subject')</th>
                                        <th>@lang('Priority')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Last Reply')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $ticket)
                                        <tr>
                                            <td data-label="@lang('S.N.')">
                                                <a href="{{ route('seller.ticket.view', $ticket->ticket) }}" class="font-weight-bold">#{{ $ticket->ticket }} </a>
                                            </td>

                                            <td data-label="@lang('Subject')">
                                                <a href="{{ route('ticket.view', $ticket->ticket) }}" class="font-weight-bold">{{ __($ticket->subject) }} </a>
                                            </td>

                                            <td data-label="@lang('Priority')">
                                                @php echo $ticket->priorityBadge() @endphp
                                            </td>

                                            <td data-label="@lang('Status')">
                                                @php echo $ticket->statusBadge() @endphp
                                            </td>

                                            <td data-label="@lang('Last Reply')">
                                                {{ \Carbon\Carbon::parse($ticket->last_reply)->diffForHumans() }}
                                            </td>

                                            <td data-label="@lang('Action')">
                                                <a href="{{ route('seller.ticket.view', $ticket->ticket) }}" class="icon-btn">
                                                    <i class="la la-desktop"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($tickets->hasPages())
                        <div class="card-footer">
                            {{ paginateLinks($tickets) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('seller.ticket.open') }}" class="btn btn--primary">@lang('Open New Ticket')</a>
@endpush

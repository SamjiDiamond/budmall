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
                    <a href="{{route('ticket.open')}}" class="btn btn--base float-right mb-3"> <i class="las la-box-open"></i> @lang('Open New Ticket') </a>
                    <table class="payment-table section-bg">
                        <thead>
                            <tr>
                                <th>@lang('Subject')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Priority')</th>
                                <th>@lang('Last Reply')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $key => $ticket)
                            <tr>
                                <td data-label="@lang('Subject')"> <a href="{{ route('ticket.view', $ticket->ticket) }}" class="font-weight-bold"> [@lang('Ticket')#{{ $ticket->ticket }}] {{ __($ticket->subject) }} </a></td>

                                <td data-label="@lang('Status')">
                                    @php echo $ticket->statusBadge() @endphp
                                </td>
                                <td data-label="@lang('Priority')">
                                    @php echo $ticket->priorityBadge() @endphp
                                </td>
                                <td data-label="@lang('Last Reply')">{{ \Carbon\Carbon::parse($ticket->last_reply)->diffForHumans() }} </td>

                                <td data-label="@lang('Action')">
                                    <a href="{{ route('ticket.view', $ticket->ticket) }}"  class="btn-normal-2 btn-sm">
                                        <i class="fa fa-desktop"></i>
                                    </a>
                                </td>
                            </tr>
                           @empty
                           <tr><td class="text-center" colspan="12">@lang('No data found')</td></tr>
                          @endforelse
                        </tbody>
                    </table>

                    {{ $tickets->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

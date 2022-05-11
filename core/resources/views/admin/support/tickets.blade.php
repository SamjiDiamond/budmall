@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="row justify-content-end">
                        <div class="col-xl-3 mb-3">
                            <form action="" method="GET" class="pt-3 px-3">
                                <div class="input-group has_append">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('Search')..." value="{{ request()->search ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn--primary" id="search-btn" type="submit"><i class="la la-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th>@lang('Ticket ID')</th>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Opened By')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Reply')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td data-label="@lang('Ticket ID')">
                                            <a href="{{ route('admin.ticket.view', $ticket->id) }}" class="font-weight-bold">{{ $ticket->ticket }}</a>
                                        </td>
                                        <td data-label="@lang('Subject')">
                                            {{ $ticket->subject }}
                                        </td>

                                        <td data-label="@lang('Opened By')">
                                            <span data-toggle="tooltip" data-title="@if($ticket->user_id) @lang('Customer') @elseif($ticket->seller_id) @lang('Seller') @else @lang('Guset') @endif">
                                                @if($ticket->user_id)
                                                <a href="{{ route('admin.users.detail', $ticket->user_id)}}" class="text--info">{{@$ticket->name}}</a>
                                                @elseif($ticket->seller_id)
                                                <a href="{{ route('admin.sellers.detail', $ticket->seller_id)}}" class="text--primary">{{@$ticket->name}}</a>
                                                @else
                                                    <span class="text--warning"> {{$ticket->name}}</span>
                                                @endif
                                            </span>
                                        </td>

                                        <td data-label="@lang('Priority')">
                                            @php echo $ticket->priorityBadge() @endphp
                                        </td>

                                        <td data-label="@lang('Status')">
                                            @php echo $ticket->statusBadge() @endphp
                                        </td>

                                        <td data-label="@lang('Last Reply')">
                                            {{ diffForHumans($ticket->last_reply) }}
                                        </td>

                                        <td data-label="@lang('Action')">
                                            <a href="{{ route('admin.ticket.view', $ticket->id) }}" class="icon-btn  ml-1" data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                                                <i class="las la-desktop"></i>
                                            </a>
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
                @if($tickets->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($tickets) }}
                </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection



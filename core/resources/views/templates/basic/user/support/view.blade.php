@extends($activeTemplate.'layouts.frontend')

@section('content')
<div class="padding-bottom padding-top">
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
                <div class="card border-0 shadow-md">
                    <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center">
                        <h5 class="card-title mt-0">
                            @if($myTicket->status == 0)
                                <span class="badge badge--success">@lang('Open')</span>
                            @elseif($myTicket->status == 1)
                                <span class="badge badge--primary text-white">@lang('Answered')</span>
                            @elseif($myTicket->status == 2)
                                <span class="badge badge--warning">@lang('Replied')</span>
                            @elseif($myTicket->status == 3)
                                <span class="badge badge--dark">@lang('Closed')</span>
                            @endif
                            [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
                        </h5>

                        @if($myTicket->status != 3)
                            <button class="btn btn--danger  close-button" type="button" title="@lang('Close Ticket')" data-toggle="modal"  data-target="#DelModal"><i class="la la-times-circle"></i>
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                            @if($myTicket->status != 4)
                                <form method="post" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="reply_ticket" value="1">
                                    <input type="hidden" name="ticket_id" value="{{ request()->ticket }}">
                                    <div class="row justify-content-between">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <textarea name="message" class="form-control" id="inputMessage" placeholder="@lang('Your Reply')" rows="4" cols="10"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputAttachments">@lang('Attachments') ( <code class="mt-0">@lang("Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx")</code> )</label>
                                        <div class="input-group">
                                            <input type="file" name="attachments[]" id="inputAttachments" class="form-control form-control-lg" accept=".jpeg,.png,.jpeg, .pdf, .doc, .docx"/>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn--success border-0 btn-sm input-group-text addFile">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="fileUploadsContainer"></div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base btn-lg btn-block text-white">
                                            <i class="las la-reply"></i> @lang('Reply')
                                        </button>
                                    </div>
                                </form>
                            @endif


                    </div>
                </div>

                <div class="card border-0 shadow-md mt-3">
                    <div class="card-body">
                        @foreach($messages as $message)
                            @if($message->admin_id == 0)
                                <div class="row border border-primary border-radius-3 my-3 py-3 mx-2">
                                    <div class="col-md-3 border-right text-right">
                                        <h5 class="my-3">{{ $message->ticket->name }}</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <p class="text-muted font-weight-bold my-3">
                                            @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                        <p>{{$message->message}}</p>
                                        @if($message->attachments()->count() > 0)
                                            <div class="mt-2">
                                                @foreach($message->attachments as $k=> $image)
                                                    <a href="{{route('ticket.download',encrypt($image->id))}}" class="mr-3"><i class="la la-file"></i>  @lang('Attachment') {{++$k}} </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="row border border-warning border-radius-3 my-3 py-3 mx-2" style="background-color: #ffd96729">
                                    <div class="col-md-3 border-right text-right">
                                        <h5 class="my-3">{{ $message->admin->name }}</h5>
                                        <p class="lead text-muted">@lang('Staff')</p>
                                    </div>
                                    <div class="col-md-9">
                                        <p class="text-muted font-weight-bold my-3">
                                            @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                        <p>{{$message->message}}</p>
                                        @if($message->attachments()->count() > 0)
                                            <div class="mt-2">
                                                @foreach($message->attachments as $k=> $image)
                                                    <a href="{{route('ticket.download',encrypt($image->id))}}" class="mr-3"><i class="la la-file"></i> &nbsp; @lang('Attachment') {{++$k}} </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>




<div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <form method="post" action="{{ route('ticket.reply', $myTicket->id) }}">
            @csrf
            <input type="hidden" name="reply_ticket" value="2">
            <div class="modal-header">
                <h5 class="modal-title"> @lang('Confirmation Alert')</h5>
                <button type="button" class="close close-button" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <strong class="text-dark">@lang('Are you sure to close this support ticket?')</strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                <button type="submit" class="btn btn--base">@lang("Yes")</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.delete-message').on('click', function (e) {
                $('.message_id').val($(this).data('id'));
            });
            $('.addFile').on('click',function(){
                $("#fileUploadsContainer").append(
                    `<div class="input-group mt-3">
                        <input type="file" name="attachments[]" class="form-control form-control-lg" required accept=".jpeg,.png,.jpeg, .pdf, .doc, .docx"/>
                        <div class="input-group-append">
                            <span class="input-group-text btn btn--danger border-0 support-btn remove-btn">
                                <i class="la la-times"></i>
                            </span>
                        </div>
                    </div>`
                );
            });
            $(document).on('click','.remove-btn',function(){
                $(this).closest('.input-group').remove();
            });
        })(jQuery);

    </script>
@endpush

@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body ">
                    <h6 class="card-title d-flex justify-content-between">
                        <span>
                            @php
                                echo $ticket->statusBadge();
                            @endphp
                            [@lang('Ticket#'){{ $ticket->ticket }}] {{ $ticket->subject }}
                        </span>

                        @if($ticket->status != 3)
                            <button class="btn btn--danger" type="button" data-toggle="modal" data-target="#DelModal">
                                <i class="fa fa-lg fa-times-circle"></i> @lang('Close Ticket')
                            </button>
                        @endif
                    </h6>

                    <form action="{{ route('admin.ticket.reply', $ticket->id) }}" enctype="multipart/form-data" method="post">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea class="form-control" name="message" rows="3" id="inputMessage" placeholder="@lang('Your Message')"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="inputAttachments" class="font-weight-bold">@lang('Attachments') ( <span class="text-danger">@lang("Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx")</span> )
                                    </label>

                                    <div class="file-upload-wrapper" data-text="@lang('Select your file!')">
                                        <input type="file" name="attachments[]" id="inputAttachments"
                                        class="file-upload-field" accept=".jpeg,.png,.jpeg, .pdf, .doc, .docx"/>
                                    </div>

                                    <div id="fileUploadsContainer"></div>

                                    <button type="button" class="btn btn--dark extraTicketAttachment">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn--primary btn-block" type="submit" name="reply_ticket" value="1">
                                <i class="la la-fw la-lg la-reply"></i> @lang('Reply')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mt-3">
            <div class="card">
                <div class="card-body">
                    @foreach($messages as $message)
                        @if($message->admin_id == 0)
                            <div class="row border border-primary border-radius-3 my-3 mx-2">
                                <div class="col-md-3 border-right text-right">
                                    <h5 class="my-3">{{ $ticket->name }}</h5>
                                    @if($ticket->user_id != null)
                                        <p><a href="{{route('admin.users.detail', $ticket->user_id)}}" >&#64;{{ $ticket->name }}</a></p>
                                    @else
                                        <p>@<span>{{$ticket->name}}</span></p>
                                    @endif
                                    <button data-id="{{$message->id}}" type="button" data-toggle="modal" data-target="#DelMessage" class="btn btn-danger btn-sm my-3 delete-message"><i class="la la-trash"></i> @lang('Delete')</button>
                                </div>

                                <div class="col-md-9">
                                    <p class="text-muted font-weight-bold my-3">
                                        @lang('Posted on') {{ showDateTime($message->created_at, 'l, dS F Y @ H:i') }}</p>
                                    <p>{{ $message->message }}</p>
                                    @if($message->attachments()->count() > 0)
                                        <div class="my-3">
                                            @foreach($message->attachments as $k=> $image)
                                                <a href="{{route('admin.ticket.download',encrypt($image->id))}}" class="mr-3"><i class="fa fa-file"></i> @lang('Attachment') {{++$k}}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="row border border-warning border-radius-3 my-3 mx-2 admin-bg-reply">
                                <div class="col-md-3 border-right text-right">
                                    <h5 class="my-3">{{ @$message->admin->name }}</h5>
                                    <p class="lead text-muted">@lang('Staff')</p>
                                    <button data-id="{{$message->id}}" type="button" data-toggle="modal" data-target="#DelMessage" class="btn btn-danger btn-sm my-3 delete-message"><i class="la la-trash"></i> @lang('Delete')</button>
                                </div>

                                <div class="col-md-9">
                                    <p class="text-muted font-weight-bold my-3">
                                        @lang('Posted on') {{showDateTime($message->created_at,'l, dS F Y @ H:i') }}</p>
                                    <p>{{ $message->message }}</p>
                                    @if($message->attachments()->count() > 0)
                                        <div class="my-3">
                                            @foreach($message->attachments as $k=> $image)
                                                <a href="{{route('admin.ticket.download',encrypt($image->id))}}" class="mr-3"><i class="fa fa-file"></i> @lang('Attachment') {{++$k}} </a>
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

    <div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="font-weight-bold">@lang('Are you sure to close this support ticket?')</span>
                </div>
                <div class="modal-footer">
                    <form method="post" action="{{ route('admin.ticket.reply', $ticket->id) }}">
                        @csrf

                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No') </button>
                        <button type="submit" class="btn btn--danger" name="reply_ticket" value="2"> @lang('Yes') </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DelMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Confirmation Alert')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="font-weight-bold">@lang('Are you sure to delete this message?')</span>
                </div>
                <div class="modal-footer">
                    <form method="post" action="{{ route('admin.ticket.delete')}}">
                        @csrf
                        <input type="hidden" name="message_id" class="message_id">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No') </button>
                        <button type="submit" class="btn btn--danger">@lang('Yes') </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection




@push('breadcrumb-plugins')
    <a href="{{ route('admin.ticket') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.delete-message').on('click', function (e) {
                $('.message_id').val($(this).data('id'));
            });

            $('.extraTicketAttachment').on('click',function(){
                $("#fileUploadsContainer").append(`
                    <div class="file-upload-wrapper mb-3" data-text="@lang('Select your file!')">
                        <input type="file" name="attachments[]" id="inputAttachments" class="file-upload-field" accept=".jpeg,.png,.jpeg, .pdf, .doc, .docx"/>
                    </div>`
                );
            });
        })(jQuery);
    </script>
@endpush

@extends('seller.layouts.app')

@section('panel')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                    <h5 class="card-title">
                        @php
                            echo $myTicket->statusBadge();
                        @endphp
                        [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
                    </h5>

                    @if($myTicket->status != 3)
                    <button class="btn btn-danger close-button" type="button" title="@lang('Close Ticket')" data-toggle="modal" data-target="#DelModal"><i class="la la-times"></i>
                    </button>
                    @endif
                </div>

                <div class="card-body">
                    @if($myTicket->status != 4)
                        <form method="post" action="{{ route('seller.ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="reply_ticket" value="1">

                            <div class="row justify-content-between">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea name="message" class="form-control form-control-lg" id="inputMessage" placeholder="@lang('Your Reply')" rows="4" cols="10"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-between">
                                <div class="col-md-12">
                                    <label for="inputAttachments" class="font-weight-bold">@lang('Attachments') ( <code class="mt-0">@lang("Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx")</code> )
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" name="attachments[]" id="customFile" class="custom-file-input" accept=".jpeg,.png,.jpeg, .pdf, .doc, .docx"/>
                                        <label class="custom-file-label" for="customFile">@lang('Choose file')</label>
                                    </div>
                                    <div id="fileUploadsContainer"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn--primary add-more-btn">
                                    <i class="la la-plus-circle"></i> @lang('Add More')
                                </button>
                            </div>

                            <button type="submit" class="btn btn--primary btn-lg btn-block"><i class="fa fa-reply"></i> @lang('Reply')</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">

            <div class="card">
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
                                            <a href="{{route('ticket.download',encrypt($image->id))}}" class="mr-3"><i class="fa fa-file"></i>  @lang('Attachment') {{++$k}} </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="row border border-primary border-radius-3 my-3 py-3 mx-2 table-primary">
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
                                            <a href="{{route('ticket.download',encrypt($image->id))}}" class="mr-3"><i class="fa fa-file"></i>  @lang('Attachment') {{++$k}} </a>
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
                <form method="post" action="{{ route('seller.ticket.reply', $myTicket->id) }}">
                    @csrf
                    <input type="hidden" name="reply_ticket" value="2">
                    <div class="modal-header">
                        <h5 class="modal-title"> @lang('Confirmation Alert')</h5>
                    </div>
                    <div class="modal-body">
                        <span class="font-weight-bold">@lang('Are you sure to close this support ticket')?</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang("Yes")</button>
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

            let itr = 0;

            $('.add-more-btn').on('click',function(){
                itr++;
                $("#fileUploadsContainer").append(`
                    <div class="form-group custom-file mb-3">
                        <input type="file" name="attachments[]" id="customFile${itr}" class="custom-file-input form-control-lg" accept=".jpeg,.png,.jpeg, .pdf, .doc, .docx"/>
                        <label class="custom-file-label" for="customFile${itr}">@lang('Choose file')</label>
                    </div>`
                );
            });

            $(document).on('click','.remove-btn',function(){
                $(this).closest('.input-group').remove();
            });

            $(document).on("change", '.custom-file-input' ,function() {
                var fileName = $(this).val().split("\\").pop();
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });

        })(jQuery);

    </script>
@endpush
@push('style')
    <style>
        .form-control[type="file"] {
            height: unset;
            line-height: 24px;
            padding: 5px;
            font-size: 14px;
        }
    </style>
@endpush

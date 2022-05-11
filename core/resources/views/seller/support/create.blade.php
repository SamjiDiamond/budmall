@extends('seller.layouts.app')
@section('panel')
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <form  action="{{route('seller.ticket.store')}}"  method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="website">@lang('Subject')</label>
                                <input type="text" name="subject" value="{{old('subject')}}" class="form-control form-control-lg" placeholder="@lang('Subject')" >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="priority">@lang('Priority')</label>
                                <select name="priority" class="form-control">
                                    <option value="3">@lang('High')</option>
                                    <option value="2">@lang('Medium')</option>
                                    <option value="1">@lang('Low')</option>
                                </select>
                            </div>
                            <div class="col-12 form-group">
                                <label for="inputMessage">@lang('Message')</label>
                                <textarea name="message" id="inputMessage" rows="6" class="form-control form-control-lg">{{old('message')}}</textarea>
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

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary btn-block" id="recaptcha" ><i class="fa fa-paper-plane"></i>&nbsp;@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('breadcrumb-plugins')
<a href="{{route('seller.ticket.index') }}" class="btn btn-sm btn--primary float-right">
    @lang('My Support Ticket')
</a>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            var itr = 0;

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

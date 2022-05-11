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
                    <div class="card-header bg-transparent">{{ __($pageTitle) }}
                        <a href="{{route('ticket') }}" class="btn btn--base float-right">
                            @lang('My Support Tickets')
                        </a>
                    </div>

                    <div class="card-body">
                        <form  action="{{route('ticket.store')}}"  method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="form-group col-md-6">
                                    <label for="website">@lang('Subject')</label>
                                    <input type="text" name="subject" value="{{old('subject')}}" class="form-control custom--style" placeholder="@lang('Subject')" >
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="priority">@lang('Priority')</label>
                                    <select name="priority" class="form-control form-control-lg shadow-none outline-0">
                                        <option value="3">@lang('High')</option>
                                        <option value="2">@lang('Medium')</option>
                                        <option value="1">@lang('Low')</option>
                                    </select>
                                </div>
                                <div class="col-12 form-group">
                                    <label for="inputMessage">@lang('Message')</label>
                                    <textarea name="message" id="inputMessage" rows="6" class="form-control custom--style">{{old('message')}}</textarea>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-sm-12 file-upload">
                                    <label for="inputAttachments">@lang('Attachments') <code> ( @lang('Allowed File Extensions'): .@lang('jpg'), .@lang('jpeg'), .@lang('png'), .@lang('pdf'), .@lang('doc'), .@lang('docx') )</code> </label>
                                    <div class="input-group">
                                        <input type="file" name="attachments[]" id="inputAttachments" class="form-control form-control-lg" accept=".jpeg,.png,.jpeg, .pdf, .doc, .docx"/>
                                        <div class="input-group-append">
                                            <button type="button" class="input-group-text btn btn--success addFile border-0">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="fileUploadsContainer"></div>
                                </div>

                            </div>

                            <div class="row form-group justify-content-center">
                                <div class="col-md-12">
                                    <button class="btn btn--base w-100 text-white" type="submit" id="recaptcha" ><i class="fa fa-paper-plane"></i>&nbsp;@lang('Submit')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="file-upload-wrapper d-none">
    <div class="input-group mt-3">
        <input type="file" name="attachments[]" class="form-control form-control-lg" required accept=".jpeg,.png,.jpeg, .pdf, .doc, .docx"/>
        <div class="input-group-append support-input-group">
            <span class="input-group-text btn btn--danger border-0 support-btn remove-btn"> <i class="fa fa-times"></i></span>
        </div>
    </div>
</div>

@endsection


@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.addFile').on('click',function(){
                $("#fileUploadsContainer").append($('.file-upload-wrapper').html());
            });
            $(document).on('click','.remove-btn',function(){
                $(this).closest('.input-group').remove();
            });
        })(jQuery);
    </script>
@endpush

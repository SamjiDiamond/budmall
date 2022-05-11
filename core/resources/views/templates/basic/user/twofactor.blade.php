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
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-6">
                        @if(Auth::user()->ts)
                            <div class="card shadow-md border-0">
                                <div class="card-header bg-transparent">
                                    <h5>@lang('Two Factor Authenticator')</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mx-auto text-center">
                                        <a href="#0"  class="btn btn-block btn-lg btn-danger" data-toggle="modal" data-target="#disableModal">
                                            @lang('Disable Two Factor Authenticator')</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card shadow-md border-0 mb-2">
                                <div class="card-header bg-transparent">
                                    <h5 class="">@lang('Two Factor Authenticator')</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" name="key" value="{{$secret}}" class="form-control form-control-lg shadow-none outline-0" id="referralURL" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn--base input-group-text copytext border-0" id="copyBoard"> <i class="fa fa-copy"></i> </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mx-auto text-center">
                                        <img class="mx-auto" src="{{$qrCodeUrl}}">
                                    </div>
                                    <div class="form-group mx-auto text-center">
                                        <a href="#0" class="btn btn--base btn-lg mt-3 mb-1" data-toggle="modal" data-target="#enableModal">@lang('Enable Two Factor Authenticator')</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
        
                    <div class="col-lg-6 col-md-6">
                        <div class="card shadow-md border-0">
                            <div class="card-header bg-transparent">
                                <h5 >@lang('Google Authenticator')</h5>
                            </div>
                            <div class=" card-body">
                                <p>@lang('Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process. To use Google Authenticator, install the Google Authenticator application on your mobile device.')</p>
                                <a class="btn btn--base btn-md mt-3" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank">@lang('DOWNLOAD APP')</a>
                            </div>
                        </div><!-- //. single service item -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



    <!--Enable Modal -->
    <div id="enableModal" class="modal fade" role="dialog">
        <div class="modal-dialog ">
            <!-- Modal content-->
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Verify Your Otp')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{route('user.twofactor.enable')}}" method="POST">
                    @csrf
                    <div class="modal-body ">
                        <div class="form-group">
                            <input type="hidden" name="key" value="{{$secret}}">
                            <input type="text" class="form-control custom--style" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark text-white" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--base">@lang('Verify')</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!--Disable Modal -->
    <div id="disableModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Verify Your Otp Disable')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{route('user.twofactor.disable')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" class="form-control custom--style" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark text-white" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--base">@lang('Verify')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        (function($){
            "use strict";

            $('.copytext').on('click',function(){
                var copyText = document.getElementById("referralURL");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                iziToast.success({message: "Copied: " + copyText.value, position: "topRight"});
            });
        })(jQuery);
    </script>
@endpush



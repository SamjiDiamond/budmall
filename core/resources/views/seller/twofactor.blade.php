@extends('seller.layouts.app')
@section('panel')

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-6">
            @if(Auth::guard('seller')->user()->ts)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Disable 2FA Security')</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mx-auto text-center">
                            <a href="#0"  class="btn btn-block btn-lg btn--danger" data-toggle="modal" data-target="#disableModal">
                                @lang('Disable')</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Enable 2FA Security')</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" name="key" value="{{$secret}}" class="form-control form-control-lg" id="referralURL" readonly>
                                <div class="input-group-append">
                                    <button class="input-group-text copytext btn btn--primary text-white border-0" type="button" id="copyBoard"> <i class="la la-copy"></i> </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mx-auto text-center">
                            <img class="mx-auto" src="{{$qrCodeUrl}}" >
                        </div>
                        <div class="form-group mx-auto text-center">
                            <a href="#0" class="btn btn--primary btn-block" data-toggle="modal" data-target="#enableModal">@lang('Enable')</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Google Authenticator')</h5>
                </div>
                <div class=" card-body">
                    <p>@lang('Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process. To use Google Authenticator, install the Google Authenticator application on your mobile device.')</p>
                    <a class="btn btn--primary btn-lg mt-3" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank"> <i class="la la-download"></i> @lang('Download')</a>
                </div>
            </div><!-- //. single service item -->
        </div>
    </div>

    <!--Enable Modal -->
    <div id="enableModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Code Verification')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{route('seller.twofactor.enable')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Code')</label>
                            <input type="hidden" name="key" value="{{$secret}}">
                            <input type="text" class="form-control" name="code" placeholder="@lang('Google Authenticator Code')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--success btn-block">@lang('Enable 2FA')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Disable Modal -->
    <div id="disableModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Code Verification')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{route('seller.twofactor.disable')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" class="form-control" name="code" placeholder="@lang('Google Authenticator Code')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--danger btn-block">@lang('Disable 2FA')</button>
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



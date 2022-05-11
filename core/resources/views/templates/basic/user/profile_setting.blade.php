@extends($activeTemplate.'layouts.frontend')
@section('content')
    <div class="user-profile-section padding-top padding-bottom">
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
                    <div class="checkout-area section-bg">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="user-profile">
                                    <div class="thumb">
                                        <img id="imagePreview" src="{{ getAvatar(imagePath()['profile']['user']['path'].'/'.$user->image ) }}" alt="@lang('user')">
                                        <label for="file-input" class="file-input-btn">
                                            <i class="la la-edit"></i>
                                        </label>
                                    </div>
                                    <div class="content">
                                        <h5 class="title">{{ $user->fullname }}</h5>
                                        <span>@lang('Username'): {{ $user->username }}</span>
                                        <span class="d-block">@lang('Email'): {{ $user->email }}</span>
                                        <span class="d-block">@lang('Mobile'): {{ $user->mobile }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-6">
                                <form action="" method="post" enctype="multipart/form-data" class="user-profile-form row mb--20">
                                    @csrf

                                    <input type='file' class="d-none" name="image" id="file-input" accept=".png, .jpg, .jpeg" />

                                    <div class="col-lg-6 mb-20">
                                        <label class="billing-label">@lang('First Name')</label>
                                        <input class="form-control custom--style" type="text" name="firstname" value="{{ $user->firstname}}" placeholder="@lang('Last Name')">
                                    </div>

                                    <div class="col-lg-6 mb-20">
                                        <label class="billing-label">@lang('Last Name')</label>
                                        <input class="form-control custom--style" type="text" name="lastname" value="{{ $user->lastname}}" placeholder="@lang('Last Name')">
                                    </div>


                                    <div class="col-lg-6 mb-20">
                                        <label for="state" class="billing-label">@lang('Country'):</label>
                                        <input type="text" class="form-control custom--style" placeholder="@lang('Country')" value="{{@$user->address->country}}" readonly>
                                    </div>

                                    <div class="col-lg-6 mb-20">
                                        <label for="state" class="billing-label">@lang('State'):</label>
                                        <input type="text" class="form-control custom--style" id="state" name="state" placeholder="@lang('state')" value="{{@$user->address->state}}" required>
                                    </div>

                                    <div class="col-lg-6 mb-20">
                                        <label for="city" class="billing-label">@lang('City'):</label>
                                        <input type="text" class="form-control custom--style" id="city" name="city" placeholder="@lang('City')" value="{{@$user->address->city}}" required>
                                    </div>

                                    <div class="col-lg-6 mb-20">
                                        <label for="zip" class="billing-label">@lang('Zip Code'):</label>
                                        <input type="text" class="form-control custom--style" id="zip" name="zip" placeholder="@lang('Zip Code')" value="{{@$user->address->zip}}" required>
                                    </div>

                                    <div class="col-md-12 mb-20">
                                        <label for="address" class="billing-label">@lang('Address'):</label>
                                        <textarea type="text" rows="2" class="form-control custom--style" id="address" name="address" placeholder="@lang('Address')" required>{{@$user->address->address}}</textarea>
                                    </div>

                                    <div class="col-md-12 ml-auto text-right mb-20">
                                        <button type="submit" class="bill-button w-unset text-white">@lang('Update Profile')</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        'use strict';
        (function($){
            $('select[name=country]').val("{{  @$user->address->country }}");

            $("#file-input").on('change',function() {
                readURL(this);
            });

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result);
                        $('#imagePreview').hide();
                        $('#imagePreview').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        })(jQuery)

    </script>
@endpush

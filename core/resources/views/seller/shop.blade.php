@extends('seller.layouts.app')
@section('panel')
    <form action="" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">@lang('Images')</h5></div>
            <div class="card-body pb-0">
                <div class="row">
                    <div class="col-lg-4 col-xl-3 col-sm-5">
                        <div class="payment-method-item">
                            <label class=" font-weight-bold">@lang('Logo')</label>
                            <div class="payment-method-header">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview" style="background-image: url({{ getImage(imagePath()['seller']['shop_logo']['path'].'/'.@$shop->logo,imagePath()['seller']['shop_logo']['size']) }})">
                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>
                                        <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-xl-9 col-sm-7">
                        <div class="payment-method-item">
                            <label class=" font-weight-bold">@lang('Cover Photo')</label>
                            <div class="payment-method-header">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview" style="background-image: url({{ getImage(imagePath()['seller']['shop_cover']['path'].'/'.@$shop->cover,imagePath()['seller']['shop_cover']['size']) }}); {{ @$shop->cover?'background-size:cover':'' }}">
                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" name="cover_image" class="profilePicUpload" id="coverImage" accept=".png, .jpg, .jpeg"/>
                                        <label for="coverImage" class="bg--primary"><i class="la la-pencil"></i></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h5 class="card-title mb-0">@lang('Basic Information')</h5></div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-3">
                        <label class="font-weight-bold">@lang('Shop Name')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control" type="text" name="name" value="{{ old('name')??@$shop->name }}" >
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class="font-weight-bold">@lang('Phone')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control" type="number" name="phone" value="{{ old('phone')??@$shop->phone }}" >
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class="font-weight-bold">@lang('Opens at')</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group clockpicker">
                            <input type="text" class="form-control" placeholder="--:--" name="opening_time" autocomplete="off" value="{{ old('opening_time')??showDateTime(@$shop->opens_at, 'H:i') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class="font-weight-bold">@lang('Closed at')</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group clockpicker">
                            <input type="text" class="form-control" value="{{ old('opening_time')??showDateTime(@$shop->closed_at, 'H:i') }}" placeholder="--:--" name="closing_time" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class="font-weight-bold">@lang('Address')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control" type="text" name="address" value="{{ old('address')??@$shop->address }}" >
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h5 class="card-title mb-0">@lang('SEO Contents')</h5></div>

            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-3">
                        <label class="font-weight-bold">@lang('Meta Title')</label>
                    </div>

                    @php
                        if(old('meta_title')) $value = old('meta_title');
                        elseif(isset($shop)) $value = $shop->meta_title;
                        else $value = null;
                    @endphp

                    <div class="col-md-9">
                        <input type="text" class="form-control" name="meta_title" value="{{ $value }}" placeholder="@lang('Meta Title')">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class="font-weight-bold">@lang('Meta Description')</label>
                    </div>
                    @php
                        if(old('meta_description')) $value = old('meta_description');
                        elseif(isset($shop)) $value = $shop->meta_description;
                        else $value = null;
                    @endphp

                    <div class="col-md-9">
                        <textarea name="meta_description" rows="5" class="form-control">{{$value}}</textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class="font-weight-bold">@lang('Meta Keywords')</label>
                    </div>
                    @php
                        if(old('meta_keywords')){
                            $metaKeywords = old('meta_keywords');
                        }elseif($shop && $shop->meta_keywords){
                            $metaKeywords = $shop->meta_keywords;
                        }else{
                            $metaKeywords = null;
                        }
                    @endphp

                    <div class="col-md-9">
                        <select name="meta_keywords[]" class="form-control select2-auto-tokenize"  multiple="multiple">
                            @if($metaKeywords)
                                @foreach($metaKeywords as $option)
                                    <option value="{{ $option }}" selected>{{ __($option) }}</option>
                                @endforeach
                            @endif
                        </select>

                        <small class="form-text text-muted">
                            <i class="las la-info-circle"></i> @lang('Type , as seperator or hit enter among keywords')
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h5 class="card-title mb-0">@lang('Social Links')</h5></div>
            <div class="card-body">

                @php
                    if(old('social_links')){
                        $socialLinks = old('social_links');
                    }elseif($shop && $shop->social_links){
                        $socialLinks = $shop->social_links;
                    }else{
                        $socialLinks = null;
                    }
                @endphp

                <div class="socials-wrapper">
                    @if($socialLinks)
                        @foreach($socialLinks as $item)
                            <div class="socials">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="social_links[{{ $loop->index }}][name]" value="{{ $item['name'] }}" placeholder="@lang('Type Name Here...')">
                                        </div>
                                    </div>

                                    @php
                                        $icon = explode('"', $item['icon']);
                                    @endphp

                                    <div class="col-md-4">
                                        <div class="input-group has_append">
                                            <input type="text" class="form-control icon-name" name="social_links[{{ $loop->index }}][icon]" value="{{ $item['icon'] }}" placeholder="@lang('Icon')" required>

                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary iconPicker" data-icon="{{ @$icon[1] }}" role="iconpicker"></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group abs-form-group d-flex justify-content-between flex-wrap">
                                            <input type="text" class="form-control" name="social_links[{{ $loop->index }}][link]" value="{{ $item['link'] }}" placeholder="@lang('Type Link Here...')">
                                            <button type="button" class="btn btn-outline--danger remove-social abs-button"><i class="la la-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    @endif
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <p class="p-2 social-info">@lang('Add social links as you want by clicking the (+) button on the right side.')</p>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline--success add-social "><i class="la la-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
            <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Save Changes')</button>
        </div>
    </form>
@endsection

@push('style')
    <style>
        .payment-method-item .payment-method-header .thumb, .payment-method-item .payment-method-header .thumb .profilePicPreview {
            width: 100%;
        }

        .payment-method-item .payment-method-header .thumb .profilePicPreview {
            height: 300px;
        }

    </style>
@endpush

@push('script-lib')
<script src="{{ asset('assets/dashboard/js/vendor/bootstrap-clockpicker.min.js')}} "></script>
<script src="{{ asset('assets/dashboard/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/bootstrap-iconpicker.min.css') }}">
@endpush

@push('script')
<script>
   'use strict';
    (function($){

        $('.clockpicker').clockpicker({
            placement: 'bottom',
            align: 'left',
            donetext: 'Done',
            autoclose:true,
        });

        $('.add-social').on('click', function(){
            var socials = $(document).find('.socials');
            var length         = socials.length;

            $('.social-info').addClass('d-none');
            var content = `<div class="socials">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="social_links[${length}][name]" placeholder="@lang('Type Name Here...')">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="input-group has_append">
                                            <input type="text" class="form-control icon-name" name="social_links[${length}][icon]" value="{{ old('icon') }}" placeholder="@lang('Icon')" required>

                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary iconPicker" data-icon="fas fa-home" role="iconpicker"></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group abs-form-group d-flex justify-content-between flex-wrap">
                                            <input type="text" class="form-control" name="social_links[${length}][link]" placeholder="@lang('Type Link Here...')">
                                            <button type="button" class="btn btn-outline--danger remove-social abs-button"><i class="la la-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

            $(content).appendTo('.socials-wrapper').hide().slideDown('slow');

            socials     = $(document).find('.socials');
            length      = socials.length;

            socials = $(document).find('.socials');
            length         = socials.length;

            if(length > 0) {
                $('.remove-social').removeClass('d-none');
            }else{
                $('.remove-social').addClass('d-none');
            }

            $(document).find('.iconPicker').iconpicker();
        });

        $(document).on('change','.iconPicker' ,function (e) {
            $(this).parent().siblings('.icon-name').val(`<i class="${e.icon}"></i>`);
        });

        $(document).on('click', '.remove-social' ,function(){
            var parent = $(this).parents('.socials');
            parent.slideUp('slow', function(e){
                this.remove();
            });
        });
    })(jQuery);
</script>

@endpush

@extends($activeTemplate.'layouts.frontend')

@php
    $contact = getContent('contact.content',true)->data_values;
@endphp


@section('content')

<!-- Contact Section -->
<div class="contact-section padding-bottom padding-top">
    <div class="contact-thumb rev-side d-none d-lg-block">
        <img src="{{getImage('assets/images/frontend/contact/'.$contact->image,'660x700')}}" alt="contact">
    </div>
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
            </div>
            <div class="col-lg-6">
                <div class="section-header text-left ml-0 mb-low">
                    <h3 class="title">{{__(@$contact->title)}}</h3>
                    <p>
                        {{__($contact->short_details)}}
                    </p>
                </div>
                <form class="contact-form row" action="" method="POST">
                    @csrf
                    <div class="form-group col-sm-6">
                        <label for="name" class="custom--label text--title">@lang('Your Name')</label>
                        <input type="text" class="form-control form--control-2 text--title" name="name" id="name"required value="{{old('')}}">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="email" class="custom--label text--title">@lang('Your Email')</label>
                        <input type="email" class="form-control form--control-2 text--title" name="email" id="email" required value="{{old('')}}">
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="email" class="custom--label text--title">@lang('Subject')</label>
                        <input type="text" class="form-control form--control-2 text--title" name="subject" required value="{{old('')}}">
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="message" class="custom--label text--title">@lang('Your Message')</label>
                        <textarea name="message" class="form-control form--control-2 text--title" id="message" required>{{old('message')}}</textarea>
                    </div>
                    <div class="form-group col-sm-12 text-right mb-0">
                        <button type="submit" class="cmn--btn theme">@lang('Send Message')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Contact Section -->
<!-- Contact Section -->
<section class="contact-section padding-bottom">
    <div class="container">
        <div class="contact--wrapper">
            <div class="contact--information">
                <div class="info__item">
                    <div class="info__thumb bg--base">
                        <i class="las la-phone"></i>
                    </div>
                    <div class="info__content">
                        <span class="info__subtitle">@lang('Contact Us!')</span>
                        <h6 class="info__title">
                            <a href="Tel:{{@$contact->phone_no}}">{{@$contact->phone_no}}</a>
                        </h6>
                    </div>
                </div>
                <div class="info__item">
                    <div class="info__thumb bg--base">
                        <i class="las la-map-marker-alt"></i>
                    </div>
                    <div class="info__content">
                        <span class="info__subtitle">@lang('Address')</span>
                        <h6 class="info__title">
                           {{@$contact->address}}
                        </h6>
                    </div>
                </div>
                <div class="info__item">
                    <div class="info__thumb bg--base">
                        <i class="las la-paper-plane"></i>
                    </div>
                    <div class="info__content">
                        <span class="info__subtitle">@lang('Email Address')</span>
                        <h6 class="info__title">
                            <a href="Mailto:{{@$contact->email_address}}">{{@$contact->email_address}}</a>
                        </h6>
                    </div>
                </div>
            </div>
            <div class="maps"></div>
        </div>
    </div>
</section>
<!-- Contact Section -->

@endsection

@push('script')
     <script src="https://maps.google.com/maps/api/js?key={{$contact->map_api}}"></script>
     <script src="{{ asset($activeTemplateTrue.'js/map.js') }}"></script>
     <script>
            'use strict';
            (function ($) {
                var lat = "{{$contact->latitude}}"
                var long = "{{$contact->longitude}}"
                var mapOptions = {
                center: new google.maps.LatLng(lat, long),
                zoom: 7,
                styles: styleArray,
                scrollwheel: false,
                backgroundColor: '#001b83',
                mapTypeControl: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                var map = new google.maps.Map(document.getElementsByClassName("maps")[0],
                mapOptions);
                var myLatlng = new google.maps.LatLng(lat, long);
                var focusplace = {lat: 55.864237, lng: -4.251806};
                var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                icon: {
                    url: "{{asset('assets/images/map-marker.png')}}"
                }
                })  
            })(jQuery);
     </script>
@endpush

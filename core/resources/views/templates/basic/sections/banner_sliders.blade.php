@php
    $sliders = getContent('banner_sliders.element');
@endphp
<div class="banner-slider">
    <div class="banner__slider owl-theme owl-carousel">
        @foreach ($sliders as $slider)
            <div class="slide-item">
                <div class="banner-slide-content">
                    <a href="{{ url(@$slider->data_values->link ?? '/') }}" class="d-block">
                        <div class="banner__img">
                            <img src="{{ getImage('assets/images/frontend/banner_sliders/'. @$slider->data_values->slider, '1024x608') }}" alt="@lang('slider')">
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="slide-progress"></div>
</div>

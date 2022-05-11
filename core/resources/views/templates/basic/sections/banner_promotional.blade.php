
@php
    $banners = getContent('banner_promotional.element', false, 2, true);
@endphp

<div class="banner__quick-thumb">
    @foreach ($banners as $banner)
    <a target="_blank" href="{{$banner->data_values->link}}" class="thumb">
        <img src="{{ getImage('assets/images/frontend/banner_promotional/'. @$banner->data_values->image, '600x500') }}" alt="banner">
    </a>
    @endforeach
</div>


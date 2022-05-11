
@php
    $subscribe = getContent('subscribe.content', true);
@endphp

{{-- @if($subscribe) --}}
<section class="newsletter-section bg--base padding-top padding-bottom">
    <div class="container">
        <div class="section-header mb-4">
            <h3 class="title mb-0">@lang(@$subscribe->data_values->text)</h3>
        </div>
        <div class="subscribe-form ml-auto mr-auto">
            <input type="text" placeholder="Enter Your Email Address" class="form-control" name="email">
            <button type="button" class="subscribe-btn">@lang('Subscribe')</button>
        </div>
    </div>
</section>
{{-- @endif --}}

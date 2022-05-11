
@php
    $invite = getContent('invite.content', true);
@endphp

@if($invite)
<section class="newsletter-section bg--base mt-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-7 col-lg-6">
                <div class="newsletter-header">
                    <h3 class="title">
                        @lang(@$invite->data_values->text)
                    </h3>
                </div>
            </div>
            <div class="col-xl-5 col-lg-6 text-lg-right">
                <a href="{{ url(@$invite->data_values->button_link) }}" class="cmn--btn white">@lang(@$invite->data_values->button_text)</a>
            </div>
        </div>
    </div>
</section>
@endif

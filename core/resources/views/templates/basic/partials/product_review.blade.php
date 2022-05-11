@if($reviews->count() > 0)
@foreach ($reviews as $item)
    <div class="review-item">
        <div class="thumb">
            <img src="{{ getAvatar('assets/images/user/profile/'. $item->user->image,'350x300') }}" alt="@lang('review')">
        </div>
        <div class="content">
            <div class="entry-meta">
                <h6 class="posted-on">
                    <a href="#">{{ $item->user->fullname }}</a>
                    <span>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                </h6>
                <div class="ratings">
                    @php echo ratings($item->rating) @endphp

                </div>
            </div>
            <div class="entry-content">
                <p>{{ $item->review }}</p>
            </div>
        </div>
    </div>
    @php
        $last_id = $item->id;
    @endphp
@endforeach
@else
<div class="alert cl-title alert--base" role="alert">
        <strong>@lang('No reviews yet for this product')</strong>
    </div>
@endif

@if($reviews->currentPage() != $reviews->lastPage())
    <div id="load_more">
        <button type="button" name="load_more_button" class="cmn-btn btn-block" id="load_more_button" data-url="{{ $reviews->nextPageUrl() }}">@lang('Load More')</button>
    </div>
@endif

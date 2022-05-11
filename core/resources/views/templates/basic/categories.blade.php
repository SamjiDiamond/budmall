@extends($activeTemplate .'layouts.frontend')

@section('content')
 <!-- Category Section Starts Here -->
 <div class="category-section padding-bottom padding-top">
    <div class="container">
        <div class="row">
            @forelse ($all_categories as $item)
                <div class="col-md-2 mb-4">
                    <div class="cate-item">
                        <a href="{{ route('products.category', ['id'=>$item->id, 'slug'=>slug($item->name)]) }}" class="cate-inner">
                            <img class="w-100" src="{{ getImage(imagePath()['category']['path'].'/'.@$item->image, imagePath()['category']['size']) }}" alt="{{ __($item->name) }}">
                            <span class="line-limitation-1">{{ $item->name }}</span>
                        </a>
                    </div>
                </div>
            @empty
            <div class="col-md-12">
                @if($brands->count() == 0)
                <div class="col-lg-12 mb-30">
                    @include($activeTemplate.'partials.emptyMessage', ['message' => __($emptyMessage)])
                </div>
                @endif
            </div>
            @endforelse
        </div>
        {{ $all_categories->appends(request()->all())->links() }}
    </div>
</div>
<!-- Category Section Ends Here -->
@endsection


@push('breadcrumb-plugins')
    <li><a href="{{route('home')}}">@lang('Home')</a></li>
@endpush


@push('meta-tags')
    @include('partials.seo')
@endpush

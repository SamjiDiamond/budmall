@extends($activeTemplate .'layouts.frontend')

@section('content')

<!-- Product Single Section Starts Here -->
<div class="category-section padding-bottom-half padding-top oh">
    <div class="container">
        <div class="row product-details-wrapper justify-content-center">
            <div class="col-md-6 text-center padding-bottom">
                <h2 class="text--base">{{__($pageDetails->data_values->pageTitle)}}</h2>
            </div>
            <div class="col-md-12">
                @php echo $pageDetails->data_values->description; @endphp
            </div>

        </div>
    </div>
</div>

@endsection

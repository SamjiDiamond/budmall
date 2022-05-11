@extends('seller.layouts.app')

@section('panel')


<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card-body bg--10 ">
            <h4 class="text-white">@lang('Product Name') : {{ __($product_name) }}</h4>
            <h5 class="text-white">@lang('Attribute Name') : {{ __($variant->name) }}</h5>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-field">
                        @if(count($images))
                        <h5 class="mb-2">@lang('Click inside the box below to add more images')</h5>
                        @endif
                        <div class="input-images"></div>
                        <small class="form-text text-muted">
                            <i class="las la-info-circle"></i> @lang('You can only upload a maximum of 6 images')</label>
                        </small>
                    </div>

                    <button type="submit" class="btn btn-block btn--primary mt-3">@lang('Save')</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('breadcrumb-plugins')
<a href="{{ route('seller.products.variant.store', $variant->product->id) }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-backward"></i>@lang('Go Back')</a>
@endpush

@push('script-lib')
<script src="{{ asset('assets/dashboard/js/image-uploader.min.js') }}"></script>
@endpush

@push('style-lib')
<link href="https://fonts.googleapis.com/css?family=Lato:300,700|Montserrat:300,400,500,600,700|Source+Code+Pro&display=swap"
rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/image-uploader.min.css') }}">
@endpush


@push('script')
<script>

    'use strict';
    (function($){

        var dropdownParent = $('.has-select2');

        @if(isset($images))
            let preloaded = @json($images);
        @else
            let preloaded = [];
        @endif

        $('.input-images').imageUploader({
            preloaded: preloaded,
            imagesInputName: 'photos',
            preloadedInputName: 'old',
            maxFiles: 6
        });

        $(document).on('input', 'input[name="images[]"]', function(){
            var fileUpload = $("input[type='file']");
            if (parseInt(fileUpload.get(0).files.length) > 6){
                $('#errorModal').modal('show');
            }
        });

    })(jQuery)

</script>
@endpush

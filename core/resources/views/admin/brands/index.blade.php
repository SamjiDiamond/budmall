@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="row justify-content-end">
                    <div class="col-xl-3 mb-3">
                        <form action="{{ request()->routeIs('admin.brand.trashed')?route('admin.brand.trashed.search'):route('admin.brand.search') }}" method="GET" class="pt-3 px-3">
                            <div class="input-group has_append">
                                <input type="text" name="search" class="form-control" placeholder="@lang('Search')..." value="{{ request()->search ?? '' }}">
                                <div class="input-group-append">
                                    <button class="btn btn--primary" id="search-btn" type="submit"><i class="la la-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Logo')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Products')</th>
                                <th class="text-center">@lang('Top Brand')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($brands as $brand)
                                <tr>
                                    <td data-label="@lang('S.N.')">
                                        {{ $brands->firstItem() + $loop->index }}
                                    </td>

                                    <td data-label="@lang('Logo')">
                                        <div class="thumbnails d-inline-block">
                                            <div class="thumb">
                                                <a href="{{ getImage(imagePath()['brand']['path'].  '/'. @$brand->logo, imagePath()['brand']['size']) }}" class="image-popup">
                                                    <img src="{{ getImage(imagePath()['brand']['path'].  '/'. @$brand->logo, imagePath()['brand']['size']) }}" alt="@lang('image')">
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="@lang('Name')">
                                       {{ $brand->name }}
                                    </td>
                                    <td data-label="@lang('Products')">{{ $brand->products->count() }}</td>

                                    <td data-label="Top Brand">
                                        @if(!$brand->trashed())
                                        <label class="switch">
                                            <input type="checkbox" class="top_brand" name="top" {{ $brand->top?'checked':'' }} data-id={{ $brand->id }}>
                                            <span class="slider round"></span>
                                        </label>
                                        @endif
                                    </td>

                                    <td data-label="@lang('Action')">
                                        <a href="javascript:void(0)"
                                            data-toggle="tooltip" data-placement="top" title="@lang('Edit')"
                                            data-brand="{{ $brand }}" data-toggle="modal"
                                            data-image="{{ getImage('assets/images/brand/'. $brand->logo, imagePath()['brand']['size']) }}"
                                            class="icon-btn {{ $brand->trashed()?'':'edit-btn' }}">
                                            <i class="la la-pencil"></i>
                                        </a>

                                        <button type="button" data-toggle="tooltip" data-placement="top" title="{{ $brand->trashed()?trans('Restore'):trans('Delete') }}"
                                            class="icon-btn btn--{{ $brand->trashed()?'success':'danger' }} delete-btn ml-1"
                                            data-type="{{ $brand->trashed()?'restore':'delete' }}"
                                            data-id='{{ $brand->id }}'>
                                            <i class="las la-{{ $brand->trashed()?'trash-restore':'trash' }}"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($brands->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($brands) }}
                </div>
            @endif
        </div>
    </div>
</div>

<div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add Brand')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('Close')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.brand.store', 0) }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="payment-method-item">
                                <label class="font-weight-bold required">@lang('Logo')</label>
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview" style="background-image: url({{ getImage(null, imagePath()['brand']['size']) }})">
                                                <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" class="profilePicUpload" name="image_input" id="profilePicUpload1" accept=".png, .jpg, .jpeg" required>
                                            <label for="profilePicUpload1" class="bg--primary">@lang('Upload Logo')</label>
                                            <small class="form-text text-muted mb-3"> @lang('Supported Files:')
                                                <b>@lang('jpeg, jpg, png')</b>.
                                                @lang("Image will be resized into ". imagePath()['brand']['size'] ."px")</b>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="font-weight-bold required">@lang('Brand Name')</label>
                                <input type="text" class="form-control" placeholder="@lang('Enter Brand Name')" value="{{ old('name') }}" name="name" required/>
                                <small class="form-text text-muted"><i class="las la-info-circle"></i>@lang('Must be Unique')</small>
                            </div>


                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Meta Title')</label>
                                <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title') }}" placeholder="@lang('Meta Title')">
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Meta Description')</label>
                                <textarea name="meta_description" rows="5" class="form-control">{{ old('meta_description') }} </textarea>
                            </div>

                            <div class="form-group ">
                                <label class="font-weight-bold">@lang('Meta Keywords')</label>

                                <select name="meta_keywords[]" class="form-control select2-auto-tokenize"  multiple="multiple"></select>

                                <small class="form-text text-muted">
                                    <i class="las la-info-circle"></i>
                                    @lang('Type , or hit enter to seperate keywords')
                                </small>

                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-block btn--success mr-2">@lang('Add')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Brand')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">

                            <div class="payment-method-item">
                                <label class="font-weight-bold">@lang('Logo')</label>
                                <div class="image-upload">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview" style="background-image: url({{ getImage(null, imagePath()['brand']['size']) }})">
                                                <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" class="profilePicUpload" name="image_input"
                                                id="profilePicUpload" accept=".png, .jpg, .jpeg" >
                                            <label for="profilePicUpload" class="bg--primary">@lang('Upload Image')</label>
                                            <small class="form-text text-muted mb-3"> @lang('Supported Files:')
                                                <b>@lang('jpeg, jpg, png')</b>.
                                                @lang("Image will be resized into ". imagePath()['brand']['size'] ."px")</b>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Brand Name')</label>
                                <input type="text" class="form-control" placeholder="@lang('Enter Brand Name')" name="name" />
                                <small class="form-text text-muted"><i class="las la-info-circle"></i>@lang('Must be Unique')</small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Meta Title')</label>
                                <input type="text" class="form-control" name="meta_title" placeholder="@lang('Meta Title')">
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Meta Description')</label>
                                <textarea name="meta_description" rows="5" class="form-control"></textarea>
                            </div>

                            <div class="form-group ">
                                <label class="font-weight-bold">@lang('Meta Keywords')</label>

                                <select name="meta_keywords[]" class="form-control select2-auto-tokenize"  multiple="multiple">

                                </select>

                                <small class="form-text text-muted">
                                    <i class="las la-info-circle"></i>
                                    @lang('Type , or hit enter to seperate keywords')
                                </small>

                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-block btn--success mr-2">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- REMOVE METHOD MODAL --}}

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="" method="POST" id="deletePostForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn-sm btn--danger">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('breadcrumb-plugins')
    @if(request()->routeIs('admin.brand.all'))
        <button data-toggle="modal" data-target="#addModal" class="btn btn-sm btn--success box--shadow1 text--small"> <i class="las la-plus"></i> @lang('Add New')</button>
    @else
        @if(request()->routeIs('admin.brand.trashed.search'))
            <a href="{{ route('admin.brand.trashed') }}"
                class="btn btn-sm btn--primary box--shadow1 text--small">
                <i class="la la-fw la-backward"></i> @lang('Go Back')
            </a>
        @else
            <a href="{{ route('admin.brand.all') }}"
                class="btn btn-sm btn--primary box--shadow1 text--small">
                <i class="la la-fw la-backward"></i> @lang('Go Back')
            </a>
        @endif
    @endif

    @if(request()->routeIs('admin.brand.all'))
        <a href="{{ route('admin.brand.trashed') }}" class="btn btn-sm btn--danger box--shadow1 text--small"><i class="las la-trash-alt"></i>@lang('Trashed')</a>
    @endif
@endpush

@push('script-lib')
    <script src="{{ asset('assets/dashboard/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/bootstrap-iconpicker.min.css') }}">
@endpush

@push('script')
    <script>
        'use strict';
        (function($){

            $('.image-popup').magnificPopup({
                type: 'image'
            });

            $('#addModal, #editModal').on('shown.bs.modal', function (e) {
                $(document).off('focusin.modal');
            });

            $('.edit-btn').on('click', function () {
                var modal = $('#editModal');
                var brand    = $(this).data('brand');
                var image       = $(this).data('image');
                $.each(brand.meta_keywords, function (i, item) {
                    modal.find('.select2-auto-tokenize').append($('<option>', {
                        value: item,
                        text : item,
                    }));
                });
                modal.find('.profilePicPreview').css('background-image', `url(${image})`);
                modal.find('input[name=name]').val(brand.name);
                modal.find('input[name=meta_title]').val(brand.meta_title);
                modal.find('textarea[name=meta_description]').val(brand.meta_description);
                modal.find('.select2-auto-tokenize').val(brand.meta_keywords);

                var form = document.getElementById('editForm');
                form.action = '{{ route('admin.brand.store', '') }}' + '/' + brand.id;

                modal.modal('show');
            });

            $('.delete-btn').on('click', function ()
            {
                var modal   = $('#deleteModal');
                var id      = $(this).data('id');
                var type    = $(this).data('type');
                var form    = document.getElementById('deletePostForm');

                if(type == 'delete'){
                    modal.find('.modal-title').text('{{ trans("Delete Brand") }}');
                    modal.find('.modal-body').text('{{ trans("Are you sure to delete this brand?") }}');
                }else{
                    modal.find('.modal-title').text('{{ trans("Restore Brand") }}');
                    modal.find('.btn--danger').removeClass('btn--danger').addClass('btn--success');
                    modal.find('.modal-body').text('{{ trans("Are you sure to restore this brand?") }}');
                }

                form.action = '{{ route('admin.brand.delete', '') }}' + '/' + id;
                modal.modal('show');
            });

            $('.top_brand').on('change', function () {
                var id = $(this).data('id');
                var mode = $(this).prop('checked');

                var data = {'id': id};
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    url: "{{ route('admin.brand.settop') }}",
                    method: 'POST',
                    data: data,
                    success: function (result) {
                        notify('success', result.success);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush

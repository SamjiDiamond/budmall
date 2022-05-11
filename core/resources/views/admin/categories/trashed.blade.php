@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">

            <div class="card-body">

                <div class="row justify-content-end">
                    <div class="col-lg-4 mb-3">
                        <form action="{{ route('admin.category.trashed.search') }}" method="GET">
                            <div class="input-group has_append">
                                <input type="text" name="search" class="form-control" placeholder="@lang('Type Category Name & Hit Enter')" value="{{ request()->search ?? '' }}">
                                <div class="input-group-append">
                                    <button class="btn btn--primary" id="search-btn" type="submit"><i class="la la-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Thumbnail')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Icon')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($categories as $category)
                                <tr>
                                    <td data-label="S.N.">
                                        {{ ($categories->currentPage()-1) * $categories->perPage() + $loop->iteration }}
                                    </td>
                                    <td data-label="Thumbnail">
                                        <div class="user">
                                            <div class="thumb">
                                                <a href="{{ getImage('assets/images/category/'  . @$category->image) }}" class="image-popup">
                                                <img src="{{ getImage('assets/images/category/'. @$category->image) }}" alt="@lang('profile-image')">
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <td data-label="@lang('Name')">{{ __($category->name) }}</td>

                                    <td data-label="Icon">@php echo $category->icon @endphp </td>

                                    <td data-label="Action">

                                        <span data-toggle="tooltip" data-placement="top" title="@lang('Restore')">
                                            <button type="button"
                                                class="icon-btn btn--success ml-1"
                                                data-toggle="modal"
                                                data-target="#deleteModal" data-id='{{ $category->id }}'>
                                                <i
                                                    class="las la-trash-restore"></i>
                                            </button>
                                        </span>
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
            @if($categories->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($categories) }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- REMOVE METHOD MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="" method="POST" id="deletePostForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize" id="deleteModalLabel">@lang('Restore Category')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-bold">
                        @lang('Are you sure to restore this category')?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn-sm btn--success">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('breadcrumb-plugins')

        @if(request()->routeIs('admin.category.trashed.search'))
            <a href="{{ route('admin.category.trashed') }}"
                class="btn btn-sm btn--primary box--shadow1 text--small">
                <i class="la la-backward"></i> @lang('Go Back')
            </a>
        @else
            <a href="{{ route('admin.category.all') }}"
                class="btn btn-sm btn--primary box--shadow1 text--small">
                <i class="la la-backward"></i> @lang('Go Back')
            </a>
        @endif
@endpush

@push('script-lib')
    <script src="{{ asset('assets/dashboard/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" />
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/bootstrap-iconpicker.min.css') }}">
@endpush


@push('script')
    <script>
        'use strict';
        (function($){
            $('.image-popup').magnificPopup({
                type: 'image'
            });
            $('#deleteModal').on('show.bs.modal', function (e) {
                var id = $(e.relatedTarget).data('id');
                var form = document.getElementById('deletePostForm');
                form.action = '{{ route('admin.category.delete', '') }}' +
                    '/' + id;
            });
        })(jQuery)
    </script>

@endpush

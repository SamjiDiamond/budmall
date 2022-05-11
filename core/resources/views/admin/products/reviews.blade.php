@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                @if(!request()->routeIs('admin.product.reviews.trashed'))
                    <div class="row justify-content-end">
                        <div class="col-xl-3 mb-3">
                            <form action="{{ route('admin.product.reviews.search') }}" method="GET" class="pt-3 px-3">
                                <div class="input-group has_append">
                                    <input type="text" name="key" class="form-control" placeholder="@lang('Search')..."
                                        value="{{request()->key ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn--primary" id="search-btn" type="submit"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Id')</th>
                                <th>@lang('Product')</th>
                                <th>@lang('User')</th>
                                <th>@lang('Rating')</th>
                                <th>@lang('Date')</th>
                                <th class="text-center">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                            <tr>
                                <td data-label="Id">
                                    {{ $reviews->firstItem() + $loop->index }}
                                </td>
                                <td data-label="@lang('Product')">{{ __($review->product->name)}}</td>
                                <td data-label="@lang('User')">{{ __($review->user->username)}}</td>
                                <td data-label="@lang('Rating')">{{ __($review->rating)}}</td>
                                <td data-label="@lang('Rating')">{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</td>
                                <td data-label="@lang('Action')">
                                    <button type="button" class="icon-btn mr-1 view-btn" data-toggle="tooltip" title="@lang('View')" data-user="{{ __($review->user->username)}}" data-rating="{{ $review->rating }}" data-review="{{ $review->review }}" data-user_link="{{ route('admin.users.detail', $review->user->id) }}">
                                        <i class="la la-desktop"></i>
                                    </button>

                                    <button type="button" class="icon-btn btn--{{$review->trashed()?'success':'danger'}} deleteBtn" data-toggle="tooltip" data-title="@lang($review->trashed()?'Restore':'Delete')" data-type="{{$review->trashed()?'restore':'delete'}}" data-id='{{$review->id}}'>
                                        <i class="la la-{{$review->trashed()?'redo':'trash'}}"></i>
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

            @if($reviews->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($reviews) }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- REMOVE METHOD MODAL --}}

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form action="" method="POST" id="deleteForm">
            @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize" id="deleteModalLabel">
                        @lang('Removal Confirmation')
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--danger">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Product Review')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush">
                   <li class="list-group-item d-flex flex-wrap justify-content-between">
                        <span class="font-weight-bold">@lang('Customer')</span>
                        <a href="" id="user-detail"><sapn id="name"></sapn></a>
                    </li>

                   <li class="list-group-item d-flex flex-wrap justify-content-between">
                        <span class="font-weight-bold">@lang('Rating')</span>
                        <span id="rating"></span>
                    </li>

                    <li class="list-group-item d-flex flex-wrap justify-content-between">
                        <span class="font-weight-bold">@lang('Review')</span>
                        <span id="review" class="text--primary font-italic"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection


@push('script')

<script>

    'use strict';
    (function($){
        $('.deleteBtn').on('click', function () {
        var modal   = $('#deleteModal');
        var id      = $(this).data('id');
        var type    = $(this).data('type');
        var form    = document.getElementById('deleteForm');

        if(type == 'delete'){
            modal.find('.modal-title').text('{{ trans("Delete Review") }}');
            modal.find('.modal-body').text('{{ trans("Are you sure to delete this review?") }}');
        }else{
            modal.find('.modal-title').text('{{ trans("Restore Review") }}');
            modal.find('.btn--danger').removeClass('btn--danger').addClass('btn--success');
            modal.find('.modal-body').text('{{ trans("Are you sure to restore this review?") }}');
        }
        form.action = '{{route('admin.product.review.delete', '')}}'+'/'+id;
        modal.modal('show');
    });

    $('.view-btn').on('click', function () {
        var modal   = $('#viewModal');

        modal.find('#name').text($(this).data('user'));
        modal.find('#rating').text($(this).data('rating'));
        modal.find('#review').text($(this).data('review'));
        modal.find('#user-detail').attr('href', $(this).data('user_link'));

        modal.modal('show');
    });

    $('.image-popup').magnificPopup({
        type: 'image'
    });
    })(jQuery)


</script>

@endpush


@push('breadcrumb-plugins')
    @if(!request()->routeIs('admin.product.reviews'))
        @if(request()->routeIs('admin.products.trashed.search'))
        <a href="{{route('admin.product.reviews.trashed')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-backward"></i>@lang('Go Back')</a>
        @else
        <a href="{{route('admin.product.reviews')}}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-backward"></i>@lang('Go Back')</a>
        @endif
    @endif

    @if(request()->routeIs('admin.product.reviews'))
    <a href="{{ route('admin.product.reviews.trashed') }}" class="btn btn-sm btn--danger box--shadow1 text--small"><i class="la la-trash-alt"></i>@lang('Trashed')</a>
    @endif
@endpush

@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Code')</th>
                                <th>@lang('Discount Type')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Expire Date')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($coupons as $coupon)
                                <tr>
                                    <td data-label="@lang('S.N.')">{{ $loop->iteration }}</td>
                                    <td data-label="@lang('Name')">
                                        {{ $coupon->coupon_name }}
                                    </td>
                                    <td data-label="@lang('Code')">
                                        {{ $coupon->coupon_code }}
                                    </td>
                                    <td data-label="@lang('Discount')">
                                        @if($coupon->discount_type == 1)
                                        <span class="text--small badge font-weight-normal badge--primary"> {{ $coupon->couponType }}</span>
                                        @else
                                        <span class="text--small badge font-weight-normal badge--dark"> {{ $coupon->couponType }}</span>
                                        @endif

                                    </td>
                                    <td data-label="@lang('Status')">

                                        <label class="switch">
                                            <input type="checkbox" class="change_status" name="top" {{ $coupon->status?'checked':'' }} data-id={{ $coupon->id }}>
                                            <span class="slider round"></span>
                                        </label>

                                    </td>
                                    <td data-label="@lang('Expire Date')" class="{{$coupon->end_date < $now?'text--danger':''}}">
                                       {{ showDateTime($coupon->end_date, 'd M, Y') }}
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <a href="{{ route('admin.coupon.edit', $coupon->id) }}" data-toggle="tooltip" data-placement="top" title="@lang('Edit')"class="icon-btn edit-btn">
                                            <i class="la la-pencil"></i>
                                        </a>

                                        <button type="button" data-toggle="tooltip" data-placement="top" title="@lang('Delete')"
                                            class="icon-btn btn--danger delete-btn ml-1"
                                            data-type="delete" data-id='{{ $coupon->id }}'>
                                            <i class="las la-trash"></i>
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
        </div>
    </div>
</div>


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
    <a href="{{ route('admin.coupon.create') }}" class="btn btn-sm btn--success box--shadow1 text--small"> <i class="las la-plus"></i> @lang('Add New')</a>

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
            $('.delete-btn').on('click', function ()
            {
                var modal   = $('#deleteModal');
                var id      = $(this).data('id');
                var type    = $(this).data('type');
                var form    = document.getElementById('deletePostForm');
                modal.find('.modal-title').text('{{ trans("Delete Coupon") }}');
                modal.find('.modal-body').text('{{ trans("Are you sure to delete this coupon?") }}');
                form.action = '{{ route('admin.coupon.delete', '') }}' + '/' + id;
                modal.modal('show');

            });

            $('.change_status').on('change',function () {
                var id = $(this).data('id');
                var mode = $(this).prop('checked');

                var data = {
                    'id': id
                };
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    url: "{{ route('admin.coupon.status') }}",
                    method: 'POST',
                    data: data,
                    success: function (result) {
                        notify('success', result.success);
                    }
                });
            });
        })(jQuery)

    </script>
@endpush

@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Charge')</th>
                                <th class="text-center">@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($shipping_methods as $s_method)
                                <tr>
                                    <td data-label="@lang('S.N.')">{{ ($shipping_methods->currentPage()-1) * $shipping_methods->perPage() + $loop->iteration }}
                                    </td>
                                    <td data-label="@lang('Name')">{{ __($s_method->name) }}</td>
                                    <td data-label="@lang('Charge')">{{ __($s_method->charge) }}</td>
                                    <td data-label="@lang('Status')">

                                        <label class="switch">
                                            <input type="checkbox" class="status-change" name="top" {{ $s_method->status==1?'checked':'' }} data-id={{ $s_method->id }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <span data-toggle="tooltip" data-placement="top" title="@lang('Edit')">
                                            <a href="{{ route('admin.shipping.methods.edit', $s_method->id) }}"
                                                class="icon-btn btn--primary btn-rounded">
                                                <i class="la la-pencil"></i>
                                            </a>
                                        </span>
                                        <span data-toggle="tooltip" data-placement="top" title="@lang('Delete')">
                                            <button type="button" class="icon-btn btn--danger ml-1" data-toggle="modal"
                                                data-target="#deleteModal" data-id='{{ $s_method->id }}'>
                                                <i class="la la-trash"></i>
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


                @if($shipping_methods->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($shipping_methods) }}
                    </div>
                @endif
            </div>
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
                    <h5 class="modal-title text-capitalize" id="deleteModalLabel">@lang('Confirmation Alert')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @lang('Are you sure to delete this method?')
                </div>
                <div class="modal-footer">
                    <button type="button" class="icon-btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="icon-btn btn--danger">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.shipping.methods.create') }}" class="btn btn-sm btn--primary box--shadow1 text-white text--small">
        <i class="la la-plus"></i> @lang('Add New')
    </a>
@endpush

@push('script')

    <script>
        "use strict";
        (function($){
            $('#deleteModal').on('show.bs.modal', function (e) {
                var id = $(e.relatedTarget).data('id');
                var form = document.getElementById('deletePostForm');
                form.action =
                    '{{ route('admin.shipping.methods.delete', '') }}' +
                    '/' + id;
            });

            $('.status-change').on('change',function () {
                var id = $(this).data('id');
                var mode = $(this).prop('checked');

                var data = {
                    'id': id
                };

                $.ajax({
                    url: `{{ route('admin.shipping.methods.status-change') }}`,
                    method: "get",
                    data: data,
                    success: function (result) {
                        notify('success', result.message);
                    }
                });
            });
        })(jQuery)

    </script>

@endpush

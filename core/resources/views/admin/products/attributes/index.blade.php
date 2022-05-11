@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="row justify-content-end">
                    <div class="col-xl-3 mb-3">
                        <form action="" method="GET" class="pt-3 px-3">
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
                                <th>@lang('Name')</th>
                                <th>@lang('Name for Customer')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse($attributes as $attr)
                            <tr>
                                <td data-label="@lang('S.N.')">{{ $attributes->firstItem() + $loop->index }}</td>
                                <td data-label="@lang('Name')">{{ __($attr->name) }}</td>
                                <td data-label="@lang('Name for Customer')">{{ __($attr->name_for_user) }}</td>
                                <td data-label="@lang('Type')">@if($attr->type==1) @lang('Text')  @elseif($attr->type==2) @lang('Color')  @else @lang('Image') @endif</td>
                                <td data-label="@lang('Action')">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-title="@lang('Edit')" data-id="{{$attr->id}}" data-name="{{ $attr->name }}" data-name_for_user="{{$attr->name_for_user}}" data-type="{{$attr->type}}" class="icon-btn editBtn">
                                        <i class="la la-pencil"></i>
                                    </a>
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
            @if($attributes->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($attributes) }}
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Add Modal --}}
<div id="addModal" class="modal fade">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add Attribute Type')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.attributes.store', 0) }}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="font-weight-bold">@lang('Name')</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" placeholder="@lang('Enter Name For Admin')" value="" name="name" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="font-weight-bold">@lang('Name for User')</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" placeholder="@lang('Enter Name For User')" value="" name="name_for_user" required/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="font-weight-bold">@lang('Type')</label>
                        </div>
                        <div class="col-md-9">
                            <select class="select2-basic" name="type" required>
                                <option value="" disabled selected>@lang('Click to Pick')</option>
                                <option value="1">@lang('Text')</option>
                                <option value="2">@lang('Color')</option>
                                <option value="3">@lang('Image')</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-block btn--success mr-2">@lang('Add')</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="modal fade">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Attribute Type')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="editForm">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="font-weight-bold">@lang('Name')</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" placeholder="@lang('Enter Name For Admin')" name="name" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="font-weight-bold">@lang('Name for User')</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" placeholder="@lang('Enter Name For User')" name="name_for_user" required/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="font-weight-bold">@lang('Type')</label>
                        </div>
                        <div class="col-md-9">
                            <select class="select2-basic" name="type" required>
                                <option value="" disabled selected>@lang('Click to Pick')</option>
                                <option value="1">@lang('Text')</option>
                                <option value="2">@lang('Color')</option>
                                <option value="3">@lang('Image')</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-block btn--success mr-2">@lang('Update')</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
    <button data-toggle="modal" data-target="#addModal" class="btn btn-sm btn--success box--shadow1 text--small"> <i class="la la-plus"></i>@lang('Add New')</button>
@endpush
@push('script')
<script>
    "use strict";
    (function ($) {
        $('.editBtn').on('click', function () {
            var modal           = $("#editModal");
            var id              = $(this).data('id');
            var name            = $(this).data('name');
            var name_for_user   = $(this).data('name_for_user');
            var type            = $(this).data('type');
            var typeName        = type==1?'Text':type==2?'Color':'Image';
            modal.find('select[name=type]').val(type);
            modal.find('.select2-selection__rendered').text(typeName);
            modal.find('input[name=name]').val(name);
            modal.find('input[name=name_for_user]').val(name_for_user);
            var form = document.getElementById('editForm');
            form.action = '{{route('admin.attributes.store', '')}}'+'/'+id;
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush

@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="row {{request()->routeIs('admin.products.pending') ? 'justify-content-between':'justify-content-end'}}">
                    @if (request()->routeIs('admin.products.pending') )
                        <div class="col-xl-9 mb-3">

                            <div class="pt-3 px-3">
                                @if ($products->count() > 0)
                                    <button class="btn btn--success" data-toggle="modal" data-target="#approveAll" type="button"><i class="las la-check-double"></i> @lang('Approve All') </button>
                                @else

                                <button class="btn btn--success disabled" disabled><i class="las la-check-double"></i> @lang('Approve All') </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="col-xl-3 mb-3">
                        <form action="" method="GET" class="pt-3 px-3">
                            <div class="input-group has_append">
                                <input type="text" name="search" class="form-control" placeholder="@lang('Search')..."
                                    value="{{request()->search ?? '' }}">
                                <div class="input-group-append">
                                    <button class="btn btn--primary" id="search-btn" type="submit"><i class="la la-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @include('admin.partials.datatable')

                <div class="table-responsive--md table-responsive">
                    <table id="example" class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                @if(request()->routeIs('admin.products.seller'))
                                <th>@lang('Seller')</th>
                                @endif
                                <th>@lang('Product')</th>
                                <th>@lang('Brand')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('In Stock')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td data-label="@lang('S.N.')">
                                    {{ $products->firstItem() + $loop->index }}
                                </td>

                                @if(request()->routeIs('admin.products.seller'))
                                <td data-label="@lang('Seller')">
                                    <span class="font-weight-bold d-block">
                                        {{ @$product->seller->fullname}}
                                    </span>
                                    <a href="{{ route('admin.sellers.detail', @$product->seller->id??0) }}">{{ @$product->seller->username }}</a>
                                </td>
                                @endif

                                <td data-label="@lang('Product')">
                                    <div class="thumbnails d-inline-block">
                                        <div class="thumb">
                                            <a href="{{ getImage(imagePath()['product']['path'].  '/'. @$product->main_image, imagePath()['product']['size']) }}" class="image-popup">
                                                <img src="{{ getImage(imagePath()['product']['path']. '/'. @$product->main_image, imagePath()['product']['size']) }}" alt="@lang('image')">
                                            </a>
                                        </div>
                                    </div>

                                    <span class="d-block mt-2">
                                        @if($product->is_featured)
                                        <span class="text--danger" data-toggle="tooltip" title="@lang('Featured')"><i class="fas fa-2x fa-fire"></i></span>
                                        @endif

                                        <a href="{{ route('admin.products.edit', [$product->id, slug($product->name)]) }}"><span class="name mb-0"  onclick="{{$product->trashed()?'return false':''}}" data-toggle="tooltip" data-placement="top" title="{{ __($product->name) }}">
                                            {{ shortDescription($product->name, 50) }}</span>
                                        </a>
                                    </span>
                                </td>


                                <td data-label="@lang('Brand')">{{$product->brand->name}}</td>
                                <td data-label="@lang('Price')">{{$product->base_price}}</td>
                                <td data-label="@lang('In Stock')">
                                    <label class="switch">
                                            <input type="checkbox" class="change_status" name="top" {{ $product->in_stock?'checked':'' }} data-id={{ $product->id }}>
                                            <span class="slider round"></span>
                                    </label>
                                </td>
                                <td data-label="@lang('Status')">
                                    @if($product->status == 1)
                                       <span class="badge badge--success">@lang('Active')</span>
                                    @else
                                       <span class="badge badge--warning">@lang('Pending')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                                    {{-- If sellers product--}}
                                    @if(request()->routeIs('admin.products.seller'))
                                        @if($product->status == 1)
                                            <a href="javascript:void(0)" class="approve-btn icon-btn btn--dark  mr-1" data-toggle="tooltip" data-placement="top" title="@lang('Mark as pending')" data-status="{{$product->status}}" data-id="{{$product->id}}">
                                                <i class="la la-ban"></i>
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" class="approve-btn icon-btn btn--success  mr-1" data-toggle="tooltip" data-placement="top" title="@lang('Approve')" data-status="{{$product->status}}" data-id="{{$product->id}}">
                                                <i class="las la-check-double"></i>
                                            </a>
                                        @endif
                                    @endif

                                    <a href="@if($product->trashed()) javascript:void(0) @else {{ route('admin.products.edit', [$product->id, slug($product->name)]) }} @endif" class="icon-btn btn--primary {{$product->trashed()?'disabled':''}} mr-1" data-toggle="tooltip" data-placement="top" title="@lang('Edit')">
                                        <i class="la la-edit"></i>
                                    </a>
                                    
                                    
                                    

                                    <button type="button" class="icon-btn btn--{{$product->trashed()?'success':'danger'}} deleteBtn" data-toggle="tooltip" data-title="{{$product->trashed()?'Restore':'Delete'}}" data-type="{{$product->trashed()?'restore':'delete'}}" data-id='{{$product->id}}'>
                                        <i class="la la-{{$product->trashed()?'redo':'trash'}}" ></i>
                                    </button>

                                    @if(!request()->routeIs('admin.products.trashed'))
                                    <div class="dropdown d-inline-flex" data-toggle="tooltip" title="@lang('More')">
                                        <button class="btn icon-btn btn--dark dropdown-toggle" type="button" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                            <span class="icon text-white"><i class="las la-chevron-circle-down mr-0"></i></span>
                                        </button>

                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a href="javascript:void(0)" class=" dropdown-item highlight-btn" data-id="{{ $product->id }}" data-featured="{{ $product->is_featured }}">
                                                @if ($product->is_featured == 1)
                                                @lang('Remove from Featured')
                                                @else
                                                @lang('Mark as Featured')
                                                @endif
                                            </a>

                                            @if($product->track_inventory)
                                                <a href="{{ route('admin.products.stock.create', [$product->id]) }}" class="dropdown-item">@lang('Manage Inventory')</a>
                                            @endif

                                            @if($product->has_variants)
                                            <a href="{{ route('admin.products.variant.store', [$product->id]) }}" class="dropdown-item">
                                                @lang('Add Variants')
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
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
            @if($products->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($products) }}
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
            <form action="" method="POST" id="deleteForm">
            @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize" id="deleteModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-bold">

                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--danger">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Approve all MODAL --}}
<div class="modal fade" id="approveAll" tabindex="-1" role="dialog" aria-labelledby=""
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{route('admin.products.approve.all')}}" method="POST">
            @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize">@lang('Approve All Product')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-bold">
                        @lang('Are you sure to approve all pending product?')
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{--Approve MODAL --}}

<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby=""
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{route('admin.products.action')}}" method="POST" id="approve">
            @csrf
                <input type="hidden" name="product_id">
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize" id="approveModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-bold msg"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn aprv-btn">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="highlight-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Mark as featured')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <form action="{{route('admin.products.featured')}}" method="post">
                @csrf
                <input type="hidden" name="product_id"/>
                <div class="modal-body">
                    <p class="msg">@lang('Are you sure to mark as featured?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--success">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('breadcrumb-plugins')
    @if (request()->routeIs('admin.products.seller'))
        <a href="{{route('admin.products.pending')}}" class="btn btn-sm btn--dark box--shadow1 text--small">
            <i class="las la-hourglass-end"></i>@lang('Pending Products')
        </a>
    @endif

    @if(request()->routeIs('admin.products.all') || request()->routeIs('admin.products.admin') )
        <a href="{{ route('admin.products.create') }}" data-toggle="tooltip" title="@lang('Shortcut'): shift+n" class="btn btn-sm btn--success box--shadow1 text--small"><i class="la la-plus"></i>@lang('Add New')</a>
    @endif

@endpush

@push('script')

<script>

    "use strict";
    (function ($) {

        $(document).keypress(function (e) {
            var unicode = e.charCode ? e.charCode : e.keyCode;
            if(unicode == 78){
                window.location = "{{ route('admin.products.create') }}";
            }
        });

        $('.deleteBtn').on('click', function () {
            var modal   = $('#deleteModal');
            var id      = $(this).data('id');
            var type    = $(this).data('type');
            var form    = document.getElementById('deleteForm');

            if(type == 'delete'){
                modal.find('.modal-title').text('{{ trans("Delete Product") }}');
                modal.find('.modal-body').text('{{ trans("Are you sure to delete this product?") }}');
            }else{
                modal.find('.modal-title').text('{{ trans("Restore Product") }}');
                modal.find('.btn--danger').removeClass('btn--danger').addClass('btn--success');
                modal.find('.modal-body').text('{{ trans("Are you sure to restore this product?") }}');
            }

            form.action = '{{ route('admin.products.delete', '') }}'+'/'+id;
            modal.modal('show');
        });

        $('.image-popup').magnificPopup({
            type: 'image'
        });

        $('.highlight-btn').on('click', function(e){
            var modal       = $('#highlight-modal');
            var id          = $(this).data('id');
            var featured    = $(this).data('featured');

            if(featured == 1){
                modal.find('.msg').text("@lang('Are you sure to remove from featured?')")
                modal.find('.modal-title').text("@lang('Remove from featured')")
            }else{
                modal.find('.msg').text("@lang('Are you sure to mark as featured?')");
                modal.find('.modal-title').text("@lang('Mark as featured')")
            }

            modal.find('input[name=product_id]').val(id);
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
                    url: "{{ route('admin.product.stock') }}",
                    method: 'POST',
                    data: data,
                    success: function (result) {
                        notify('success', result.success);
                    }
                });
            });


        $('.approve-btn').on('click', function () {
            var modal = $("#approveModal")
            var status = $(this).data('status')
            var text;
            var label;
            var btn;
            if(status == 1){
                text = `@lang('Are you sure to mark this product as pending?')`
                label = `@lang('Mark as pending')`
                btn = 'btn--danger'
            }else{
                text = `@lang('Are you sure to approve this product?')`
                label = '@lang('Product Approval')'
                btn = 'btn--primary'
            }
            modal.find('.msg').text(text)
            modal.find('input[name=product_id]').val($(this).data('id'))
            modal.find('.aprv-btn').addClass(btn)
            modal.find('#approveModalLabel').text(label)
            modal.modal('show')
        });
    })(jQuery)
    
</script>

@endpush

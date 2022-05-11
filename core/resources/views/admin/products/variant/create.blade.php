@extends('admin.layouts.app')

@section('panel')

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card-body bg--10 py-2">
            <h4 class="text-white">@lang('Product Name') : {{ __($productName) }}</h4>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card">
            @if($currentAttributes->count())
                <div class="card-header">
                    <h5 class="ml-3 text-danger font-weight-bold">
                        @lang('If you add new a new type of variant, your previous stock records for this product will be removed.')
                    </h5>
                </div>
            @endif
            <form action="{{ route('admin.products.variant.store', $productId) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="attr_type">
                <div class="card-body has_select2">

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Type')</label>
                                <select class="form-control select2-basic attrId" name="attr_id" required>
                                    <option selected value="" disabled>@lang('Select One')</option>
                                    @foreach ($attributes as $attr)
                                        <option data-type="{{$attr->type}}" value="{{$attr->id}}">{{$attr->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="attr-wrapper"></div>

                    <button type="button" class="btn btn-outline--success add_more d-none"><i class="la la-plus"></i></button>
                </div>
                <div class="card-footer">

                    <div class="form-row justify-content-center">
                        <div class="form-group col-xl-12">
                            <button type="submit" class="btn btn-block btn--success mr-xl-2">@lang('Add')</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mt-5">
        <h6 class="page-title">@lang('Current Variants')</h6>
    </div>

    @forelse ($currentAttributes as $attr)
    <div class="col-lg-12">
        <div class="card my-3">
            <div class="card-header">
                <h5>{{ $attr[0]->productAttribute->name }}</h5>
            </div>
            <div class="card-body">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Value')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($attr as $item)

                               <tr>

                                   <td data-label="@lang('S.N.')">{{ $loop->iteration }}</td>
                                   <td data-label="@lang('Name')">{{ __($item->name) }}</td>
                                   <td data-label="@lang('Value')">
                                        @if($attr[0]->productAttribute->type == 2)
                                        <span class="px-3 p-2 w-50" style="background-color: #{{ $item->value }}">&nbsp;</span>
                                        @elseif($attr[0]->productAttribute->type == 3)
                                        <div class="thumbnails d-inline-block">
                                            <div class="thumb">
                                                <a href="{{ getImage(imagePath()['attribute']['path'].  '/'. @$item->value, imagePath()['attribute']['size']) }}" class="image-popup">
                                                    <img src="{{ getImage(imagePath()['attribute']['path']. '/'. @$item->value, imagePath()['attribute']['size']) }}" alt="@lang('image')">
                                                </a>
                                            </div>
                                        </div>
                                        @else
                                        {{ $item->value }}
                                        @endif
                                    </td>
                                   <td data-label="@lang('Price')">{{ $item->extra_price }}</td>
                                   <td data-label="@lang('Action')">

                                    @if($attr[0]->productAttribute->type == 2 || $attr[0]->productAttribute->type == 3)
                                        <a href="{{ route('admin.products.add-variant-images', $item->id) }}" data-toggle="tooltip" data-title="@lang('Add Variant Images')"  class="icon-btn btn--info"> <i class="la la-image"></i></a>
                                    @endif
                                       <a href="javascript:void(0)" data-toggle="tooltip" data-title="@lang('Edit')" data-item="{{ $item }}" @if($attr[0]->productAttribute->type == 3) data-image="{{ getImage(imagePath()['attribute']['path'].'/'.$item->value, imagePath()['attribute']['size']) }}" @endif class="icon-btn editBtn">
                                           <i class="la la-pencil"></i>
                                       </a>

                                       <a href="javascript:void(0)" class="icon-btn btn--danger deleteBtn ml-1" data-toggle="tooltip" data-title="@lang('Delete')"  data-link="{{route('admin.products.variant.delete', $item->id)}}"><i class="la la-trash"></i></a>
                                        </div>
                                   </td>
                               </tr>
                               @endforeach
                            </tbody>
                        </table>
                    </div>
        </div>
    </div>

    @empty
    <div class="col-lg-12 mt-3">
        <div class="alert border border--warning" role="alert">
            <div class="alert__icon bg--warning"><i class="far fa-bell"></i></div>
            <p class="alert__message">{{ __($emptyMessage) }}</p>
            
        </div>
    </div>

    @endforelse
</div>

<!-- Modal -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="" method="POST" id="editForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Modal title')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--success btn-block">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- REMOVE MODAL --}}
<div class="modal fade" id="deleteModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form  method="POST" id="deleteForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize" id="deleteModalLabel">@lang('Delete Variant')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   @lang('Are you sure to delete this Variant')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--danger">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script-lib')
    <script src="{{ asset('assets/dashboard/js/spectrum.js') }}"></script>
@endpush

@if(! request()->session()->has('label') && request()->session()->get('label') != 'new')
    @push('breadcrumb-plugins')
        <a href="{{ route('admin.products.all') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-backward"></i>@lang('Go Back')</a>
    @endpush
@endif

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/spectrum.css') }}">
@endpush

@push('script')

<script>
    'use strict';
    var itr = 0;
    (function($){

        $(document).on('click', '.deleteBtn' ,function () {
            var modal   = $('#deleteModal');
            var link    = $(this).data('link');
            var form    = $('#deleteForm');
            $(form).attr('action', link);
            modal.modal('show');
        });

        $('.image-popup').magnificPopup({
            type: 'image'
        });

        $('.select2-basic').select2({
            dropdownParent: $('.has_select2')
        });

        $(document).on('click', '.add_more' ,function(){
            var type = $('input[name="attr_type"]').val();
            itr ++;
            if(type==1){
                var content = textContent();
                $(content).appendTo('.attr-wrapper').hide().slideDown('slow');
            }else if(type==2){
               var content = colorContent();
                $(content).appendTo('.attr-wrapper').hide().slideDown('slow');
                addSpectrum();
            }else if(type==3){
                var content = imageContent();
                $(content).appendTo('.attr-wrapper').hide().slideDown('slow');
            }

        });

        $(document).on('change', '.attrId' ,function(){
            itr=0;
            var type = $(this).children('option:selected').data('type');
            $('input[name="attr_type"]').val(type);
            if(type==1){
                var content = textContent();
                $('.attr-wrapper').html('');
                $(content).appendTo('.attr-wrapper').hide().slideDown('slow');
            }else if(type==2){
               var content = colorContent();
                $('.attr-wrapper').html('');
                $(content).appendTo('.attr-wrapper').hide().slideDown('slow');
                addSpectrum();
            }else if(type==3){
                var content = imageContent();

                $('.attr-wrapper').html('');
                $(content).appendTo('.attr-wrapper').hide().slideDown('slow')
            }


            $('.add_more').removeClass('d-none');

            $('.select2-basic').select2({
                dropdownParent: $(this).parents('.card-body')
            });

        });

        $(document).on('click', '.removeBtn', function() {
            var parent = $(this).parents('.single-attr');
            parent.slideUp('slow', function(){
                this.remove();
            });

        });

        $(document).on('change', ".profilePicUpload", function () {
            proPicURL(this);
        });

        $('.editBtn').on('click', function(){
            var modal   = $('#editModal');
            var item    = $(this).data('item');
            modal.find('.modal-title').text(`@lang('Edit -  ${item.product_attribute.name}')`);
            var content = ``;
            if(item.product_attribute.type == 1){
                content = `<div class="form-group">
                                <label class="font-weight-bold">@lang('Name')</label>
                                <input type="text" placeholder="@lang('Type Here')..." class="form-control" name="name" required />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Value')</label>
                                <input type="text" placeholder="@lang('Type Here')..." class="form-control" name="value" required />
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Price')</label>
                                <input type="text" class="form-control mr-xl-2 numeric-validation" name="price" value="0" placeholder="@lang('Type Here')..." required />
                            </div>
                        `;
                modal.find('.modal-body').html(content);
                modal.find('input[name=name]').val(item.name);
                modal.find('input[name=value]').val(item.value);
                modal.find('input[name=price]').val(item.extra_price);

            }else if(item.product_attribute.type == 2){
                content = `
                            <div class="form-group"><label class="font-weight-bold">@lang('Name')</label>
                                <input type="text" class="form-control" name="name" placeholder="@lang('Type Here')..." required />
                            </div>
                            <label class="font-weight-bold">@lang('Color')</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <input type='text' class="form-control colorPicker" value=""/>
                                </span>
                                <input type="text" class="form-control colorCode" name="value" value="" required/>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Price')</label>
                                <input type="text" class="form-control mr-xl-2 numeric-validation" name="price" value ="0" placeholder="@lang('Type Here')..." required />
                            </div>
                        </div>
                `;
                modal.find('.modal-body').html(content);
                modal.find('input[name=name]').val(item.name);
                modal.find('input[name=value]').val(item.value);
                modal.find('.colorPicker').val(item.value);
                modal.find('input[name=price]').val(item.extra_price);
                addSpectrum();

            }else if(item.product_attribute.type == 3){
                content = `
                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Name')</label>
                                <input type="text" class="form-control" name="name" placeholder="@lang('Type Here')..." required />
                            </div>
                            <div class="form-group">
                                <div class="payment-method-item">
                                <label class="font-weight-bold">@lang('Value')</label>
                                    <div class="payment-method-header">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview"></div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" name="image" class="profilePicUpload" id="image-update" accept=".png, .jpg, .jpeg" >
                                                <label for="image-update" class="bg--primary"><i class="la la-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Price')</label>
                                <input type="text" class="form-control numeric-validation" name="price" value ="0" placeholder="@lang('Type Here')..." required />
                            </div>
                        `;
                modal.find('.modal-body').html(content);
                modal.find('input[name=name]').val(item.name);
                var image    = $(this).data('image');
                modal.find('.profilePicPreview').css("background-image", "url(" + image + ")");
                modal.find('input[name=price]').val(item.extra_price);
            }else{
                modal.find('.modal-body').html(content);
            }
            var form = document.getElementById('editForm');
            form.action = `{{ route('admin.products.variant.update','') }}/${item.id}`;
            modal.modal('show');
        });
    })(jQuery);

    function addSpectrum() {
        $('.colorPicker:empty').spectrum({
            color: $(this).data('color'),
            change: function (color) {
                $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
            }
        });
    }

    function textContent(){
        return `
                    <div class="row single-attr">
                        <div class="col-xl-4">
                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Name')</label>
                                <input type="text" placeholder="@lang('Type Here')..." class="form-control" name="text[${itr}][name]" required />
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="form-group">
                                <label class="font-weight-bold">@lang('Value')</label>
                                <input type="text" placeholder="@lang('Type Here')..." class="form-control" name="text[${itr}][value]" required />
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <label class="font-weight-bold">@lang('Price')</label>
                            <div class="form-group abs-form-group d-flex justify-content-between flex-wrap">
                                <input type="text" class="form-control mr-xl-2 numeric-validation" name="text[${itr}][price]" value ="0" placeholder="@lang('Type Here')..." required />
                                <button type="button" class="btn btn-outline--danger removeBtn abs-button"><i class="la la-minus"></i></button>
                            </div>
                        </div>
                    </div>

            `;
    }

    function colorContent(){
        return `
                <div class="row single-attr">
                    <div class="col-xl-4">
                        <div class="form-group"><label class="font-weight-bold">@lang('Name')</label>
                            <input type="text" class="form-control" name="color[${itr}][name]" placeholder="@lang('Type Here')..." required />
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <label class="font-weight-bold">@lang('Color')</label>
                        <div class="input-group">
                            <span class="input-group-addon ">
                                <input type='text' class="form-control colorPicker" value="e81f1f"/>
                            </span>
                            <input type="text" class="form-control colorCode" name="color[${itr}][value]" value="e81f1f" required/>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <label class="font-weight-bold">@lang('Price')</label>
                        <div class="form-group abs-form-group d-flex justify-content-between flex-wrap">
                            <input type="text" class="form-control mr-xl-2 numeric-validation" name="color[${itr}][price]" value ="0" placeholder="@lang('Type Here')..." required />
                            <button type="button" class="btn btn-outline--danger removeBtn abs-button"><i class="la la-minus"></i></button>
                        </div>
                    </div>
                </div>
            `;
    }

    function imageContent(){
        return `
            <div class="row single-attr">
                <div class="col-xl-4">
                    <div class="form-group">
                        <label class="font-weight-bold">@lang('Name')</label>
                        <input type="text" class="form-control" name="img[${itr}][name]" placeholder="@lang('Type Here')..." required />
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="form-group">
                        <label for="inputAttachments">@lang('Value')</label>
                        <div class="file-upload-wrapper" data-text="Select your file!">
                            <input type="file" name="img[${itr}][value]" class="file-upload-field" accept=".png, .jpg, .jpeg" required/>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <label class="font-weight-bold">@lang('Price')</label>
                    <div class="form-group abs-form-group d-flex justify-content-between flex-wrap">
                        <input type="text" class="form-control mr-xl-2 numeric-validation" name="img[${itr}][price]" value ="0" placeholder="@lang('Type Here')..." required />
                        <button type="button" class="btn btn-outline--danger removeBtn abs-button"><i class="la la-minus"></i></button>
                    </div>
                </div>
            </div>
            `;
    }

</script>

@endpush

@push('style')
<style>
.sp-replacer {
    padding: 0;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: 5px 0 0 5px;
    border-right: none;
}
.sp-preview {
    width: 70px;
    height: 38px;
    border: 0;
}
.sp-preview-inner {
    width: 110px;
}
.sp-dd{
    display: none;
}
.input-group > .form-control:not(:first-child) {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}

.file-upload-wrapper:before {
    background: #5E50EE;
}

.file-upload-wrapper:hover:before {
    background: #5E50EE;
    opacity: 0.9;
}
</style>
@endpush

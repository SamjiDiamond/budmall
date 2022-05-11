@extends(activeTemplate().'layouts.frontend')

@section('content')

<div class="container padding-top padding-bottom">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-md">
                    <h5 class="cl-1 pt-3 text-center">@lang('Payment Validaion')</h5>
                    <hr>
                    <div class="card-body">
                        <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="gateway" value="{{ $method->id}}"/>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="text-center">@lang('You\'re requested to pay the final amount of ') {{ getAmount($data['final_amo']) }} {{ $method->currency }} @lang('including') {{ getAmount($data['charge']*$data['rate']) }} {{ $method->currency }} @lang('for charge.')</h4>
                                    <hr>

                                    <h4 class="text-center bg--warning p-2 mb-2">@lang('Please, follow the instruction')</h4>
                                    <p class="pt-2">@php echo __($method->method->description) @endphp</p>
                                </div>

                                @if($method->gateway_parameter)

                                    @foreach(json_decode($method->gateway_parameter) as $k => $v)

                                        @if($v->type == "text")
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label><strong>{{__(inputTitle($v->field_level))}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                                    <input type="text" class="form-control form-control-lg"
                                                           name="{{$k}}"  value="{{old($k)}}" placeholder="{{__($v->field_level)}}">
                                                </div>
                                            </div>
                                        @elseif($v->type == "textarea")
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label><strong>{{__(inputTitle($v->field_level))}}
                                                            @if($v->validation == 'required')
                                                            <span class="text-danger">*</span>
                                                            @endif</strong>
                                                        </label>
                                                        <textarea name="{{$k}}"  class="form-control"  placeholder="{{__($v->field_level)}}" rows="3">{{old($k)}}</textarea>

                                                    </div>
                                                </div>
                                        @elseif($v->type == "file")
                                            <div class="col-md-12">

                                                <label class="text-uppercase">
                                                    <strong>
                                                        {{__($v->field_level)}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif
                                                    </strong>
                                                </label>
                                                <div class="verification-img">
                                                    <div class="image-upload">
                                                        <div class="image-edit">
                                                            <input type='file' name="{{$k}}" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                                            <label for="imageUpload"></label>
                                                        </div>
                                                        <div class="image-preview">
                                                            <div id="imagePreview" style="background-image: url({{ asset(imagePath()['image']['default']) }});">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    @endforeach

                                @endif
                                <div class="col-md-12 my-2">
                                    <button type="submit" class="btn btn--base btn-block mt-2 text-center">@lang('Confirm')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    "use strict";
    (function($){
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url('+e.target.result +')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imageUpload").on('change', function() {
            readURL(this);
        });
    })(jQuery)
</script>
@endpush

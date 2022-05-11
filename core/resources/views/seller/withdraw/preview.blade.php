@extends('seller.layouts.app')

@section('panel')
    <div class="container">
        <div class="row justify-content-center mt-2">
            <div class="col-lg-12">
                <div class="card card-deposit">
                    <div class="card-header">
                        <h5 class="text-center my-1">@lang('Current Balance') :
                        <strong>{{ showAmount(seller()->balance)}}  {{ __($general->cur_text) }}</strong></h5>
                    </div>

                    <div class="card-body mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="font-weight-bold">@lang('Withdrawal Details')</label>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex flex-wrap justify-content-between">
                                        <span class="font-weight-bold">@lang('Current Balance')</span>
                                        <span>{{ showAmount(seller()->balance)}}  {{ __($general->cur_text) }}</span>
                                    </li>

                                    <li class="list-group-item d-flex flex-wrap justify-content-between">
                                        <span class="font-weight-bold">@lang('Amount')</span>
                                        <span>{{showAmount($withdraw->amount)  }} {{__($general->cur_text)}}</span>
                                    </li>

                                    <li class="list-group-item d-flex flex-wrap justify-content-between">
                                        <span class="font-weight-bold">@lang('Charge')</span>
                                        <span class="text--danger">{{showAmount($withdraw->charge) }} {{__($general->cur_text)}}</span>
                                    </li>
                                    <li class="list-group-item d-flex flex-wrap justify-content-between">
                                        <span class="font-weight-bold">@lang('Including Charge')</span>
                                        <span>{{showAmount($withdraw->after_charge) }} {{__($general->cur_text)}}</span>
                                    </li>

                                    @if($general->cur_text == $withdraw->currency)
                                    <li class="list-group-item d-flex flex-wrap justify-content-between">
                                        <span class="font-weight-bold">@lang('Conversion Rate')</span>
                                        <span>  1 {{__($general->cur_text)}} = {{showAmount($withdraw->rate)  }} {{__($withdraw->currency)}}</span>
                                    </li>
                                    @endif

                                    <li class="list-group-item d-flex flex-wrap justify-content-between">
                                        <span class="font-weight-bold">@lang('You Will Get')</span>
                                        <span>{{showAmount($withdraw->final_amount) }} {{__($withdraw->currency)}}</span>
                                    </li>

                                   
                                </ul>
                            </div>
                            <div class="col-md-8">
                                <form action="{{route('seller.withdraw.submit')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @if($withdraw->method->user_data)
                                    @foreach($withdraw->method->user_data as $k => $v)
                                        @if($v->type == "text")
                                            <div class="form-group">
                                                <label><strong>{{__($v->field_level)}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                                <input type="text" name="{{$k}}" class="form-control" value="{{old($k)}}" placeholder="{{__($v->field_level)}}" @if($v->validation == "required") required @endif>
                                                @if ($errors->has($k))
                                                    <span class="text-danger">{{ __($errors->first($k)) }}</span>
                                                @endif
                                            </div>
                                        @elseif($v->type == "textarea")
                                            <div class="form-group">
                                                <label><strong>{{__($v->field_level)}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                                <textarea name="{{$k}}"  class="form-control"  placeholder="{{__($v->field_level)}}" rows="3" @if($v->validation == "required") required @endif>{{old($k)}}</textarea>
                                                @if ($errors->has($k))
                                                    <span class="text-danger">{{ __($errors->first($k)) }}</span>
                                                @endif
                                            </div>
                                        @elseif($v->type == "file")

                                            <div class="form-group">
                                                <label><strong>{{__($v->field_level)}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                                <div class="thumb">
                                                    <div class="avatar-preview">
                                                        <div class="profilePicPreview" style="background-image: url({{getImage('/')}})"></div>
                                                    </div>
                                                    <div class="avatar-edit">
                                                        <input type="file" name="{{$k}}" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg" @if($v->validation == "required") required @endif/>
                                                        <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    @endif
                                    @if(seller()->ts)
                                    <div class="form-group">
                                        <label>@lang('Google Authenticator Code')</label>
                                        <input type="text" name="authenticator_code" class="form-control" required>
                                    </div>
                                    @endif
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--primary btn-block btn-lg mt-4 text-center">@lang('Confirm')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .thumb .profilePicPreview {
            width: 100%;
            height: 210px;
            display: block;
            border: 3px solid #f1f1f1;
            box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.25);
            border-radius: 10px;
            background-size: cover;
            background-position: center
        }


        .thumb .profilePicUpload {
            font-size: 0;
            opacity: 0;
            width: 0;
        }

        .thumb .avatar-edit label {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            text-align: center;
            line-height: 45px;
            border: 2px solid #fff;
            font-size: 18px;
            cursor: pointer;
        }

        .thumb {
            width: 100%;
            position: relative;
            margin-bottom: 30px;
        }

        .thumb .avatar-edit {
            position: absolute;
            bottom: -15px;
            right: 0;
        }

    </style>
@endpush

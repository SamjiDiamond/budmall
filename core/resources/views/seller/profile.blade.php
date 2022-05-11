@extends('seller.layouts.app')

@section('panel')

    <div class="card">
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <div class="col-md-3">
                        <label class=" font-weight-bold">@lang('Image')</label>
                    </div>
                    <div class="col-md-9">
                        <div class="payment-method-item">
                            <div class="payment-method-header">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview" style="background-image: url({{ getImage(imagePath()['seller']['profile']['path'].'/'.$seller->image,imagePath()['seller']['profile']['size']) }})">

                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>
                                        <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <small>@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png').</b> @lang('Image will be resized into 400x400px') </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class="font-weight-bold">@lang('First Name')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control" type="text" name="firstname" value="{{ old('firstname')??$seller->firstname }}" >
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class=" font-weight-bold">@lang('Last Name')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control" type="text" name="lastname" value="{{ old('lastname')??$seller->lastname }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class=" font-weight-bold">@lang('Email')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control bg--white" type="email" value="{{ $seller->email }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class=" font-weight-bold">@lang('Mobile')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control bg--white" type="number" value="{{ old('mobile')??$seller->mobile }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class=" font-weight-bold">@lang('Country')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control bg--white" type="text" value="{{ @$seller->address->country }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class=" font-weight-bold">@lang('State')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control" type="text" name="state" value="{{ old('state')??$seller->address->state }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class=" font-weight-bold">@lang('City')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control" type="text" name="city" value="{{ old('city')??$seller->address->city }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <label class=" font-weight-bold">@lang('Zip Code')</label>
                    </div>
                    <div class="col-md-9">
                        <input class="form-control" type="number" name="zip" value="{{ old('zip')??$seller->address->zip }}">
                    </div>
                </div>


                <div class="form-group row">
                    <div class="col-md-3">
                        <label class=" font-weight-bold">@lang('Address')</label>
                    </div>

                    <div class="col-md-9">
                        <textarea class="form-control" name="address">{{ old('address')??$seller->address->address }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Save Changes')</button>
                </div>


            </form>
        </div>
    </div>

@endsection




@extends($activeTemplate.'layouts.frontend')


@section('content')
<div class="dashboard-section padding-bottom padding-top">
    <div class="container">
        <div class="row">
            @foreach($gatewayCurrency as $data)
                <div class="col-lg-2 col-md-3 mb-4">
                    <div class="card">
                        <h5 class="card-header p-1">
                            <img src="{{$data->methodImage()}}" class="card-img-top" alt="{{__($data->name)}}">
                        </h5>
                        <div class="card-body p-1">
                            <form action="{{route('user.deposit.insert')}} " method="POST">
                                @csrf
                                <input type="hidden" name="currency" class="edit-currency" value="{{$data->currency}}">
                                <input type="hidden" name="method_code" class="edit-method-code" value="{{$data->method_code}}">
                                <button type="submit" class="cmn-btn btn-block">@lang('Pay Now')</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

@endsection


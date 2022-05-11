@extends($activeTemplate.'layouts.frontend')


@section('content')

<div class="dashboard-section padding-bottom padding-top">
    <div class="container">
        <div class="row">
            <div class="col-xl-3">
                <div class="dashboard-menu">
                    @include($activeTemplate.'user.partials.dp')
                    <ul>
                        @include($activeTemplate.'user.partials.sidebar')
                    </ul>
                </div>
            </div>
             <div class="col-xl-9"> 

             <div class="card border-0 shadow-md">
                    <div class="card-header bg-transparent d-flex justify-content-between">
                        <!--<img src=" " class="card-img-top w-25" @lang('gateway-image')">-->
                        <h3 class="align-self-center cl-1">
                         Payment Details
                        </h3>
                    </div>
                    <div class="card-body">

                        <ul class="list-group list-group-flush text-center ">
                           
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Customer Name'):
                                <span><strong>{{$customer}}</strong> </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Customer Address'):
                                <span><strong>{{$address}}</strong> </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Meter Number'):
                                <span><strong>{{$meter}}</strong> </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">@lang('Status'): <strong>{{$status}}</strong></li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Cost'): <strong>{{$general->cur_sym}}{{showAmount($amount)}} </strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Disco'): <strong>{{$disco}}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Token'): <strong>{{$token}}</strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Unit'): <strong>{{$unit}}</strong>
                            </li>


                            
                        </ul>

                    </div>
                </div>

 
    </div>

</div>
</div>
</div>
</div>
 
@endsection
@push('script')

@endpush


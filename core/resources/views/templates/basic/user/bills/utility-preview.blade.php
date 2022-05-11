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
                        {{$meter}} Bill Payment
                        </h3>
                    </div>
                    <div class="card-body">

                        <ul class="list-group list-group-flush text-center ">
                           
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Meter Name'):
                                <span><strong>{{$customer}}</strong> </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Meter Number'):
                                <span><strong>{{$number}}</strong> </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">@lang('Type'): <strong>{{$type}}</strong></li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Cost'): <strong>{{$general->cur_sym}}{{showAmount($cost)}} </strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Service Charge'): <strong>{{$general->cur_sym}}{{env('POWERCHARGE')}}</strong>
                            </li>


                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Total NGN'): <strong>{{$general->cur_sym}}{{showAmount($cost + env('POWERCHARGE'),2)}} </strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                            @php
                            $total = $cost + env('POWERCHARGE');
                            @endphp
                                @lang('Total USD'): <strong>${{number_format($total/$general->usdrate,2)}}</strong>
                            </li>
                            

                                <li class="list-group-item p-0">
                                <form class="form" id="kt_form"  method="post" enctype="multipart/form-data">
                                                                 @csrf
                                                            <input name="customer" hidden value="{{$customer}}">
                                                            <input name="number" hidden value="{{$number}}">
                                                            <input name="plan" hidden value="{{$plancode}}">
                                                            <input name="type" hidden value="{{$type}}">
                                                            <input name="amount" hidden value="{{$cost}}">
															<button type="submit" class="cmn-btn btn-block">Make Payment</button>
								</form>
                                   
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


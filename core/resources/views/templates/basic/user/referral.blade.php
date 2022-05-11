@extends($activeTemplate.'layouts.frontend')
@section('content')
    <div class="user-profile-section padding-top padding-bottom">
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
                   <h4> <b>Referral System</b></h4>
                   <br>
                     <span>Share your Referral Code to with your friends and family and enjoy <b>{{$general->ref}}%</b> of any amount they purchase on {{$general->sitename}}<br>
                    <b>Guide:</b> They are to enter <b>{{$user->ref_code}}</b> in the referral code field while signing up on {{$general->sitename}} 
                    </span>
                    <hr>
                    <small>Referral URL</small><br>
						    <div class="input-group">
                            <input readonly value="{{url('/')}}/user/register?ref={{$user->ref_code}}"  id="referralURL2" class="form-control " type="text">
                            
                            <badge class="badge badge-info btn-sm text-white" onclick="myFunction2()" type="button"><li class="fa fa-copy"></li></badge>
                            </div>
                    <br>
                   
                   
                    
                     
                           <small>Referral Code</small><br>
						    <div class="input-group">
                            <input readonly value="{{$user->ref_code}}"  id="referralURL" class="form-control " type="text">
                            
                            <badge class="badge badge-info btn-sm text-white" onclick="myFunction()" type="button"><li class="fa fa-copy"></li></badge>
                            </div>
                            
                           
                            <br>
                            <p class="product-share">
                             <b>@lang('Share Code'):</b><br>
                            
                            <a class="bg-info" href="https://www.facebook.com/sharer/sharer.php?u={{url('/')}}" title="@lang('Facebook')" target="blank">

                                <i class="fab fa-facebook"></i>
                            </a>
  

                            <a class="bg-info" href="https://twitter.com/intent/tweet?text=Use my Referral code {{$user->ref_code}} to signup on {{url('/')}}" title="@lang('Twitter')" target="blank">

                                <i class="fab fa-twitter"></i>
                            </a>
                            <a class="bg-success"  href="whatsapp://send?text=Use my Referral code {{$user->ref_code}} to signup on {{url('/')}}". data-action="share/whatsapp/share" title="@lang('Whatsapp')" target="blank">

                                <i class="fab fa-whatsapp"></i>
                            </a>
                            </p>
                            <br>
                     
                    
                    
                        <div class="row">
                    <div class="col-sm-6 col-lg-6">
                        <div class="dashboard-item">
                            <a href="#" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-wallet"></i>
                                </span>
                                <div class="cont">

                                    <div class="dashboard-header">
                                        <h3 class="title">$ {{number_format($wallet->balance,2)}}</h3>
                                    </div>
                                    @lang('Referral Wallet')
                                </div>
                                @if($wallet->balance > 0)
                                <a class="btn btn-primary btn-sm text-white" href="{{route('user.profile.referralwithdraw')}}">Withdraw Balance</a><br>
                                                                <small class="text-primary">Please click the button below to withdraw your refferal earning to your deposit wallet.</small>

                                
                                @endif
                            </a>
                        </div>
                    </div>
                     <div class="col-sm-6 col-lg-6">
                        <div class="dashboard-item">
                            <a href="#" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-users"></i>
                                </span>
                                <div class="cont">

                                    <div class="dashboard-header">
                                        <h3 class="title">{{count($ref)}}</h3>
                                    </div>
                                    @lang('Total Referral')
                                </div>
                            </a>
                        </div>
                    </div>

                   
                    </div>

                    
                    
                                   
                    
            <div class="checkout-area section-bg">
             <ul class="nav nav-tabs">
            <li>
                <a href="#referred" class="active"  data-toggle="tab">@lang('Referred Users')</a>
            </li>

            <li>
                <a href="#earning" data-toggle="tab">@lang('Referral Earnings')</a>
            </li>
            </ul>
             <div class="tab-content">
            <div class="tab-pane fade  show active" id="referred">
       
                        <h5>You have a total of {{count($ref)}} referred user(s).</h5>
                         <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Joined At')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($ref as $ref)
                                <tr>
                                    <td data-label="@lang('Customer')">
                                        <span class="font-weight-bold d-block">{{$ref->fullname}}</span>
                                        <a>{{ $ref->username }}</a>
                                    </td>

                                     
                                    <td data-label="@lang('Country')">
                                        <span class="font-weight-bold d-block">{{ $ref->country_code }}</span>
                                        {{ @$ref->address->country }}
                                    </td>

                                    <td data-label="@lang('Joined At')">
                                        <span class="font-weight-bold d-block">{{ showDateTime($ref->created_at) }}</span>
                                        {{ diffForHumans($ref->created_at) }}
                                    </td>

                                    
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">You don't have any referred customer at the moment.</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
             </div>
             <div class="tab-pane fade" id="earning">
                    <hr>
                     <h5>You have earned a total of ${{number_format($sum,2)}}.</h5>
                         <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('TRX ID')</th>
                                     <th>@lang('Amount')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($earning as $data)
                                <tr>
                                    <td data-label="@lang('Customer')">
                                        <span class="font-weight-bold d-block">{{$data->trx}}</span>
                                         
                                    </td> 

                                     
                                    <td data-label="@lang('Amount')">
                                        <span class="font-weight-bold d-block">${{ number_format($data->usd_amount,2) }}</span>
                                        
                                    </td>

                                    <td data-label="@lang('Joined At')">
                                        <span class="font-weight-bold d-block">{{ showDateTime($data->created_at) }}</span>
                                        {{ diffForHumans($data->created_at) }}
                                    </td>

                                    
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">You don't have any referred earning at the moment.</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                </div>
                         
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
   
    <script>
        function myFunction2() {
            var copyText = document.getElementById("referralURL2");
            copyText.select();
            copyText.setSelectionRange(0, 99999); /*For mobile devices*/
            document.execCommand("copy");
            var alertStatus = "{{$general->alert}}";
            toastr.success("Referral Link Copied Successfully","👋 Copied!!!");
        }
    </script>
   
    <script>
        function myFunction() {
            var copyText = document.getElementById("referralURL");
            copyText.select();
            copyText.setSelectionRange(0, 99999); /*For mobile devices*/
            document.execCommand("copy");
            var alertStatus = "{{$general->alert}}";
            toastr.success("Referral Code Copied Successfully","👋 Copied!!!");
        }
    </script>
@endpush

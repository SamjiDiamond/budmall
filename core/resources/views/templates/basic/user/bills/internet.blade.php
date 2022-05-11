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
             <div class="col-12 card">
            <br>
             <b><h4>{{$pageTitle}}</h4></b>

             <form class="contact-form" class="currency_validate" action="" method="post" enctype="multipart/form-data">
                                        @csrf

 
                                                <div class="form-group col-12"> 
                                                <label>Select Preferred Network</label>
                                                    <div class="input-group">
                                                   
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text mobile-code">    </span>
                                                            
                                                        </div>
                                                        <select name="network" id="s1" onChange="populate()"  class="form-control" data-placeholder="Network">
													<option label="Choose one">Select Network
													</option>
													@foreach($network as $data)
													<option value="{{$data->symbol}}">{{$data->name}}</option>
													@endforeach
												</select>
                                                    </div>
                                                </div>

                                                <div class="form-group col-12"> 
                                                <label>Select Internet Plan</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text mobile-code">    </span>
                                                        </div>
                                                        <select id="s2" name="plan" class="form-control">
                                                        <option selected disabled>Select Plan</option>
                                                        <option value=""></option>
                                                        </select>
                                                    </div>
                                                </div>


 



                                                <div class="form-group col-12"> 
                                                <label>Enter Phone Number</label>
                                                    <div class="input-group">
                                                    
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text mobile-code">+234</span>
                                                            
                                                        </div>
                                                        <input type="number" name="phone" id="phone" value="{{ old('phone') }}" class="form-control checkUser" placeholder="@lang('Your Phone Number')">
                                                        <small class="text--danger mobileExist d-block"></small>
                                                    </div>
                                                </div>

                                                
                                                <div class="form-group col-12">
                                                     

                                                    <button type="submit" class="cmn-btn">Proceed</button>


                                                </div>


                                        </div>
                                        </form>
            <br>
        <b><h4>Transaction Logs</h4></b>

        <table class="payment-table section-bg">
                        <thead class="bg--base">
                                                        <th>ID</th>
														<th>Phone</th>
														<th>Network</th>
														<th>Network</th>
														<th>Amount (NGN)</th>
														<th>Amount (USD)</th>
														<th>Status</th>
														<th>Date</th>
                        </thead>
                        <tbody>
                        @if(count($bills) >0)
                            @foreach($bills as $k=>$data)
                                <tr>
                                    <td data-label="#@lang('Trx')">{{$data->trx}}</td>
                                    <td data-label="@lang('Phone')">{{$data->phone}}</td>
                                    <td data-label="@lang('Network')">
                                        <strong>{{strtoupper($data->network)}}</strong>
                                    </td> 
                                    <td data-label="@lang('Network')">
                                        <strong>{{strtoupper($data->accountname)}}</strong>
                                    </td> 
                                    <td data-label="@lang('Amount')">
                                        <strong>{{$general->cur_sym}}{{number_format($data->amount,2)}}</strong>
                                    </td>
                                    <td data-label="@lang('Amount')">
                                        <strong>${{number_format($data->usd,2)}}</strong>
                                    </td>
                                    <td>
                                        @if($data->status == 1)
                                            <span class="badge badge--success badge-capsule">@lang('Complete')</span>
                                        @elseif($data->status == 2)
                                            <span class="badge badge--warning badge-capsule">@lang('Pending')</span>
                                        @elseif($data->status == 3)
                                            <span class="badge badge--danger badge-capsule">@lang('Cancel')</span>
                                        @endif

                                        @if($data->admin_feedback != null)
                                            <button class="btn-info btn-rounded  badge detailBtn" data-admin_feedback="{{$data->admin_feedback}}"><i class="fa fa-info"></i></button>
                                        @endif

                                    </td>
                                    <td data-label="@lang('Time')">
                                        {{showDateTime($data->created_at, 'd M, Y h:i A')}}
                                    </td>

                                    
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="100%">There is no transaction log at the moment</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    {{$bills->links()}}
    </div>

</div>
</div>
</div>
</div>
 
@endsection
@push('script')
<script>
function populate(){
console.log('start populate')
var s1 = document.getElementById('s1');
var s2 = document.getElementById('s2');
s2.innerHTML = "";
console.log('start deciding');
if (s1.value == "mtn"){
  var optionArray = ["|Select",@foreach($bill as $data)@if($data['networkcode'] == "mtn" )"{{$data['plan']}}| {{$data['name']}} ",@endif @endforeach];             }
else if (s1.value == "glo"){
var optionArray = ["|Select",@foreach($bill as $data)@if($data['networkcode'] == "glo" )"{{$data['plan']}}| {{$data['name']}}",@endif @endforeach]; }
else if (s1.value == "etisalat"){
  var optionArray = ["|Select",@foreach($bill as $data)@if($data['networkcode'] == "9mobile" )"{{$data['plan']}}| {{$data['name']}} ",@endif @endforeach];  }
else if (s1.value == "smile"){
  var optionArray = ["|Select",@foreach($bill as $data)@if($data['networkcode'] == "smile" )"{{$data['plan']}}| {{$data['name']}} ",@endif @endforeach];  }
else if (s1.value == "airtel"){
var optionArray = ["|Select",@foreach($bill as $data)@if($data['networkcode'] == "airtel" )"{{$data['plan']}}| {{$data['name']}}",@endif @endforeach];  }
console.log('i want to split');
      for (var option in optionArray){
        var pair = optionArray[option].split("|");
        var newOption = document.createElement("option");
        newOption.value = pair[0];
        newOption.innerHTML = pair[1];
        s2.options.add(newOption);
      }
  }
</script>
@endpush


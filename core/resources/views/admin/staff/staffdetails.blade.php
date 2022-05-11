@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-5 col-md-5 mb-30">

            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body p-0">
                    <div class="p-3 bg--white">
                        <div class="">
                            <img src="{{ getImage(imagePath()['profile']['admin']['path'].'/'. $user->image,imagePath()['profile']['admin']['size'])}}" alt="@lang('Profile Image')" class="b-radius--10 w-100">
                        </div>
                        <div class="mt-15">
                            <h4 class="">{{$user->fullname}}</h4>
                            <span class="text--small">@lang('Joined At') <strong>{{showDateTime($user->created_at,'d M, Y h:i A')}}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card b-radius--10 overflow-hidden mt-30 box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Staff information')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span class="font-weight-bold">{{$user->username}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if($user->status == 1)
                                <span class="badge badge-pill bg--success">@lang('Active')</span>
                            @elseif($user->status == 0)
                                <span class="badge badge-pill bg--danger">@lang('Banned')</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card b-radius--10 overflow-hidden mt-30 box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Staff action')</h5>

                    <a href="{{route('admin.staff.email.single',$user->id)}}"
                       class="btn btn--info btn--shadow btn-block btn-lg">
                        @lang('Send Email')
                    </a>

                    <a href="{{route('admin.staff.email.log',$user->id)}}" class="btn btn--info btn--shadow btn-block btn-lg">
                        @lang('Email Log')
                    </a>
                    @if($user->type == 2)
                    <a href="#" data-toggle="modal" data-target="#addModal"  class="btn btn--success btn--shadow btn-block btn-lg">
                        @lang('Credit Wallet')
                    </a>
                    <a href="{{route('admin.staff.creditlog',$user->id)}}" class="btn btn--success btn--shadow btn-block btn-lg">
                        @lang('Credit Log')
                    </a>

                    <a href="#" data-toggle="modal" data-target="#subModal" class="btn btn--warning btn--shadow btn-block btn-lg">
                        @lang('Debit Wallet')
                    </a>
                    <a href="{{route('admin.staff.debitlog',$user->id)}}" class="btn btn--warning btn--shadow btn-block btn-lg">
                        @lang('Debit Log')
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-7 col-md-7 mb-30">

        @if($user->type == 2)
            <div class="row mb-none-30">
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                        <div class="dashboard-w1 bg--deep-purple b-radius--10 box-shadow has--link">
                            <a href="{{route('admin.users.deposits',$user->id)}}" class="item--link"></a>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numbers">
                                    <span class="currency-sign"> {{__($general->cur_sym)}}</span>
                                    <span class="amount">{{getAmount($wallet->balance,2)}}</span>
                                </div>
                                <div class="desciption">
                                    <span>@lang('Wallet Balance')</span>
                                </div>
                            </div>
                        </div>
                    </div><!-- dashboard-w1 end -->


                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                        <div class="dashboard-w1 bg--indigo b-radius--10 box-shadow has--link">
                            <a href="#" class="item--link"></a>
                            <div class="icon">
                                <i class="la la-exchange-alt"></i>
                            </div>
                            <div class="details">
                                <div class="numbers">
                                    <span class="currency-sign"> {{__($general->cur_sym)}}</span>
                                    <span class="amount">{{getAmount($totalcredit,2)}}</span>
                                </div>
                                <div class="desciption">
                                    <span>@lang('Total Wallet Credit')</span>
                                </div>
                            </div>
                        </div>
                    </div><!-- dashboard-w1 end -->

                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                        <div class="dashboard-w1 bg--12 b-radius--10 box-shadow has--link">
                            <a href="{{route('admin.report.order.user',$user->id)}}" class="item--link"></a>
                            <div class="icon">
                                <i class="las la-cart-plus"></i>
                            </div>
                            <div class="details">
                                <div class="numbers">
                                    <span class="amount">{{ @$totalOrders }}</span>
                                </div>
                                <div class="desciption">
                                    <span>@lang('Total Orders')</span>
                                </div>
                            </div>
                        </div>
                    </div><!-- dashboard-w1 end -->
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                        <div class="dashboard-w1 bg--12 b-radius--10 box-shadow has--link">
                            <a href="{{route('admin.report.order.user',$user->id)}}" class="item--link"></a>
                            <div class="icon">
                                <i class="las la-cart-plus"></i>
                            </div>
                            <div class="details">
                                <div class="numbers">
                                    <span class="amount">{{ @$dispatchedOrders }}</span>
                                </div>
                                <div class="desciption">
                                    <span>@lang('Orders Delivered')</span>
                                </div>
                            </div>
                        </div>
                    </div><!-- dashboard-w1 end -->
            </div>
            @endif


            <div class="card mt-50">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Update') {{$user->name}}</h5>

                     <form action="{{ route('admin.staff.editstaff',$user->id) }}" method="POST" >
            @csrf
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                <div class="form-group">
                   <label>Staff Fullname</label>
                   <input type="text"  value="{{$user->name}}" name="name" class="form-control">
                </div>

                <div class="form-group">
                   <label>Staff Username</label>
                   <input type="text"   value="{{$user->username}}"  name="username" class="form-control">
                </div>
                <div class="form-group">
                   <label>Staff Email</label>
                   <input  type="email" name="email"  value="{{$user->email}}"  class="form-control">
                </div>
                <div class="form-group">
                   <label>Staff Password</label><br>
                   <small class="text-danger">Leave empty if you are not updating staff password</small>
                   <input  type="text" name="password" placeholder="Optional" class="form-control">
                </div>


                <div class="form-group">
                @php
                $sstate = App\Models\State::get();
                @endphp
                   <label>Staff State</label>
                   <select name="state" class="form-control select2 select-2">
                   <option selected disabled>Select Staff State</option>
                   @foreach($sstate as $data)
                   <option @if($user->state == $data->id) selected @endif value="{{$data->id}}">{{$data->state}}</option>
                   @endforeach
                   </select>
                </div>

                 <div class="form-group">
                    <label>Staff Status</label>
                   <select name="status" class="form-control select2 select-2">
                   <option @if($user->status == 1) selected @endif value="1">Active</option>
                   <option @if($user->status == 0) selected @endif value="0">Deactivate</option>
                   </select>
                 </div>



                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--success btn--shadow  btn-lg">@lang('Update')</button>
                </div>
            </form>
                </div>
            </div>
        </div>
    </div>

@if($user->type == 2)
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form action="{{ route('admin.staff.credit',$user->id) }}" method="POST" >
            @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize" id="deleteModalLabel">@lang('Credit Staff')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                <div class="form-group">
                   <label>Enter Amount To Credit</label>
                   <input type="number"  placeholder="0.00"   value="{{old('amount')}}" name="amount" class="form-control">
                </div>
                <div class="form-group">
                   <label>Credit Narration</label>
                   <textarea type="text"  placeholder="Please enter narration"   value="{{old('narration')}}" name="narration" class="form-control"></textarea>
                </div>
         </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--warning btn--shadow  btn-lg" data-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--success btn--shadow  btn-lg">@lang('Credit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="subModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form action="{{ route('admin.staff.debit',$user->id) }}" method="POST" >
            @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize" id="deleteModalLabel">@lang('Debit Staff')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                <div class="form-group">
                   <label>Enter Amount  To Debit</label>
                   <input type="number"  placeholder="0.00" value="{{old('amount')}}" name="amount" class="form-control">
                </div>

                <div class="form-group">
                   <label>Debit Narration</label>
                   <textarea type="text"  placeholder="Please enter narration"   value="{{old('narration')}}" name="narration" class="form-control"></textarea>
                </div>
         </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--warning btn--shadow btn-lg" data-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--danger btn--shadow  btn-lg">@lang('Debit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

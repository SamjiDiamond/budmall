@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">

                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('State')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td data-label="@lang('Customer')">
                                        <span class="font-weight-bold d-block">{{$user->name}}</span>

                                    </td>

                                    <td data-label="@lang('Email') | @lang('Mobile')">
                                        <span class="font-weight-bold d-block">
                                            {{ $user->email }}
                                        </span>

                                    </td>

                                    <td data-label="@lang('Country')">
                                        <span class="font-weight-bold d-block">{{App\Models\State::whereId($user->state)->first()->state ?? "N/A" }}</span>

                                    </td>
                                    <td data-label="@lang('Country')">
                                        <span class="font-weight-bold d-block">
                                        @if($user->status != 1)
                                        <badge class="badge bg-danger">Inactive</badge>
                                        @else
                                        <badge class="badge bg-success">Active</badge>
                                        @endif
                                        </span>

                                    </td>

                                    <td data-label="@lang('Joined At')">
                                        <span class="font-weight-bold d-block">{{ showDateTime($user->created_at) }}</span>
                                        {{ diffForHumans($user->created_at) }}
                                    </td>

                                    <td data-label="@lang('Action')" >
                                         <div class="dropdown d-inline-flex" data-toggle="tooltip" title="@lang('Delete')">
                                            <a href="{{ route('admin.staff.deletestaff', $user->id) }}"  class="btn icon-btn btn-danger" >
                                                <span class="icon text-white"><i class="las la-trash mr-0"></i></span>
                                            </a>

                                        </div>
                                        <div class="dropdown d-inline-flex" data-toggle="tooltip" title="@lang('Edit')">
                                            <a href="{{ route('admin.staff.editstaff', $user->id) }}"  class="btn icon-btn btn-info" >
                                                <span class="icon text-white"><i class="las la-edit mr-0"></i></span>
                                            </a>

                                        </div>
                                    </td>
                                </tr>



                                <div class="modal fade" id="editModal{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form action="{{ route('admin.staff.editstaff',$user->id) }}" method="POST" >
            @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize" id="deleteModalLabel">@lang('Update Staff')</h5>
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
                    <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-success btn-sm">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if($users->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($users) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form action="{{ route('admin.users.addstaff') }}" method="POST" >
            @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize" id="deleteModalLabel">@lang('Create Staff')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                <div class="form-group">
                   <label>Staff Fullname</label>
                   <input type="text"  value="{{old('name')}}" name="name" class="form-control">
                </div>
                <input value="{{$type}}" name="type" hidden>

                <div class="form-group">
                   <label>Staff Username</label>
                   <input type="text"   value="{{old('username')}}"  name="username" class="form-control">
                </div>
                <div class="form-group">
                   <label>Staff Email</label>
                   <input  type="email" name="email"   value="{{old('email')}}"  class="form-control">
                </div>
                <div class="form-group">
                   <label>Staff Password</label>
                   <input  type="text" name="password" value="staff1234" readonly class="form-control">
                </div>


                <div class="form-group">
                @php
                $sstate = App\Models\State::get();
                @endphp
                   <label>Staff State</label>
                   <select name="state" class="form-control select2 select-2">
                   <option selected disabled>Select Staff State</option>
                   @foreach($sstate as $data)
                   <option value="{{$data->id}}">{{$data->state}}</option>
                   @endforeach
                   </select>
                </div>



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-success btn-sm">@lang('Create')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('breadcrumb-plugins')
    <a href="#"  data-toggle="modal" data-target="#addModal" class="btn btn-sm btn--primary box--shadow1 text-white text--small">
        <i class="la la-plus"></i> @lang('Add New')
    </a>
@endpush
@endsection

@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="row justify-content-end">
                        <div class="col-xl-3 mb-3">
                            <form action="{{ route('admin.users.search', $scope ?? str_replace('admin.users.', '', request()->route()->getName())) }}" method="GET" class="pt-3 px-3">
                                <div class="input-group has_append">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('Username or email')" value="{{ $search ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Email') | @lang('Mobile')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td data-label="@lang('Customer')">
                                        <span class="font-weight-bold d-block">{{$user->fullname}}</span>
                                        <a href="{{ route('admin.users.detail', $user->id) }}">{{ $user->username }}</a>
                                    </td>

                                    <td data-label="@lang('Email') | @lang('Mobile')">
                                        <span class="font-weight-bold d-block">
                                            {{ $user->email }}
                                        </span>
                                        +{{ $user->mobile }}
                                    </td>

                                    <td data-label="@lang('Country')">
                                        <span class="font-weight-bold d-block">{{ $user->country_code }}</span>
                                        {{ @$user->address->country }}
                                    </td>

                                    <td data-label="@lang('Joined At')">
                                        <span class="font-weight-bold d-block">{{ showDateTime($user->created_at) }}</span>
                                        {{ diffForHumans($user->created_at) }}
                                    </td>

                                    <td data-label="@lang('Action')">
                                        <a href="{{ route('admin.users.detail', $user->id) }}" class="icon-btn" data-toggle="tooltip" title="@lang('Details')">
                                            <i class="las la-desktop text--shadow"></i>
                                        </a>

                                        <div class="dropdown d-inline-flex" data-toggle="tooltip" title="@lang('More')">
                                            <button class="btn icon-btn btn--dark dropdown-toggle" type="button" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                                <span class="icon text-white"><i class="las la-chevron-circle-down mr-0"></i></span>
                                            </button>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a href="{{route('admin.users.login',$user->id)}}" target="_blank" class="dropdown-item">
                                                    @lang('Login as User')
                                                </a>

                                                <a href="{{ route('admin.users.login.history.single', $user->id) }}"
                                                    class="dropdown-item">
                                                     @lang('Login Logs')
                                                 </a>
                                                 <a href="{{route('admin.users.email.single',$user->id)}}"
                                                    class="dropdown-item">
                                                     @lang('Send Email')
                                                 </a>

                                                 <a href="{{route('admin.users.email.log',$user->id)}}" class="dropdown-item">
                                                     @lang('Email Log')
                                                 </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
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
@endsection

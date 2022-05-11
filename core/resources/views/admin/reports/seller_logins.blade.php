@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">

                    <div class="row justify-content-end">
                        <div class="col-xl-3 mb-3">
                            @if(request()->routeIs('admin.report.seller.login.history'))
                            <form action="{{ route('admin.report.seller.login.history') }}" method="GET" class="pt-3 px-3">
                                <div class="input-group has_append">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('Search Username')" value="{{ $search ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>


                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Seller')</th>
                                <th>@lang('Login at')</th>
                                <th>@lang('IP')</th>
                                <th>@lang('Location')</th>
                                <th>@lang('Browser | OS')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($loginLogs as $log)
                                <tr>

                                <td data-label="@lang('User')">
                                    <span class="font-weight-bold">{{ @$log->seller->fullname }}</span>
                                    <br>
                                    <span class="small"> <a href="{{ route('admin.sellers.detail', $log->seller_id) }}"><span>@</span>{{ @$log->seller->username }}</a> </span>
                                </td>


                                    <td data-label="@lang('Login at')">
                                        {{showDateTime($log->created_at) }} <br> {{diffForHumans($log->created_at) }}
                                    </td>



                                    <td data-label="@lang('IP')">
                                        <span class="font-weight-bold">
                                        <a href="{{route('admin.report.seller.login.ipHistory',[$log->seller_ip])}}">{{ $log->seller_ip }}</a>
                                        </span>
                                    </td>

                                    <td data-label="@lang('Location')">{{ __($log->city) }} <br> {{ __($log->country) }}</td>
                                    <td data-label="@lang('Browser | OS')">
                                        {{ __($log->browser) }} <br> {{ __($log->os) }}
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
                @if($loginLogs->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($loginLogs) }}
                </div>
                @endif
            </div><!-- card end -->
        </div>


    </div>
@endsection

@if(request()->routeIs('admin.report.sellerlogin.ipHistory'))
    @push('breadcrumb-plugins')
    <a href="https://www.ip2location.com/{{ $ip }}" target="_blank" class="btn btn--primary">@lang('Lookup IP') {{ $ip }}</a>
    @endpush
@endif

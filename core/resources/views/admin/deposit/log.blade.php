@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center">
    @if(request()->routeIs('admin.deposit.list') || request()->routeIs('admin.deposit.method') || request()->routeIs('admin.users.deposits') || request()->routeIs('admin.users.deposits.method'))
        <div class="col-md-6 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--success">
            <div class="widget-two__content">
                <h2 class="text-white">{{ __($general->cur_sym) }}{{ showAmount($successful) }}</h2>
                <p class="text-white">@lang('Successful Payment')</p>
            </div>
            </div><!-- widget-two end -->
        </div>
        <div class="col-md-6 col-sm-6 mb-30">
            <div class="widget-two box--shadow2 b-radius--5 bg--6">
                <div class="widget-two__content">
                    <h2 class="text-white">{{ __($general->cur_sym) }}{{ showAmount($pending) }}</h2>
                    <p class="text-white">@lang('Pending Payment')</p>
                </div>
            </div><!-- widget-two end -->
        </div> 
    @endif

    <div class="col-md-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                                @include('admin.partials.datatable')

                <div class="table-responsive--sm table-responsive">
                    <table id="example" class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Gateway | Trx')</th>
                                <th>@lang('Initiated')</th>
                                <th>@lang('User')</th>
                                <th>@lang('Order Amount')</th>
                                <th>@lang('Vat')</th>
                                <th>@lang('Delivery Charge')</th>
                                <th>@lang('Total Amount')</th>
                                <th>@lang('Total Amount USD')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deposits as $deposit)
                                @php
                                    $details = $deposit->detail ? json_encode($deposit->detail) : null;
                                @endphp
                                <tr>
                                    <td data-label="@lang('Gateway | Trx')">
                                        @if ($deposit->method_code == 0)
                                        <span class="font-weight-bold">@lang('Wallet')</span>
                                        @else
                                        <span class="font-weight-bold"> <a href="#">{{ App\Models\Gateway::whereCode($deposit->method_code)->first()->name ?? "N/A"}}</a> </span>
                                        @endif
                                       
                                        <br>
                                        <small> {{ $deposit->order_number }} </small>
                                    </td>

                                    <td data-label="@lang('Date')">
                                        {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                    </td>
                                    <td data-label="@lang('User')">
                                        <span class="font-weight-bold">{{ $deposit->user->fullname }}</span>
                                        <br>
                                        <span class="small">
                                        <a href="{{ route('admin.users.detail', $deposit->user_id) }}"><span>@</span>{{ $deposit->user->username }}</a>
                                        </span>
                                    </td>
                                    <td data-label="@lang('Cart Amount')">
                                    
                                        <strong data-toggle="tooltip" data-original-title="@lang('Amount with charge')">
                                        {{ showAmount($deposit->cart_amount) }} {{ __($general->cur_text) }}
                                        </strong>
                                    </td>
                                    <td data-label="@lang('Vat')">
                                   <span class="text-danger" data-toggle="tooltip" data-original-title="@lang('Vat')"> {{ __($general->cur_sym) }} {{ showAmount($deposit->vat)}} </span>
                                    </td>
                                    <td data-label="@lang('Delivery Cost')">
                                     <span class="text-danger" data-toggle="tooltip" data-original-title="@lang('Delivery Cost')">{{ __($general->cur_sym) }}{{ showAmount($deposit->shipping_charge)}} </span>
                                        <br>
                                       
                                    </td>
                                    
                                    <td data-label="@lang('Total Amount')">
                                         <strong data-toggle="tooltip" data-original-title="@lang('Amount with charge')">
                                        {{ showAmount($deposit->total_amount) }} {{ __($general->cur_text) }}
                                        </strong>
                                    </td>
                                    
                                    <td data-label="@lang('Total Amount USD')">
                                         <strong data-toggle="tooltip" data-original-title="@lang('Amount with charge')">
                                        {{ showAmount($deposit->total_amount_usd) }} USD
                                        </strong>
                                    </td>
                                     
                                    <td data-label="@lang('Status')">
                                         @if($deposit->payment_status == 1)
                                            <span class="badge badge--success">@lang('Successful')</span>
                                            <br>{{ diffForHumans($deposit->updated_at) }}
                                        @else
                                            <span class="badge badge--danger">@lang('Not Paid')</span>
                                            <br>{{ diffForHumans($deposit->updated_at) }}
                                        @endif
                                    </td>
                                    <td data-label="@lang('Action')">
                                        
                                        <a href="{{ route('admin.order.details', $deposit->id) }}"
                                            class="icon-btn ml-1 " data-toggle="tooltip" title="" data-original-title="@lang('Detail')">
                                                <i class="la la-desktop"></i>
                                            </a>
                                        
                                       
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
            <div class="card-footer py-4">
                {{ paginateLinks($deposits) }}
            </div>
        </div><!-- card end -->
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    @if(!request()->routeIs('admin.users.deposits') && !request()->routeIs('admin.users.deposits.method'))
        <form action="{{route('admin.deposit.search', $scope ?? str_replace('admin.deposit.', '', request()->route()->getName()))}}" method="GET" class="form-inline float-sm-right bg--white mb-2 ml-0 ml-xl-2 ml-lg-0">
            <div class="input-group has_append  ">
                <input type="text" name="search" class="form-control" placeholder="@lang('Trx number/Username')" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <form action="{{route('admin.deposit.dateSearch',$scope ?? str_replace('admin.deposit.', '', request()->route()->getName()))}}" method="GET" class="form-inline float-sm-right bg--white">
            <div class="input-group has_append ">
                <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control" data-position='bottom right' placeholder="@lang('Min date - Max date')" autocomplete="off" value="{{ @$dateSearch }}">
                <input type="hidden" name="method" value="{{ @$methodAlias }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

    @endif
@endpush


@push('script-lib')
  <script src="{{ asset('assets/dashboard/js/vendor/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/dashboard/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
  <script>
    (function($){
        "use strict";
        if(!$('.datepicker-here').val()){
            $('.datepicker-here').datepicker();
        }
    })(jQuery)
  </script>
@endpush

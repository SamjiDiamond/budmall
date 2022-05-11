@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="row justify-content-end">
                        <div class="col-xl-3 mb-3">
                            <form action="{{ route('admin.sellers.search', $scope ?? str_replace('admin.sellers.', '', request()->route()->getName())) }}" method="GET" class="pt-3 px-3">
                                <div class="input-group has_append">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('sellername or email')" value="{{ $search ?? '' }}">
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
                                <th>@lang('Seller')</th>
                                <th>@lang('Email') | @lang('Mobile')</th>
                                <th>@lang('Products') | @lang('Sale')</th>
                                <th>@lang('Balance')</th>
                                <th>@lang('Featured')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($sellers as $seller)
                            <tr>
                                <td data-label="@lang('Seller')">
                                    <span class="font-weight-bold d-block">{{$seller->fullname}}</span>
                                    <a href="{{ route('admin.sellers.detail', $seller->id) }}">{{ $seller->username }}</a>
                                </td>

                                <td data-label="@lang('Email') | @lang('Mobile')">
                                    {{ $seller->email }}<br>{{ $seller->mobile }}
                                </td>

                                <td data-label="@lang('Products') | @lang('Sale')">
                                    <span data-toggle="tooltip" title="@lang('Total Products')">{{ $seller->products->count() }}</span>
                                    <span class="d-block font-weight-bold" data-toggle="tooltip" title="@lang('Total Sale')">
                                        {{ $seller->totalSold() }} @lang('pcs')
                                    </span>
                                </td>

                                <td data-label="@lang('Balance')">
                                    <span class="font-weight-bold">
                                        {{ $general->cur_sym }}{{ showAmount($seller->balance) }}
                                    </span>
                                </td>

                                <td data-label="@lang('Featured')">
                                    @if ($seller->featured == 1)
                                     <span class="badge badge--success">@lang('Yes')</span>
                                    @else
                                     <span class="badge badge--dark">@lang('No')</span>
                                    @endif
                                </td>

                                <td data-label="@lang('Action')">


                                    <a href="{{ route('admin.sellers.detail', $seller->id) }}" class="icon-btn" data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                                        <i class="las la-desktop text--shadow"></i>
                                    </a>

                                    @if($seller->featured == 1)
                                        <button data-route="{{ route('admin.sellers.feature', $seller->id) }}" class="feature icon-btn btn--danger" data-remove="1" data-toggle="tooltip" title="" data-original-title="@lang('Remove from featured list')">
                                        <i class="las la-fire"></i>
                                        </button>
                                    @else
                                        <button data-route="{{ route('admin.sellers.feature', $seller->id) }}" class="feature icon-btn btn--secondary" data-remove="" data-toggle="tooltip" title="" data-original-title="@lang('Mark as featured seller')">
                                            <i class="las la-fire"></i>
                                        </button>
                                    @endif

                                    <div class="dropdown d-inline-flex" data-toggle="tooltip" title="@lang('More')">
                                        <button class="btn icon-btn btn--dark dropdown-toggle" type="button" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                            <span class="icon text-white"><i class="las la-chevron-circle-down mr-0"></i></span>
                                        </button>

                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a href="{{route('admin.sellers.login',$seller->id)}}" target="_blank" class="dropdown-item">
                                                @lang('Login as Seller')
                                            </a>

                                            <a href="{{ route('admin.sellers.login.history.single', $seller->id) }}"
                                                class="dropdown-item">
                                                 @lang('Login Logs')
                                             </a>
                                             <a href="{{route('admin.sellers.email.single',$seller->id)}}"
                                                class="dropdown-item">
                                                 @lang('Send Email')
                                             </a>

                                             <a href="{{route('admin.sellers.email.log',$seller->id)}}" class="dropdown-item">
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
                @if($sellers->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($sellers) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
         <div class="modal fade" id="featureModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="title"></h5>
                        <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p class="msg">@lang('Are you sure to mark as feature this seller?')</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                            <button type="submit"  class="btn btn--primary del">@lang('Yes')</button>
                        </div>
                    </form>
              </div>
            </div>
        </div>
@endsection

@push('script')
     <script>
            'use strict';
            (function ($) {
                $('.feature').on('click',function () {
                    var route = $(this).data('route');
                    var remove = $(this).data('remove');
                    var modal = $('#featureModal');
                    if(remove == 1){
                        modal.find('.msg').text('@lang("Are you sure wants to remove this seller from featured?")');
                        modal.find('.title').text('@lang("Remove from featured.")');
                    } else{
                        modal.find('.msg').text('@lang("Are you sure to mark as feature this seller?")');
                        modal.find('.title').text('@lang("Mark as featured")');
                    }
                    modal.find('form').attr('action',route);
                    modal.modal('show');
                });
            })(jQuery);
     </script>
@endpush

@extends('seller.layouts.app')

@section('panel')
    <div class="container">
        <div class="row justify-content-center mt-2">
            @foreach($withdrawMethod as $data)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card card-withdraw">
                        <h4 class="card-header text-center">{{__($data->name)}}</h4>
                        <div class="card-body text-center p-0">
                            <img src="{{getImage(imagePath()['withdraw']['method']['path'].'/'. $data->image,imagePath()['withdraw']['method']['size'])}}" class="card-img-top w-50 mt-3" alt="{{__($data->name)}}">

                            <ul class="list-group list-group-flush text-center mt-3">
                                <li class="list-group-item d-flex flex-wrap justify-content-between border-top">
                                    <span>
                                        @lang('Limit')
                                    </span>
                                    <span>
                                        {{$general->cur_sym.showAmount($data->min_limit) }} - {{ $general->cur_sym.showAmount($data->max_limit) }}
                                    </span>
                                </li>

                                <li class="list-group-item d-flex flex-wrap justify-content-between">
                                    <span>@lang('Charge')</span>
                                    <span>
                                        {{ $general->cur_sym.showAmount($data->fixed_charge) }}
                                        + {{ getAmount($data->percent_charge) }}%
                                    </span>
                                </li>
                                <li class="list-group-item d-flex flex-wrap justify-content-between">
                                    <span>@lang('Processing Time')</span>
                                    <span>{{ $data->delay }}</span>
                                </li>
                            </ul>
                        </div>

                        <div class="card-footer">
                            <a href="javascript:void(0)"  data-id="{{$data->id}}"
                               data-resource="{{$data}}"
                               data-min_amount="{{showAmount($data->min_limit)}}"
                               data-max_amount="{{showAmount($data->max_limit)}}"
                               data-fix_charge="{{showAmount($data->fixed_charge)}}"
                               data-percent_charge="{{showAmount($data->percent_charge)}}"
                               data-base_symbol="{{__($general->cur_text)}}"
                               class="btn btn-block  btn--primary withdraw" data-toggle="modal" data-target="#withdrawModal">
                                @lang('Withdraw Now')</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title method-name" id="withdrawModalLabel">@lang('Withdraw')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('seller.withdraw.money')}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <p class="text-danger withdrawLimit"></p>
                        <p class="text-danger withdrawCharge"></p>

                        <div class="form-group">
                            <input type="hidden" name="currency"  class="edit-currency form-control">
                            <input type="hidden" name="method_code" class="edit-method-code  form-control">
                        </div>
                        <div class="form-group">
                            <label>@lang('Enter Amount'):</label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control form-control-lg" name="amount" placeholder="0.00" required value="{{old('amount')}}">

                                <div class="input-group-append">
                                    <span class="input-group-text addon-bg currency-addon bg--primary border-0">{{__($general->cur_text)}}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Confirm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.withdraw').on('click', function () {
                var id = $(this).data('id');
                var result = $(this).data('resource');
                var minAmount = $(this).data('min_amount');
                var maxAmount = $(this).data('max_amount');
                var fixCharge = $(this).data('fix_charge');
                var percentCharge = $(this).data('percent_charge');

                var withdrawLimit = `@lang('Withdraw Limit'): ${minAmount} - ${maxAmount}  {{__($general->cur_text)}}`;
                $('.withdrawLimit').text(withdrawLimit);
                var withdrawCharge = `@lang('Charge'): ${fixCharge} {{__($general->cur_text)}} ${(0 < percentCharge) ? ' + ' + percentCharge + ' %' : ''}`
                $('.withdrawCharge').text(withdrawCharge);
                $('.method-name').text(`@lang('Withdraw Via') ${result.name}`);
                $('.edit-currency').val(result.currency);
                $('.edit-method-code').val(result.id);
            });
        })(jQuery);
    </script>

@endpush


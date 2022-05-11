@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="table-responsive table-responsive-xl">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>@lang('S.N.')</th>
                            <th>@lang('Product')</th>
                            <th>@lang('Variation')</th>
                            <th class="text-center">@lang('Quantity')</th>
                            <th class="text-center">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @forelse($stock_data as $item)

                        <tr>
                            <td data-lable="@lang('S.N.')">{{ $stock_data->hasPages() + $lopp->firstItem() }}</td>
                            <td data-lable="@lang('Product')">
                                <a href="{{route('admin.products.edit', [$item->product->id, slug($item->product->name)])}}">{{__($item->product->name)}}</a>
                            </td>
                            <td data-lable="@lang('Variation')">
                                @if($item->attributes != null)
                                    @foreach(json_decode($item->attributes) as $attr_id)
                                        @php $variants = getProuductVariants($attr_id) @endphp

                                        {{$variants['name'] .' : '.$variants['value']}}
                                        @if(!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td data-lable="@lang('Quantity')" class="text-center">
                                <span class="badge badge-pill badge-{{$item->quantity>0?'success':'dark'}} p-3">
                                    {{ sprintf('%02d',$item->quantity)}}
                                </span>
                            </td>
                            <td data-lable="@lang('Action')">
                            <a class="btn btn-primary" href="{{route('admin.products.stock.create', $item->product->id)}}" role="button"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            @if($stock_data->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($stock_data) }}
                </div>
            @endif

        </div>
    </div>
</div>


@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.products.all') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="las la-backward"></i>@lang('Go Back')</a>
@endpush

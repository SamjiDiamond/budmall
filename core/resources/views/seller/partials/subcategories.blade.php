

@if ($subcategory->allSubcategories)
@php $prefix .='|--'  @endphp
    @foreach ($subcategory->allSubcategories as $childCategory)
    @include('seller.partials.subcategories', ['subcategory' => $childCategory])
        <option value="{{ $subcategory->id }}" > {{ $prefix }} @lang($childCategory->name)</option>
    @endforeach
@endif

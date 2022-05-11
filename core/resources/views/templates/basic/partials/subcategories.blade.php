

@if ($subcategory->allSubcategories)
@php $prefix .='|--'  @endphp
    @foreach ($subcategory->allSubcategories as $childCategory)
        <option value="{{ $subcategory->id }}" > {{ $prefix }} @lang($childCategory->name)</option>
        @include($activeTemplate.'partials.subcategories', ['subcategory' => $childCategory])
    @endforeach
@endif

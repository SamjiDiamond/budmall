<li class="cardf">
    <a href="{{ route('products.category', ['id'=>$subcategory->id, 'slug'=>slug($subcategory->name)]) }}">
        {{ __($subcategory->name) }}
    </a>

    @if ($subcategory->allSubcategories && $subcategory->allSubcategories->count() >0)
    <span class="open-links"></span>
        <ul class="category-sublink card">
            @foreach ($subcategory->allSubcategories as $childCategory)
                @include($activeTemplate.'partials.menu_subcategories', ['subcategory' => $childCategory])
            @endforeach
        </ul>
    @endif
</li>

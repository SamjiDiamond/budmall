<div class="category-link-wrapper d-none d-lg-block">
    <h6 class="category__header">@lang('Categories')</h6>
    <ul class="category-link d-none d-lg-block">
        @foreach ($allCategories->take(10) as $category)
            <li>
                <a href="{{ route('products.category', ['id'=>$category->id, 'slug'=>slug($category->name)]) }}">
                    {{ $category->name }}
                </a>
                @if($category->allSubcategories->count()>0)
                <ul class="category-sublink">
                    @foreach ($category->allSubcategories as $subcategory)
                        @include($activeTemplate.'partials.menu_subcategories', ['subcategory' => $subcategory])
                    @endforeach
                </ul>
                @endif
            </li>
        @endforeach
        <hr>
        <li>
                <a href="#">
                   <b> PAY BILLS</b>
                </a> 
                <ul class="category-sublink">
                    <li><a href="https://www.budmall.ng/user/airtime">@lang('Airtime')</a></li>
                    <li><a href="https://www.budmall.ng/user/cabletv">@lang('Cable TV')</a></li>
                    <li><a href="https://www.budmall.ng/user/internet">@lang('Internet')</a></li>
                    <li><a href="https://www.budmall.ng/user/utility">@lang('Utility Bills')</a></li>
                </ul>
                
            </li>
        
         

        @if($allCategories->count()>10)
            <li> <a href="{{ route('categories') }}">@lang('View All')</a></li>
        @endif
    </ul>
     
                   
</div>
 
 
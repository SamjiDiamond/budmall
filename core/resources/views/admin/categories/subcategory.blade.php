<li class="folder-root @if($subcategory->allSubcategories->count()>0) open @endif" >
    @if($subcategory->allSubcategories->count()>0)
        <i class="las la-minus-circle file-opener-i"></i>
    @endif
    <a href="javascript:void(0)" class="parent" data-category="{{ $subcategory }}" data-image="{{ getImage(imagePath()['category']['path']. '/' .$subcategory->image, imagePath()['category']['size']) }}">
        {{ __($subcategory->name) }}
    </a>
    <span type="button" class="p-0 ml-3 delete-btn bg-danger d-none" data-id="{{ $subcategory->id }}"><i class="las la-times-circle"></i></span>

</li>
@if ($subcategory->allSubcategories)
    <ul class="childs">
        @foreach ($subcategory->allSubcategories as $childCategory)
            @include('admin.categories.subcategory', ['subcategory' => $childCategory])
        @endforeach
    </ul>
@endif

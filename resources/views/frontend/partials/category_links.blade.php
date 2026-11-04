<li><a href="{{ getAppHomepageLink() }}">{{ __('Home') }}</a></li>
<li><a href="{{ route('products') }}">{{ __('All Categories') }}</a></li>

@if (isset($subSubCategory))
    <li>
        <a
            href="{{ $subSubCategory->subCategory->category->getCategoryLink() }}">
            {{ $subSubCategory->subCategory->category->getTranslationNameAttribute() }}
        </a>
    </li>
    <li>
        <a
            href="{{ $subSubCategory->subCategory->getSubCategoryLink() }}">
            {{ $subSubCategory->subCategory->getTranslationNameAttribute() }}
        </a>
    </li>
    <li class="active">
        <a
            href="{{ $subSubCategory->getSubSubCategoryLink() }}">
            {{ $subSubCategory->getTranslationNameAttribute() }}
        </a>
    </li>
@elseif (isset($subCategory))
    <li>
        <a
            href="{{  $subCategory->category->getCategoryLink() }}">
            {{ $subCategory->category->getTranslationNameAttribute() }}
        </a>
    </li>
    <li class="active">
        <a
            href="{{ $subCategory->getSubCategoryLink() }}">
            {{ $subCategory->getTranslationNameAttribute() }}
        </a>
    </li>
@elseif (isset($category))
    <li class="active">
        <a
            href="{{ $category->getCategoryLink() }}">
            {{ $category->getTranslationNameAttribute() }}
        </a>
    </li>
@endif
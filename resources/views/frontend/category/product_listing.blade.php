@extends('frontend.layouts.app')
@if (isset($subsubcategory_id))
    @php

        $meta_title = $subSubCategory->translation_meta_title;
        $meta_description = $subSubCategory->translation_meta_description;
    @endphp
@elseif (isset($subcategory_id))
    @php

        $meta_title = $subCategory->translation_meta_title;
        $meta_description = $subCategory->translation_meta_description;

    @endphp
@elseif (isset($category_id))
    @php

        $meta_title = $category->translation_meta_title;
        $meta_description = $category->translation_meta_description;

    @endphp
@elseif (isset($brand_id))
    @php
        $meta_title = \App\Brand::find($brand_id)->meta_title;
        $meta_description = \App\Brand::find($brand_id)->meta_description;
    @endphp
@else
    @php
        $meta_title = env('APP_NAME'); //here
        $meta_description = \App\SeoSetting::first()->description;
    @endphp
@endif
@section('meta_title'){{ $meta_title }}@stop
@section('meta_description'){{ $meta_description }}@stop
@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta_title }}">
    <meta itemprop="description" content="{{ $meta_description }}">
    <!-- Twitter Card data -->
    <meta name="twitter:title" content="{{ $meta_title }}">
    <meta name="twitter:description" content="{{ $meta_description }}">
    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_description }}" />
    <style>
        .star-rating.active {
            background-color: #ebebeb;
            border-radius: 0.78125rem;
            font-weight: 500;
            padding: 1px 10px;

            border-radius: 0.78125rem;
        }

        .star-rating {
            background-color: #fff;
            border-radius: 0.78125rem;
            font-weight: 500;
            padding: 1px 10px;
            border-radius: 0.78125rem;
            cursor: pointer;
        }

        .star-rating:hover {
            background-color: #ebebeb;
        }
    </style>
@endsection
@section('content')
    <div class="breadcrumb-area">
        <div class="container">
            <div class="row">
                <div class="col">
                    <ul class="breadcrumb">
                        @include('frontend.partials.category_links')
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <section class="gry-bg py-4">
        <div class="container sm-px-0">
            <form class="" id="search-form" method="GET">
                <input type="hidden" name="q"
                    @isset($query) value="{{ $query }}" @endisset />
                <input type="hidden" name="sort_by"
                    @isset($sort_by) value="{{ $sort_by }}" @endisset/>
                <button type="submit" class="d-none"></button>
                <div class="row">
                    <div class="col-xl-3 side-filter d-xl-block">
                        <div class="filter-overlay filter-close"></div>
                        <div class="filter-wrapper c-scrollbar" style="max-height: 70vh; overflow-y: scroll">
                            <div class="filter-title d-flex d-xl-none justify-content-between pb-3 align-items-center">
                                <h3 class="h6">{{ __('Filters') }}</h3>
                                <button type="button" class="close filter-close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="bg-white sidebar-box">
                                <!-- Category Filter -->
                                <div class="box-title bg-white border-bottom-0 border-top">
                                    <h6 class="font-weight-bold mb-0">{{ __('Categories') }}</h6>
                                </div>
                                <div class="box-content pr-3 pl-3 pb-3">
                                    @foreach ($mainCategories as $key => $cat)
                                        <div class="category-title {{ $key >= 5 ? 'category-hidden-item' : '' }} pb-2"
                                            style="{{ $key >= 5 ? 'display:none' : '' }}">
                                            <a class="alert-link text-dark"
                                                href="{{ route('category.view', $cat->slug) }}">
                                                {{ $cat->translation_name }}
                                            </a>
                                            @if (count($cat->subCategories) > 0)
                                                <a class="dropdown-toggle text-dark" data-toggle="collapse"
                                                    href="#category-collapse-{{ $cat->id }}" role="button"
                                                    aria-expanded="false"
                                                    aria-controls="category-collapse-{{ $cat->id }}"></a>
                                            @endif
                                        </div>
                                        <div class="collapse pr-3 pl-3" id="category-collapse-{{ $cat->id }}">
                                            @foreach ($cat->subCategories as $subCat)
                                                @if ($subCat->status === 1)
                                                    <div class="pb-2">
                                                        <a class="alert-link text-muted"
                                                            href="{{ route('category.view', ['category_slug' => $cat->slug, 'subcategory_slug' => $subCat->slug]) }}">
                                                            {{ $subCat->translation_name }}
                                                        </a>
                                                        @if (count($subCat->subSubcategories) > 0)
                                                            <a class="dropdown-toggle text-muted" data-toggle="collapse"
                                                                href="#subcategory-collapse-{{ $subCat->id }}"
                                                                role="button"></a>
                                                        @endif
                                                    </div>
                                                    @if (count($subCat->subSubcategories) > 0)
                                                        <div class="collapse pr-3 pl-3"
                                                            id="subcategory-collapse-{{ $subCat->id }}">
                                                            @foreach ($subCat->subSubcategories as $subSubCat)
                                                                @if ($subSubCat->status === 1)
                                                                    <div class="pb-2">
                                                                        <a class="alert-link text-black-50"
                                                                            href="{{ route('category.view', ['category_slug' => $cat->slug, 'subcategory_slug' => $subCat->slug, 'subsubcategory_slug' => $subSubCat->slug]) }}">
                                                                            {{ $subSubCat->translation_name }}
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                    @if (count($mainCategories) >= 6)
                                        <div class="text-center text-black-50">
                                            <span role="button" data-status="hide" id="view-more-categories">
                                                <span>{{ __('View more') }}</span>
                                                <i class="caret-icon fa fa-caret-down"></i>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <!-- END -->

                                <!-- Brand Filter -->
                                @if (count($mainBrands) > 0)
                                    <div class="box-title bg-white border-bottom-0 border-top">
                                        <h6 class="font-weight-bold mb-0">{{ __('Brands') }}</h6>
                                    </div>
                                    <div class="box-content pr-3 pl-3 pb-3">
                                        <!-- Filter by color -->
                                        @foreach ($mainBrands as $key => $brand)
                                            <div class="custom-control custom-checkbox {{ $key >= 5 ? 'brand-hidden-item' : '' }}"
                                                style="{{ $key >= 5 ? 'display:none' : '' }}">
                                                <input name="brand_ids[]" type="checkbox" class="custom-control-input"
                                                    id="custom-cb-brand-{{ $brand->id }}" value="{{ $brand->id }}"
                                                    @isset($brandIds) @if (in_array($brand->id, $brandIds)) checked @endif @endisset
                                                    onchange="filter()">
                                                <label class="custom-control-label"
                                                    for="custom-cb-brand-{{ $brand->id }}">{{ $brand->name }}</label>
                                            </div>
                                        @endforeach
                                        @if (count($mainBrands) >= 6)
                                            <div class="text-center text-black-50">
                                                <span role="button" data-status="hide" id="view-more-brands">
                                                    <span>{{ __('View more') }}</span>
                                                    <i class="caret-icon fa fa-caret-down"></i>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <!-- END -->

                                <!-- Seller Filter -->
                                @if (!isMicrosite())
                                    <div class="box-title bg-white border-bottom-0 border-top">
                                        <h6 class="font-weight-bold mb-0">{{ __('Sellers') }}</h6>
                                    </div>
                                    <div class="box-content pr-3 pl-3 pb-3">
                                        @foreach ($sellers as $key => $seller)
                                            <div class="custom-control custom-checkbox {{ $key >= 5 ? 'seller-hidden-item' : '' }}"
                                                style="{{ $key >= 5 ? 'display:none' : '' }}">
                                                <input name="seller_ids[]" type="checkbox" class="custom-control-input"
                                                    id="custom-cb-seller-{{ $seller->id }}"
                                                    value="{{ $seller->id }}"
                                                    @isset($sellerIds) @if (in_array($seller->id, $sellerIds)) checked @endif @endisset
                                                    onchange="filter()">
                                                <label class="custom-control-label"
                                                    for="custom-cb-seller-{{ $seller->id }}">{{ $seller->name }}</label>
                                            </div>
                                        @endforeach
                                        @if (count($sellers) >= 6)
                                            <div class="text-center text-black-50">
                                                <span role="button" data-status="hide" id="view-more-sellers">
                                                    <span>{{ __('View more') }}</span>
                                                    <i class="caret-icon fa fa-caret-down"></i>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <!-- Price Filter -->
                                <div class="box-title bg-white border-bottom-0 border-top">
                                    <h6 class="font-weight-bold mb-0">{{ __('Price range') }}</h6>
                                </div>
                                <div class="box-content pr-3 pl-3 pb-3">
                                    <div class="form-row d-flex align-items-center justify-content-between">
                                        <input name="min_price" value="{{ $min_price }}" type="number"
                                            placeholder="Min" class="form-control col-md-5">
                                        <div class="col-md-1 border-bottom border-dark"></div>
                                        <input name="max_price" value="{{ $max_price }}" type="number"
                                            placeholder="Max" class="form-control col-md-5">
                                    </div>
                                </div>
                                <!-- END -->

                                <!-- star -->
                                <div class="box-title bg-white border-bottom-0 border-top">
                                    <h6 class="font-weight-bold mb-0">{{ __('Rate') }}</h6>
                                </div>
                                <div class="box-content pr-3 pl-3 pb-3">
                                    <div class="form-row d-flex">
                                        @for ($i = 0; $i < 5; $i++)
                                        <div class="star-rate star-rating star-rating-sm mt-1 {{ isset($rate) && $rate == 5 - $i ? 'active' : '' }}" data-value="{{ 5 - $i }}">
                                            @for ($j = 0; $j < 5; $j++)
                                                <i class="fa fa-star {{ $i + $j < 5 ? 'active' : '' }}" style="font-size: 15px;padding: 0 0px;"></i>
                                            @endfor
                                            <span>{{ $i != 0 ? __('& Up') : '' }}</span>
                                        </div>
                                        @endfor
                                    </div>
                                    <input type="hidden" value="" name="rate" id="star-rate">
                                </div>
                                <!-- END -->

                                {{-- promotion --}}
                                <div class="box-title bg-white border-bottom-0 border-top">
                                    <h6 class="font-weight-bold mb-0">{{ __('Promotion') }}</h6>
                                </div>
                                <div class="box-content pr-3 pl-3 pb-3">
                                    <div class="form-row d-flex">
                                        <div class="custom-control custom-checkbox " style="">
                                            <input name="discount" type="checkbox" class="custom-control-input"
                                                id="discount" value="1"
                                                {{ isset($discount) && $discount == 1 ? 'checked' : '' }}
                                                onchange="filter()">
                                            <label class="custom-control-label"
                                                for="discount">{{ __('On sale off') }}</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- CSS --}}
                                @if (isset($canViewCSSPage) && $canViewCSSPage === true)
                                    <div class="box-title bg-white border-bottom-0 border-top">
                                        <h6 class="font-weight-bold mb-0">{{ __('Store type') }}</h6>
                                    </div>
                                    <div class="box-content pr-3 pl-3 pb-3">
                                        <div class="form-row d-flex">
                                            <div class="custom-control custom-checkbox " style="">
                                                <input name="store_types[2]" type="checkbox" class="custom-control-input"
                                                    value="css" id="store_types_css"
                                                    {{ isset($storeTypes) && isset($storeTypes[2]) && $storeTypes[2] == 'css' ? 'checked' : '' }}
                                                    onchange="filter()">
                                                <label class="custom-control-label"
                                                    for="store_types_css">{{ __('CSS') }}</label>
                                            </div>
                                        </div>

                                        <div class="form-row d-flex">
                                            <div class="custom-control custom-checkbox " style="">
                                                <input name="store_types[1]" type="checkbox" class="custom-control-input"
                                                    value="flagship" id="store_types_flagship"
                                                    {{ isset($storeTypes) && isset($storeTypes[1]) && $storeTypes[1] == 'flagship' ? 'checked' : '' }}
                                                    onchange="filter()">
                                                <label class="custom-control-label"
                                                    for="store_types_flagship">{{ __('Flagship') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <!-- END -->
                            <!-- Color Filter -->
                            @if (count($all_colors) > 0)
                                <div class="box-title bg-white border-bottom-0 border-top">
                                    <h6 class="font-weight-bold mb-0">{{ __('Filter by color') }}</h6>
                                </div>
                                <div class="box-content pr-3 pl-3 pb-3">
                                    <!-- Filter by color -->
                                    <ul class="list-inline checkbox-color checkbox-color-circle mb-0">
                                        @foreach ($all_colors as $key => $color)
                                            <li>
                                                <input type="radio" id="color-{{ $key }}" name="color"
                                                    value="{{ $color }}"
                                                    @if (isset($selected_color) && $selected_color == $color) checked @endif onchange="filter()">
                                                <label style="background: {{ $color }};"
                                                    for="color-{{ $key }}" data-toggle="tooltip"
                                                    data-original-title="{{ \App\Color::where('code', $color)->first()->name }}"></label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @foreach ($attributes as $key => $attribute)
                                @if (array_key_exists($attribute['id'], $existing_attributes))
                                    <div class="box-title bg-white border-bottom-0 border-top">
                                        <h6 class="font-weight-bold mb-0">
                                            Filter by {{ $existing_attributes[$attribute['id']] }}
                                        </h6>
                                    </div>
                                    <div class="box-content pr-3 pl-3 pb-3">
                                        <!-- Filter by others -->
                                        @foreach ($attribute['values'] as $key => $value)
                                            @php
                                                $flag = false;
                                                if (array_key_exists($attribute['id'], $selected_attributes)) {
                                                    if (in_array($value, $selected_attributes[$attribute['id']])) {
                                                        $flag = true;
                                                    }
                                                }

                                            @endphp
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="attribute_{{ $attribute['id'] }}_value_{{ $value }}"
                                                    name="attribute_{{ $attribute['id'] }}[]"
                                                    value="{{ $value }}"
                                                    @if ($flag) checked @endif onchange="filter()">
                                                <label class="custom-control-label"
                                                    for="attribute_{{ $attribute['id'] }}_value_{{ $value }}">{{ $value }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                            <!-- END -->
                        </div>

                        {{-- <button type="submit" class="btn btn-styled btn-block btn-base-4">Apply filter</button> --}}
                    </div>

                <div class="col-xl-9">
                    <div class="sort-by-bar row no-gutters mb-3 px-3">
                        @include('frontend.product_filter.sort_by_bar')
                    </div>
                    <div class="products-box-bar p-3 bg-white">
                        @include('frontend.inc.product_list.list', ['products' => $products])
                    </div>
                    <div class="products-pagination bg-white p-3">
                        <nav aria-label="Center aligned pagination">
                            <ul class="pagination justify-content-center">
                                {{ $products->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
</section>
@include('frontend.inc.restricted_age.popup')
@endsection
@section('script')
<script src="{{ static_asset('/js/view_more.js') }}"></script>
<script type="text/javascript">
    function filter() {
        $('#search-form').submit();
    }

    $(document).on('change', '#min_price, #max_price', function() {
        let minPrice = $('#min_price').val();
        let maxPrice = $('#max_price').val();

        if (minPrice !== '' && maxPrice !== '') {
            filter();
        }
    });

    $(document).on('click', '.star-rate', function() {
        $('.star-rate').each(function() {
            $(this).removeClass('active');
        })
        $(this).toggleClass('active');
        $('#star-rate').val($(this).data('value'));
        filter();
    })
</script>
@endsection
<style>
.category-sidebar1 ul.categories {
    position: relative !important;
    height: 300px !important;
    overflow: scroll;
    box-shadow: none !important;
}

.category-sidebar1 ul.categories:hover {
    width: 900px;
}

.category-sidebar1 {
    background-color: #fff;
}

.category-sidebar1 .all-category {
    height: 49px;
    line-height: 49px;
    padding-left: 16px;
    font-size: 16px;
    color: #fff;
    background-color: rgba(88, 55, 134, 1);
}

.category-sidebar1 .all-category>span {
    font-size: 16px;
    margin-right: 130px;
    font-weight: 600;
    color: #fff;
}

.category-sidebar1 .all-category a {
    color: #fff;
    font-size: 12px;
}

.category-sidebar1 .categories>li {
    border: 0;
}

.category-sidebar1 .categories>li>a {
    padding: 8px 10px;
    color: #333;
    font-size: 16px;
    border: 1px solid transparent;
    position: relative;
}

.category-sidebar1 .categories>li:hover>a::before {
    position: absolute;
    height: 100%;
    width: 3px;
    background: #fff;
    content: "";
    top: 0;
    right: -2px;
    z-index: 999;
    display: block;
}

.category-sidebar1 .categories>li:hover>a {
    border-color: #fff;
}

.category-sidebar1 .all-category a:hover {
    color: #fff !important;
}

.category-sidebar1 .categories>li>a .cat-image {
    width: 16px;
    opacity: 0.6;
    margin-right: 10px;
}

.category-sidebar1 .sub-cat-menu {
    display: none;
    /* width: 800px !important; */
    position: absolute;
    /* margin: auto; */
    left: 100px;
    height: 500px;
    /* overflow: hidden; */
    top: 0px;
    z-index: 100;
    background-color: #fff;
    border: 1px solid #eee;
    overflow-y: auto;
    box-shadow: 0 3px 3px 0 rgba(51, 51, 51, 0.2);
}

.category-sidebar1 .categories>li:hover .sub-cat-menu {
    display: block;
}

.sub-cat-content .sub-cat-list {
    padding: 10px;
    padding-top: 0;
}

.sub-cat-content .sub-cat-list .card {
    margin: 10px;
    border: 0;
}

.card .sub-cat-items {
    padding: 0;
    list-style: none;
}

.card .sub-cat-items li a {
    font-size: 16px;
    color: #333;
    display: block;
    text-overflow: ellipsis;
    word-wrap: normal;
    white-space: nowrap;
    overflow: hidden;
    line-height: 26px;
}

.card .sub-cat-items li.sub-cat-name a {
    margin-bottom: 5px;
    font-weight: 600;
    line-height: 35px;
    height: 35px;
    border-bottom: 1px solid #e9e9e9;
}

.sub-cat-featured {
    padding: 20px;
}

.sub-cat-featured a {
    display: flex;
    height: 60px;
    border: 1px solid #e9e9e9;
    text-align: right;
}

.sub-cat-featured a .featured-img {
    height: auto;
    width: 70px;
    background: #fff;
    margin-top: -10px;
    margin-right: 6px;
}

.sub-cat-featured a .featured-name {
    width: 100%;
    padding: 13px 20px 13px 13px;
    font-size: 16px;
    font-weight: 600;
    line-height: 16px;
    height: 42px;
    overflow: hidden;
    white-space: normal;
    word-wrap: break-word;
    text-align: left;
}

.sub-cat-brand {
    padding: 10px 15px;
    padding-left: 25px;
}

.sub-cat-brand .sub-brand-list {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    padding: 0;
    list-style: none;
}

.sub-cat-brand .sub-brand-item {
    width: calc(50% - 4px);
    height: 50px;
    border: 1px solid #e9e9e9;
    padding: 5px 10px;
    margin: 2px;
    overflow: hidden;
    background-color: #fff;
}

.sub-cat-brand .sub-brand-item img {
    max-width: 80px;
    max-height: 40px;
}

/* icon hover category menu */
.hover-category-menu {
    position: absolute;
    width: 100%;
    display: none;
}

.hover-category-menu .category-sidebar1 {
    box-shadow: 0 3px 3px 0 rgba(51, 51, 51, 0.2);
    padding-top: 22px;
}

.hover-category-menu .category-sidebar1 .all-category {
    position: relative;
}

.hover-category-menu .category-sidebar1 .all-category::before {
    position: absolute;
    height: 8px;
    width: 8px;
    border-top: 0;
    border-right: 8px solid transparent;
    border-bottom: 8px solid #f2f2f2;
    border-left: 8px solid transparent;
    right: 20px;
}

.hover-category-menu .all-category {
    padding-left: 10px;
    line-height: 41px;
    height: 49px;
    line-height: 49px;
    color: #333;
    border-bottom: 1px solid #e9e9e9;
}

.hover-category-menu .all-category span {
    font-size: 14px;
}

.hover-category-menu .category-sidebar1 .sub-cat-menu {
    height: calc(100% - 8px);
    top: 8px;
    box-shadow: 2px 3px 5px 0 rgba(51, 51, 51, 0.2);
}

.hover-category-menu .category-sidebar1 .categories>li>a {
    padding: 8px 10px;
}

.notify-badge {
    position: absolute;
    right: 10px !important;
}
</style>

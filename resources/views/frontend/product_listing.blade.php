@extends('frontend.layouts.app')
@if (isset($subSubCategory))
    @php

        $meta_title = $subSubCategory->translation_meta_title;
        $meta_description = $subSubCategory->translation_meta_description;
    @endphp
@elseif (isset($subCategory))
    @php

        $meta_title = $subCategory->translation_meta_title;
        $meta_description = $subCategory->translation_meta_description;

    @endphp
@elseif (isset($category))
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

    @if (isset($sellers) && count($sellers) > 0)
        @include('frontend.partials.sellers_section', ['sellers' => $sellers])
    @endif
    <section class="gry-bg py-4">
        <div class="container sm-px-0">
            @if (!empty($products->items()))
                <form class="" id="search-form" action="{{ route('search') }}" method="GET">
                    <input type="hidden" name="q"
                        @isset($query) value="{{ $query }}" @endisset />
                    <input type="hidden" name="sort_by"
                        @isset($sort_by) value="{{ $sort_by }}" @endisset />
                    <input type="hidden" name="coupon_id"
                        @isset($coupon) value="{{ encrypt($coupon->id) }}" @endisset />
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
                                    @include('frontend.product_filter.category_filter')
                                    <!-- END -->

                                    <!-- Sub Category Filter -->
                                    @include('frontend.product_filter.subCategory_filter')
                                    <!-- END -->

                                    <!-- Brand Filter -->
                                    @include('frontend.product_filter.brand_filter')
                                    <!-- END -->

                                    <!-- Seller Filter -->
                                    @include('frontend.product_filter.seller_filter')
                                    <!-- END -->

                                    <!-- Color Filter -->
                                    @include('frontend.product_filter.color_filter')
                                    <!-- END -->
                                </div>
                                {{-- <button type="submit" class="btn btn-styled btn-block btn-base-4">Apply filter</button> --}}
                            </div>
                        </div>
                        <div class="col-xl-9">
                            @if (!empty($coupon))
                                @include('frontend.product_filter.coupon_filter')
                            @endif
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
                </form>
            @else
                <div class="d-flex flex-column align-items-center justify-content-center mt-5 mb-3">
                    <div class="w-25">
                        <img src="{{ my_asset('assets/frontend/images/result_search.png') }}" alt="ysg-result-search-img"
                            width="100%">
                    </div>
                    <div class="">
                        <h5 class="font-weight-bold">No results found</h5>
                    </div>
                    <div class="">
                        <h6>Try different or more general keywords</h6>
                    </div>
                </div>
            @endif
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

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
    <div>ssds</div>
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

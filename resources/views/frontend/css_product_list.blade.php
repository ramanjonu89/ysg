@extends('frontend.layouts.app')

@section('meta_title'){{ $meta['metaTitle'] }}@stop
@section('meta_description'){{ $meta['metaDescription'] }}@stop
@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta['metaTitle'] }}">
    <meta itemprop="description" content="{{ $meta['metaDescription'] }}">
    <!-- Twitter Card data -->
    <meta name="twitter:title" content="{{ $meta['metaTitle'] }}">
    <meta name="twitter:description" content="{{ $meta['metaDescription'] }}">
    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta['metaTitle'] }}"/>
    <meta property="og:description" content="{{ $meta['metaDescription'] }}"/>
@endsection

@section('content')
    <div class="breadcrumb-area">
        <div class="container">
            <div class="row">
                <div class="col">
                    <ul class="breadcrumb">
                        @foreach($breadCrumb as $item)
                            <li class="{{ $item['active']?'active':'' }}">
                                <a href="{{ $item['link'] }}">
                                    {{ $item['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <section class="gry-bg py-4">
        <div class="container sm-px-0">
            <form class="" id="search-form" action="{{ route('products.css') }}" method="GET">
                <div class="row">

                    @include('frontend.css_product_list.side_filter')

                    <div class="col-xl-9">
                        <!-- <div class="bg-white"> -->
                        @isset($requestQuery->category)
                            <input type="hidden" name="category" value="{{ $requestQuery->category }}">
                        @endisset
                        @isset($requestQuery->category)
                            <input type="hidden" name="subCategory" value="{{ $category_id }}">
                        @endisset
                        @isset($requestQuery->category)
                            <input type="hidden" name="subsubcategory"
                                   value="{{ \App\Models\SubSubCategory::find($subsubcategory_id)->slug }}">
                        @endisset

                        @include('frontend.css_product_list.top_filter')

                        @include('frontend.css_product_list.list')

                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@section('script')
    <script src="{{ static_asset('/js/view_more.js') }}"></script>
    <script type="text/javascript">
        function filter() {
            $('#search-form').submit();
        }
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

    .category-sidebar1 .all-category > span {
        font-size: 16px;
        margin-right: 130px;
        font-weight: 600;
        color: #fff;
    }

    .category-sidebar1 .all-category a {
        color: #fff;
        font-size: 12px;
    }

    .category-sidebar1 .categories > li {
        border: 0;
    }

    .category-sidebar1 .categories > li > a {
        padding: 8px 10px;
        color: #333;
        font-size: 16px;
        border: 1px solid transparent;
        position: relative;
    }

    .category-sidebar1 .categories > li:hover > a::before {
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

    .category-sidebar1 .categories > li:hover > a {
        border-color: #fff;
    }

    .category-sidebar1 .all-category a:hover {
        color: #fff !important;
    }

    .category-sidebar1 .categories > li > a .cat-image {
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

    .category-sidebar1 .categories > li:hover .sub-cat-menu {
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

    .hover-category-menu .category-sidebar1 .categories > li > a {
        padding: 8px 10px;
    }

    .notify-badge {
        position: absolute;
        right: 10px !important;
    }
</style>

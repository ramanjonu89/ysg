@extends('frontend.layouts.app')
@section('content')
<div class="all-category-wrap py-4 gry-bg">
    <div class="sticky-top">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="bg-white all-category-menu">
                        <ul class="d-flex flex-wrap no-scrollbar">
                            @if(count($categories) > 12)
                            @foreach($categories as $key =>  $category)
                                @if($category->status == 1)
                                <li class="@php if($key == 0) echo '' @endphp">
                                    <a href="#{{ $key }}" class="row no-gutters align-items-center">
                                        <div class="col-md-9">
                                            <div class="cat-name">{{ $category->getTranslationNameAttribute() }}</div>
                                        </div>
                                    </a>
                                </li>
                                @endif
                            @endforeach

                                {{-- @endfor --}}
                                <li class="">
                                    <a href="#more" class="row no-gutters align-items-center">
                                        <div class="col-md-3">
                                            <i class="fa fa-ellipsis-h cat-icon"></i>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="cat-name">{{ __('More Categories') }}</div>
                                        </div>
                                    </a>
                                </li>
                                @else
                                @foreach ($categories as $key => $category)
                                @if ($category->status == 1)
                                <li class="@php if($key == 0) echo '' @endphp">
                                    <a href="#{{ $key }}" class="row no-gutters align-items-center">
                                        <!-- <div class="col-md-3">
                                            <img loading="lazy"  class="cat-image" src="{{ my_asset($category->icon) }}">
                                        </div>-->
                                        <div class="col-md-9">
                                            <div class="cat-name">{{  $category->getTranslationNameAttribute() }}</div>
                                        </div>
                                    </a>
                                </li>
                                @endif
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div class="container">
                @foreach ($categories as $key => $category)
                @if(count($categories)>12 && $key == 11)
                <div class="mb-3 bg-white">
                    <div class="sub-category-menu " id="more">
                        <h3 class="category-name border-bottom pb-2"><a href="{{ route('category.view', $category->slug) }}">{{ $category->getTranslationNameAttribute() }}</a></h3>
                        <div class="row">
                            @foreach ($category->subCategories as $key => $subcategory)
                            <div class="col-lg-4 col-6">
                                <h6 class="mb-3"><a href="{{ route('category.view', ['category_slug' => $category->slug ,'subcategory_slug' => $subcategory->slug]) }}">{{ $subcategory->getTranslationNameAttribute() }}</a></h6>
                                <ul class="mb-3">
                                    @foreach ($subcategory->subSubcategories as $key => $subsubcategory)
                                    <li class="w-100"><a href="{{ route('category.view', ['category_slug' => $category->slug,'subcategory_slug' => $subcategory->slug,'subsubcategory_slug' => $subsubcategory->slug]) }}" >{{  $subsubcategory->getTranslationNameAttribute()  }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="mb-3 bg-white">
                    <div class="sub-category-menu @php if($key < 12) echo ''; @endphp" id="{{ $key }}">
                        <h3 class="category-name border-bottom pb-2"><b><a href="{{ route('category.view', $category->slug) }}" >{{  $category->getTranslationNameAttribute() }}</a></b></h3>
                        <div class="row">
                            @foreach ($category->subCategories as $key => $subcategory)
                            @if ($subcategory->status === 1)
                            <div class="col-lg-4 col-6">
                                <h5 class="mb-3" ><a href="{{ route('category.view', ['category_slug' => $category->slug ,'subcategory_slug' => $subcategory->slug]) }}">{{ $subcategory->getTranslationNameAttribute() }}</a></h5>
                                <ul class="mb-3">
                                    @foreach ($subcategory->subSubcategories as $key => $subsubcategory)
                                    @if ($subsubcategory->status === 1)
                                    <h6 class="mb-3" > <li class="w-100"><a href="{{ route('category.view', ['category_slug' => $category->slug,'subcategory_slug' => $subcategory->slug,'subsubcategory_slug' => $subsubcategory->slug]) }}" >{{ $subsubcategory->getTranslationNameAttribute() }}</a></li></h6>
                                    @endif
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                           @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
         <div class="mt-4">
            <div class="container">
                @if (!isMicrosite())
                <div class="col-lg-12">
                    <div class="section-title-1 clearfix">
                        <h3 class="heading-5 strong-700 mb-0 float-left">
                            <span class="mr-4">{{ __('Shop by Brands') }}</span>
                        </h3>
                        <ul class="float-right inline-links">
                            <li>
                                <a href="{{ route('brands.all') }}" class="">{{ __('View All Brands') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="row gutters-5">
                        @foreach ($topBrands as $brand)
                        <div class="mb-3 col-6">
                            <a href="{{ URL::route('search', array('brand_ids' => [$brand->id] )) }}" class="bg-white border d-block c-base-2 box-2 icon-anim pl-2">
                                <div class="row align-items-center no-gutters">
                                    <div class="col-3 text-center">
                                        <img src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($brand->logo) }}" alt="{{ __($brand->name) }}" class="img-fluid img lazyload">
                                    </div>
                                    <div class="info col-7">
                                        <div class="name text-truncate pl-3 py-4">{{ $brand->name }}</div>
                                    </div>
                                    <div class="col-2 text-center">
                                        <i class="la la-angle-right c-base-1"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endsection

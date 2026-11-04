@extends('frontend.layouts.app')
@section('content')
    <div class="py-4 gry-bg">
        <div class="mt-4">
            <div class="container">
                <div class="bg-white px-3 pt-3">
                    <div class="row gutters-10">
                        @foreach ($brands as $brand)
                            <div class="col-xxl-2 col-lg-4 col-sm-6 text-center">
                                <a href="{{ URL::route('search', array('brand_ids' => [$brand->id] )) }}" class="d-block p-3 mb-3 border rounded">
                                    <img src="{{ my_asset($brand->logo) }}" class="lazyload img-fit" height="50" alt="{{  translate($brand->name) }}">
                                    <div class="name text-truncate"><b>{{ __($brand->name) }}</b></div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="row" style="margin-top:100px;">
                    <div class="col-lg-12">
                        <div class="section-title-1 clearfix">
                            <h3 class="heading-5 strong-700 mb-0 float-left">
                                <span class="mr-4">{{ __('Shop by Categories') }}</span>
                            </h3>
                            <ul class="float-right inline-links">
                                <li>
                                    <a href="{{ route('categories.all') }}" class="active">{{ __('View All Categories') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="row gutters-5">
                            @foreach ($topCategories as $category)
                                <div class="mb-3 col-6">
                                    <a href="{{ route('category.view', $category->slug) }}" class="bg-white border d-block c-base-2 box-2 icon-anim pl-2">
                                        <div class="row align-items-center no-gutters">
                                        <!--<div class="col-3 text-center">

                                        <img src="{{ my_asset('frontend/images/placeholder.jpg') }}" data-src="{{ my_asset($category->banner) }}" alt="{{ $category->getTranslationNameAttribute() }}" class="img-fluid img lazyload">

                                    </div>-->
                                            <div class="info col-10">
                                                <div class="name text-truncate pl-3 py-4">{{ $category->getTranslationNameAttribute()  }}</div>
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
                </div>
            </div>
        </div>
    </div>
@endsection

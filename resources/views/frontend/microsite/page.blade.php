@extends('frontend.layouts.app')

@section('meta_title'){{ getMicrosite()->meta_title }}@stop
@section('meta_description'){{ getMicrosite()->meta_description }}@stop
@section('meta_keywords'){{ getMicrosite()->tags }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ getMicrosite()->meta_title }}">
    <meta itemprop="description" content="{{ getMicrosite()->meta_description }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="page">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ getMicrosite()->meta_title }}">
    <meta name="twitter:description" content="{{ getMicrosite()->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ getMicrosite()->meta_title }}" />
    <meta property="og:type" content="page" />
    <meta property="og:description" content="{{ getMicrosite()->meta_description }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
@endsection

@section('content')
<section class="gry-bg py-4">
	<div class="container">
        <div class="row">
            <div class="col">
                <div class="p-4 bg-white">
                    {!! $content !!}
                </div>
            </div>
        </div>
	</div>
</section>

@endsection


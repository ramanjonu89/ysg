@extends('frontend.layouts.app')

@section('content')
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @if($user->user_type == 'seller')
                        @include('frontend.inc.seller_side_nav')
                    @elseif($user->user_type == 'customer')
                        @include('frontend.inc.customer_side_nav')
                    @endif
                </div>

                <div class="col-lg-9">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-6 col-12">
                                    <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                        {{ __('Referral') }}
                                    </h2>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="float-md-right">
                                        @include('frontend.inc.breadcrumb', ['breadcrumbs' => [
                                            [
                                                'link' => route('dashboard'),
                                                'text' => __('Dashboard')
                                            ],
                                            [
                                                'link' => route('referral.index'),
                                                'text' => __('Referral')
                                            ],
                                        ]])
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if($user->user_type == 'seller')
                                <div class="col-md-6">
                                    <div class="card no-border mt-4">
                                        <div class="card-header py-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-12 col-xl-12">
                                                    <h6 class="mb-0">{{ __('Referral') }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">

                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <div class="card no-border mt-4">
                                    <div class="card-header py-2">
                                        <div class="row align-items-center">
                                            <div class="col-md-12 col-xl-12">
                                                <h6 class="mb-0">{{ __('Corporate Access Sharing') }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @include('frontend.inc.customer_side_nav')
                </div>
                <div class="col-lg-9">
                    <!-- Page title -->
                    <div class="page-title">
                        <div class="row align-items-center">
                            <div class="col-md-6 col-12">
                                <h2 class="heading heading-6 text-capitalize strong-600 mb-0">
                                    {{ __('Corporate Access Pass') }}
                                </h2>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="float-md-right">
                                    @include('frontend.inc.breadcrumb', ['breadcrumbs' => [
                                        [
                                            'link' => route('referral.access'),
                                            'text' => __('Corporate Access Pass')
                                        ]
                                    ]])
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="card col-md-12">
                            <div class="card-body">
                                <ul class="nav nav-pills mb-3" id="corporate-access-pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Available Access Passes</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Corporate Access</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                        <p class="text-info">*Your Time-limited Corporate Access is valid for 24 hours upon activation.</p>
                                        <div class="row d-flex">
                                            @foreach($userCorporateAccesses as $access)
                                                @if($access->expire_at && $access->expire_at->lt(\Carbon\Carbon::now()))
                                                    <div class="col-sm-6 mb-4">
                                                        <div class="card h-100">
                                                            <div class="card-body red-widget text-white border d-flex justify-content-between">
                                                                <div>
                                                                    <h5 class="card-title">Access for: {{ $access->domain }}</h5>
                                                                    <p class="card-text mb-0">
                                                                        <strong>Duration: {{ \App\Utility\DateTimeHelper::getDurationString($access->duration) }}</strong>
                                                                    </p>
                                                                    @if($access->expire_at)
                                                                        <p class="card-text mb-0">
                                                                            <strong>Expired at: {{ $access->expire_at->format('Y-m-d H:i:s') }}</strong>
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                                <div class="d-flex align-items-center">
                                                                    <span style="font-size: 14px" class="badge badge-pill badge-light text-danger">Expired!</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($access->is_active)
                                                    <div class="col-sm-6 mb-4">
                                                        <div class="card h-100">
                                                            <div class="card-body green-widget text-white border d-flex justify-content-between">
                                                                <div>
                                                                    <h5 class="card-title">Access for: {{ $access->domain }}</h5>
                                                                    <p class="card-text mb-0">
                                                                        <strong>Duration: {{ \App\Utility\DateTimeHelper::getDurationString($access->duration) }}</strong>
                                                                    </p>
                                                                    @if($access->expire_at)
                                                                        <p class="card-text mb-0">
                                                                            <strong>Expire at: {{ $access->expire_at->format('Y-m-d H:i:s') }}</strong>
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                                <div class="d-flex align-items-center">
                                                                    <span style="font-size: 14px" class="badge badge-pill badge-light text-success">Activated</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-sm-6 mb-4">
                                                        <div class="card h-100">
                                                            <div class="card-body {{ $access->is_active ? 'green-widget text-white' : '' }} border d-flex justify-content-between">
                                                                <input type="hidden" name="access_id" value="{{ $access->id }}">
                                                                <div>
                                                                    <h5 class="card-title">Access for: {{ $access->domain }}</h5>
                                                                    <p class="card-text">
                                                                        <strong>Duration: {{ \App\Utility\DateTimeHelper::getDurationString($access->duration) }}</strong>
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex align-items-center">
                                                                    <button {{ !$canActivate ? 'disabled' : '' }}
                                                                            data-active-url="{{ route('api.corporateAccess.activate', ['user'=>$currentUser, 'access'=>$access]) }}"
                                                                            class="btn btn-base-1 activate-access-btn">Activate</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                        <h6 id="access_countdown" class="text-center text-danger mb-0">
                                        </h6>
                                        @if(!empty($sellers) && $corporateAccessExpireTime->greaterThan(\Carbon\Carbon::now()))
                                            <h6 class="text-underline text-bold mt-5">{{ __('Corporate Sale Stores Access:') }}</h6>
                                            <div class="row">
                                                @foreach($sellers as $seller)
                                                    <div class="col-md-6">
                                                        <div class="caorusel-card my-1">
                                                            <div class="row no-gutters box-3 align-items-center border">
                                                                <div class="col-4">
                                                                    <a href="{{ route('shop.visit', $seller->user->shop->slug) }}" class="d-block product-image p-3">
                                                                        <img
                                                                            src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                                                            data-src="@if ($seller->user->shop->logo !== null) {{ my_asset($seller->user->shop->logo) }} @else {{ my_asset('frontend/images/placeholder.jpg') }} @endif"
                                                                            alt="{{ $seller->user->shop->name }}"
                                                                            class="img-fluid lazyload"
                                                                        >
                                                                    </a>
                                                                </div>
                                                                <div class="col-8 border-left">
                                                                    <div class="p-3">
                                                                        <h2 class="product-title mb-0 p-0 text-truncate">
                                                                            <a href="{{ route('shop.visit', $seller->user->shop->slug) }}">{{ translate($seller->user->shop->name) }}</a>
                                                                        </h2>
                                                                        <div class="">
                                                                            <a href="{{ route('shop.visit', $seller->user->shop->slug) }}" class="icon-anim">

                                                                                {{ __('Visit Store') }} <i class="la la-angle-right text-sm"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function () {
            $('#access_countdown').countdown('{{ $corporateAccessExpireTime ? $corporateAccessExpireTime->format('Y/m/d H:i:s') : '' }}')
                .on('update.countdown', function(event) {
                    var format = '%H hours %M mins %S sec';
                    format = '%-d day%!d ' + format;
                    if(event.offset.weeks > 0) {
                        format = '%-w week%!w ' + format;
                    }
                    $(this).html(event.strftime('Your Corporate Sale Store Access will expire in: ' + format));
                })
                .on('finish.countdown', function(event) {
                    $(this).html(
                        '{{ __('Oops! You can’t access the Corporate Sales Store now either because your access pass has not been activated or it has already expired.') }}' +
                        '<br/>' +
                        '{{ __('You can still however, enjoy great savings from YSG Marketplace with your existing account.') }}'
                    )
                    .parent().addClass('disabled');
                });


            $('body').on('click', '.activate-access-btn', function () {
                const url = $(this).data('active-url');

                $.ajax({
                    url: url,
                    headers: {
                        "Authorization": "Bearer " + "{{ Session::get('laravel_token') }}",
                    },
                    method: 'POST',
                    dataType: 'json',
                    beforeSend: function () {
                        $.LoadingOverlay("show", {
                            size: 10,
                            image: "",
                            fontawesome: "fa fa-cog fa-spin"
                        });
                    },
                    success: function (response) {
                        if (response.expire_at) {
                            sessionStorage.setItem('corporateAccessExpire', response.expire_at);
                        }
                        setTimeout(function () {
                            $.LoadingOverlay('hide');
                            swal({
                                text: response.message,
                                type: "success",
                                allowOutsideClick: false
                            }).then(function () {
                                window.location.reload();
                            });
                        }, 500)
                    },
                    error: function (xhr) {
                        $.LoadingOverlay('hide');
                        setTimeout(function () {
                            swal({
                                text: xhr.responseJSON.message,
                                type: "error",
                                allowOutsideClick: false
                            }).then(function (result) {
                                window.location.reload();
                            });
                        },500)
                    }
                });
            });
        })
    </script>
@endsection

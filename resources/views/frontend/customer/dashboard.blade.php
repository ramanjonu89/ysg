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
                                    {{ __('Dashboard') }}
                                </h2>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="float-md-right">
                                    @include('frontend.inc.breadcrumb', ['breadcrumbs' => [
                                        [
                                            'link' => route('dashboard'),
                                            'text' => __('Dashboard')
                                        ],

                                    ]])
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- dashboard content -->
                    <div class="" id="dashboard-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="dashboard-widget text-center green-widget mt-4 c-pointer widget-cart">
                                    <a href="{{ route('cart') }}" class="d-block">
                                        <i class="fa fa-shopping-cart"></i>
                                        @if($totalItems > 0)
                                            <span
                                                class="d-block title">{{ $totalItems }} {{ __('Product(s)') }}</span>
                                        @else
                                            <span class="d-block title">0 {{ __('Product') }}</span>
                                        @endif
                                        <span class="d-block sub-title">{{ __('In your cart') }}</span>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="dashboard-widget text-center red-widget mt-4 c-pointer">
                                    <a href="{{ route('wishlists.index') }}" class="d-block">
                                        <i class="fa fa-heart"></i>
                                        <span
                                            class="d-block title">{{ $totalWishlists }} {{ __('Product(s)') }}</span>
                                        <span class="d-block sub-title">{{ __('in your wishlist') }}</span>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="dashboard-widget text-center yellow-widget mt-4 c-pointer">
                                    <a href="{{ route('purchase_history.index') }}" class="d-block">
                                        <i class="fa fa-building"></i>
                                        <span class="d-block title">{{ $totalOrders }} {{ __('Order(s)') }}</span>
                                        <span class="d-block sub-title">{{ __('you ordered') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row d-flex mt-4">
                            <!-- Referral -->
                            @if($referralEnabled->value)
                                <div class="col-md-6">
                                    <div class="card no-border h-100">
                                        <div class="card-header py-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-12 col-xl-12">
                                                    <h6 class="mb-0">{{ __('Earn as you share') }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="text-center">
                                                {{ __('Gift your friend a voucher to shop at YSG and be rewarded with a discount voucher for your next YSG purchase.') }}
                                            </h6>
                                            <div class="form-group">
                                                <div class="col-md-10 offset-md-1">
                                                    <div class="input-group">
                                                        <input id="referral_link" type="text" class="form-control bg-white" readonly value="{{ $referralLink }}" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                                        <div class="input-group-append" style="cursor: pointer">
                                                            <span id="copy_referral_link" data-toggle="tooltip" data-placement="top" title="Copy to clipboard" class="input-group-text">Copy</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row d-flex justify-content-center align-items-center">
                                                <p class="m-0">Share via:</p>
                                                <ul class="my-3 my-md-0 social-nav model-2 m-0">
                                                    <li>
                                                        <a class="google-plus" target="_blank" data-toggle="modal" data-target="#share_via_email_modal">
                                                            <i class="fa fa-envelope"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="https://api.whatsapp.com/send?text={{ $referralLink }}" class="whatsapp" target="_blank" data-toggle="tooltip" data-original-title="Whatsapp">
                                                            <i class="fa fa-whatsapp"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Corporate Access Sharing -->
                            @if($corporateRefEnabled && $canShareCA)
                                <div class="col-md-6">
                                    <div class="card no-border h-100">
                                        <div class="card-header py-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-12 col-xl-12">
                                                    <h6 class="mb-0">{{ __(sprintf('Corporate Store Access Pass (%s/%s Access Passes left)', $corporateRefLeft, $corporateRefLimit)) }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="text-center">
                                                {{ __('Share your exclusive Corporate Sale Store savings with your family and friends. Each time-limited pass is only valid for 24 hours.') }}
                                            </h6>
                                            <div class="row d-flex justify-content-center align-items-center">
                                                <p class="m-0">Share via:</p>
                                                <ul class="my-3 my-md-0 social-nav model-2 m-0" id="corporate_sharing_social_list">
                                                    <li>
                                                        @if($corporateRefLeft > 0)
                                                            <a data-toggle="modal" data-target="#share_corporate_email_modal" class="google-plus">
                                                                <i class="fa fa-envelope"></i>
                                                            </a>
                                                        @else
                                                            <a class="google-plus">
                                                                <i class="fa fa-envelope"></i>
                                                            </a>
                                                        @endif
                                                    </li>
                                                    <li class="d-none">
                                                        <a href="https://api.whatsapp.com/send?text={{ 'abc' }}" class="whatsapp" target="_blank" data-toggle="tooltip" data-original-title="Whatsapp">
                                                            <i class="fa fa-whatsapp"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <div class="card no-border h-100">
                                    <div class="card-header py-2">
                                        <div class="row align-items-center">
                                            <div class="col-md-12 col-xl-12">
                                                <h6 class="mb-0 text-center">{{ __('Referral Code') }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="text-center">
                                            {{ __('Unlock rewards together: Share your unique Referral code for double the benefits! Win - win referral for you and your lucky connections') }}
                                        </h6>
                                        <div class="row d-flex justify-content-center align-items-center">
                                            <div class="bg-red mb-sm-2" id="referral_code_register">{{ Auth::user()->referral_code_register }}</div>
                                        </div>
                                        <div class="row d-flex justify-content-center align-items-center">
                                            <p class="m-0">Share code:</p>
                                            <ul class="my-3 my-md-0 social-nav model-2 m-0" id="share_referral_code">
                                                <li>
                                                    <a data-toggle="modal" data-target="#shareReferralCode" class="google-plus">
                                                        <i class="fa fa-envelope"></i>
                                                    </a>
                                                </li>
                                                <li class="d-none">
                                                    <a href="https://api.whatsapp.com/send?text={{ 'code' }}" class="whatsapp" target="_blank" data-toggle="tooltip" data-original-title="Whatsapp">
                                                        <i class="fa fa-whatsapp"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if (\App\BusinessSetting::where('type', 'classified_product')->first()->value)
                                <div class="col-md-6">
                                    <div class="form-box bg-white mt-4">
                                        <div class="form-box-title px-3 py-2 clearfix ">
                                            {{ __('Purchased Package') }}
                                        </div>
                                        @php
                                            $customer_package = \App\CustomerPackage::find(Auth::user()->customer_package_id);
                                        @endphp
                                        <div class="form-box-content p-3">
                                            @if($customer_package != null)
                                                <div class="form-box-content p-2 category-widget text-center">
                                                    <center><img alt="Package Logo"
                                                                 src="{{ my_asset($customer_package->logo) }}"
                                                                 style="height:100px; width:90px;"></center>
                                                    <br>
                                                    <left><strong><p>{{ __('Product Upload') }}
                                                                : {{ $customer_package->product_upload }} {{ __('Times') }}</p>
                                                        </strong></left>
                                                    <strong><p>{{ __('Product Upload Remaining') }}
                                                            : {{ Auth::user()->remaining_uploads }} {{ __('Times') }}</p>
                                                    </strong>
                                                    <strong><p>
                                                        <div class="name mb-0">{{ __('Current Package') }}
                                                            : {{ $customer_package->name }} <span class="ml-2"><i
                                                                    class="fa fa-check-circle" style="color:green"></i></span>
                                                        </div>
                                                        </p></strong>
                                                </div>
                                            @else
                                                <div class="form-box-content p-2 category-widget text-center">
                                                    <center><strong><p>{{ __('Package Not Found') }}</p></strong>
                                                    </center>
                                                </div>
                                            @endif
                                            <div class="text-center">
                                                <a href="{{ route('customer_packages_list_show') }}"
                                                   class="btn btn-styled btn-base-1 btn-outline btn-sm">{{ __('Upgrade Package') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="share_via_email_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">I’d like to share a voucher with:</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <select class="form-control" id="referralEmails" multiple="multiple"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_send_ref_email" class="btn btn-primary">Share</button>
                </div>
            </div>
        </div>
    </div>
    @if($corporateRefLeft > 0)
        <div class="modal fade" id="share_corporate_email_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Send a Corporate Sale Store Access Pass to:</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <select class="form-control" id="referralAccessEmails" multiple="multiple"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_send_corporate_ref_email" class="btn btn-primary">Share</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="modal fade" id="shareReferralCode" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Send a Referral code:</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 pr-0 pl-0">
                        <select class="form-control" id="referral-code" multiple="multiple"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_send_referral_code" class="btn btn-primary">Share</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        #referral_code_register {
            padding: 0.4rem 3rem;
        }

        .share-referral-code{
            width: 100%;
            padding: 0.4rem;
        }

    </style>
    <script>
        var corporateRefLeft = {{ $corporateRefLeft }};

        $(document).ready(function () {
            function copyToClipboard(event) {
                var element = event.data.element;
                var target = event.data.target;
                element.select();
                element.setSelectionRange(0, 99999)
                document.execCommand("copy");

                target.tooltip('dispose');
                target.attr('title', 'Copied');
                target.tooltip('show');

                target.on('hidden.bs.tooltip', function () {
                    target.tooltip('dispose');
                    target.attr('title', 'Copy to clipboard');
                    target.tooltip();
                })
            }

            $('#copy_referral_link').bind('click', {
                element: $('#referral_link').get(0),
                target: $('#copy_referral_link')
            }, copyToClipboard);

            $("#referralEmails").select2({
                placeholder: "Key in email addresses here",
                tags: true,
                selectOnClose: true
            });
            $('#referralAccessEmails').select2({
                placeholder: "Key in email addresses here",
                tags: true,
                selectOnClose: true
            });

            $('#referral-code').select2({
                placeholder: "Key in email addresses here",
                tags: true,
                selectOnClose: true
            });

            if (corporateRefLeft <= 0) {
                $('#corporate_sharing_social_list a').each(function () {
                    $(this).unbind('click').bind('click', function (e) {
                        e.preventDefault();
                        swal({
                            text: '{{ __('Your corporate access sharing has exceeded the limit!') }}',
                            type: 'error'
                        })
                    })
                });
            }

            $('#btn_send_referral_code').prop("disabled", true);

            $('#referral-code').on('change', function() {
                if (this.value !== "undefined" && this.value) {
                    $('#btn_send_referral_code').prop("disabled", false);
                } else {
                    $('#btn_send_referral_code').prop("disabled", true);
                }
            });
        });

        var senderName = '{{ $currentUser->name }}';

        $('#btn_send_ref_email').click(function() {
            if (!senderName) {
                swal({
                    title: '',
                    html: "{!! __("Please update your profile's name before continue.") !!}",
                    type: "error",
                });

                return false;
            }

            var data = {
                referral_link: '{{ $referralLink }}',
                emails: $('#referralEmails').val() ? $('#referralEmails').val() : [],
                type: {{ \App\BusinessSetting::REFERRAL_TYPE_NORMAL }},
                sender_id: {{ $currentUser->id }}
            };
            $.ajax({
                url: "{{ route('api.referral.send_mail') }}",
                headers: {
                    "Authorization": "Bearer " + "{{ Session::get('laravel_token') }}",
                },
                method: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    $.LoadingOverlay("show", {
                        size: 10,
                        image: "",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
                complete: function () {
                    $.LoadingOverlay('hide');
                    $('#share_via_email_modal').modal('hide');
                },
                success: function (response) {
                    setTimeout(function () {
                        swal({
                            title: '',
                            html: response.message,
                            type: "success",
                        })
                    },500)
                },
                error: function (xhr) {
                    var message = xhr.responseJSON.message
                    setTimeout(function () {
                        swal({
                            text: message,
                            type: "error",
                        });
                    },500)
                }
            });
        });
        $('#btn_send_corporate_ref_email').click(function() {
            if (!senderName) {
                swal({
                    title: '',
                    html: "{!! __("Please update your profile's name before continue.") !!}",
                    type: "error",
                });

                return false;
            }
            var data = {
                emails: $('#referralAccessEmails').val() ? $('#referralAccessEmails').val() : [],
            };
            $.ajax({
                url: "{{ route('api.corporateAccess.send_mail') }}",
                headers: {
                    "Authorization": "Bearer " + "{{ Session::get('laravel_token') }}",
                },
                method: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    $.LoadingOverlay("show", {
                        size: 10,
                        image: "",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
                complete: function () {
                    $.LoadingOverlay('hide');
                    $('#share_corporate_email_modal').modal('hide');
                },
                success: function (response) {
                    setTimeout(function () {
                        swal({
                            title: '',
                            html: response.message,
                            type: "success",
                        })
                    },500)
                },
                error: function () {
                    setTimeout(function () {
                        swal({
                            text: "An error occured!",
                            type: "error",
                        });
                    },500)
                }
            });
        });

        $('#btn_send_referral_code').click(function() {
            if (!senderName) {
                swal({
                    title: '',
                    html: "{!! __("Please update your profile's name before continue.") !!}",
                    type: "error",
                });

                return false;
            }
            var data = {
                emails: $('#referral-code').val() ? $('#referral-code').val() : [],
            };
            $.ajax({
                url: "{{ route('api.referral.send_referral_code') }}",
                headers: {
                    "Authorization": "Bearer " + "{{ Session::get('laravel_token') }}",
                },
                method: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    $.LoadingOverlay("show", {
                        size: 10,
                        image: "",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
                complete: function () {
                    $.LoadingOverlay('hide');
                    $('#shareReferralCode').modal('hide');
                },
                success: function (response) {
                    setTimeout(function () {
                        if (response.status == 1) {
                            swal({
                                title: '',
                                html: response.message,
                                type: "success",
                            })
                        } else {
                            swal({
                                title: '',
                                html: response.message,
                                type: "error",
                            })
                        }
                    },500)
                }
            });
        });
    </script>
@endsection


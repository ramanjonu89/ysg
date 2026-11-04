@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg bg-confirm-age">
        <div class="profile">
            <div class="bg-blur-overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            <div class="px-5 py-3 py-lg-4">
                                <button type="button" class="close absolute-close-btn" data-dismiss="modal" aria-label="Close" id="btn_close_modal" data-redirect="{{ getAppHomepageLink() }}"">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <form action="{{ route('age_restricted.confirm_age_restricted', ['slug' => $slug, 'shop' => $shop]) }}" method="POST">
                                    @csrf
                                    <div class="title-confirm-restricted-age">
                                        <div class="text-confirm-restricted-age">
                                            {!! @$content['top_content'] !!}
                                        </div>
                                        <div class="button-confirm">
                                            <button id="btn_confirm_restricted_age"
                                                class="btn btn-restricted-age btn-confirm-age">{{ __('Yes') }}</button>
                                            <a id="btn_cancel_confirm_restricted_age"
                                                class="btn btn-restricted-age btn-cancel-confirm-age"
                                                data-redirect="{{ getAppHomepageLink() }}">
                                                {{ __('No') }}
                                            </a>
                                        </div>
                                        <div class="warning-notice-restricted-age">
                                            {!! @$content['body_content'] !!}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .title-confirm-restricted-age {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 0.875rem;
        }

        .button-confirm {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1rem;
        }

        .btn-confirm-age {
            background-color: #FF825B;
            margin-right: 1.5rem;
            border: 1px solid #E15023;
        }

        .btn-cancel-confirm-age {
            background-color: #ED4C67;
            border: 2px solid #FF825B;
        }

        .btn-restricted-age {
            border-radius: 0.25rem;
            padding: 0.625rem 2rem;
            font-size: 1.0625rem;
            color: #fff;
        }

        .text-confirm-restricted-age p,
        .warning-notice-restricted-age p {
            margin-bottom: 0 !important;
        }

        .bg-blur-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 130%;
            backdrop-filter: blur(15px); 
            background-color: rgba(0, 0, 0, .5);
            z-index: 800;
        }

        .card {
            background-color: white;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
@section('script')
    <script type="text/javascript">
        $('#btn_cancel_confirm_restricted_age').click(function() {
            window.location.href = $(this).data('redirect');
        });

        $('#btn_close_modal').click(function() {
            window.location.href = $(this).data('redirect');
        });
    </script>
@endsection

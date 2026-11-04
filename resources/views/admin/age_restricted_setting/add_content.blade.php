@extends('layouts.app')
@section('content')

<div class="row">
    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title strong-600">{{ __('18 Years & Above') }}</h3>
            </div>
            <!--Horizontal Form-->
            <form class="form-horizontal age-restricted" action="{{ route('age_restricted.add_content', ['age' => '18']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name">{{ __('Top Body Text') }}</label>
                        <div class="col-sm-10">
                            <textarea id="top_content_18" class="editor" name="top_content"> @isset($content_eighteen) {{ $content_eighteen->top_content }} @endisset </textarea>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-sm-2 control-label" for="name">{{ __('Body Text') }}</label>
                        <div class="col-sm-10">
                            <textarea id="body_content_18" class="editor" name="body_content"> @isset($content_eighteen) {{ $content_eighteen->body_content }} @endisset </textarea>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
            <!--End Horizontal Form-->
        </div>
    </div>
    <div class="col-lg-5">
        <div class="panel pb-1 display-content-18 d-none">
            <div class="panel-heading">
                <h3 class="panel-title strong-600">{{ __('Display 18 Years & Above') }}</h3>
            </div>
            <div class="modal-content position-relative">
                <div class="modal-body content-age-restricted">
                    <div class="title-confirm-restricted-age">
                        <div class="display-top-content-18-default">

                        </div>
                        <div class="display-top-content-18 d-none">

                        </div>
                        <div class="button-confirm">
                            <button id="btn_confirm_restricted_age" class="btn btn-restricted-age btn-confirm-age">{{ __('Yes') }}</button>
                            <button id="btn_cancel_confirm_restricted_age" class="btn btn-restricted-age btn-cancel-confirm-age">{{ __('No') }}</button>
                        </div>
                        <div class="display-body-content-18-default">

                        </div>
                        <div class="display-body-content-18 d-none">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title strong-600">{{ __('21 Years & Above') }}</h3>
            </div>
            <!--Horizontal Form-->
            <form class="form-horizontal age-restricted" action="{{ route('age_restricted.add_content', ['age' => '21']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name">{{ __('Top Body Text') }}</label>
                        <div class="col-sm-10">
                            <textarea id="top_content_21" class="editor" name="top_content"> @isset($content_twentyone) {{ $content_twentyone->top_content }} @endisset </textarea>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="col-sm-2 control-label" for="name">{{ __('Body Text') }}</label>
                        <div class="col-sm-10">
                            <textarea id="body_content_21" class="editor" name="body_content"> @isset($content_twentyone) {{ $content_twentyone->body_content }} @endisset </textarea>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
            <!--End Horizontal Form-->
        </div>
    </div>
    <div class="col-lg-5">
        <div class="panel pb-1 display-content-21 d-none">
            <div class="panel-heading">
                <h3 class="panel-title strong-600">{{ __('Display 21 Years & Above') }}</h3>
            </div>
            <div class="modal-content position-relative">
                <div class="modal-body content-age-restricted">
                    <div class="title-confirm-restricted-age">
                        <div class="display-top-content-21-default">

                        </div>
                        <div class="display-top-content-21 d-none">

                        </div>
                        <div class="button-confirm">
                            <button id="btn_confirm_restricted_age" class="btn btn-restricted-age btn-confirm-age" id="confirm-restricted-age">{{ __('Yes') }}</button>
                            <button id="btn_cancel_confirm_restricted_age" class="btn btn-restricted-age btn-cancel-confirm-age">{{ __('No') }}</button>
                        </div>
                        <div class="display-body-content-21-default">

                        </div>
                        <div class="display-body-content-21 d-none">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .age-restricted .jodit_container .jodit_workplace {
        min-height: 150px !important;
    }
    .content-age-restricted {
        width: 550px;
        border: 1px solid #dee2e6 !important;
        margin: 2rem 5rem;
    }

    .modal-body {
        padding: 1rem 5rem !important;
    }

    .title-confirm-restricted-age {
        display: flex;
        flex-direction: column;
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
        border: 1px solid #FF825B;
    }

    .btn-cancel-confirm-age {
        background-color: #ED4C67;
        border: 2px solid #ED4C67;
    }

    .btn-restricted-age {
        border-radius: 0.25rem;
        padding:0.625rem 2rem;
        font-size: 17px;
        font-weight: 600;
        color: #fff;
    }
    .d-none {
        display: none;
    }
</style>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            const top_content_18  = `{!! isset($content_eighteen)  ? $content_eighteen->top_content   : '' !!}`;
            const body_content_18 = `{!! isset($content_eighteen)  ? $content_eighteen->body_content  : '' !!}`;
            const top_content_21  = `{!! isset($content_twentyone) ? $content_twentyone->top_content  : '' !!}`;
            const body_content_21 = `{!! isset($content_twentyone) ? $content_twentyone->body_content : '' !!}`;

            $('.display-top-content-18-default').html(top_content_18);
            $('.display-body-content-18-default').html(body_content_18);
            $('.display-top-content-21-default').html(top_content_21);
            $('.display-body-content-21-default').html(body_content_21);

            const content = {
                18: `{!! isset($content_eighteen) ? $content_eighteen : '' !!}`,
                21: `{!! isset($content_twentyone) ? $content_twentyone : '' !!}`
            };

            Object.keys(content).forEach(function(key) {
                if (content[key]) {
                    $(`.display-content-${key}`).removeClass('d-none');
                }
            });
        });

        $("[id^='top_content'], [id^='body_content']").change(function() {
            const contentType = this.id.includes("top") ? "top" : "body";
            const contentNumber = this.id.split('_')[2];
            
            $('.display-' + contentType + '-content-' + contentNumber + '-default').addClass('d-none');
            $('.display-' + contentType + '-content-' + contentNumber).removeClass('d-none').html(this.value);
            if (this.value) {
                $('.display-content-' + contentNumber).removeClass('d-none');
            } else {
                $('.display-content-' + contentNumber).addClass('d-none');
            }
        });
    </script>
@endsection


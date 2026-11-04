<div class="modal fade bg-modal-confirm-age" id="confirmed_restricted_age" style="padding-right: 17px" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size"
    role="document">
        <div class="modal-content position-relative">
            <button type="button" class="close absolute-close-btn" id="btn_close_modal" data-dismiss="modal" aria-label="Close" data-redirect="{{ getAppHomepageLink() }}">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body border-0 rounded-bottom">
                <div class="title-confirm-restricted-age">
                    <div class="text-confirm-restricted-age">

                    </div>
                    <div class="button-confirm">
                        <button id="btn_confirm_restricted_age" class="btn btn-restricted-age btn-confirm-age">{{ __('Yes') }}</button>
                        <button id="btn_cancel_confirm_restricted_age" class="btn btn-restricted-age btn-cancel-confirm-age"
                            data-redirect="{{ getAppHomepageLink() }}"
                        >
                            {{ __('No') }}
                        </button>
                    </div>
                    <div class="warning-notice-restricted-age">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    #confirmed_restricted_age .modal-dialog {
        max-width: 550px;
    }

    #confirmed_restricted_age {
        z-index: 10000;
    }

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
        padding:0.625rem 2rem;
        font-size: 1.0625rem;
        color: #fff;
    }

    .text-confirm-restricted-age p,
    .warning-notice-restricted-age p {
        margin-bottom: 0 !important;
    }

    .bg-modal-confirm-age {
        backdrop-filter: blur(15px);
    }

</style>
<script type="text/javascript">
    function confirmRestrictedAge(value) {
        $('#confirmed_restricted_age').modal('hide');
        const confirmedAgeStatus = sessionStorage.setItem('isConfirmedRestrictedAge', value);
        doAddToCart();
    }

    function confirmRestrictedAgeForBuyNow(value) {
        $('#confirmed_restricted_age').modal('hide');
        const confirmedAgeStatus = sessionStorage.getItem('isConfirmedRestrictedAge');
        if (value == '18' || confirmedAgeStatus != '21') {
            sessionStorage.setItem('isConfirmedRestrictedAge', value)
            if (value == '21') {
                $('.cover-image').addClass('hidden-image');
                $('.normal-image').removeClass('hidden-image');
            }
        }
        doBuyNow();
    }

    $('#btn_cancel_confirm_restricted_age').click(function() {
        $('#confirmed_restricted_age').modal('hide');
        window.location.href = $(this).data('redirect');
    });

    $('#btn_close_modal').click(function() {
        window.location.href = $(this).data('redirect');
    });

</script>


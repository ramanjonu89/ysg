<section class="sct-color-1 pt-5 pb-4">
    <div class="container">
        <div class="section-title section-title--style-1 text-center mb-4">
            <h3 class="section-title-inner heading-3 strong-600">
                {{ __('Coming soon') }}
            </h3>
        </div>
        <div class="row">
            <div class="col mb-4">
                <div class="flipper" data-reverse="true" data-datetime="{{$shop->user->company_start_date}} 00:00:00" data-template="dd|HH|ii|ss" data-labels="Days|Hours|Minutes|Seconds" id="myFlipper"></div>
            </div>
        </div>
    </div>
</section>

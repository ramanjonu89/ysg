@php
    $total = $shop->user->product_reviews_count;
    $rating = $shop->user->sum_review_rating;
@endphp

<section class="gry-bg pt-4 pb-2">
    <div class="container">
        <div class="row align-items-baseline">
            <div class="col-md-6">
                <div class="d-flex">
                    <img
                    height="70"
                    class="lazyload"
                    src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                    data-src="@if ($shop->logo !== null) {{ my_asset($shop->logo) }} @else {{ my_asset('frontend/images/placeholder.jpg') }} @endif"
                    alt="{{ $shop->name }}"
                    >
                    <div class="pl-4">
                        <h3 class="strong-700 heading-4 mb-0">{{ $shop->name }} @if ($shop->user->company_type == 2) <span class="btn btn-success">PRIVATE EVENT</span> @endif
                            @if ($shop->user->seller->verification_status == 1)
                            <span class="ml-2"><i class="fa fa-check-circle" style="color:green"></i></span>
                            @else
                            <span class="ml-2"><i class="fa fa-times-circle" style="color:red"></i></span>
                            @endif
                        </h3>
                        <div class="star-rating star-rating-sm mb-1">
                            @if ($total > 0)
                            {{ renderStarRating($rating / $total) }}
                            @else
                            {{ renderStarRating(0) }}
                            @endif
                        </div>
                        <div class="location alpha-6">{{ $shop->address }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="text-md-right mt-4 mt-md-0 social-nav model-2">
                    @if ($shop->facebook != null)
                    <li>
                        <a href="{{ $shop->facebook }}" class="facebook social_a" target="_blank" data-toggle="tooltip" data-original-title="Facebook">
                            <i class="fa fa-facebook" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                    @if ($shop->twitter != null)
                    <li>
                        <a href="{{ $shop->twitter }}" class="twitter social_a" target="_blank" data-toggle="tooltip" data-original-title="Twitter">
                            <i class="fa fa-twitter" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                    @if ($shop->google != null)
                    <li>
                        <a href="{{ $shop->google }}" class="google-plus social_a" target="_blank" data-toggle="tooltip" data-original-title="Google">
                            <i class="fa fa-google-plus" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                    @if ($shop->youtube != null)
                    <li>
                        <a href="{{ $shop->youtube }}" class="youtube social_a" target="_blank" data-toggle="tooltip" data-original-title="Youtube">
                            <i class="fa fa-youtube" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                    @if ($shop->instagram != null)
                    <li>
                        <a href="{{ $shop->instagram }}" class="instagram social_a" target="_blank" data-toggle="tooltip" data-original-title="instagram">
                            <i class="fa fa-instagram" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                    @if ($shop->linkedin != null)
                    <li>
                        <a href="{{ $shop->linkedin }}" class="youtube social_a" target="_blank" data-toggle="tooltip" data-original-title="linkedin">
                            <i class="fa fa-linkedin" style="margin-top: 10px;"></i>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</section>
@if (!isMicrosite())
    <div class="box-title bg-white border-bottom-0 border-top">
        <h6 class="font-weight-bold mb-0">{{ __('Sellers') }}</h6>
    </div>
    <div class="box-content pr-3 pl-3 pb-3">
        @foreach ($sellers as $key => $seller)
            <div class="custom-control custom-checkbox {{ $key >= 5 ? 'seller-hidden-item' : '' }}"
                style="{{ $key >= 5 ? 'display:none' : '' }}">
                <input name="seller_ids[]" type="checkbox" class="custom-control-input"
                    id="custom-cb-seller-{{ $seller->id }}" value="{{ $seller->id }}"
                    @isset($sellerIds) @if (in_array($seller->id, $sellerIds)) checked @endif @endisset
                    onchange="filter()">
                <label class="custom-control-label"
                    for="custom-cb-seller-{{ $seller->id }}">{{ $seller->name }}</label>
            </div>
        @endforeach
        @if (count($sellers) > 4)
            <div class="text-center text-black-50">
                <span role="button" data-status="hide" id="view-more-sellers">
                    <span>{{ __('View more') }}</span>
                    <i class="caret-icon fa fa-caret-down"></i>
                </span>
            </div>
        @endif
    </div>

    <!-- Price Filter -->
    <div class="box-title bg-white border-bottom-0 border-top">
        <h6 class="font-weight-bold mb-0">{{ __('Price range') }}</h6>
    </div>
    <div class="box-content pr-3 pl-3 pb-3">
        <div class="form-row d-flex align-items-center justify-content-between">
            <input id="min_price" name="min_price" value="{{ $min_price }}" type="number" placeholder="Min"
                class="form-control col-md-5">
            <div class="col-md-1 border-bottom border-dark"></div>
            <input id="max_price" name="max_price" value="{{ $max_price }}" type="number" placeholder="Max"
                class="form-control col-md-5">
        </div>
    </div>
    <!-- END -->
    <!-- star -->
    <div class="box-title bg-white border-bottom-0 border-top">
        <h6 class="font-weight-bold mb-0">{{ __('Rate') }}</h6>
    </div>
    <div class="box-content pr-3 pl-3 pb-3">
        <div class="form-row d-flex">
            @for ($i = 0; $i < 5; $i++)
                <div class="star-rate star-rating star-rating-sm mt-1 {{ isset($rate) && $rate == 5 - $i ? 'active' : '' }}"
                    data-value="{{ 5 - $i }}">
                    @for ($j = 0; $j < 5; $j++)
                        <i class="fa fa-star {{ $i + $j < 5 ? 'active' : '' }}"
                            style="font-size: 15px;padding: 0 0px;"></i>
                    @endfor
                    <span>{{ $i != 0 ? __('& Up') : '' }}</span>
                </div>
            @endfor
        </div>
        <input type="hidden" value="" name="rate" id="star-rate">
    </div>
    <!-- END -->

    {{-- promotion --}}
    <div class="box-title bg-white border-bottom-0 border-top">
        <h6 class="font-weight-bold mb-0">{{ __('Promotion') }}</h6>
    </div>
    <div class="box-content pr-3 pl-3 pb-3">
        <div class="form-row d-flex">
            <div class="custom-control custom-checkbox " style="">
                <input name="discount" type="checkbox" class="custom-control-input" id="discount" value="1"
                    {{ isset($discount) && $discount == 1 ? 'checked' : '' }} onchange="filter()">
                <label class="custom-control-label" for="discount">{{ __('On sale off') }}</label>
            </div>
        </div>
    </div>

    {{-- CSS --}}
    @if (isset($canViewCSSPage) && $canViewCSSPage === true)
        <div class="box-title bg-white border-bottom-0 border-top">
            <h6 class="font-weight-bold mb-0">{{ __('Store type') }}</h6>
        </div>
        <div class="box-content pr-3 pl-3 pb-3">
            <div class="form-row d-flex">
                <div class="custom-control custom-checkbox " style="">
                    <input name="store_types[2]" type="checkbox" class="custom-control-input" value="css"
                        id="store_types_css"
                        {{ isset($storeTypes) && isset($storeTypes[2]) && $storeTypes[2] == 'css' ? 'checked' : '' }}
                        onchange="filter()">
                    <label class="custom-control-label" for="store_types_css">{{ __('CSS') }}</label>
                </div>
            </div>

            <div class="form-row d-flex">
                <div class="custom-control custom-checkbox " style="">
                    <input name="store_types[1]" type="checkbox" class="custom-control-input" value="flagship"
                        id="store_types_flagship"
                        {{ isset($storeTypes) && isset($storeTypes[1]) && $storeTypes[1] == 'flagship' ? 'checked' : '' }}
                        onchange="filter()">
                    <label class="custom-control-label" for="store_types_flagship">{{ __('Flagship') }}</label>
                </div>
            </div>
        </div>
    @endif
@endif

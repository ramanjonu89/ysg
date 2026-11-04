<div class="modal-body p-4">
    <div class="row no-gutters cols-xs-space cols-sm-space cols-md-space">
        <div class="col-lg-6">
            <div class="product-gal sticky-top d-flex flex-row-reverse">
                @if ($isAdultImage)
                    <div class="product-gal-img">
                        <img src="{{ my_asset('assets/frontend/images/adult_notice.png') }}" class="xzoom img-fluid lazyload">
                    </div>
                    <div class="product-gal-thumb">
                        <div class="xzoom-thumbs">
                            <img src="{{ my_asset('assets/frontend/images/adult_notice.png') }}"
                                class="xzoom-gallery lazyload"
                                src="{{ my_asset('assets/frontend/images/adult_notice.png') }}" width="80">
                        </div>
                    </div>
                @elseif (is_array(json_decode($product->photos)) && count(json_decode($product->photos)) > 0)
                    <div class="product-gal-img">
                        <img src="{{ my_asset('frontend/images/placeholder.jpg') }}" class="xzoom img-fluid lazyload"
                            data-src="{{ my_asset(json_decode($product->photos)[0]) }}"
                            xoriginal="{{ my_asset(json_decode($product->photos)[0]) }}">
                    </div>
                    <div class="product-gal-thumb">
                        <div class="xzoom-thumbs">
                            @foreach (json_decode($product->photos) as $key => $photo)
                                <a href="{{ my_asset($photo) }}">
                                    <img src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                        class="xzoom-gallery lazyload" width="80"
                                        data-src="{{ my_asset($photo) }}"
                                        @if ($key == 0) xpreview="{{ my_asset($photo) }}" @endif>
                                </a>
                            @endforeach

                            @foreach ($product->stocks as $stockIndex => $stock)
                                @if ($stock->image)
                                    <a href="{{ my_asset($stock->image) }}" data-variant="{{$stock->variant}}">
                                        <img src="{{ my_asset('frontend/images/placeholder.jpg') }}"
                                            class="xzoom-gallery lazyload"
                                            src="{{ my_asset('frontend/images/placeholder.jpg') }}" width="80"
                                            style="width: 50px; height: 50px; object-fit: contain; object-position: center;"
                                            data-src="{{ my_asset($stock->image) }}"
                                            @if ($key == 0) xpreview="{{ my_asset($stock->image) }}" @endif>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <!-- Product description -->
            <div class="product-description-wrapper">
                <!-- Product title -->
                <h2 class="product-title">
                    {{ $product->translation_name }}
                </h2>
                <div class="row no-gutters mt-4 d-flex align-items-center ">
                    <div class="col-md-3">
                        <div class="product-description-label">{{ __('Price') }}:</div>
                    </div>
                    <div class="col-md-9">
                        <div class="row d-flex align-items-center">
                            @if ($product->stocks->count())
                                <div class="d-flex pr-0 text-center" id="del_unit_price">
                                    @if ($product->range_unit_price != home_discounted_price($product->id))
                                        <del class="product-price-old pr-3 pl-2 mt-1">
                                            <span class="" id="range_unit_price">
                                                {{ $product->range_unit_price }}
                                            </span>
                                            <span class="d-none" id="chonse_unit_price">

                                            </span>
                                        </del>
                                        <div class="product-price font-weight-bold" id="lowest_highest_price">
                                            {{ home_discounted_price($product->id) }}
                                        </div>
                                        <div class="product-price d-none" id="variant_price">
                                            <strong id="chosen_price">

                                            </strong>
                                        </div>
                                        <div class="product-price-old pr-2 pl-3 mt-1">
                                            <span class="" id="range_unit_price">({{ $product->discount_badge }})</span>
                                            <span class="d-none" id="chonse_discount_badge">

                                            </span>
                                        </div>
                                    @else
                                        <div class="product-price font-weight-bold" id="lowest_highest_price">
                                            {{ home_discounted_price($product->id) }}
                                        </div>
                                        <div class="product-price d-none" id="variant_price">
                                            <strong id="chosen_price">

                                            </strong>
                                        </div>
                                    @endif
                                </div>
                            @else
                                @if ($product->has_discount)
                                    <del class="product-price-old pr-3 pl-2 mt-1" id="del_unit_price">
                                        <span class="" id="range_unit_price">
                                            {{ single_price($product->unit_price) }}
                                        </span>
                                        <span class="d-none" id="chonse_unit_price">

                                        </span>
                                    </del>
                                    <div class="product-price font-weight-bold" id="lowest_highest_price">
                                            {{ single_price($product->final_price) }}
                                    </div>
                                    <div class="product-price d-none" id="variant_price">
                                        <strong id="chosen_price">

                                        </strong>
                                    </div>
                                    <div class="product-price-old pr-2 pl-3 mt-1">
                                        <span class="">({{ $product->discount_badge }})</span>
                                    </div>
                                @else
                                    <div class="product-price" id="lowest_highest_price">
                                        <strong>
                                            {{ single_price($product->unit_price) }}
                                        </strong>
                                    </div>
                                    <div class="product-price d-none" id="variant_price">
                                        <strong id="chosen_price">

                                        </strong>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                @if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated && $product->earn_point > 0)
                    <div class="row no-gutters mt-4">
                        <div class="col-2">
                            <div class="product-description-label">{{ __('Club Point') }}:</div>
                        </div>
                        <div class="col-10">
                            <div class="d-inline-block club-point bg-soft-base-1 border-light-base-1 border">
                                <span class="strong-700">{{ $product->earn_point }}</span>
                            </div>
                        </div>
                    </div>
                @endif
                <hr>
                <form id="option-choice-form">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="is_age_restricted" value="{{ $isAgeRestricted }}">
                    <!-- Quantity + Add to cart -->
                    @if ($product->digital != 1)
                        @if ($product->choice_options != null)
                            @foreach (json_decode($product->choice_options) as $key => $choice)
                                <div class="row no-gutters">
                                    <div class="col-2">
                                        <div class="product-description-label mt-2 ">
                                            {{ \App\Attribute::find($choice->attribute_id)->name }}
                                            :
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <ul
                                            class="list-inline checkbox-alphanumeric checkbox-alphanumeric--style-1 mb-2">
                                            @foreach ($choice->values as $itemKey => $value)
                                                <li>
                                                    <input type="radio"
                                                        class="product-variants"
                                                        id="{{ $choice->attribute_id }}-{{ $value }}"
                                                        name="attribute_id_{{ $choice->attribute_id }}"
                                                        value="{{ $value }}"
                                                        data-offset-key="{{ $key }}">
                                                    <label
                                                        class="variant-label variant-{{ $choice->attribute_id }}"
                                                        for="{{ $choice->attribute_id }}-{{ $value }}">
                                                        {{ $value }}
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        @if (count(json_decode($product->colors)) > 0)
                            <div class="row no-gutters">
                                <div class="col-2">
                                    <div class="product-description-label mt-2">{{ __('Color') }}:</div>
                                </div>

                                <div class="col-10">
                                    <ul class="list-inline checkbox-color mb-1">
                                        @foreach (json_decode($product->colors) as $key => $color)
                                            <li>
                                                <input type="radio"
                                                    id="{{ $product->id }}-color-{{ $key }}" name="color"
                                                    value="{{ $color }}"
                                                    @if ($key == 0) checked @endif>

                                                <label style="background: {{ $color }};"
                                                    for="{{ $product->id }}-color-{{ $key }}"
                                                    data-toggle="tooltip"></label>

                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <hr>
                        @endif

                        <div class="row no-gutters">
                            <div class="col-2">
                                <div class="product-description-label mt-2">{{ __('Quantity') }}:</div>
                            </div>
                            <div class="col-10">
                                <div class="product-quantity d-flex align-items-center">
                                    <div class="input-group input-group--style-2 pr-3" style="width: 160px;">
                                        <span class="input-group-btn">
                                            <button class="btn btn-number" type="button" data-type="minus"
                                                data-field="quantity" disabled="disabled">
                                                <i class="la la-minus"></i>
                                            </button>
                                        </span>

                                        <input type="text" name="quantity"
                                            class="form-control input-number h-auto text-center"
                                            style="padding:0 !important;" placeholder="1" value="1" min="1" max="10">
                                        <span class="input-group-btn">
                                            <button class="btn btn-number" type="button" data-type="plus"
                                                data-field="quantity">
                                                <i class="la la-plus"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <div class="avialable-amount">
                                        (<span id="available-quantity">

                                        </span>
                                        {{ __('available') }})
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    @endif
                </form>

                <div class="d-table width-100 mt-3">
                    <div class="d-table-cell">
                        <!-- Add to cart button -->
                        @if ($product->digital == 1)
                            <button type="button"
                                class="btn btn-styled btn-alt-base-1 c-white btn-icon-left strong-700 hov-bounce hov-shaddow ml-2 add-to-cart"
                                onclick="addToCart()">
                                <i class="la la-shopping-cart"></i>
                                <span class="d-none d-md-inline-block"> {{ __('Add to cart') }}</span>
                            </button>
                        @elseif ($product->quantity > 0)
                            <button type="button"
                                class="btn btn-styled btn-alt-base-1 c-white btn-icon-left strong-700 hov-bounce hov-shaddow ml-2 add-to-cart"
                                onclick="addToCart()">
                                <i class="la la-shopping-cart"></i>
                                <span class="d-none d-md-inline-block"> {{ __('Add to cart') }}</span>
                            </button>
                        @else
                            <button type="button" class="btn btn-styled btn-base-3 btn-icon-left strong-700" disabled>
                                <i class="la la-cart-arrow-down"></i> {{ __('Out of Stock') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@php
    $attributes = json_decode($product->attributes);
@endphp
<script type="text/javascript">
    $(function() {
        cartQuantityInitialize();
        $('#option-choice-form input').on('change', function() {
            getVariantPrice();
        });
    });

    $(document).ready(function () {
        const productId  = @json($product->id);
        const attributes = @json($attributes);

        if (attributes.length === 1) {
            checkStock({ productId, attributes });
        }

        $(document).on('change', '.product-variants', function () {
            let params = { productId, attributes };

            if (attributes.length === 1) {
                checkStock(params);
                return;
            }

            const selections = getSelections();
            const variantMapping = buildVariants(selections);

            checkStock({
                ...params,
                variantMapping,
                selections
            });
        });

        $('#option-choice-form input').on('change', function () {
            let currentVariant = [];
            $('#option-choice-form input.product-variants').each(function () {
                if(this.checked) {
                    currentVariant.push(this.value);
                }
            });
            currentVariant = currentVariant.join('{{ \App\Models\ProductStock::SPLIT_SYMBOL }}');
            $('.product-gal-thumb a').each(function () {
                if ($(this).data('variant') == currentVariant) {
                    $(this).trigger('click');
                }
            });
        });

        function getSelections() {
            let selections = {};
            $(".product-variants:checked").each(function () {
                const attrId = $(this).attr('name').replace('attribute_id_', '');
                selections[attrId] = $(this).val();
            });
            return selections;
        }
    });

</script>

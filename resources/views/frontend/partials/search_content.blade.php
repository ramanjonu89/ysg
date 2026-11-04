@if (!isMicrosite())

@if(\App\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)

    <div class="product">

        @if (count($shops) > 0)
            <div class="title">{{ __('Shops') }}</div>
            <ul>
                @foreach ($shops as $key => $shop)
                    <li>
                        <a href="{{ route('shop.visit', $shop->slug) }}">
                            <div class="d-flex search-product align-items-center">
                                <div style="width:100px;">
                                    <img src="{{ my_asset($shop->logo) }}" style="height:50px;">
                                </div>
                                <div class="w-100 overflow--hidden ">
                                    <div class="product-name text-truncate heading-6 strong-600" style="font-size:12px !important;">
                                        {{ $shop->name }}
                                        <div class="stock-box d-inline-block">
                                            @if($shop->user->seller->verification_status == 1)
                                                <span class="ml-2"><i class="fa fa-check-circle" style="color:green"></i></span>
                                            @else
                                                <span class="ml-2"><i class="fa fa-times-circle" style="color:red"></i></span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="price-box alpha-6" style="font-size:12px !important;">
                                        {{ $shop->address }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
<div class="keyword">

    @if (sizeof($keywords) > 0)
        <div class="title">{{ __('Popular Suggestions') }}</div>
        <ul>
            @foreach ($keywords as $key => $keyword)
                <li><a href="{{ route('suggestion.search', $keyword) }}">{{ $keyword }}</a></li>
            @endforeach
        </ul>
    @endif

</div>

<div class="category">

    @if (count($subsubcategories) > 0)
        <div class="title">{{ __('Category Suggestions') }}</div>
        <ul>
            @foreach ($subsubcategories as $key => $subsubcategory)
            @if ($subsubcategory->status == 1)
                <li><a href="{{ route('category.view', ['category_slug' => $subsubcategory->subcategory->category->slug,'subcategory_slug' => $subsubcategory->subcategory->slug, 'subsubcategory_slug' => $subsubcategory->slug]) }}">{{ __($subsubcategory->name) }}</a></li>
            @endif
            @endforeach
        </ul>
    @endif
</div>
@endif

<div class="product">
    @if (count($products) > 0)
        <div class="title">{{ __('Products') }}</div>
        <ul>
            @foreach ($products as $key => $product)
                <li>
                    @if ($product->isProductForAdults())
                        <a href="{{ route('product', $product->slug) }}" class="pt-2">
                            <div class="d-flex search-product align-items-center">
                                <div class="w-100 overflow--hidden">
                                    <div class="product-name text-truncate" style="font-size:12px; margin-left:12px">
                                        {{ $product->translation_name }}
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </a>
                    @else
                        <a href="{{ route('product', $product->slug) }}" class="pt-2">
                        <div class="d-flex search-product align-items-center">
                            {{-- <div class="image" style="background-image:url('{{ my_asset($product->thumbnail_img) }}');">
                            </div> --}}
                            <div class="w-100 overflow--hidden">
                                <div class="product-name text-truncate" style="font-size:12px; margin-left:12px">
                                    {{ $product->translation_name }}
                                </div>
                                <div class="clearfix">
                                    {{-- <div class="price-box float-left">
                                        @if(home_base_price($product->id) != home_discounted_base_price($product->id))
                                            <del class="old-product-price strong-400">{{ home_base_price($product->id) }}</del>
                                        @endif
                                        <span class="product-price strong-600">{{ home_discounted_base_price($product->id) }}</span>
                                    </div>
                                    <div class="stock-box float-right">
                                        @php
                                            $qty = 0;
                                            foreach (json_decode($product->variations) as $key => $variation) {
                                                $qty += $variation->qty;
                                            }
                                        @endphp
                                        @if(count(json_decode($product->variations, true)) >= 1)
                                            @if ($qty > 0)
                                                <span class="badge badge-pill bg-green">{{ __('In stock') }}</span>
                                            @else
                                                <span class="badge badge badge-pill bg-red">{{ __('Out of stock') }}</span>
                                            @endif
                                        @endif
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </a>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>




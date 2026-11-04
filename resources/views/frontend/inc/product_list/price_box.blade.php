<div class="price-box">
    <del class="old-product-price strong-400">
        @if ($product->has_discount)
            {{ format_price($product->min_price) }}
            @if ($product->min_price != $product->max_price)
                {{ ' - ' . format_price($product->max_price) }}
            @endif
        @endif
    </del>
    <br>
    <span class="product-price strong-600">
        {{ format_price($product->min_final_price) }}
        @if ($product->min_final_price != $product->max_final_price)
            {{ ' - ' . format_price($product->max_final_price) }}
        @endif
    </span>
</div>

<div class="cart-add d-flex border-top border-bottom">
    @if ($product->quantity)
        <button type="button" class="btn btn-block btn-icon-left"
                onclick="showNoticeAgeRestrictedProduct({{ json_encode(['id' => $product->id, 'age' => $product->category->is_age_restricted]) }})">
            <span
                class="d-none d-sm-inline-block">{{ __('Add to cart') }}</span><i
                class="la la-shopping-cart ml-2"></i>
        </button>
    @else
        <button type="button" class="btn btn-block btn-icon-left bg-red"
                disabled="">
            <span
                class="d-none d-sm-inline-block">{{ __('Out of stock') }}</span><i
                class="la la-shopping-cart ml-2"></i>
        </button>
    @endif
</div>
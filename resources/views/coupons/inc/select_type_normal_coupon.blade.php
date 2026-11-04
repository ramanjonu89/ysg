<select name="coupon_type" id="coupon_type" class="form-control demo-select2" onchange="coupon_form()" required>
    <option value="">{{ __('Select One') }}</option>
    <option value="product_base">{{ __('For Products') }}</option>
    <option value="cart_base">{{ __('For Total Orders/Shop') }}</option>
    <option value="category_base">{{ __('For Categories') }}</option>
</select>
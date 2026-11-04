@php
    $data = $flashDealProduct->toArray();
    unset($data['product']);
@endphp
<tr data-flash-deal-product="{{ json_encode($data) }}" data-ajax-url="{{ route('flash_deals.products.view', [$flashDeal, $flashDealProduct]) }}">
    <td>
        <p>{{ $product->id }}</p>
    </td>
    <td>
        <div class="col-sm-3">
            <img loading="lazy"  class="img-md" src="{{ my_asset($product->thumbnail_img)}}" alt="Image">
        </div>
        <div class="col-sm-9">
            <label for="" class="control-label">{{ __($product->name) }}</label>
        </div>
    </td>
    <td>
        <p>{{ $product->unit_price }}</p>
    </td>
    <td>
        <p>{{ $flashDealProduct->discount_price }}</p>
    </td>
    <td>
        <input type="number" name="discount" value="{{ $flashDealProduct->discount }}" min="0" step="1" class="form-control" required>
    </td>
    <td>
        <select class="demo-select2" name="discount_type_{{ $flashDealProduct->id }}">
            <option value="amount" <?php if($flashDealProduct->discount_type == 'amount') echo "selected";?> >$</option>
            <option value="percent" <?php if($flashDealProduct->discount_type == 'percent') echo "selected";?> >%</option>
        </select>
    </td>
    <td>
        <button class="btn btn-danger" type="button">{{translate('Remove')}}</button>
    </td>
</tr>
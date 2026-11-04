{{-- @include('partials.coupons.inc.visibility_coupon') --}}

<div class="panel-heading">
    <h3 class="panel-title pl-0">{{ __('Coupon Settings') }}</h3>
</div>

<div class="form-group">
    <label class="col-lg-3 control-label" for="coupon_name">{{ __('Coupon Name') }}</label>
    <div class="col-lg-9">
        <input type="text" placeholder="{{ __('Coupon Name') }}" id="coupon_name" name="coupon_name"
            class="form-control" required>
    </div>
</div>
<div class="form-group">
    <label class="col-lg-3 control-label" for="coupon_code">{{ __('Coupon Code') }}</label>
    <div class="col-lg-9">
        <input type="text" placeholder="{{ __('Coupon Code') }}" id="coupon_code" name="coupon_code"
            class="form-control" required>
    </div>
</div>
@if ($is_generation_bulk)
    <div class="form-group">
        <label class="col-lg-3 control-label" for="max_coupon_to_generate">{{ __('No. of coupon To Generate') }}</label>
        <div class="col-lg-9">
            <input type="text" placeholder="{{ __('No. of coupon To Generate') }}" id="max_coupon_to_generate"
                name="max_coupon_to_generate" class="form-control" required>
        </div>
    </div>
@endif
<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('Seller') }}</label>
    <div class="col-lg-9">
        <select class="form-control category_id" id="select_sellers" name="seller_ids[]" multiple>
            <option value="0">{{ __('Select All Sellers') }}</option>
            @foreach ($shops as $key => $shop)
                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-lg-3 control-label" for="start_date">{{ __('Date') }}</label>
    <div class="col-lg-9">
        <div id="demo-dp-range">
            <div class="input-group" id="datepicker">
                <input type="text" name="daterange" value="" />
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('No. of coupon') }}</label>
    <div class="col-lg-9">
        <input id="max_coupon" type="number" min="0" step="1" placeholder="{{ __('Maximum Limit') }}"
            name="max_coupon" class="form-control" required>
    </div>
</div>
<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('Minimum Cart Value') }}</label>
    <div class="col-lg-9">
        <input id="min_buy" type="number" min="0" step="0.01" placeholder="{{ __('Minimum Shopping') }}"
            name="min_buy" class="form-control" required>
    </div>
</div>
<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('Discount') }}</label>
    <div class="col-lg-8">
        <input id="discount" type="number" min="0" step="0.01" placeholder="{{ __('Discount') }}"
            name="discount" class="form-control" required>
    </div>
    <div class="col-lg-1">
        <select class="demo-select2" id="discount_type" name="discount_type">
            <option value="amount">$</option>
        </select>
    </div>
</div>

<style>
    .select2-selection__rendered {
        max-height: 120px;
        overflow-y: auto !important;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
        $('.demo-select2').select2();
        $('input[name="daterange"]').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('#select_sellers').select2({
            closeOnSelect: false,
        }).on('select2:selecting', function(e) {
            const value_selected = e.params.args.data.id;
            if (value_selected == 0) {
                $("#select_sellers").find('option').prop("selected", true);
                $("#select2-select_sellers-results").find(".select2-results__option").attr(
                    "aria-selected", true);
                $("#select_sellers").trigger('change');
            } else {
                const value_old = (e.target.value == '') ? [value_selected] : $(e.target).val().concat([
                    value_selected
                ]);
                $(e.target).val(value_old).trigger('change');
                $(e.params.args.originalEvent.currentTarget).attr('aria-selected', true);
                return false;
            }
        }).on('select2:unselecting', function(e) {
            const value_unselected = e.params.args.data.id;
            if (value_unselected == 0) {
                $("#select_sellers").find('option').prop("selected", false);
                $("#select2-select_sellers-results").find(".select2-results__option").attr(
                    "aria-selected", false);
                $("#select_sellers").trigger('change');
            } else {
                const value_old = (e.target.value == '') ? [value_unselected] : $(e.target).val()
                    .concat([value_unselected]);
                $(e.target).val(value_old).trigger('change');
                $(e.params.args.originalEvent.currentTarget).attr('aria-selected', false);
                $("#select_sellers").find('option[value="0"]').prop("selected", false);
                $("#select_sellers").find('option[value="' + value_unselected + '"]').prop("selected",
                    false);
                $("#select2-select_sellers-results").find(".select2-results__option:nth-child(1)").attr(
                    "aria-selected", false);
                $("#select_sellers").trigger('change');
                return false;
            }
        });
    });
</script>

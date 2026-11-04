
@include('partials.coupons.inc.visibility_coupon')

<div class="panel-heading">
    <h3 class="panel-title">{{ __('Add Your Catgory Base Coupon') }}</h3>
</div>
<div class="form-group">
    <label class="col-lg-3 control-label" for="coupon_code">{{ __('Coupon code') }}</label>
    <div class="col-lg-9">
        <input type="text" placeholder="{{ __('Coupon code') }}" id="coupon_code" name="code" value="{{ $coupon->code }}" class="form-control" required>
    </div>
</div>

<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('Free Shipping') }}</label>
    <div class="col-lg-9">
        <div style="padding: 6px 0;">
            <label class="switch" style="margin-bottom: 0">
                <input value="1" name="free_shipping" type="checkbox" {{ ($coupon->free_shipping)?'checked':'' }}>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
</div>

<div class="product-choose-list">
    <div class="product-choose">
        <div class="form-group">
            <label class="col-lg-3 control-label">{{ __('Categories') }}</label>
            <div class="col-lg-9">
                <select class="form-control category_id demo-select2" name="category_ids[]" multiple>
                    @foreach($categories as $key => $category)
                        <option value="{{$category->id}}" {{in_array($category->id,$details->categoryIds) ? 'selected' : ''}}>{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group" id="subcategory">
            <label class="col-lg-3 control-label">{{ __('Sub Categories') }}</label>
            <div class="col-lg-9">
                <select class="form-control subcategory_id demo-select2" name="subcategory_ids[]" multiple>
                    @if(isset($subCategories) && count($subCategories) > 0)
                        @foreach( $subCategories as $key => $subcategory)
                            <option value="{{$subcategory->id}}" {{in_array($subcategory->id, $details->subcategoryIds) ? 'selected' : ''}}>{{$subcategory->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <div class="form-group" id="subsubcategory">
            <label class="col-lg-3 control-label">{{ __('Sub Sub Categories') }}</label>
            <div class="col-lg-9">
                <select class="form-control subsubcategory_id demo-select2" name="subsubcategory_ids[]" multiple>
                    @foreach( $subSubCategories as $key => $subsubcategory)
                        <option value="{{$subsubcategory->id}}" {{in_array($subsubcategory->id, $details->subsubcategoryIds) ? 'selected' : ''}}>{{$subsubcategory->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">{{ __('No. of coupon') }}</label>
            <div class="col-lg-9">
                <input id="max_coupon type="number" min="0" step="1" placeholder="{{ __('Maximum Limit') }}" name="max_coupon" class="form-control" @isset($details->max_coupon) value="{{ $details->max_coupon }}" @endisset required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">{{ __('Minimum Cart Value') }}</label>
            <div class="col-lg-9">
                <input id="min_buy" type="number" min="0" step="0.01" name="min_buy" class="form-control" value="{{ $details->min_buy }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">{{ __('Discount') }}</label>
            <div class="col-lg-8">
                <input id="discount" type="number" min="0" step="0.01" placeholder="{{ __('Discount') }}" value="{{ $coupon->discount }}" name="discount" class="form-control" required>
            </div>
            <div class="col-lg-1">
                <select class="demo-select2" id="discount_type" name="discount_type">
                    <option value="amount" @if ($coupon->discount_type == 'amount') selected  @endif>$</option>
                    <option value="percent" @if ($coupon->discount_type == 'percent') selected  @endif>%</option>
                </select>
            </div>
        </div>

        <div class="form-group" id="maximum_discount_amount">
            <label class="col-lg-3 control-label">{{ __('Maximum Discount Amount') }}</label>
            <div class="col-lg-8">
                <input id="max_discount" type="number" min="0" step="0.01" placeholder="{{ __('Maximum Discount Amount') }}" name="max_discount" class="form-control" value="{{ $details->max_discount }}" required>
            </div>
            <div class="col-lg-1">
                <div class="enable_maximum_discount_amount">
                    <label class="switch mb-0">
                        <input value="1" name="enable_maximum_discount_amount" type="checkbox" id="enable_maximum_discount_amount">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-lg-3 control-label" for="start_date">{{ __('Date') }}</label>
    <div class="col-lg-9">
        <div id="demo-dp-range">
            <div class="input-group" id="datepicker">
                <input type="text" name="daterange" value="{{ date('d/m/Y', $coupon->start_date).' - '.date('d/m/Y', $coupon->end_date) }}" />
            </div>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('Only once') }}</label>
    <div class="col-sm-9">
        <label class="pull-left mt-2">
            <input type="checkbox" name="only_once" value="1" {{ $coupon->only_once  == 1 ? 'checked' : '' }}>
        </label>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.demo-select2').select2();
        //get_subcategories_by_category($('.category_id'));
        $('input[name="daterange"]').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            }
        });

        const discount_type = $('#discount_type').val();
        const max_discount  = $('#max_discount').val();

        enabled_discount_amount(discount_type, max_discount);
        shown_input_max_discount_amount(discount_type);

    });

    $('#discount_type').on('change', function() {
        const max_discount    = $('#max_discount').val();
        const enabled_maximum = $('#enable_maximum_discount_amount').prop('checked');
        shown_input_max_discount_amount(this.value)
        enabled_discount_amount(this.value, max_discount);
    });

    $('#enable_maximum_discount_amount').on('change', function() {
        const value = $('#enable_maximum_discount_amount').prop('checked');
        enabled_discount_amount('percent', value);
    });

    function shown_input_max_discount_amount(discount_type) {
        if (discount_type === 'percent') {
            $('#maximum_discount_amount').removeClass('d-none');
        } else {
            $('#max_discount').val('');
            $('#maximum_discount_amount').addClass('d-none');
        }
    }

    function enabled_discount_amount(discount_type, max_discount) {
        if (discount_type == 'percent' && max_discount) {
            $('#max_discount').prop('disabled', false);
            $('#enable_maximum_discount_amount').prop('checked', true);
        } else {
            $('#max_discount').prop('disabled', true);
            $('#enable_maximum_discount_amount').prop('checked', false);
        }
    }
</script>

@include('partials.coupons.inc.visibility_coupon')

<div class="panel-heading">
    <h3 class="panel-title">{{ __('Add Your Catgory Base Coupon') }}</h3>
</div>
<div class="form-group">
    <label class="col-lg-3 control-label" for="coupon_code">{{ __('Coupon code') }}</label>
    <div class="col-lg-9">
        <input type="text" placeholder="{{ __('Coupon code') }}" id="coupon_code" name="coupon_code" class="form-control" required>
    </div>
</div>
@if ($is_generation_bulk)
    <div class="form-group">
        <label class="col-lg-3 control-label" for="max_coupon_to_generate">{{ __('No. of coupon To Generate') }}</label>
        <div class="col-lg-9">
            <input type="text" placeholder="{{ __('No. of coupon To Generate') }}" id="max_coupon_to_generate" name="max_coupon_to_generate" class="form-control" required>
        </div>
    </div>
@endif
<div class="product-choose-list">
    <div class="product-choose">
        <div class="form-group">
            <label class="col-lg-3 control-label">{{ __('Free Shipping') }}</label>
            <div class="col-lg-9">
                <div style="padding: 6px 0;">
                    <label class="switch" style="margin-bottom: 0">
                        <input value="1" name="free_shipping" type="checkbox">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">{{ __('Categories') }}</label>
            <div class="col-lg-9">
                <select class="form-control category_id demo-select2" name="category_ids[]" multiple>
                    <option value="">{{ __('Select Categories')}}</option>
                    @foreach($categories as $key => $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group" id="subcategory">
            <label class="col-lg-3 control-label">{{ __('Sub Categories') }}</label>
            <div class="col-lg-9">
                <select class="form-control subcategory_id demo-select2" name="subcategory_ids[]" multiple>
                    <option value="">Select</option>
                    @if(isset($subCategories) && count($subCategories) > 0)
                        @foreach($subCategories as $key => $subCategory)
                            <option value="{{$subCategory->id}}">{{$subCategory->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <!--7 Feb 2021 --Add Sub sub category -->


        <div class="form-group" id="subcategory">
            <label class="col-lg-3 control-label">{{ __('Sub Sub Categories') }}</label>
            <div class="col-lg-9">
                <select class="form-control subsubcategory_id demo-select2" name="subsubcategory_ids[]" multiple>
                    <option value="">Select</option>
                    @if(isset($subSubCategories) && count($subSubCategories) > 0)
                        @foreach($subSubCategories as $key => $subSubCategory)
                            <option value="{{$subSubCategory->id}}">{{$subSubCategory->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">{{ __('No. of coupon') }}</label>
            <div class="col-lg-9">
                <input id="max_coupon" type="number" min="0" step="1" placeholder="{{ __('Maximum Limit') }}" name="max_coupon" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">{{ __('Minimum Cart Value') }}</label>
            <div class="col-lg-9">
                <input id="min_buy" type="number" min="0" step="0.01" placeholder="{{ __('Minimum Shopping') }}" name="min_buy" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">{{ __('Discount') }}</label>
            <div class="col-lg-8">
                <input id="discount" type="number" min="0" step="0.01" placeholder="{{ __('Discount') }}" name="discount" class="form-control" required>
            </div>
            <div class="col-lg-1">
                <select class="demo-select2" id="discount_type" name="discount_type">
                    <option value="amount">$</option>
                    <option value="percent">%</option>
                </select>
            </div>
        </div>
        <div class="form-group d-none" id="maximum_discount_amount">
            <label class="col-lg-3 control-label">{{ __('Maximum Discount Amount') }}</label>
            <div class="col-lg-8">
                <input id="max_discount" type="number" min="0" step="0.01" placeholder="{{ __('Maximum Discount Amount') }}" 
                       name="max_discount" class="form-control" required disabled>
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
                <input type="text" name="daterange" value="" />
            </div>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('Only once') }}</label>
    <div class="col-sm-9">
        <label class="pull-left mt-2">
            <input type="checkbox" name="only_once" value="1">
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
    });

</script>
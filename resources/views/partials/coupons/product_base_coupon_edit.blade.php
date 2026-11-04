@include('partials.coupons.inc.visibility_coupon')

<div class="panel-heading">
    <h3 class="panel-title">{{ __('Add Your Product Base Coupon') }}</h3>
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

<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('No. of coupon') }}</label>
    <div class="col-lg-9">
        <input id="max_coupon" type="number" min="0" step="1" placeholder="{{ __('Maximum Limit') }}" name="max_coupon" class="form-control" @isset($details->max_coupon) value="{{ $details->max_coupon }}" @endisset required>
    </div>
</div>

<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('Minimum Cart Value') }}</label>
    <div class="col-lg-9">
        <input type="number" id="min_buy" min="0" step="0.01" name="min_buy" class="form-control" value="{{ $coupon->min_buy }}" required>
    </div>
</div>

<div class="product-choose-list">
    <hr>
    @foreach ($details->info as $key => $info)
        <div class="product-choose">
            @if ($key !== 0)
                <div class="mb-3 text-right">
                    <button class="btn btn-danger " type="button" name="button" onclick="RemoveProductChoose(this)">
                        <i class="fa fa-recycle"></i>
                        <span>{{ __('Remove') }}</span>
                    </button>
                </div>
            @endif
            <div class="form-group">
                <label class="col-lg-3 control-label">{{ __('Category') }}</label>
                <div class="col-lg-9">
                    <select class="form-control category_id demo-select2" name="category_ids[]" required>
                        @foreach($categories as $key => $category)
                            <option value="{{$category->id}}" @if ($info->category_id == $category->id)
                            selected
                                @endif >{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group" id="subcategory">
                <label class="col-lg-3 control-label">{{ __('Sub Category') }}</label>
                <div class="col-lg-9">
                    <select class="form-control subcategory_id demo-select2" name="subcategory_ids[]" required>
                        @foreach(\App\Models\SubCategory::where('category_id', $info->category_id)->get() as $key => $subcategory)
                            <option value="{{$subcategory->id}}" @if ($info->subcategory_id == $subcategory->id)
                            selected
                                @endif >{{$subcategory->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group" id="subsubcategory">
                <label class="col-lg-3 control-label">{{ __('sub Sub Category') }}</label>
                <div class="col-lg-9">
                    <select class="form-control subsubcategory_id demo-select2" name="subsubcategory_ids[]">
                        @foreach(\App\Models\SubSubCategory::where('sub_category_id', $info->subcategory_id)->get() as $key => $subsubcategory)
                            <option value="{{$subsubcategory->id}}" @if ($info->subsubcategory_id == $subsubcategory->id)
                            selected
                                @endif >{{$subsubcategory->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label" for="name">{{ __('Product') }}</label>
                <div class="col-lg-9">
                    <select name="product_ids[]" class="form-control product_id demo-select2" required>
                        @foreach(\App\Product::where('category_id', $info->category_id)->get() as $key => $product)
                            <option value="{{$product->id}}" @if ($info->product_id == $product->id)
                            selected
                                @endif >{{$product->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr>
        </div>
    @endforeach
</div>
<div class="more hide">
    <div class="product-choose">
        <div class="mb-3 text-right">
            <button class="btn btn-danger " type="button" name="button" onclick="RemoveProductChoose(this)">
                <i class="fa fa-recycle"></i>
                <span>{{ __('Remove') }}</span>
            </button>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">{{ __('Category') }}</label>
            <div class="col-lg-9">
                <select class="form-control category_id" name="category_ids[]" onchange="get_subcategories_by_category(this)">
                    <option value="">Select a Category</option>
                    @foreach($categories as $key => $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group" id="subcategory">
            <label class="col-lg-3 control-label">{{ __('Sub Category') }}</label>
            <div class="col-lg-9">
                <select class="form-control subcategory_id" name="subcategory_ids[]" onchange="get_subsubcategories_by_subcategory(this);get_products_by_subcategory(this)">
                </select>
            </div>
        </div>

        <div class="form-group" id="subcategory">
            <label class="col-lg-3 control-label">{{ __('Sub Sub Category') }}</label>
            <div class="col-lg-9">
                <select class="form-control subsubcategory_id" name="subsubcategory_ids[]" onchange="get_products_by_subsubcategory(this)">
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label" for="name">{{ __('Product') }}</label>
            <div class="col-lg-9">
                <select name="product_ids[]" class="form-control product_id">
                </select>
            </div>
        </div>
        <hr>
    </div>
</div>
<div class="text-right">
    <button class="btn btn-primary" type="button" name="button" onclick="appendNewProductChoose()">{{ __('Add More') }}</button>
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
        <input type="number" id="max_discount" min="0" step="0.01" placeholder="{{ __('Maximum Discount Amount') }}" name="max_discount" class="form-control"  @isset($details->max_discount) value="{{ $details->max_discount }}" @endisset required>
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

<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('Only once') }}</label>
    <div class="col-sm-9">
        <label class="pull-left mt-2">
            <input type="checkbox" name="only_once" value="1" {{ $coupon->only_once  == 1 ? 'checked' : '' }}>
        </label>
    </div>
</div>
<script type="text/javascript">
    function appendNewProductChoose(){
        $('.product-choose-list').append($('.more').html());
        $('.product-choose-list').find('.product-choose').last().find('.category_id').select2();
    }

    function RemoveProductChoose(el){
        if (el.closest('.product-choose')) {
            el.closest('.product-choose').remove();
        }
    }

    function get_subcategories_by_category(el){
        var category_id = $(el).val();
        console.log(category_id);
        $(el).closest('.product-choose').find('.subcategory_id').html(null);
        $.post('{{ route('subcategories.get_subcategories_by_category') }}',{_token:'{{ csrf_token() }}', category_id:category_id}, function(data){
            $(el).closest('.product-choose').find('.subcategory_id').append($('<option>', {
                value: "",
                text: "Select a sub category"
            }));
            for (var i = 0; i < data.length; i++) {
                $(el).closest('.product-choose').find('.subcategory_id').append($('<option>', {
                    value: data[i].id,
                    text: data[i].name
                }));
            }
            $(el).closest('.product-choose').find('.subcategory_id').select2();
            get_subsubcategories_by_subcategory($(el).closest('.product-choose').find('.subcategory_id'));
        });
    }
    function get_products_by_subcategory(el){
        var subcategory_id = $(el).val();
        console.log(subcategory_id);
        $(el).closest('.product-choose').find('.product_id').html(null);
        $.post('{{ route('products.get_products_by_subcategory') }}',{_token:'{{ csrf_token() }}', subcategory_id:subcategory_id}, function(data){
            for (var i = 0; i < data.length; i++) {
                $(el).closest('.product-choose').find('.product_id').append($('<option>', {
                    value: data[i].id,
                    text: data[i].name
                }));
            }
            $(el).closest('.product-choose').find('.product_id').select2();
        });
    }
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

    $('.category_id').on('change', function() {
        get_subcategories_by_category(this);
    });
    $('.subcategory_id').on('change', function() {
        get_subsubcategories_by_subcategory(this);
    });
    $('.subsubcategory_id').on('change', function() {
        get_products_by_subsubcategory(this);
    });
    function get_subsubcategories_by_subcategory(el){

        var subcategory_id = $(el).val();
        console.log(subcategory_id);
        console.log("subsub");
        $(el).closest('.product-choose').find('.subsubcategory_id').html(null);
        $.post('{{ route('subsubcategories.get_subsubcategories_by_subcategory') }}',{_token:'{{ csrf_token() }}', subcategory_id:subcategory_id}, function(data){
            $(el).closest('.product-choose').find('.subsubcategory_id').append($('<option>', {
                value: "",
                text: "Select a sub sub category"
            }));
            for (var i = 0; i < data.length; i++) {
                $(el).closest('.product-choose').find('.subsubcategory_id').append($('<option>', {
                    value: data[i].id,
                    text: data[i].name
                }));
            }
            $(el).closest('.product-choose').find('.subsubcategory_id').select2();
            get_products_by_subcategory($(el).closest('.product-choose').find('.subcategory_id'));
        });
    }
    function get_products_by_subsubcategory(el){
        var subsubcategory_id = $(el).val();
        console.log(subsubcategory_id);
        $(el).closest('.product-choose').find('.product_id').html(null);
        $.post('{{ route('products.get_products_by_subsubcategory') }}',{_token:'{{ csrf_token() }}', subsubcategory_id:subsubcategory_id}, function(data){
            for (var i = 0; i < data.length; i++) {
                $(el).closest('.product-choose').find('.product_id').append($('<option>', {
                    value: data[i].id,
                    text: data[i].name
                }));
            }
            $(el).closest('.product-choose').find('.product_id').select2();
        });
    }
</script>

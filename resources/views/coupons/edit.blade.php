@extends('layouts.app')
@section('content')
    <div class="col-lg-8 col-lg-offset-2">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ __('Coupon Information Update') }}</h3>
            </div>
            <form id="form_edit" class="form-horizontal" action="{{ route('coupon.update', $coupon->id) }}" method="POST" enctype="multipart/form-data">
                <input name="_method" type="hidden" value="PATCH">
            	@csrf
                <div class="panel-body">
                    <input type="hidden" name="id" value="{{ $coupon->id }}" id="id">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="name">{{ __('Coupon Type') }}</label>
                        <div class="col-lg-9">
                            <select name="coupon_type" id="coupon_type" class="form-control demo-select2-placeholder" onchange="coupon_form()" required>
                                @if ($coupon->type == "product_base")
                                    <option value="product_base" selected>{{ __('For Products') }}</option>
                                @elseif ($coupon->type == "cart_base")
                                    <option value="cart_base">{{ __('For Total Orders') }}</option>
                                @elseif ($coupon->type == "category_base")
                                    <option value="category_base">{{ __('For Categories') }}</option>
                                @elseif ($coupon->type == "cash_base")
                                    <option value="cash_base">{{ __('For Bok Choy') }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div id="coupon_form">

                    </div>
                <div class="panel-footer text-right">
                    <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
<script type="text/javascript">
    function coupon_form(){
        var coupon_type = $('#coupon_type').val();
        var id = $('#id').val();
		$.post('{{ route('coupon.get_coupon_form_edit') }}',{_token:'{{ csrf_token() }}', coupon_type:coupon_type, id:id}, function(data){
            $('#coupon_form').html(data);
            $('#demo-dp-range .input-daterange').datepicker({
                startDate: '-0d',
                todayBtn: "linked",
                autoclose: true,
                todayHighlight: true
        	});

            $("#form_edit").validate({
                rules: {
                    discount: {
                        min: 0,
                        max: function() 
                        {
                            if($("#discount_type").val() === "percent") {
                                return 100
                            }  
                        }
                    },
                    max_coupon: {
                        min: 0
                    },
                    min_buy: {
                        min: 0
                    },
                    max_discount: {
                        min: 0
                    }
                },
            });

            $('#discount_type').on('change', function() {
                change_type_attribute_validation();
            });
            
            function change_type_attribute_validation() {
                $("#form_edit").validate({
                    rules: {
                        discount: {
                            min: 0,
                            max: function()
                            {
                                if($("#discount_type").val() === "percent") {
                                    return 100
                                }  
                            }
                        },
                        max_discount: {
                            min: 0
                        },
                        min_buy: {
                            min: 0
                        },
                        max_discount: {
                            min: 0
                        }
                    },
                });
            }

		});

    }

    $(document).ready(function(){
        coupon_form();
    });

</script>
@endsection
@section('css')
<style>
    .d-none {
        display: none !important;
    }
    .enable_maximum_discount_amount {
        padding: 6px 11px;
        border: 1px solid 607489;
        border-radius: 6px;
        background: #607489;
    }
</style>
@endsection


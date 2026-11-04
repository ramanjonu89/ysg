@extends('layouts.app')
@section('content')
    <div class="col-lg-8 col-lg-offset-2">
        <div class="panel">
            <div class="panel-heading">
                @if ($is_generation_bulk)
                    <h3 class="panel-title">{{ __('Bulk Generation Coupons') }}</h3>
                @elseif ($is_cash_voucher)
                    <h3 class="panel-title">{{ __('New Bok Choy Set Up') }}</h3>
                @else
                    <h3 class="panel-title">{{ __('Coupon Information Adding') }}</h3>
                @endif
            </div>
            <form id="form_create" class="form-horizontal" action="{{ route('coupon.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="name">{{ __('Coupon Type') }}</label>
                        <div class="col-lg-9">
                            <input type="hidden" name="is_generation_bulk" id="is_generation_bulk" value="{{ $is_generation_bulk }}">
                            @if ($is_cash_voucher)
                                @include('coupons.inc.select_type_cash_coupon')
                            @else
                                @include('coupons.inc.select_type_normal_coupon')
                            @endif
                        </div>
                    </div>
                    <div id="coupon_form">
                    </div>
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
            const coupon_type = $('#coupon_type').val();
            const is_generation_bulk = $('#is_generation_bulk').val();

            $.post('{{ route('coupon.get_coupon_form') }}',{_token:'{{ csrf_token() }}', 
                coupon_type:coupon_type,
                is_generation_bulk:is_generation_bulk
            }, function(data){
                $('#coupon_form').html(data);
                $('#demo-dp-range .input-daterange').datepicker({
                    startDate: '-0d',
                    todayBtn: "linked",
                    autoclose: true,
                    todayHighlight: true
                });

                $('#discount_type').on('change', function() {
                    change_type_attribute_validation();
                });
                
                function change_type_attribute_validation() {
                    $("#form_create").validate({
                        rules: {
                            discount: {
                                min: 0,
                                max: function()
                                {
                                    if($("#discount_type").val() === "percent")
                                    {
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
                }

                $('#discount_type').on('change', function() {
                    if (this.value === 'percent') {
                        $('#maximum_discount_amount').removeClass('d-none');
                    } else {
                        $('#max_discount').val('');
                        $('#max_discount').prop('disabled', true);
                        $('#maximum_discount_amount').addClass('d-none');
                        $('#enable_maximum_discount_amount').prop('checked', false);
                    }
                });
                $('#enable_maximum_discount_amount').on('change', function() {
                    const isDisabled = $('#max_discount').prop('disabled');
                    if (!isDisabled) {
                        $('#max_discount').prop('disabled', true);
                    } else {
                        $('#max_discount').prop('disabled', false);
                    }
                });

            });
        }
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

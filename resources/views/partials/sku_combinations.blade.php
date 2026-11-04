@if(count($combinations[0]) > 0)
	<table class="table table-bordered">
		<thead>
			<tr>
				<td class="text-center">
					<label for="" class="control-label">{{ __('Variant') }}</label>
				</td>
				<td class="text-center">
					<label for="" class="control-label">{{ __('Variant Price') }}</label>
				</td>
				<td class="text-center">
					<label for="" class="control-label">{{ __('SKU') }}</label>
				</td>
				<td class="text-center">
					<label for="" class="control-label">{{ __('Quantity') }}</label>
				</td>
			</tr>
		</thead>
		<tbody>
		@foreach ($combinations as $key => $combination)
			@php
				$sku = '';
				foreach (explode(' ', $product_name) as $key => $value) {
					$sku .= substr($value, 0, 1);
				}
				$str = '';
				foreach ($combination as $key => $item){
					if ($key > 0 ){
						$str .= '-'.str_replace(' ', '', $item);
						$sku .='-'.str_replace(' ', '', $item);
					} else {
						if ($colors_active == 1) {
							$color_name = \App\Color::where('code', $item)->first()->name;
							$str .= $color_name;
							$sku .='-'.$color_name;
						} else {
							$str .= str_replace(' ', '', $item);
							$sku .='-'.str_replace(' ', '', $item);
						}
					}
				}
			@endphp
			@if(strlen($str) > 0)
				<tr class="variant">
					<td>
						<label for="" class="control-label">{{ $str }}</label>
					</td>
					<td>
						<input type="number" name="price_{{ $str }}" value="{{ $unit_price }}" id="price_stock" min="0" step="0.01" class="form-control" required>
					</td>
					<td>
						<input type="text" name="sku_{{ $str }}" value="{{ $sku }}" class="form-control" required>
					</td>
					<td>
						<input type="number" name="qty_{{ $str }}" value="10" min="0" step="1" class="form-control" required>
					</td>
					<td>
						<div class="d-flex">
							<div class="w-75 pr-1">
								<input type="number" id="discount_stock_{{ $str }}" name="discount_{{ $str }}" step="0.01" class="form-control discount_stock" value="{{ $discount }}" required>
							</div>
							<div class="w-25">
								<select class="demo-select2 select_discount_type" name="discount_type_{{ $str }}" required>
									<option value="amount" >{{__('Flat')}}</option>
									<option value="percent">{{__('Percent')}}</option>
								</select>
							</div>
						</div>
					</td>
				</tr>
			@endif
		@endforeach
	</tbody>
</table>
<script type="text/javascript">
	
	$('.variant').each(function() {
		const variantElement = $(this);
		const rules = $.extend({}, getDiscountValidationRules(variantElement));
		$("#choice_form").validate().settings.rules = $.extend({}, $("#choice_form").validate().settings.rules, rules);
	});

	function getDiscountValidationRules(variantElement) {
		const price = parseFloat(variantElement.find('.price-stock input[type="number"]').val());
		const discountName = variantElement.find('.discount_stock').attr('name');
		const validationRules = {};
		validationRules[discountName] = {
			min: 0,
			max: price,
		};
		return validationRules;
	}

	function change_type_attribute_validation_variant(element) {
			const price = element.closest('tr').find('#price').val();
			const discount_name = element.closest('.d-flex').find('.discount_stock').attr('name');
			const discount_type = element.val();
			const validationRules = {};

			validationRules[discount_name] = {
				min: 0,
				max: function() {
					if (discount_type === "percent") {
						return 100;
					}
					return parseFloat(price);
				}
			
			};
			validationRules.discount = {
					min: 0,
					max: function()
					{
						if ($("#discount_type").val() === "percent") {
							return 100;
						} 

						return parseFloat($("#unit_price").val());
					}
				};

			$("#choice_form").validate().settings.rules = validationRules;

		}

		$('.select_discount_type').on('change', function() {
			change_type_attribute_validation_variant($(this));
		});
	</script>
@endif


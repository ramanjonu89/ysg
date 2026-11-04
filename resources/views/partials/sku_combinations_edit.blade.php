@if(count($combinations[0]) > 0)
	<table class="table table-bordered" id="table_sku">
		<thead>
			<tr class="text-center">
				<td>
					<label for="" class="control-label">{{ __('Variant') }}</label>
				</td>
				<td>
					<label for="" class="control-label">{{ __('Variant Price') }}</label>
				</td>
				<td>
					<label for="" class="control-label">{{ __('SKU') }}</label>
				</td>
				<td>
					<label for="" class="control-label">{{ __('Quantity') }}</label>
				</td>
				<td>
					<label for="" class="control-label">{{ __('Discount') }}</label>
				</td>
			</tr>
		</thead>
		<tbody class="tbody">
		@foreach ($combinations as $key => $combination)
			@php
				$sku = '';
				$str = '';
				foreach (explode(' ', $product_name) as $key => $value) {
					$sku .= substr($value, 0, 1);
				}
				foreach ($combination as $key => $item){
					if ($key > 0) {
						$str .= '___'.str_replace(' ', '', $item);
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
				$productStock = $product->stocks->where('variant', $str)->first();
				if ($productStock) {
					$sku		   = $productStock->sku;
					$qty           = $productStock->qty;
					$price         = $productStock->price;
					$discount      = $productStock->discount;
					$discount_type = $productStock->discount_type;
				} else {
					$qty      = 10;
					$discount = 0;
					$sku 	  = $product->unit . '-' . $str;
					$price         = $unit_price;
					$discount_type = 'amount';
				}
			@endphp
			@if(strlen($str) > 0)
				<tr class="variant">
					<td>
						<label for="" class="control-label">{{ $str }}</label>
					</td>
					<td class="price-stock">
						<input type="number" id="price" name="price_{{ $str }}" value="{{ $price }}" min="0" step="0.01" class="form-control price_stock" required>
					</td>
					<td>
						<input type="text" name="sku_{{ $str }}" value="{{ $sku }}" class="form-control" required>
					</td>
					<td>
						<input type="number" name="qty_{{ $str }}" value="{{ $qty }}" min="0" step="1" class="form-control qty_stock" required>
					</td>
					<td>
						<div class="d-flex">
							<div class="w-75 pr-1">
								<input type="number" id="discount_stock_{{ $str }}" name="discount_{{ $str }}" step="0.01" class="form-control discount_stock" value="{{ $discount }}" required>
							</div>
							<div class="w-25">
								<select class="demo-select2 select_discount_type" name="discount_type_{{ $str }}" required>
									<option value="amount"  @if($discount_type == 'amount') selected @endif>{{__('Flat')}}</option>
									<option value="percent" @if($discount_type == 'percent') selected @endif>{{__('Percent')}}</option>
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
		$('.demo-select2').select2();

		$(document).ready(function() {
			$('.variant').each(function() {
				const variantElement = $(this);
				const rules = $.extend({}, getDiscountValidationRules(variantElement));
				$("#choice_form").validate().settings.rules = $.extend({}, $("#choice_form").validate().settings.rules, rules);
			});

			function getDiscountValidationRules(variantElement) {
				const discountType = variantElement.find('.select_discount_type').val();
				const price = parseFloat(variantElement.find('.price-stock input[type="number"]').val());
				const { qtyName, priceName, discountName } = getValidationRuleNames(variantElement);

				const validationRules = {
					[discountName]: createValidationRule(discountType, price),
					[qtyName]: { 
							min: 0 
						},
					[priceName]: { 
							min: 0 
						}
				};

				return validationRules;
			}
		});

		$('.price-stock input[type="number"]').on('change', function() {
			const rules = $.extend({}, getDiscountValidationRulesOnChangePrice($(this)));
			$("#choice_form").validate().settings.rules = $.extend({}, $("#choice_form").validate().settings.rules, rules);
		});

		function getDiscountValidationRulesOnChangePrice(element) {
			const price = parseFloat(element.val());
			const { qtyName, priceName, discountName } = getValidationRuleNames(element.closest('.variant'));
			const validationRules = {
				[discountName]: {
					min: 0,
					max: price,
				}
			};

			return validationRules;
		}

		$('.select_discount_type').on('change', function() {
			const rules = $.extend({}, getDiscountValidationRulesOnChangeAttributeVariant($(this)));
			$("#choice_form").validate().settings.rules = $.extend({}, $("#choice_form").validate().settings.rules, rules);
		});

		function getDiscountValidationRulesOnChangeAttributeVariant(element) {
			const price = parseFloat(element.closest('tr').find('#price').val());
			const discountType = element.val();
			const { qtyName, priceName, discountName } = getValidationRuleNames(element.closest('.variant'));
			const validationRules = {
				[discountName]: createValidationRule(discountType, price),
				[qtyName]: { 
						min: 0 
					},
				[priceName]: { 
						min: 0 
					},
			};

			return validationRules;
		}


		function getValidationRuleNames(variantElement) {
			const qtyName = variantElement.find('.qty_stock').attr('name');
			const priceName = variantElement.find('.price_stock').attr('name');
			const discountName = variantElement.find('.discount_stock').attr('name');
		
			return { qtyName, priceName, discountName };
		}

		function createValidationRule(discountType, price) {
			return {
				min: 0,
				max: function() {
				if (discountType === "percent") {
					return 100;
				}
					return price;
				}
			};
		}

	</script>
@endif


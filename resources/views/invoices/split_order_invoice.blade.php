<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Laravel</title>
	<meta http-equiv="Content-Type" content="text/html;"/>
	<meta charset="UTF-8">
	<style media="all">
		@font-face {
			font-family: 'Roboto';
			src: url("{{ my_asset('fonts/Roboto-Regular.ttf') }}") format("truetype");
			font-weight: normal;
			font-style: normal;
		}
		* {
			margin: 0;
			padding: 0;
			line-height: 1.3;
			font-family: 'Roboto';
			color: #333542;
		}
		body {
			font-size: .875rem;
		}
		.gry-color *,
		.gry-color {
			color: #878f9c;
		}
		table {
			width: 100%;
		}
		table th {
			font-weight: normal;
		}
		table.padding th {
			padding: .5rem .7rem;
		}
		table.padding td {
			padding: .7rem;
		}
		table.sm-padding td {
			padding: .2rem .7rem;
		}
		table td .variation {
			padding-left: 1rem;
		}
		.border-bottom td,
		.border-bottom th {
			border-bottom: 1px solid #eceff4;
		}
		.text-left {
			text-align: left;
		}
		.text-right {
			text-align: right;
		}
		.small {
			font-size: .85rem;
		}
		.currency {

		}
	</style>
</head>
<body>
<div style="margin-left:auto;margin-right:auto;">
	<div style="background: #eceff4;padding: 1.5rem;">
		<table>
			<tr>
				<td>
					<img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($seller->shop->logo))) }}" height="40" style="display:inline-block;">
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td style="font-size: 1.2rem;" class="strong">{{ $seller->shop->name }}</td>
				<td class="text-right"></td>
			</tr>
			<tr>
				<td class="gry-color small">{{ $seller->shop->address }}</td>
				<td class="text-right"></td>
			</tr>
			<tr>
				<td class="gry-color small">{{ __('Email') }}: {{ $seller->email }}</td>
				<td class="text-right small"><span class="gry-color small">{{ __('Order ID') }}:</span> <span
							class="strong">{{ $order->code }}</span></td>
			</tr>
			<tr>
				<td class="gry-color small">{{ __('Phone') }}: {{ $seller->phone }}</td>
				<td class="text-right small"><span class="gry-color small">{{ __('Order Date') }}:</span>
					<span class=" strong">{{ date('d-m-Y', $order->date) }}</span></td>
			</tr>
			<tr>
				<td class="gry-color small">{{  __('Postal Code') }}: {{ $seller->postal_code }}</td>
			</tr>
		</table>

	</div>

	<div style="padding: 1.5rem;padding-bottom: 0">
		<table>
			<tr>
				<td class="strong small gry-color">{{ __('Bill to') }}:</td>
			</tr>
			<tr>
				<td class="strong">{{ $shippingAddress->name }}</td>
			</tr>
			<tr>
				@if ($isSelfCollection)
				<td class="gry-color small">-</td>
				@else
				<td class="gry-color small">{{ $shippingAddress->address }}, {{ $shippingAddress->city }}
					, {{ $shippingAddress->country }}</td>
				@endif
			</tr>
			<tr>
				<td class="gry-color small">{{ __('Email') }}: {{ $shippingAddress->email }}</td>
			</tr>
			<tr>
				<td class="gry-color small">{{ __('Postal Code') }}: {{ $shippingAddress->postal_code }}</td>
			</tr>

		</table>
	</div>

	<div style="padding: 1.5rem;">
		<table class="padding text-left small border-bottom">
			<thead>
			<tr class="gry-color" style="background: #eceff4;">
				<th width="35%">{{ __('Product Name') }}</th>
				<th width="35%">{{ __('Variation') }}</th>
				<th width="15%">{{ __('Delivery Type') }}</th>
				<th width="10%">{{ __('Qty') }}</th>
				<th width="15%">{{ __('Unit Price') }}</th>
				<th width="10%">{{ __('Tax') }}</th>
				<th width="15%" class="text-right">{{ __('Total') }}</th>
			</tr>
			</thead>
			<tbody class="strong">
			@foreach ($orderItems as $key => $orderDetail)
				@if ($orderDetail->product)
					<tr class="">
						<td>
							{{ $orderDetail->product->name }}
							@if ($isSelfCollection)
								{{ __('Self-Collection address:') }}
								{{ $orderDetail->collection_address }}
							@endif
						</td>
						<td>
							<div class="variation">
								<ul class="mb-0 pl-4">
									@foreach ($orderDetail->getVariantDetails() as $variant)
										<li>
											{{ $variant }}
										</li>
									@endforeach
								</ul>
							</div>
						</td>
						<td>
							@if ($orderDetail->isShippingTypeHomeDelivery())
								{{ __('Home Delivery') }}
							@elseif ($orderDetail->isShippingTypeSelfCollection())
								{{ __('Self-Collection') }}
							@elseif ($orderDetail->isShippingTypePickupPoint())
								{{ $orderDetail->pickup_point->name }} ({{ __('Pickup Point') }})
							@endif
						</td>
						<td class="gry-color">{{ $orderDetail->quantity }}</td>
						<td class="gry-color currency">{{ single_price($orderDetail->price/$orderDetail->quantity) }}</td>
						<td class="gry-color currency">{{ single_price($orderDetail->tax/$orderDetail->quantity) }}</td>
						<td class="text-right currency">{{ single_price($orderDetail->price+$orderDetail->tax) }}</td>
					</tr>
				@endif
			@endforeach
			</tbody>
		</table>
	</div>

	<div style="padding:0 1.5rem;">
		<table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">
			<tbody>
			<tr>
				<th class="gry-color text-left">{{ __('Sub Total') }}</th>
				<td class="currency">{{ single_price($splitOrder->sub_total) }}</td>
			</tr>
			<tr>
				<th class="gry-color text-left">{{ __('Shipping Cost') }}</th>
				<td class="currency">{{ single_price(($splitOrder->shipping_cost)) }}</td>
			</tr>
			<tr>
				<th class="gry-color text-left">{{ __('Total Tax') }}</th>
				<td class="currency">{{ single_price($orderItems->sum('tax')) }}</td>
			</tr>
			<tr class="border-bottom">
				<th class="gry-color text-left">{{ __('Discount') }}</th>
				<td class="currency">{{ single_price($splitOrder->discount_amount) }}</td>
			</tr>
			<tr>
				<th class="text-left strong">{{ __('Grand Total') }}</th>
				<td class="currency">{{ single_price($splitOrder->grand_total) }}</td>
			</tr>
			</tbody>
		</table>
	</div>

</div>
</body>
</html>

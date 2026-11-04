@extends('layouts.app')
@section('content')
	<div>
		<h1 class="page-header text-overflow">{{ __('Edit Product') }}</h1>
	</div>
	<div class="row mar-top">
		<div class="col-lg-8">
			<form class="form form-horizontal" action="{{route('products.update', $product->id)}}" method="POST" enctype="multipart/form-data" id="choice_form">
				<input name="_method" type="hidden" value="POST">
				<input type="hidden" name="id" value="{{ $product->id }}">
				@csrf
				<div class="panel">
					<div class="panel-heading bord-btm">
						<h3 class="panel-title">{{__('Product Information')}}</h3>
					</div>
					<div class="panel-body">
						{{-- fix --}}
						@if (isset($systemLanguages) && count($systemLanguages) > 0)
						<div id="tab-form">
							<ul class="nav nav-pills mb-5" style="background-color: #d486e1;">
									@foreach ($systemLanguages as $key => $language)
										<li class="{{ $key == 0 ? 'active' : '' }}"><a href="#{{ $language->id }}"
												style="color:#fff" data-toggle="tab">{{ $language->name }}</a></li>
									@endforeach
							</ul>

							<div class="tab-content clearfix">
								@foreach ($systemLanguages as $key => $language)
									<div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">
										@if (isset($product) && $product->productTranslation($language->id) !== null)
											<div class="form-group">
												<label class="col-lg-2 control-label">{{ __('Product Name') }}</label>
												<div class="col-lg-7">
													<input type="text" class="form-control" name="name[]" placeholder="{{ __('Product Name') }}" value="{{ $product->productTranslation($language->id)->name }}" {{ $key == 0 ? 'required' : '' }}>
												</div>
											</div>

											<div class="form-group">
												<label class="col-lg-2 control-label">{{ __('Description') }}</label>
												<div class="col-lg-9">
													<textarea class="editor" name="description[]">{{$product->productTranslation($language->id)->description}}</textarea>
												</div>
											</div>

											<div class="form-group">
												<label class="col-lg-2 control-label">{{ __('Meta Title') }}</label>
												<div class="col-lg-7">
													<input type="text" class="form-control" name="meta_title[]" value="{{ $product->productTranslation($language->id)->meta_title }}" placeholder="{{ __('Meta Title') }}">
												</div>
											</div>

											<div class="form-group">
												<label class="col-lg-2 control-label">{{ __('Meta Description') }}</label>
												<div class="col-lg-7">
													<textarea name="meta_description[]" rows="8" class="form-control">{{ $product->productTranslation($language->id)->meta_description }}</textarea>
												</div>
											</div>
										@else
											<div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">
												<div class="form-group">
													<label class="col-lg-2 control-label">{{ __('Product Name') }}</label>
													<div class="col-lg-7">
														<input type="text" class="form-control" name="name[]" placeholder="{{ __('Product Name') }}" value="{{ $product->name }}" {{ $key == 0 ? 'required' : '' }}>
													</div>
												</div>

												<div class="form-group">
													<label class="col-lg-2 control-label">{{ __('Description') }}</label>
													<div class="col-lg-9">
														<textarea class="editor" name="description[]">{{$product->description}}</textarea>
													</div>
												</div>
												<div class="form-group">
													<label class="col-lg-2 control-label">{{ __('Meta Title') }}</label>
													<div class="col-lg-7">
														<input type="text" class="form-control" name="meta_title[]"  value="{{ $product->meta_title }}"  placeholder="{{ __('Meta Title') }}">
													</div>
												</div>

												<div class="form-group">
													<label class="col-lg-2 control-label">{{ __('Meta Description') }}</label>
													<div class="col-lg-7">
														<textarea name="meta_description[]" rows="8" class="form-control">{{ $product->meta_description }}</textarea>
													</div>
												</div>
											</div>
										@endif

									</div>
									<input type="hidden" name="language_id[]" value="{{ $language->id }}">
								@endforeach
							</div>
						</div>
						@endif
						{{-- end fix --}}

						<div class="form-group" id="category">
							<label class="col-lg-2 control-label">{{__('Category')}}</label>
							<div class="col-lg-7">
								<select class="form-control demo-select2-placeholder" name="category_id" id="category_id">
									<option value="">{{ __('Select an option') }}</option>
									@foreach($categories as $category)
										<option value="{{$category->id}}" <?php if($product->category_id == $category->id) echo "selected"; ?> >{{__($category->name)}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group" id="subcategory">
							<label class="col-lg-2 control-label">{{__('Subcategory')}}</label>
							<div class="col-lg-7">
								<select class="form-control demo-select2-placeholder" name="subcategory_id" id="subcategory_id">
								</select>
							</div>
						</div>
						<div class="form-group" id="subsubcategory">
							<label class="col-lg-2 control-label">{{__('Sub Subcategory')}}</label>
							<div class="col-lg-7">
								<select class="form-control demo-select2-placeholder" name="subsubcategory_id" id="subsubcategory_id">
								</select>
							</div>
						</div>
						<div class="form-group" id="brand">
							<label class="col-lg-2 control-label">{{__('Brand')}}</label>
							<div class="col-lg-7">
								<select class="form-control demo-select2-placeholder" name="brand_id" id="brand_id">
									<option value="">{{ ('Select Brand') }}</option>
									@foreach ($brands as $brand)
										<option value="{{ $brand->id }}" @if($product->brand_id == $brand->id) selected @endif>{{ $brand->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Unit')}}</label>
							<div class="col-lg-7">
								<input type="text" class="form-control" name="unit" placeholder="{{ __('Unit (e.g. KG, Pc etc)') }}" value="{{$product->unit}}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Minimum Qty')}}</label>
							<div class="col-lg-7">
								<input type="number" class="form-control" name="min_qty" value="@if($product->min_qty <= 1){{1}}@else{{$product->min_qty}}@endif" min="1">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Tags')}}</label>
							<div class="col-lg-7">
								<input type="text" class="form-control" name="tags[]" id="tags" value="{{ $product->tags }}" placeholder="{{ __('Type to add a tag') }}" data-role="tagsinput">
							</div>
						</div>
						@php
							$pos_addon = \App\Addon::where('unique_identifier', 'pos_system')->first();
						@endphp
						@if ($pos_addon != null && $pos_addon->activated == 1)
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Barcode')}}</label>
								<div class="col-lg-7">
									<input type="text" class="form-control" name="barcode" placeholder="{{ __('Barcode') }}" value="{{ $product->barcode }}">
								</div>
							</div>
						@endif
						@php
							$refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
						@endphp
						@if ($refund_request_addon != null && $refund_request_addon->activated == 1)
							<div class="form-group">
								<label class="col-lg-2 control-label">{{__('Refundable')}}</label>
								<div class="col-lg-7">
									<label class="switch" style="margin-top:5px;">
										<input type="checkbox" name="refundable" @if ($product->refundable == 1) checked @endif>
										<span class="slider round"></span></label>
									</label>
								</div>
							</div>
						@endif

					</div>
				</div>
				{{-- image --}}
				<div class="panel">
					<div class="panel-heading bord-btm">
						<h3 class="panel-title">{{__('Product Images')}}</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Gallery Images')}}</label>
							<div class="col-lg-7">
								<div id="photos">
									@if(is_array(json_decode($product->photos)))
										@foreach (json_decode($product->photos) as $key => $photo)
											<div class="col-md-4 col-sm-4 col-xs-6">
												<div class="img-upload-preview">
													<img loading="lazy"  src="{{ my_asset($photo) }}" alt="" class="img-responsive">
													<input type="hidden" name="previous_photos[]" value="{{ $photo }}">
													<button type="button" class="btn btn-danger close-btn remove-files"><i class="fa fa-times"></i></button>
												</div>
											</div>
										@endforeach
									@endif
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Thumbnail Image')}} <small>(290x300)</small></label>
							<div class="col-lg-7">
								<div id="thumbnail_img">
									@if ($product->thumbnail_img != null)
										<div class="col-md-4 col-sm-4 col-xs-6">
											<div class="img-upload-preview">
												<img loading="lazy"  src="{{ my_asset($product->thumbnail_img) }}" alt="" class="img-responsive">
												<input type="hidden" name="previous_thumbnail_img" value="{{ $product->thumbnail_img }}">
												<button type="button" class="btn btn-danger close-btn remove-files"><i class="fa fa-times"></i></button>
											</div>
										</div>
									@endif
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Meta Image') }}</label>
							<div class="col-lg-7">
								<div id="meta_photo">
									@if ($product->meta_img != null)
										<div class="col-md-4 col-sm-4 col-xs-6">
											<div class="img-upload-preview">
												<img loading="lazy"  src="{{ my_asset($product->meta_img) }}" alt="" class="img-responsive">
												<input type="hidden" name="previous_meta_img" value="{{ $product->meta_img }}">
												<button type="button" class="btn btn-danger close-btn remove-files"><i class="fa fa-times"></i></button>
											</div>
										</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				{{-- video --}}
				<div class="panel">
					<div class="panel-heading bord-btm">
						<h3 class="panel-title">{{__('Product Videos')}}</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Video Provider')}}</label>
							<div class="col-lg-7">
								<select class="form-control demo-select2-placeholder" name="video_provider" id="video_provider">
									<option value="youtube" <?php if($product->video_provider == 'youtube') echo "selected";?> >{{__('Youtube')}}</option>
									<option value="dailymotion" <?php if($product->video_provider == 'dailymotion') echo "selected";?> >{{__('Dailymotion')}}</option>
									<option value="vimeo" <?php if($product->video_provider == 'vimeo') echo "selected";?> >{{__('Vimeo')}}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Video Link')}}</label>
							<div class="col-lg-7">
								<input type="text" class="form-control" name="video_link" value="{{ $product->video_link }}" placeholder="{{ __('Video Link') }}">
							</div>
						</div>
					</div>
				</div>
				{{-- Product price + stock --}}
				<div class="panel">
					<div class="panel-heading bord-btm">
						<h3 class="panel-title">{{__('Product price + stock')}}</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Unit price')}}</label>
							<div class="col-lg-7">
								<input id="unit_price" type="text" placeholder="{{__('Unit price')}}" name="unit_price" class="form-control" value="{{$product->unit_price}}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Purchase price')}}</label>
							<div class="col-lg-7">
								<input type="number" id="purchase_price" min="0" step="0.01" placeholder="{{__('Purchase price')}}" name="purchase_price" class="form-control" value="{{$product->purchase_price}}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Tax')}}</label>
							<div class="col-lg-7">
								<input type="number" id="tax" min="0" step="0.01" placeholder="{{__('tax')}}" name="tax" class="form-control" value="{{$product->tax}}">
							</div>
							<div class="col-lg-1">
								<select class="demo-select2" name="tax_type" required>
									<option value="amount" <?php if($product->tax_type == 'amount') echo "selected";?> >{{__('Flat')}}</option>
									<option value="percent" <?php if($product->tax_type == 'percent') echo "selected";?> >{{__('Percent')}}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{__('Discount')}}</label>
							<div class="col-lg-7">
								<input id="discount" type="number" min="0" step="0.01" placeholder="{{__('Discount')}}" name="discount" class="form-control" value="{{ $product->discount }}" required>
							</div>
							<div class="col-lg-1">
								<select class="demo-select2" id="discount_type" name="discount_type" required>
									<option value="amount" <?php if($product->discount_type == 'amount') echo "selected";?> >{{__('Flat')}}</option>
									<option value="percent" <?php if($product->discount_type == 'percent') echo "selected";?> >{{__('Percent')}}</option>
								</select>
							</div>
						</div>
						<div class="form-group" id="quantity">
							<label class="col-lg-2 control-label">{{__('Quantity')}}</label>
							<div class="col-lg-7">
								<input type="number" value="{{ $product->current_stock }}" step="1" placeholder="{{__('Quantity')}}" name="current_stock" class="form-control" required>
							</div>
						</div>
						<br>
					</div>
				</div>

				{{-- Product Shipping Cost --}}
				@if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'product_wise_shipping')
					<div class="panel">
						<div class="panel-heading bord-btm">
							<h3 class="panel-title">{{__('Product Shipping Cost')}}</h3>
						</div>
						<div class="panel-body">
							<div class="row bord-btm">
								<div class="col-md-2">
									<div class="panel-heading">
										<h3 class="panel-title">{{__('Free Shipping')}}</h3>
									</div>
								</div>
								<div class="col-md-10">
									<div class="form-group">
										<label class="col-lg-2 control-label">{{__('Status')}}</label>
										<div class="col-lg-7">
											<label class="switch" style="margin-top:5px;">
												<input type="radio" name="shipping_type" value="free" @if($product->shipping_type == 'free') checked @endif>
												<span class="slider round"></span>
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="row bord-btm">
								<div class="col-md-2">
									<div class="panel-heading">
										<h3 class="panel-title">{{__('Flat Rate')}}</h3>
									</div>
								</div>
								<div class="col-md-10">
									<div class="form-group">
										<label class="col-lg-2 control-label">{{__('Status')}}</label>
										<div class="col-lg-7">
											<label class="switch" style="margin-top:5px;">
												<input type="radio" name="shipping_type" value="flat_rate" @if($product->shipping_type == 'flat_rate') checked @endif>
												<span class="slider round"></span>
											</label>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-2 control-label">{{__('Shipping cost')}}</label>
										<div class="col-lg-7">
											<input type="number" min="0" step="0.01" placeholder="{{__('Shipping cost')}}" name="flat_shipping_cost" class="form-control" value="{{ $product->shipping_cost }}" required>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
			@endif
			<!--<div class="panel">
				<div class="panel-heading bord-btm">
					<h3 class="panel-title">{{__('PDF Specification')}}</h3>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-lg-2 control-label">{{__('PDF Specification')}}</label>
						<div class="col-lg-7">
							<input type="file" class="form-control" placeholder="{{__('PDF')}}" name="pdf" accept="application/pdf" value="{{$product->pdf}}">
						</div>
						<input type="text" class="form-control" placeholder="{{__('PDF')}}" name="pdf"  value="{{$product->pdf}}">

					</div>
				</div>
			</div>-->

			{{--  SEO Meta Tags --}}
				<div class="mar-all text-right">
					<button type="button" id="btn-save" name="button" class="btn btn-info">{{ __('Update Product') }}</button>
				</div>
			</form>
		</div>

		<div class="col-lg-4">
			@include('partials.histories', ['item' => $product])
		</div>
	</div>
	<style>
		.error-border {
			border: 1px solid red !important; 
		}
	</style>
@endsection
@section('script')
	<script type="text/javascript">
		// var i = $('input[name="choice_no[]"').last().val();
		// if(isNaN(i)){
		// 	i =0;
		// }
		function add_more_customer_choice_option(i, name){
			$('#customer_choice_options').append('<div class="form-group"><div class="col-lg-2"><input type="hidden" name="choice_no[]" value="'+i+'"><input type="text" class="form-control" name="choice[]" value="'+name+'" readonly></div><div class="col-lg-7"><input type="text" class="form-control" name="choice_options_'+i+'[]" placeholder="{{ __('Enter choice values') }}" data-role="tagsinput" onchange="update_sku()"></div><div class="col-lg-2"><button onclick="delete_row(this)" class="btn btn-danger btn-icon"><i class="demo-psi-recycling icon-lg"></i></button></div></div>');
			$("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
		}
		$('input[name="colors_active"]').on('change', function() {
			if(!$('input[name="colors_active"]').is(':checked')){
				$('#colors').prop('disabled', true);
			}
			else{
				$('#colors').prop('disabled', false);
			}
			update_sku();
		});
		$('#colors').on('change', function() {
			update_sku();
		});
		// $('input[name="unit_price"]').on('keyup', function() {
		//     update_sku();
		// });
		function delete_row(em){
			$(em).closest('.form-group').remove();
			update_sku();
		}
		function update_sku(){

		}
		function get_subcategories_by_category(){
			var category_id = $('#category_id').val();
			$.post('{{ route('subcategories.get_subcategories_by_category') }}',{_token:'{{ csrf_token() }}', category_id:category_id}, function(data){
				$('#subcategory_id').html(null);
				$('#subcategory_id').append($('<option>', {
					value: null,
					text: null
				}));

				for (var i = 0; i < data.length; i++) {
					$('#subcategory_id').append($('<option>', {
						value: data[i].id,
						text: data[i].name
					}));
				}
				$("#subcategory_id > option").each(function() {
					if(this.value == '{{$product->subcategory_id}}'){
						$("#subcategory_id").val(this.value).change();
					}
				});
				$('.demo-select2').select2();
				get_subsubcategories_by_subcategory();
			});
		}
		function get_subsubcategories_by_subcategory(){
			var subcategory_id = $('#subcategory_id').val();
			$.post('{{ route('subsubcategories.get_subsubcategories_by_subcategory') }}',{_token:'{{ csrf_token() }}', subcategory_id:subcategory_id}, function(data){
				$('#subsubcategory_id').html(null);
				$('#subsubcategory_id').append($('<option>', {
					value: null,
					text: null
				}));
				for (var i = 0; i < data.length; i++) {
					$('#subsubcategory_id').append($('<option>', {
						value: data[i].id,
						text: data[i].name
					}));
				}
				$("#subsubcategory_id > option").each(function() {
					if(this.value == '{{$product->subsubcategory_id}}'){
						$("#subsubcategory_id").val(this.value).change();
					}
				});
				$('.demo-select2').select2();
				//get_brands_by_subsubcategory();
				//get_attributes_by_subsubcategory();
			});
		}
		// function get_brands_by_subsubcategory(){
		// 	var subsubcategory_id = $('#subsubcategory_id').val();
		// 	$.post('{{ route('subsubcategories.get_brands_by_subsubcategory') }}',{_token:'{{ csrf_token() }}', subsubcategory_id:subsubcategory_id}, function(data){
		// 	    $('#brand_id').html(null);
		// 	    for (var i = 0; i < data.length; i++) {
		// 	        $('#brand_id').append($('<option>', {
		// 	            value: data[i].id,
		// 	            text: data[i].name
		// 	        }));
		// 	    }
		// 	    $("#brand_id > option").each(function() {
		// 	        if(this.value == '{{$product->brand_id}}'){
		// 	            $("#brand_id").val(this.value).change();
		// 	        }
		// 	    });
		//
		// 	    $('.demo-select2').select2();
		//
		// 	});
		// }
		function get_attributes_by_subsubcategory(){
			var subsubcategory_id = $('#subsubcategory_id').val();
			$.post('{{ route('subsubcategories.get_attributes_by_subsubcategory') }}',{_token:'{{ csrf_token() }}', subsubcategory_id:subsubcategory_id}, function(data){
				$('#choice_attributes').html(null);
				for (var i = 0; i < data.length; i++) {
					$('#choice_attributes').append($('<option>', {
						value: data[i].id,
						text: data[i].name
					}));
				}
				$("#choice_attributes > option").each(function() {
					var str = @php echo $product->attributes @endphp;
					$("#choice_attributes").val(str).change();
				});
				$('.demo-select2').select2();
			});
		}

		$('#choice_form').validate({
			rules: {
				brand_id: {
					required: true
				},
				category_id: {
					required: true
				},
				current_stock: {
					min: 0,
					required: true
				},
				discount: {
					min: 0,
					max: function()
					{
						if ($("#discount_type").val() === "percent") {
							return 100;
						}

						return parseFloat($("#unit_price").val());
					}
				},
				min_qty: {
					min: 1,
					required: true,
				},
				unit_price: {
					min: 0,
					required: true
				},
				tax: {
					min: 0,
					required: true
				},
				purchase_price: {
					min: 0
				},
				unit: {
					required: true
				}
			},
			errorPlacement: function(error, element) {
				if (element.attr("name") === "brand_id" || element.attr("name") === "category_id") {
					error.insertAfter(element.next('.select2-container'));
				} else {
					error.insertAfter(element);
				}
			},
			highlight: function(element) {
				if ($(element).attr("name") === "brand_id" || $(element).attr("name") === "category_id") {
					$(element).next('.select2').find('.select2-selection').addClass('error-border');
				} else {
					$(element).addClass('error-border'); 
					$(element).addClass('error');
				}
			},
			unhighlight: function(element) {
				if ($(element).attr("name") === "brand_id" || $(element).attr("name") === "category_id") {
					$(element).next('.select2').find('.select2-selection').removeClass('error-border');
				} else {
					$(element).removeClass('error-border');
					$(element).removeClass('error');
				}
			},
			submitHandler: function(form) {
				var isValid = true; 
				$('textarea[name="description[]"]').each(function() {
					var content = $(this).val();
					if (content.length <= 50) {
						isValid = false; 
						$(this).next('.error').remove(); 
						$(this).after('<label class="error">The Description must be more than 50 characters.</label>');
						$('.jodit_container').addClass('error-border'); 
					} else {
						$(this).next('.error').remove(); 
						$('.jodit_container').removeClass('error-border'); 
					}
				});

				if (isValid) {
					form.submit();
				}
			}
		});

		function change_type_attribute_validation() {

			$('#choice_form').validate({
				rules: {
					brand_id: {
						required: true
					},
					category_id: {
						required: true
					},
					current_stock: {
						min: 0,
						required: true
					},
					discount: {
						min: 0,
						max: function() {
							if ($("#discount_type").val() === "percent") {
								return 100;
							}

							return parseFloat($("#unit_price").val());
						}
					},
					min_qty: {
						min: 1,
						required: true,
					},
					unit_price: {
						min: 0,
						required: true
					},
					tax: {
						min: 0,
						required: true
					},
					purchase_price: {
						min: 0,
						required: true
					},
					unit: {
						required: true
					}
				},
				errorPlacement: function(error, element) {
					if (element.attr("name") === "brand_id" || element.attr("name") === "category_id") {
						error.insertAfter(element.next('.select2-container'));
					} else {
						error.insertAfter(element);
					}
				},
				highlight: function(element) {
					if ($(element).attr("name") === "brand_id") {
						$(element).next('.select2').find('.select2-selection').addClass('error-border');
					} else {
						$(element).addClass('error-border'); 
						$(element).addClass('error');
					}
				},
				unhighlight: function(element) {
					if ($(element).attr("name") === "brand_id") {
						$(element).next('.select2').find('.select2-selection').removeClass('error-border');
					} else {
						$(element).removeClass('error-border');
						$(element).removeClass('error');
					}
				},
				submitHandler: function(form) {
					var isValid = true; 
					$('textarea[name="description[]"]').each(function() {
						var content = $(this).val();
						if (content.length <= 50) {
							isValid = false; 
							$(this).next('.error').remove(); 
							$(this).after('<label class="error">The Description must be more than 50 characters.</label>');
							$('.jodit_container').addClass('error-border'); 
						} else {
							$(this).next('.error').remove(); 
							$('.jodit_container').removeClass('error-border'); 
						}
					});

					if (isValid) {
						form.submit();
					}
				}
			});
		}

		$(document).ready(function(){
			get_subcategories_by_category();
			$("#photos").spartanMultiImagePicker({
				fieldName:        'photos[]',
				maxCount:         10,
				rowHeight:        '200px',
				groupClassName:   'col-md-4 col-sm-4 col-xs-6',
				maxFileSize:      '',
				dropFileLabel : "Drop Here",
				onExtensionErr : function(index, file){
					console.log(index, file,  'extension err');
					alert('Please only input png or jpg type file')
				},
				onSizeErr : function(index, file){
					console.log(index, file,  'file size too big');
					alert('File size too big');
				}
			});
			$("#thumbnail_img").spartanMultiImagePicker({
				fieldName:        'thumbnail_img',
				maxCount:         1,
				rowHeight:        '200px',
				groupClassName:   'col-md-4 col-sm-4 col-xs-6',
				maxFileSize:      '',
				dropFileLabel : "Drop Here",
				onExtensionErr : function(index, file){
					console.log(index, file,  'extension err');
					alert('Please only input png or jpg type file')
				},
				onSizeErr : function(index, file){
					console.log(index, file,  'file size too big');
					alert('File size too big');
				}
			});
			$("#meta_photo").spartanMultiImagePicker({
				fieldName:        'meta_img',
				maxCount:         1,
				rowHeight:        '200px',
				groupClassName:   'col-md-4 col-sm-4 col-xs-6',
				maxFileSize:      '',
				dropFileLabel : "Drop Here",
				onExtensionErr : function(index, file){
					console.log(index, file,  'extension err');
					alert('Please only input png or jpg type file')
				},
				onSizeErr : function(index, file){
					console.log(index, file,  'file size too big');
					alert('File size too big');
				}
			});
			update_sku();
			$('.remove-files').on('click', function(){
				$(this).parents(".col-md-4").remove();
			});
		});

		$('#discount_type').on('change', function() {
			change_type_attribute_validation();
		});
		$('#category_id').on('change', function() {
			get_subcategories_by_category();
		});
		$('#subcategory_id').on('change', function() {
			get_subsubcategories_by_subcategory();
		});
		$('#subsubcategory_id').on('change', function() {
			//get_brands_by_subsubcategory();
			//get_attributes_by_subsubcategory();
		});
		$('#choice_attributes').on('change', function() {
			//$('#customer_choice_options').html(null);
			$.each($("#choice_attributes option:selected"), function(j, attribute){
				flag = false;
				$('input[name="choice_no[]"]').each(function(i, choice_no) {
					if($(attribute).val() == $(choice_no).val()){
						flag = true;
					}
				});
				if(!flag){
					add_more_customer_choice_option($(attribute).val(), $(attribute).text());
				}
			});
			var str = @php echo $product->attributes @endphp;
			$.each(str, function(index, value){
				flag = false;
				$.each($("#choice_attributes option:selected"), function(j, attribute){
					if(value == $(attribute).val()){
						flag = true;
					}
				});
				if(!flag){
					//console.log();
					$('input[name="choice_no[]"][value="'+value+'"]').parent().parent().remove();
				}
			});
			update_sku();
		});
	</script>
@endsection

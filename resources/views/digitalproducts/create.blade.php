@extends('layouts.app')
@section('content')
	<div>
		<h1 class="page-header text-overflow">{{ __('Add New Digital	 Product') }}</h1>
	</div>
	<div class="row">
		<div class="col-lg-8 col-lg-offset-2">
			<form class="form form-horizontal mar-top" action="{{route('digitalproducts.store') }}" method="POST" enctype="multipart/form-data" id="choice_form">
				@csrf
				<input type="hidden" name="added_by" value="admin">
				<div class="panel">
					<div class="panel-heading">
						<h3 class="panel-title">{{ __('General') }}</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Product Name') }}</label>
							<div class="col-lg-7">
								<input type="text" class="form-control" name="name" placeholder="{{ __('Product Name') }}" required>
							</div>
						</div>
						<div class="form-group" id="category">
							<label class="col-lg-2 control-label">{{ __('Category') }}</label>
							<div class="col-lg-7">
								<select class="form-control demo-select2-placeholder" name="category_id" id="category_id" required>
									@foreach(\App\Models\Category::where('digital', 1)->get() as $category)
										<option value="{{$category->id}}">{{ $category->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group" id="subcategory">
							<label class="col-lg-2 control-label">{{ __('Subcategory') }}</label>
							<div class="col-lg-7">
								<select class="form-control demo-select2-placeholder" name="subcategory_id" id="subcategory_id" required>
								</select>
							</div>
						</div>
						<div class="form-group" id="subsubcategory">
							<label class="col-lg-2 control-label">{{ __('Sub Subcategory') }}</label>
							<div class="col-lg-7">
								<select class="form-control demo-select2-placeholder" name="subsubcategory_id" id="subsubcategory_id">
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Product File') }}</label>
							<div class="col-lg-7">
								<input type="file" class="form-control" name="file" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Tags') }}</label>
							<div class="col-lg-7">
								<input type="text" class="form-control" name="tags[]" placeholder="{{ __('Type to add a tag') }}" data-role="tagsinput">
							</div>
						</div>
					</div>
				</div>
				<div class="panel">
					<div class="panel-heading bord-btm">
						<h3 class="panel-title">{{ __('Images') }}</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Main Images') }} </label>
							<div class="col-lg-7">
								<div id="photos">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Thumbnail Image') }} <small>(290x300)</small></label>
							<div class="col-lg-7">
								<div id="thumbnail_img">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel">
					<div class="panel-heading bord-btm">
						<h3 class="panel-title">{{ __('Meta Tags') }}</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Meta Title') }}</label>
							<div class="col-lg-7">
								<input type="text" class="form-control" name="meta_title" placeholder="{{ __('Meta Title') }}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Description') }}</label>
							<div class="col-lg-7">
								<textarea name="meta_description" rows="8" class="form-control"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Meta Image') }}</label>
							<div class="col-lg-7">
								<div id="meta_photo">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel">
					<div class="panel-heading bord-btm">
						<h3 class="panel-title">{{ __('Price') }}</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Unit price') }}</label>
							<div class="col-lg-7">
								<input type="number" min="0" value="0" step="0.01" placeholder="{{ __('Unit price') }}" name="unit_price" class="form-control" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Purchase price') }}</label>
							<div class="col-lg-7">
								<input type="number" min="0" value="0" step="0.01" placeholder="{{ __('Purchase price') }}" name="purchase_price" class="form-control" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Tax') }}</label>
							<div class="col-lg-7">
								<input type="number" min="0" value="0" step="0.01" placeholder="{{ __('Tax') }}" name="tax" class="form-control" required>
							</div>
							<div class="col-lg-1">
								<select class="demo-select2" name="tax_type">
									<option value="amount">$</option>
									<option value="percent">%</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Discount') }}</label>
							<div class="col-lg-7">
								<input type="number" min="0" value="0" step="0.01" placeholder="{{ __('Discount') }}" name="discount" class="form-control" required>
							</div>
							<div class="col-lg-1">
								<select class="demo-select2" name="discount_type">
									<option value="amount">$</option>
									<option value="percent">%</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="panel">
					<div class="panel-heading bord-btm">
						<h3 class="panel-title">{{ __('Product Information') }}</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-lg-2 control-label">{{ __('Description') }}</label>
							<div class="col-lg-9">
								<textarea class="editor" name="description"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" name="button" class="btn btn-info">{{ __('Save') }}</button>
				</div>
			</form>
		</div>
	</div>
@endsection
@section('script')
	<script type="text/javascript">
	function get_subcategories_by_category(){
		var category_id = $('#category_id').val();
		$.post('{{ route('subcategories.get_subcategories_by_category') }}',{_token:'{{ csrf_token() }}', category_id:category_id}, function(data){
			$('#subcategory_id').html(null);
			for (var i = 0; i < data.length; i++) {
				$('#subcategory_id').append($('<option>', {
					value: data[i].id,
					text: data[i].name
				}));
				$('.demo-select2').select2();
			}
			get_subsubcategories_by_subcategory();
		});
	}
	function get_subsubcategories_by_subcategory(){
		var subcategory_id = $('#subcategory_id').val();
		$.post('{{ route('subsubcategories.get_subsubcategories_by_subcategory') }}',{_token:'{{ csrf_token() }}', subcategory_id:subcategory_id}, function(data){
			$('#subsubcategory_id').html(null);
			for (var i = 0; i < data.length; i++) {
				$('#subsubcategory_id').append($('<option>', {
					value: data[i].id,
					text: data[i].name
				}));
				$('.demo-select2').select2();
			}
		});
	}
	function get_brands_by_subsubcategory(){
		var subsubcategory_id = $('#subsubcategory_id').val();
		$.post('{{ route('subsubcategories.get_brands_by_subsubcategory') }}',{_token:'{{ csrf_token() }}', subsubcategory_id:subsubcategory_id}, function(data){
			$('#brand_id').html(null);
			for (var i = 0; i < data.length; i++) {
				$('#brand_id').append($('<option>', {
					value: data[i].id,
					text: data[i].name
				}));
				$('.demo-select2').select2();
			}
		});
	}
	$(document).ready(function(){
		$('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
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
	});
	$('#category_id').on('change', function() {
		get_subcategories_by_category();
	});
	$('#subcategory_id').on('change', function() {
		get_subsubcategories_by_subcategory();
	});
	$('#subsubcategory_id').on('change', function() {
		get_brands_by_subsubcategory();
	});
</script>
@endsection

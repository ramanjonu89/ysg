@extends('layouts.app')
@section('content')
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ __('Add products')}}</h3>
            </div>
            <!--Horizontal Form-->
            <!--===================================================-->

            <form class="form-horizontal" action="{{route('flash_deals.products.add', [$flashDeal])}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="productIds">{{ __('Product IDs' )}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ __('Title')}}" id="productIds" name="productIds" class="form-control">
                            <p class="help-block">Example: 123,456,789</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="demo-hor-2">{{ __('Category Banner') }}</label>
                        <div class="col-sm-9">
                            <span class="pull-left btn btn-default btn-file">{{ __('Import exel') }}<input type="file" name="exelFile" id="exelFile" accept="image"></span>
                            <span class="ml-3 btn-file-name pull-left control-label" id="wrap"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="demo-hor-2"></label>
                        <div class="col-sm-9">
                            <button class="btn btn-purple" type="submit">{{ __('Add') }}</button>
                        </div>
                    </div>

                </div>
            </form>
            <!--===================================================-->
            <!--End Horizontal Form-->
        </div>
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ __('Product list')}}</h3>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="product_list_table" class="table table-bordered">
                            <thead>
                            <tr>
                                <td class="text-center col-sm-1" style="">
                                    <label for="" class="control-label">{{ __('Product ID')}}</label>
                                </td>
                                <td class="text-center" style="">
                                    <label for="" class="control-label">{{ __('Product')}}</label>
                                </td>
                                <td class="text-center col-sm-1">
                                    <label for="" class="control-label">{{ __('Base Price')}}</label>
                                </td>
                                <td class="text-center col-sm-1">
                                    <label for="" class="control-label">{{ __('Discount Price')}}</label>
                                </td>
                                <td class="text-center col-sm-1">
                                    <label for="" class="control-label">{{ __('Discount')}}</label>
                                </td>
                                <td class="text-center col-sm-1">
                                    <label for="" class="control-label">{{ __('Discount Type')}}</label>
                                </td>
                                <td class="text-center col-sm-1">
                                    <label for="" class="control-label">{{ __('Action')}}</label>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($flashDealProducts as $key => $flashDealProduct)
                                @php
                                    $data = $flashDealProduct->toArray();
                                    unset($data['product']);
                                    $product = $flashDealProduct->product;
                                @endphp
                                <tr data-flash-deal-product="{{ json_encode($data) }}" data-ajax-url="{{ route('flash_deals.products.view', [$flashDeal, $flashDealProduct]) }}">
                                    <td>
                                        <p>{{ $product->id }}</p>
                                    </td>
                                    <td>
                                        <div class="col-sm-3">
                                            <img loading="lazy"  class="img-md" src="{{ my_asset($product->thumbnail_img)}}" alt="Image">
                                        </div>
                                        <div class="col-sm-9">
                                            <label for="" class="control-label">{{ __($product->name) }}</label>
                                        </div>
                                    </td>
                                    <td>
                                        <p>{{ $product->unit_price }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $flashDealProduct->discount_price }}</p>
                                    </td>
                                    <td class="col-discount">
                                        <input type="number" name="discount" value="{{ $flashDealProduct->discount }}" min="0" step="1" class="form-control" required>
                                    </td>
                                    <td class="col-discount-type">
                                        <select class="demo-select2 discount_type" name="discount_type">
                                            <option value="amount" <?php if($flashDealProduct->discount_type == 'amount') echo "selected";?> >$</option>
                                            <option value="percent" <?php if($flashDealProduct->discount_type == 'percent') echo "selected";?> >%</option>
                                        </select>
                                    </td>
                                    <td class="col-action">
                                        <button class="btn btn-danger remove" type="button">{{ __('Remove')}}</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="panel-footer text-right">
                <a href="{{ route('flash_deals.edit', [$flashDeal]) }}">
                    <button class="btn btn-success" type="button">{{ __('Back')}}</button>
                </a>
                <a href="{{ route('flash_deals.index') }}">
                    <button class="btn btn-primary" type="button">{{ __('Done')}}</button>
                </a>
            </div>

        </div>
    </div>
    <div class="modal fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">{{ __('Confirmation') }}</h4>
                </div>
                <div class="modal-body">
                    <p id="confirm_message">

                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button id="remove_product_flashDeal" class="btn btn-danger btn-ok">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function updateItem(data, url, tr) {

            $.post(url,
            {
                _token:'{{ @csrf_token() }}',
                data: data
            },
            function(data){
                if (data.success) {
                    tr.replaceWith(data.html);
                    $(".demo-select2").select2();
                }
            });
        };

        $(document).on("change", "#product_list_table td input[name='discount']", function(){
            const tr = $(this).closest('tr');
            const url = tr.data('ajax-url') + '/update';
            let data = tr.data('flash-deal-product');
            data.discount = $(this).val();
            updateItem(data, url, tr);
        });

        $(document).on("change", "#product_list_table td select", function(){
            const tr = $(this).closest('tr');
            const url = tr.data('ajax-url') + '/update';
            let data = tr.data('flash-deal-product');
            data.discount_type = $(this).val();
            updateItem(data, url, tr);
        });

        $(document).on("click", "#product_list_table td button", function(){
            const tr = $(this).closest('tr');
            let data = tr.data('flash-deal-product');
            const message = `Do you want to delete "${data.product_name}" ?`;
            const url = tr.data('ajax-url') + '/remove';
            jQuery('#confirm_delete').modal('show', {backdrop: 'static'});

            $('#confirm_message').html(message);
            $('#remove_product_flashDeal').off('click');
            $('#remove_product_flashDeal').on('click', function() {
                updateItem({}, url, tr);
                jQuery('#confirm_delete').modal('hide');
            });
        });

        function showFileName(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                let el = $(input).parent('span').next('span.btn-file-name');
                el.html(file.name);
            }
        }

        $(".btn-file input").change(function() {
            showFileName(this);
        });

    </script>
@endsection

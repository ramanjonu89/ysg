@extends('layouts.app')
@section('content')
    <div class="panel tab-pane" id="block">
        <div class="row block-container">
            <div class="col-md-6">
                @include('admin.custom_page.block.inc.content')
            </div>
            <div class="col-md-6">
                @if ($block->type === 'ca_shop_list')
                    @include('admin.custom_page.block.inc.ca_shop_list_item_list')
                @elseif ($block->type === 'product_carousel')
                    @include('admin.custom_page.block.inc.product_carousel_item_list')
                @elseif ($block->type === 'ca_brand_list')
                    @include('admin.custom_page.block.inc.ca_brand_list')
                @else
                    @include('admin.custom_page.block.inc.item_list')
                @endif
            </div>
        </div>
    </div>

@endsection


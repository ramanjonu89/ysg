@extends('layouts.app')
@section('content')
    <div class="panel tab-pane" id="block">
        <div class="row block-container">
            <div class="col-md-6">
                @include('admin.custom_page.block.product_carousel.content')
            </div>
            <div class="col-md-6">
                @include('admin.custom_page.block.inc.product_carousel_item_list')
            </div>
        </div>
    </div>

@endsection


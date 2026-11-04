@extends('layouts.app')
@section('content')
    <div class="panel tab-pane" id="block">
        <div class="row block-container">
            <div class="col-md-6">
                @include('admin.custom_page.block.microsite_slider.content')
            </div>
            <div class="col-md-6">
                @include('admin.custom_page.block.microsite_slider.item_list')
            </div>
        </div>
    </div>

@endsection


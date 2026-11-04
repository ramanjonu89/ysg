@extends('layouts.app')
@section('content')
    <div class="tab-base">
        <!--Nav Tabs-->
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#tab-1" aria-expanded="true">{{ __('Block') }}</a>
            </li>
            <li class="">
                <a data-toggle="tab" href="#tab-2" aria-expanded="false">{{ __('Block Items') }}</a>
            </li>
        </ul>
        <!--Tabs Content-->
        <div class="tab-content">
            @include('admin.custom_page.block.'.$block->type.'.content')
            @include('admin.custom_page.block.'.$block->type.'.item_list')
        </div>
    </div>
<input type="hidden" id="block_id" value="{{$block->id}}">
@include('admin.custom_page.block.modal')
@endsection

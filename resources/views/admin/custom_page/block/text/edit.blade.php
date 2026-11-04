@extends('layouts.app')
@section('content')
    <div class="tab-base">
        <!--Nav Tabs-->
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#tab-1" aria-expanded="true">{{ translate('Block') }}</a>
            </li>
        </ul>
        <!--Tabs Content-->
        <div class="tab-content">
            @include('admin.custom_page.block.'.$block->type.'.content')
        </div>
    </div>
    <input type="hidden" id="block_id" value="{{$block->id}}">
@include('admin.custom_page.block.modal')
@endsection

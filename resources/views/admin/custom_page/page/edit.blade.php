@extends('layouts.app')
@section('content')

    <div class="panel tab-pane" id="page">
        <div class="row page-container">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    @include('admin.custom_page.page.content')
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    @include('admin.custom_page.page.blocks', ['pageType' => 'page'])
                </div>
            </div>
        </div>
    </div>


@endsection


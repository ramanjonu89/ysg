@extends('layouts.app')

@section('content')

<div class="col-lg-6 col-lg-offset-3">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Review Category Information') }}</h3>
        </div>

        <form class="form-horizontal" action="{{ route('review_categories.update', $review_category->id) }}" method="POST">
            <input name="_method" type="hidden" value="PATCH">
        	@csrf
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="title">{{ __('Title') }}</label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="{{ __('Title') }}" id="title" name="title" class="form-control" required value="{{ $review_category->title }}" autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="enabled">{{ __('Enabled') }}</label>
                    <div class="col-sm-10">
                        <label class="switch">
                            <input type="checkbox" name="enabled" id="enabled" @if($review_category->enabled) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="panel-footer text-right">
                <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection


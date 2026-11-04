@extends('layouts.app')

@section('content')
<form action="{{ route('admin.css.bulk_update') }}" method="POST">
    @csrf

    <fieldset class="solid-border">
        <legend class="">{{ __('Bulk update CSS access') }}</legend>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="select_sellers">{{ __('Seller') }}</label>
                    <select class="form-control category_id" id="select_sellers" name="seller_ids[]" multiple>
                        <option value="0">{{ __('Select All Sellers') }}</option>
                        @foreach($sellers as $key => $seller)
                            <option value="{{$seller->id}}" @if (!empty(old('seller_ids')) && in_array($seller->id, old('seller_ids'))) selected @endif>{{$seller->user->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="action_type">{{ __('Action type') }}</label>
                    <div class="form-control">
                        <label class="radio-inline">
                            <input type="radio" name="action_type" id="inlineRadio1" value="create" @if (old('action_type') == 'create') checked @endif> Create
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="action_type" id="inlineRadio2" value="delete" @if (old('action_type') == 'delete') checked @endif> Delete
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="domains">{{ __('Domains') }}</label>
                    <textarea class="form-control" name="domains" id="domains" rows="8">{{ old('domains') }}</textarea>
                    <p class="help-block">One domain per line</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="emails">{{ __('Emails') }}</label>
                    <textarea class="form-control" name="emails" id="emails" rows="8">{{ old('emails') }}</textarea>
                    <p class="help-block">One email per line</p>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button class="btn btn-purple" type="submit">{{__('Save') }}</button>
        </div>
        
    </fieldset>
</form>
@endsection

@include('admin.inc.js.select_sellers')
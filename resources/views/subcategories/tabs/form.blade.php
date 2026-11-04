
@if(isset($subcategory) && $subcategory->subCategoryTranslation($language->id) !== null)
<div class="form-group">
    <label class="col-sm-3 control-label" for="name">{{ __('Name') }}</label>
    <div class="col-sm-9">
        <input type="text" placeholder="{{ __('Name') }}" id="name" name="name[]" value="{{$subcategory->subCategoryTranslation($language->id)->name}}" class="form-control" {{$key == 0 ? 'required' : ''}}>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">{{ __('Meta Title') }}</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="meta_title[]" value="{{$subcategory->subCategoryTranslation($language->id)->meta_title}}" placeholder="{{ __('Meta Title') }}">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">{{ __('Description') }}</label>
    <div class="col-sm-9">
        <textarea name="meta_description[]" rows="8" class="form-control">{{$subcategory->subCategoryTranslation($language->id)->meta_description}}</textarea>
    </div>
</div>

@else
<div class="form-group">
    <label class="col-sm-3 control-label" for="name">{{ __('Name') }}</label>
    <div class="col-sm-9">
        <input type="text" placeholder="{{ __('Name') }}" id="name" name="name[]" value="{{isset($subcategory) ? $subcategory->name : ''}}" class="form-control" {{$key == 0 ? 'required' : ''}}>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">{{ __('Meta Title') }}</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="meta_title[]" value="{{isset($subcategory) ? $subcategory->meta_title : ''}}" placeholder="{{ __('Meta Title') }}">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">{{ __('Description') }}</label>
    <div class="col-sm-9">
        <textarea name="meta_description[]" rows="8" class="form-control">{{isset($subcategory) ? $subcategory->meta_description : ''}}</textarea>
    </div>
</div>

@endif

<input type="hidden" name="language_id[]" value="{{$language->id}}">

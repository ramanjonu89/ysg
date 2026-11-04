
@if(isset($subSubCategory) && $subSubCategory->subSubCategoryTranslation($language->id) !== null)
<div class="form-group">
    <label class="col-sm-3 control-label" for="name">{{ __('Name') }}</label>
    <div class="col-sm-9">
        <input type="text" placeholder="{{ __('Name') }}" id="name" name="name[]" value="{{$subSubCategory->subSubCategoryTranslation($language->id)->name}}" class="form-control" {{$key == 0 ? 'required' : ''}}>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">{{ __('Meta Title') }}</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="meta_title[]" value="{{$subSubCategory->subSubCategoryTranslation($language->id)->meta_title}}" placeholder="{{ __('Meta Title') }}">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">{{ __('Description') }}</label>
    <div class="col-sm-9">
        <textarea name="meta_description[]" rows="8" class="form-control">{{$subSubCategory->subSubCategoryTranslation($language->id)->meta_description}} </textarea>
    </div>
</div>

@else
<div class="form-group">
    <label class="col-sm-3 control-label" for="name">{{ __('Name') }}</label>
    <div class="col-sm-9">
        <input type="text" placeholder="{{ __('Name') }}" id="name" name="name[]" value="{{isset($subSubCategory) ? $subSubCategory->name : ''}}" class="form-control" {{$key == 0 ? 'required' : ''}}>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">{{ __('Meta Title') }}</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="meta_title[]" value="{{isset($subSubCategory) ? $subSubCategory->meta_title : ''}}" placeholder="{{ __('Meta Title') }}">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">{{ __('Description') }}</label>
    <div class="col-sm-9">
        <textarea name="meta_description[]" rows="8" class="form-control">{{isset($subSubCategory) ? $subSubCategory->meta_description : ''}}</textarea>
    </div>
</div>

@endif
<input type="hidden" name="language_id[]" value="{{$language->id}}">

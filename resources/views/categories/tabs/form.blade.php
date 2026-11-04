@if (isset($category) && $category->categoryTranslation($language->id) !== null)
    <div class="form-group">
        <label class="col-sm-2 control-label" for="name">{{ __('Name') }}</label>
        <div class="col-sm-10">
            <input type="text" placeholder="{{ __('Name') }}" id="name" name="name[]"
                value="{{ $category->categoryTranslation($language->id)->name }}" class="form-control"
                {{ $key == 0 ? 'required' : '' }}>
        </div>
    </div>
    <div class="form-group" style="display:none">
        <label class="col-sm-2 control-label" for="name">{{ __('Type') }}</label>
        <div class="col-sm-10">
            <select name="digital" required class="form-control demo-select2-placeholder">
                <option value="0" {{ isset($category) && @$category->digital == 0 ? 'selected' : '' }}>
                    {{ __('Physical') }}</option>
                <option value="1" {{ isset($category) && @$category->digital == 1 ? 'selected' : '' }}>
                    {{ __('Digital') }}</option>
            </select>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('Meta Title') }}</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="meta_title[]"
                value="{{ $category->categoryTranslation($language->id)->meta_title }}"
                placeholder="{{ __('Meta Title') }}">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('Description') }}</label>
        <div class="col-sm-10">
            <textarea name="meta_description[]" rows="8"
                class="form-control">{{ $category->categoryTranslation($language->id)->meta_description }}</textarea>
        </div>
    </div>

@else
    <div class="form-group">
        <label class="col-sm-2 control-label" for="name">{{ __('Name') }}</label>
        <div class="col-sm-10">
            <input type="text" placeholder="{{ __('Name') }}" id="name" name="name[]"
                value="{{ isset($category) ? $category->name : '' }}" class="form-control"
                {{ $key == 0 ? 'required' : '' }}>
        </div>
    </div>
    <div class="form-group" style="display:none">
        <label class="col-sm-2 control-label" for="name">{{ __('Type') }}</label>
        <div class="col-sm-10">
            <select name="digital" required class="form-control demo-select2-placeholder">
                <option value="0" {{ isset($category) && @$category->digital == 0 ? 'selected' : '' }}>
                    {{ __('Physical') }}</option>
                <option value="1" {{ isset($category) && @$category->digital == 1 ? 'selected' : '' }}>
                    {{ __('Digital') }}</option>
            </select>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('Meta Title') }}</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="meta_title[]"
                value="{{ isset($category) ? @$category->meta_title : '' }}"
                placeholder="{{ __('Meta Title') }}">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('Description') }}</label>
        <div class="col-sm-10">
            <textarea name="meta_description[]" rows="8"
                class="form-control">{{ isset($category) ? $category->meta_description : '' }}</textarea>
        </div>
    </div>

@endif

@if (\App\BusinessSetting::where('type', 'category_wise_commission')->first()->value == 1)
    <div class="form-group" style="display:none">
        <label class="col-sm-2 control-label" for="name">{{ __('Commission Rate') }}</label>
        <div class="col-sm-8">
            <input type="number" min="0" step="0.01" placeholder="{{ __('Commission Rate') }}"
                id="commision_rate" name="commision_rate"
                value="{{ isset($category) ? $category->commision_rate : 0 }}" class="form-control">
        </div>
        <div class="col-lg-2">
            <option class="form-control">%</option>
        </div>
    </div>
@endif
<input type="hidden" name="language_id[]" value="{{ $language->id }}">

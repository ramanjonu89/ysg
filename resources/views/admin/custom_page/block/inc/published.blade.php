<div class="form-group">
    <label class="col-sm-3 control-label" for="active">{{ __('Active/ Inactive') }}</label>
    <div class="col-sm-9">
        <label class="switch">
            <input type="checkbox" id="active" name="active"
                {{ $block->is_enabled ? 'checked' : '' }}>
            <span class="slider round"></span>
        </label>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label" for="margin_top">{{__('Space at top of block') }}</label>
    <div class="col-sm-9">
        <label class="switch">
            <input type="checkbox" id="margin_top" name="options[margin_top]" {{isset($block->options->margin_top)?'checked':''}}>
            <span class="slider round"></span>
        </label>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label" for="margin_bottom">{{__('Space at bottom of block') }}</label>
    <div class="col-sm-9">
        <label class="switch">
            <input type="checkbox" id="margin_bottom" name="options[margin_bottom]" {{isset($block->options->margin_bottom)?'checked':''}}>
            <span class="slider round"></span>
        </label>
    </div>
</div>
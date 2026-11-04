<div class="form-group">
    <label class="col-sm-3 control-label" for="hide_on_mobile">{{__('Hide on mobile') }}</label>
    <div class="col-sm-9">
        <label class="switch">
            <input type="checkbox" id="hide_on_mobile" name="options[hide_on_mobile]" {{isset($block->options->hide_on_mobile)?'checked':''}}>
            <span class="slider round"></span>
        </label>
    </div>
</div>

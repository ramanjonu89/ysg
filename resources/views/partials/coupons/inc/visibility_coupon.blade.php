<div class="form-group">
    <label class="col-lg-3 control-label">{{ __('Visibility') }}</label>
    <div class="col-lg-9">
        <div style="padding: 6px 0;">
            <label class="switch" style="margin-bottom: 0">
                <input value="1" name="published" type="checkbox" {{ isset($coupon) && $coupon->published ? 'checked' : ''}}>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
</div>
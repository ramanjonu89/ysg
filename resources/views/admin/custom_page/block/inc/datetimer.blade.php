<div class="form-group">
    <label class="col-sm-3 control-label" for="daterange">{{ __('Date/Time') }}</label>
    <div class="col-sm-2">
        <select class="demo-select2" id="date_timer_type" name="date_timer_type">
            <option value="date_range" @if($daterange) selected @endif>Date Range</option>
            <option value="start_date_only" @if($start_date && !$end_date) selected @endif>Start Date Only</option>
            <option value="end_date_only" @if($end_date && !$start_date) selected @endif>End Date Only</option>
        </select>
    </div>
    <div class="col-sm-7">
        <div class="date-range">
            <input type="text" name="daterange" @if($daterange) value="{{ $daterange }}" @endif class="form-control"/>
        </div>
        <div class="start-date d-none">
            <input type="text" name="start_date" @if($start_date) value="{{ $start_date }}" @endif class="form-control"/>
        </div>
        <div class="end-date d-none">
            <input type="text" name="end_date" @if($end_date) value="{{ $end_date }}" @endif class="form-control"/>
        </div>
    </div>
</div>
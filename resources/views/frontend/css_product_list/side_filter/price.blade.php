<!-- Price Filter -->
<div class="box-title bg-white border-bottom-0 border-top">
    <h6 class="font-weight-bold mb-0">{{ __('Price range') }}</h6>
</div>
<div class="box-content pr-3 pl-3 pb-3">
    <div class="form-row d-flex align-items-center justify-content-between">
        <input name="min_price" value="{{ $requestQuery['min_price']??'' }}" type="number" placeholder="Min" class="form-control col-md-5">
        <div class="col-md-1 border-bottom border-dark"></div>
        <input name="max_price" value="{{ $requestQuery['max_price']??'' }}"  type="number" placeholder="Max" class="form-control col-md-5">
    </div>
</div>
<!-- END -->

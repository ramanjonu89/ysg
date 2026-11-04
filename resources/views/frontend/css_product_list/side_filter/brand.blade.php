<!-- Brand Filter -->
<div class="box-title bg-white border-bottom-0 border-top">
    <h6 class="font-weight-bold mb-0">{{ __('Brands') }}</h6>
</div>
<div class="box-content pr-3 pl-3 pb-3">
    <!-- Filter by color -->
    @php( $count=0 )
    @foreach($brands as $key => $brand)
        <div class="custom-control custom-checkbox {{ $count >= 5 ? 'brand-hidden-item' : '' }}" style="{{ $count >= 5 ? 'display:none' : '' }}">
            <input name="brand_ids[]" type="checkbox" class="custom-control-input"
                   id="custom-cb-brand-{{ $brand->id }}" value="{{ $brand->id }}"
                   {{ isset($requestQuery['brand_ids'])?in_array($brand->id, $requestQuery['brand_ids'])?'checked':'':'' }}
                   onchange="filter()"
            >
            <label class="custom-control-label" for="custom-cb-brand-{{ $brand->id }}">{{ $brand->name }}</label>
        </div>
        @php( $count++ )
    @endforeach
    @if(count($brands) >= 6)
        <div class="text-center text-black-50">
            <span role="button" data-status="hide" id="view-more-brands">
                <span>{{ __('View more') }}</span>
                <i class="caret-icon fa fa-caret-down"></i>
            </span>
        </div>
    @endif
</div>
<!-- END -->

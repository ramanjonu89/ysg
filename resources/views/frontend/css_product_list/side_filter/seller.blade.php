<!-- Seller Filter -->
<div class="box-title bg-white border-bottom-0 border-top">
    <h6 class="font-weight-bold mb-0">{{ __('Sellers') }}</h6>
</div>
<div class="box-content pr-3 pl-3 pb-3">
    @php( $count=0 )
    @foreach($sellers as $key => $seller)
        <div class="custom-control custom-checkbox {{ $count >= 5 ? 'seller-hidden-item' : '' }}" style="{{ $count >= 5 ? 'display:none' : '' }}">
            <input name="seller_ids[]" type="checkbox" class="custom-control-input"
                   id="custom-cb-seller-{{ $seller->id }}" value="{{ $seller->id }}"
                   {{ isset($requestQuery['seller_ids'])?in_array($seller->id, $requestQuery['seller_ids'])?'checked':'':'' }}
                   onchange="filter()"
            >
            <label class="custom-control-label" for="custom-cb-seller-{{ $seller->id }}">{{ $seller->user->name }}</label>
        </div>
        @php( $count++ )
    @endforeach
    @if(count($sellers) >= 6)
        <div class="text-center text-black-50">
                                            <span role="button" data-status="hide" id="view-more-sellers">
                                                <span>{{ __('View more') }}</span>
                                                <i class="caret-icon fa fa-caret-down"></i>
                                            </span>
        </div>
    @endif
</div>
<!-- END -->

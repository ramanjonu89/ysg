<div class="box-title bg-white border-bottom-0 border-top">
    <h6 class="font-weight-bold mb-0">{{ __('Categories') }}</h6>
</div>
<div class="box-content pr-3 pl-3 pb-3">
    @foreach ($categories as $key => $cat)
    <div class="custom-control custom-checkbox {{ $key >= 5 ? 'category-hidden-item' : '' }}"
        style="{{ $key >= 5 ? 'display:none' : '' }}">
        <input name="category_ids[]" type="checkbox" class="custom-control-input" id="custom-cb-category-{{ $cat->id }}" 
        value="{{ $cat->id }}" 
        @isset($categoryIds) 
        @if (in_array($cat->id, $categoryIds)) 
        checked 
        @endif @endisset
        onchange="filter()"
        >
        <label class="custom-control-label"
                for="custom-cb-category-{{ $cat->id }}">{{ $cat->translation_name }}</label>
    </div>
    @endforeach
    @if(count($categories) >= 6)
    <div class="text-center text-black-50">
        <span role="button" data-status="hide" id="view-more-categories">
            <span>{{ __('View more') }}</span>
            <i class="caret-icon fa fa-caret-down"></i>
        </span>
    </div>
    @endif
</div>
@if (count($subCategories) > 0)
    <div class="box-title bg-white border-bottom-0 border-top">
        <h6 class="font-weight-bold mb-0">{{ __('Sub Categories') }}</h6>
    </div>
    <div class="box-content pr-3 pl-3 pb-3">
        @foreach($subCategories as $key => $subCat)
        <div 
            class="custom-control custom-checkbox {{ $key >= 6 ? 'sub-category-hidden-item' : '' }}"
            style="{{ $key >= 6 ? 'display:none' : '' }}">
            <input name="subcategory_ids[]" type="checkbox" class="custom-control-input"
                    id="custom-cb-subcategory-{{ $subCat->id }}"
                    value="{{ $subCat->id }}" 
                        @isset($subCategoryIds) 
                        @if (in_array($subCat->id, $subCategoryIds)) 
                        checked 
                        @endif @endisset
                    onchange="filter()"
            >
            <label class="custom-control-label"
                    for="custom-cb-subcategory-{{ $subCat->id }}">{{ $subCat->translation_name }}</label>
        </div>
        @endforeach
        @if(count($subSubCategories) > 0)
        
            @foreach($subSubCategories as $key => $subSubCat)
            <div 
                class="custom-control custom-checkbox {{ $key >= 0 ? 'sub-category-hidden-item' : '' }}"
                style="{{ $key >= 0 ? 'display:none' : '' }}">
                <input name="subsubcategory_ids[]" type="checkbox" class="custom-control-input"
                        id="custom-cb-subsubcategory-{{ $subSubCat->id }}"
                        value="{{ $subSubCat->id }}" 
                            @isset($subSubCategoryIds) 
                            @if (in_array($subSubCat->id, $subSubCategoryIds)) 
                            checked 
                            @endif @endisset
                        onchange="filter()"
                >
                <label class="custom-control-label"
                        for="custom-cb-subsubcategory-{{ $subSubCat->id }}">{{ $subSubCat->translation_name }}</label>
            </div>
            @endforeach
        @endif
        @if(count($subCategories) >= 3)
        <div class="text-center text-black-50">
            <span role="button" data-status="hide" id="view-more-subcategories">
                <span>{{ __('View more') }}</span>
                <i class="caret-icon fa fa-caret-down"></i>
            </span>
        </div>
        @endif
    </div>
@endif
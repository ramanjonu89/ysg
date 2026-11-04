<!-- Category Filter -->
<div class="box-title bg-white border-bottom-0 border-top">
    <h6 class="font-weight-bold mb-0">{{ __('Categories') }}</h6>
</div>
<div class="box-content pr-3 pl-3 pb-3">
    @php( $count=0 )
    @foreach ($categories as $key => $category)
        <div class="category-title {{ $count >= 5 ? 'category-hidden-item' : '' }} pb-2" style="{{ $count >= 5 ? 'display:none' : '' }}">
            <a class="alert-link text-dark" href="{{ route('products.css', ['category' => $category['item']->id, 'level' => 1]) }}">
                {{ $category['item']->translation_name }}
            </a>
            @if(count($category['children']) > 0)
                <a class="dropdown-toggle text-dark" data-toggle="collapse" href="#category-collapse-{{ $category['item']->id }}" role="button" aria-expanded="false" aria-controls="category-collapse-{{ $category['item']->id }}"></a>
            @endif
        </div>
        <div class="collapse pr-3 pl-3" id="category-collapse-{{ $category['item']->id }}">
            @foreach($category['children'] as $subCategory)
                @if ($subCategory['item']->status == 1)
                    <div class="pb-2">
                        <a class="alert-link text-muted" href="{{ route('products.css', ['category' => $subCategory['item']->id, 'level' => 2]) }}">
                            {{ $subCategory['item']->translation_name }}
                        </a>
                        @if(count($subCategory['children']) > 0)
                            <a class="dropdown-toggle text-muted" data-toggle="collapse" href="#subcategory-collapse-{{ $subCategory['item']->id }}" role="button"></a>
                        @endif
                    </div>
                    @if(count($subCategory['children']) > 0)
                        <div class="collapse pr-3 pl-3" id="subcategory-collapse-{{ $subCategory['item']->id }}">
                            @foreach($subCategory['children'] as $subSubCategory)
                                @if ($subSubCategory['item']->status == 1)
                                    <div class="pb-2">
                                        <a class="alert-link text-black-50" href="{{ route('products.css', ['category' => $subSubCategory['item']->id, 'level' => 3]) }}">
                                            {{ $subSubCategory['item']->translation_name }}
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
        @php( $count++ )
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
<!-- END -->

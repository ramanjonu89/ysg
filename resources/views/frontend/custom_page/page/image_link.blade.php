@php
    $blockContents = $block->blockContents;
@endphp
@if(count($blockContents))
<div class="block image_link">
    <div class="wrap">
        @foreach($blockContents as $blockContent)
            @if (!$blockContent->isExpired())
                <div class="block-content">
                    @if (isset($blockContent->options->link) && trim($blockContent->options->link) != '')
                        <a href="{{$blockContent->options->link}}">
                            <img src="{{$blockContent->image}}" alt="{{$blockContent->getTranslationTitleAttribute()}}" class="width-100">
                        </a>
                    @else
                    <div>
                        <img src="{{$blockContent->image}}" alt="{{$blockContent->getTranslationTitleAttribute()}}" class="width-100">
                    </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
</div>
@endif

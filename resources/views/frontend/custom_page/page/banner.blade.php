<div class="block banner">
    <p>
        @if (isset($block->options->link))
            <a href="{{$block->options->link}}">
                <img src="{{$block->image}}" alt="{{$block->getTranslationTitleAttribute()}}" class="width-100">
            </a>
        @else
            <img src="{{$block->image}}" alt="{{$block->getTranslationTitleAttribute()}}" class="width-100">
        @endif
    </p>
</div>
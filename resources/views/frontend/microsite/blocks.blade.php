<section class="microsite-block-section">
    <div class="container">
        <div class="ysg-custom-page">
            @foreach($blocks as $block)
                @if (!$block->isExpired())
                    <div class="{{isset($block->options->margin_top)?'mt-4':''}} {{isset($block->options->margin_bottom)?'mb-4':''}}">
                        @include("frontend.microsite.blocks.{$block->type}")
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@if(isset($page) && count($blocks = $page->blocks))
<section class="home-block-section">
    <div class="container">
        <div class="ysg-custom-page">
            @foreach($blocks as $block)
                @if ($block->is_enabled && !$block->isExpired())
                    <div class="{{isset($block->options->margin_top)?'mt-4':''}} {{isset($block->options->margin_bottom)?'mb-4':''}}">
                        @includeFirst([
                            'frontend.custom_page.'.$page->type.'.'.$block->type,
                            'frontend.custom_page.page.'.$block->type
                            ])
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

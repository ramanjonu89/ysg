<div class="row gutters-5">
    @foreach ($images as $content)
        <div class="col">
        @if (!empty($content->options->link))
            <a href="{{ $content->options->link }}">
                <img class="d-block w-100 lazyloaded" src=" {{ $content->image }}" alt="{{ $content->image }}">
            </a>
        @else
            <img class="d-block w-100 lazyloaded" src=" {{ $content->image }}" alt="{{ $content->image }}">
        @endif
        </div>
    @endforeach
</div>

<ul class="breadcrumb">
    <li><a href="{{ $logoLink }}">{{ __('Home') }}</a></li>
    @foreach ($breadcrumbs as $item)
    <li class="@if($loop->last) active @endif">
    	<a href="{{ $item['link'] }}">{{ $item['text'] }}</a>
    </li>
    @endforeach
</ul>
<!-- Color Filter -->
@if(count($all_colors) > 0)
    <div class="box-title bg-white border-bottom-0 border-top">
        <h6 class="font-weight-bold mb-0">{{ __('Filter by color') }}</h6>
    </div>
    <div class="box-content pr-3 pl-3 pb-3">
        <!-- Filter by color -->
        <ul class="list-inline checkbox-color checkbox-color-circle mb-0">
            @foreach ($all_colors as $key => $color)
                <li>
                    <input type="radio" id="color-{{ $key }}" name="color"
                           value="{{ $color }}"
                           @if(isset($selected_color) && $selected_color == $color) checked
                           @endif onchange="filter()">
                    <label style="background: {{ $color }};" for="color-{{ $key }}"
                           data-toggle="tooltip"
                           data-original-title="{{ \App\Color::where('code', $color)->first()->name }}"></label>
                </li>
            @endforeach
        </ul>
    </div>
@endif
@foreach ($attributes as $key => $attribute)
    @if (array_key_exists($attribute['id'], $existing_attributes))
        <div class="box-title bg-white border-bottom-0 border-top">
            <h6 class="font-weight-bold mb-0">
                Filter by {{ $existing_attributes[$attribute['id']] }}
            </h6>
        </div>
        <div class="box-content pr-3 pl-3 pb-3">
            <!-- Filter by others -->
            @foreach ($attribute['values'] as $key => $value)
                @php
                    $flag = false;
                    if (array_key_exists($attribute['id'], $selected_attributes)) {
                        if (in_array($value, $selected_attributes[$attribute['id']])) {
                            $flag = true;
                        }
                    }

                @endphp
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                           id="attribute_{{ $attribute['id'] }}_value_{{ $value }}"
                           name="attribute_{{ $attribute['id'] }}[]"
                           value="{{ $value }}" @if ($flag) checked
                           @endif onchange="filter()">
                    <label class="custom-control-label"
                           for="attribute_{{ $attribute['id'] }}_value_{{ $value }}">{{ $value }}</label>
                </div>
            @endforeach
        </div>
    @endif
@endforeach
<!-- END -->

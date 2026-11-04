<div class="panel-body">
  <table class="table table-striped" cellspacing="0" width="100%" style="table-layout: fixed">
    <tr>
      <th>Field</th>
      <th>Old value</th>
      <th>New Value</th>
    </tr>

    @foreach ($history->updatedFieldsArray() as $field)
      <tr style="overflow-wrap: break-word">
        <td>{{ $field }}</td>
        <td>
          @if (!is_scalar($history->oldValueObject()->$field))
          <span>{{ json_encode($history->oldValueObject()->$field) }}</span>
          @else
          <span>{{ $history->oldValueObject()->$field }}</span>
          @endif
        </td>
        <td>
          @if (!is_scalar($history->newValueObject()->$field))
          <span>{{ json_encode($history->newValueObject()->$field) }}</span>
          @else
          <span>{{ $history->newValueObject()->$field }}</span>
          @endif
        </td>
      </tr>
    @endforeach
  </table>
</div>
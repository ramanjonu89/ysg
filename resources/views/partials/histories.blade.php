<div class="panel">
  <div class="panel-heading bord-btm">
    <h3 class="panel-title">{{__('Change Histories')}}</h3>
  </div>
  <div class="panel-body">
    <table class="table table-striped" cellspacing="0" width="100%">
      <tbody>
        @foreach ($item->histories as $history)
        <tr>
          <td>
            <p>{{ $history->user->name }} @if( $history->user->deleted_at !== null) (deleted) @endif<small>({{ $history->ip_address }})</small> {{ $history->action }} this item at {{ $history->created_at }}</p>
            @if ($history->isUpdated())
              <p>Updated fields: {{ $history->updatedFields() }}</p>
            @endif
          </td>
          <td>
            @if ($history->isUpdated())
            <span class="show-ajax-modal-detail" data-url="{{ route('ajax.hitories.show', $history->id) }}" style="cursor: pointer;">
              <span class="fa fa-eye"></span>
              <span style="text-decoration: underline;">View</span>
            </span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@include('admin.inc.modal_ajax_content', ['modalTitle' => ""])
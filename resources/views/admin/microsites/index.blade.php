@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="javascript:void(0)" onclick="showCreateModal();"
               class="btn btn-rounded btn-info pull-right">{{__('Add New Microsite') }}</a>
        </div>
    </div>
    <br>

    <div class="panel">
        <div class="panel-body">
            <table class="table table-striped res-table mar-no"
                   style="width: 100%; border-collapse: collapse; border-spacing: 0;">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{__('Seller') }}</th>
                    <th>{{ __('Microsite Url') }}</th>
                    <th>{{__('Active') }}</th>
                    <th style="width: 10%;">{{__('Options') }}</th>
                </tr>
                </thead>

                <tbody>
                @if($entities)
                    @foreach($entities as $entity)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $entity->seller->user->name }}</td>
                            <td><a href="{{ route('microsite.index', ['slug' => $entity->seller->user->shop->slug]) }}" target="_blank">{{ route('microsite.index', ['slug' => $entity->seller->user->shop->slug]) }}</a></td>
                            <td>
                                <label class="switch">
                                    <input onchange="toggle_active(this)" value="{{ $entity->id }}" type="checkbox" <?php if($entity->active == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                <div class="btn-group dropdown">
                                    <form action="{{ route('admin.microsites.destroy', $entity->id) }}"
                                          class="delete-form" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon"
                                                data-toggle="dropdown" type="button">
                                            {{ __('Actions') }} <i class="dropdown-caret"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right" style="opacity: 1;">
                                            <li>
                                                <a href="{{route('admin.microsites.edit', $entity->id)}}">{{ __('Edit') }}</a>
                                            </li>

                                            <li>
                                                <a href="javascript:void(0)" onclick="confirm_modal({{ json_encode(['item' => $entity->seller->user->name, 'url' => route('admin.microsite.destroy', $entity->id)]) }});">
                                                    {{ __('Delete') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="create-microsite-modal" role="dialog" aria-labelledby="createMicrositeModel"
         aria-hidden="true">
        <div class="modal-dialog">
            <form class="form-horizontal" action="{{ route('admin.microsites.store') }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="createMicrositeModel">{{__('Confirmation') }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="name">{{__('Seller') }}</label>
                                <div class="col-sm-9">
                                    <select class="form-control demo-select2-placeholder"
                                            data-placeholder="Select Seller" name="seller_id" id="seller">
                                        @foreach($sellers as $seller)
                                            <option value="{{$seller->id}}">{{$seller->user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Cancel') }}</button>
                        <button class="btn btn-purple" type="submit">{{__('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')

    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>

    <script type="text/javascript">

        function toggle_active(el){
            $.post('{{ route('admin.api.microsites.toggle_active') }}', {_token:'{{ csrf_token() }}', id: el.value}, function(data){
                if(data.code == 200){
                    showAlert('success', '{{ __('Toggle Display Successfully') }}');
                }
                else{
                    showAlert('danger', '{{ __('Something went wrong') }}');
                }
            });
        }

        function showCreateModal() {
            $('#create-microsite-modal').modal('show', {backdrop: 'static'});
        }

    </script>
@endsection

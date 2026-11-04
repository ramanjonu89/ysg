@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="#" onclick="showCreateModal();" class="btn btn-rounded btn-info pull-right">{{ __('Add New Page') }}</a>
        </div>
    </div>
    <br>
    <!-- Basic Data Tables -->
    <!--===================================================-->
    <div class="panel">
        <div class="panel-body">
            <table class="table table-striped res-table mar-no" style="width: 100%; border-collapse: collapse; border-spacing: 0;">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Slug') }}</th>
                    <th>{{ __('Full Url') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Active') }}</th>
                    <th>{{ __('Exclusive Access') }}</th>
                    <th style="width: 10%;">{{ __('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($pageList as $key => $page)
                    <tr>
                        <td>
                            <span class="item-index">{{ ($key+1) + ($pageList->currentPage() - 1)*$pageList->perPage() }}</span>
                            <input class="item-id" type="hidden" value="{{ $page->id }}"/>
                        </td>
                        <td>{{$page->title}}</td>
                        <td>{{$page->slug}}</td>
                        <td><a href="{{route('custom-pages.show_custom_page', $page->slug)}}" target="_blank">{{route('custom-pages.show_custom_page', $page->slug)}}</a></td>
                        <td>{{$page->type}}</td>
                        <td>{{$page->is_enabled?__('Yes'):__('No') }}</td>
                        <td>{{$page->exclusive_access?__('Yes'):__('No') }}</td>
                        <td>
                            <div class="btn-group dropdown">
                                <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button">
                                    {{ __('Actions') }} <i class="dropdown-caret"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" style="opacity: 1;">
                                    <li><a href="{{route('admin.custom_page.page.edit', $page->id)}}">{{ __('Edit') }}</a></li>
                                    <li>
                                        <a onclick="confirm_modal({{ json_encode(['item' => $page->title, 'url' => route('admin.custom_page.page.destroy', $page->id)]) }});">
                                            {{ __('Delete') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">
                    {{ $pageList->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="create-page-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="form-horizontal" id="form-multiple-language"  action="{{ route('admin.custom_page.page.store') }}" method="POST"
                          enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">{{ __('Confirmation') }}</h4>
                    </div>
                    <div class="modal-body">
                        
                            @csrf
                        <div class="panel-body">
                            {{-- view here --}}
                            <div class="row">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-10">
                                    <ul class="nav nav-pills mb-5" style="background-color: #d486e1;">
                                        @if (isset($systemLanguages) && count($systemLanguages) > 0)
                                            @foreach ($systemLanguages as $key => $language)
                                                <li class="{{ $key == 0 ? 'active' : '' }}"><a href="#{{ $language->id }}" style="color:#fff" data-toggle="tab">{{ $language->name }}</a></li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-content clearfix" style="background-color: #fff">
                                @if (isset($systemLanguages) && count($systemLanguages) > 0)
                                    @foreach ($systemLanguages as $key => $language)
                                        <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="{{ $language->id }}">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="name">{{ __('Title') }}</label>
                                                <div class="col-sm-10">
                                                    <input type="text" placeholder="{{ __('Title') }}" id="title" name="title[]"
                                                        class="form-control" {{ $key == 0 ? 'required' : '' }}>
                                                </div>
                                            </div>
                                            <input type="hidden" name="language_id[]" value="{{ $language->id }}">
                                        </div>
                                    @endforeach
                                @endif
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="name">{{ __('Type') }}</label>
                                    <div class="col-sm-10">
                                        <select name="type" id="type" class="form-control">
                                            @foreach($service->getListType() as $value => $title)
                                                <option value="{{$value}}">{{$title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button id="btn-save" class="btn btn-purple" type="button">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')

    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>

    <script type="text/javascript">

        function showCreateModal()
        {
            $('#create-page-modal').modal('show', {backdrop: 'static'});
        }

        function toggle_display(el){
            $.post('{{ route('admin.api.sellers.toggle_display') }}', {_token:'{{ csrf_token() }}', sellerId:el.value}, function(data){
                if(data.code === 200){
                    showAlert('success', '{{ __('Toggle Display Successfully') }}');
                }
                else{
                    showAlert('danger', '{{ __('Something went wrong') }}');
                }
            });
        }

    </script>
@endsection


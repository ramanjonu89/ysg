@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{ route('newsletters.create') }}"
                class="btn btn-rounded btn-info pull-right">{{ __('Add New Banner') }}</a>
        </div>
    </div>
    <br>
    <!-- Basic Data Tables -->
    <!--===================================================-->
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Promotion Banner') }}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered demo-dt-basic sortable" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Photo') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Link') }}</th>
                        <th>{{ __('Active') }}</th>
                        <th>{{ __('Start Date/Time') }}</th>
                        <th>{{ __('End Date/Time') }}</th>
                        <th width="10%">{{ __('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($newsletters as $key => $newsletter)
                        <tr>
                            <td>
                                <span class="item-index">{{ $key + 1 }}</span>
                                <input class="item-id" type="hidden" value="{{ $newsletter->id }}" />
                            </td>

                            <td><img loading="lazy" class="img-md" src="{{ my_asset($newsletter->photo) }}"
                                    alt="banner Image"></td>
                            <td>{{ $newsletter->description }}</td>
                            <td>{{ $newsletter->link }}</td>
                            <td><label class="switch">
                                    <input
                                        onchange="update_status(this)" value="{{ $newsletter->id }}"
                                        type="checkbox" @if ($newsletter->status) checked @endif>
                                    <span class="slider round"></span></label></td>
                            <td>
                                {{ $newsletter->start_date_time }}
                            </td>
                            <td>
                                {{ $newsletter->end_date_time }}
                            </td>
                            <td>
                                <div class="btn-group dropdown">
                                    <button class="btn btn-primary dropdown-toggle dropdown-toggle-icon"
                                        data-toggle="dropdown" type="button">
                                        {{ __('Actions') }} <i class="dropdown-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a
                                                href="{{ route('newsletters.edit', $newsletter->id) }}">{{ __('Edit') }}</a>
                                        </li>
                                        <li>
                                            <a
                                                onclick="confirm_modal({{ json_encode(['item' => 'New Sletter ' . ($key + 1), 'url' => route('newsletters.destroy', $newsletter->id)]) }});">
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
        </div>
    </div>
    <!--===================================================-->
    <!-- End Basic Data Tables -->
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Subscribers List') }}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped res-table mar-no" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Email Address') }}</th>
                        <th>{{ __('Created on') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subscribers as $subscriber)
                        <tr>
                            <td> {{ $subscriber->id }}</td>
                            <td> {{ $subscriber->email }}</td>
                            <td> {{ $subscriber->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">
                    {{ $subscribers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function update_status(el){
            $.post('{{ route('newsletters.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value}, function(data){
                data = JSON.parse(data);
                if(data.status === 1) {
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);

                    setTimeout(function () {
                        el.checked = false;
                    }, 500);
                }
            });
        }
    </script>
@endsection

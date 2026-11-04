@extends('layouts.app')

@section('content')

    <div class="panel">
        <div class="panel-body">
            <a class="btn btn-primary" href="{{ route('report.histories.export') }}">{{ __('Download Excel') }}</a>
            <table class="table table-striped"
                   style="width: 100%; border-collapse: collapse; border-spacing: 0;">
                <thead>
                <tr>
                    <th>{{__('User') }}</th>
                    <th>{{__('Seller') }}</th>
                    <th>{{__('Action') }}</th>
                    <th>{{__('Object') }}</th>
                    <th>{{ __('Object ID') }}</th>
                    <th>{{__('Name') }}</th>
                    <th>{{__('Fields change') }}</th>
                    <th>{{__('Date change') }}</th>
                    <th style="width: 10%;">{{__('View') }}</th>
                </tr>
                </thead>

                <tbody>
                @if($histories)
                    @foreach($histories as $entity)
                        <tr>
                            <td>{{ $entity->user_name }} @if ($entity->user_deleted_at !== null) (deleted) @endif</td>
                            <td>{{ $entity->user_type == 'seller' ? $entity->user_name : ($entity->user_type == 'customer' ? 'customer' : 'admin'), }}</td>
                            <td>{{ $entity->action }}</td>
                            <td>{{ $entity->module_name }}</td>
                            <td>{{ $entity->historiable_id }}</td>
                            <td>{{ $entity->name }}</td>
                            <td>{{ getHistoryUpdatedFields($entity->new_value) }}</td>
                            <td>{{ $entity->created_at }}</td>
                            <td>
                                <span class="show-ajax-modal-detail" data-url="{{ route('ajax.hitories.show', $entity->id) }}" style="cursor: pointer;">
                                    <span class="fa fa-eye"></span>
                                    <span style="text-decoration: underline;">View</span>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">
                    {{ $histories->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@include('admin.inc.modal_ajax_content', ['modalTitle' => ''])
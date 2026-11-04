@extends('layouts.app')

@section('content')
    <!-- Basic Data Tables -->
    <!--===================================================-->
    <div class="panel">
        <div class="panel-body">
            <table class="table table-striped res-table mar-no" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{__('Email') }}</th>
                    <th>{{__('Access Passes left') }}</th>
                    <th width="10%">{{__('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $key => $user)
                    <tr>
                        <td>{{$user->id}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{ $shareLimit - ($user->shared ?? 0) }} / {{ $shareLimit }}</td>
                        <td>
                            <a href="{{ route('admin.referral.user_reset', ['user' => $user->id]) }}"
                               title="Refresh Access Pass">
                                <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection


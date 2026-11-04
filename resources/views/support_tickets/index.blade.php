@extends('layouts.app')



@section('content')



<div class="panel">

    <div class="panel-heading bord-btm clearfix pad-all h-100">

        <h3 class="panel-title pull-left pad-no">{{ __('Support Desk') }}</h3>

        <div class="pull-right clearfix" >

            <form class="" id="sort_support" action="" method="GET">

                <div class="box-inline pad-rgt pull-left">

                    <div class="" style="min-width: 300px;">

                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ __('Type ticket code & Enter') }}">

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="panel-body">

        <table class="table table-striped demo-dt-basic mar-no" cellspacing="0" width="100%">

            <thead>

                <tr>

                    <th>{{ __('Ticket ID') }}</th>

                    <th>{{ __('Sending Date') }}</th>

                    <th>{{ __('Subject') }}</th>

                    <th>{{ __('User') }}</th>

                    <th>{{ __('Status') }}</th>

                    <th>{{ __('Last reply') }}</th>

                    <th>{{ __('Options') }}</th>

                </tr>

            </thead>

            <tbody>

                    @foreach ($tickets as $key => $ticket)

                    @if ($ticket->user != null)
                        @php
                        $id = $ticket->id + 100000502;
                        @endphp
                        <tr>

                            <td>#{{ $id  }}</td>

                            <td>{{ $ticket->created_at }} @if($ticket->viewed == 0) <span class="pull-right badge badge-info">{{ __('New') }}</span> @endif</td>

                            <td>{{ $ticket->subject }}</td>

                            <td>{{ $ticket->user->name }}</td>

                            <td>

                                @if ($ticket->status == 'pending')

                                    <span class="badge badge-pill badge-danger">{{ __('Pending') }}</span>

                                @elseif ($ticket->status == 'open')

                                    <span class="badge badge-pill badge-secondary">{{ __('Open') }}</span>

                                @else

                                    <span class="badge badge-pill badge-success">{{ __('Solved') }}</span>

                                @endif

                            </td>

                            <td>

                                @if (count($ticket->ticketreplies) > 0)

                                    {{ $ticket->ticketreplies->last()->created_at }}

                                @else

                                    {{ $ticket->created_at }}

                                @endif

                            </td>

                            <td>

                                <a href="{{route('support_ticket.admin_show', encrypt($ticket->id))}}" class="btn-link">{{ __('View Details') }}</a>

                            </td>

                        </tr>

                    @endif

                @endforeach

            </tbody>

        </table>

        <div class="clearfix">

            <div class="pull-right">

               {{ $tickets->appends(request()->input())->links() }}

            </div>

        </div>

    </div>

</div>
<style>
    .dataTables_paginate{
    display:none;
    }
    </style>
    


@endsection


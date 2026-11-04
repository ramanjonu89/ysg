@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-sm-12">
        </div>
    </div>
    <br>
    <div class="panel">
        <div class="panel-heading bord-btm clearfix pad-all h-100">
            <h3 class="panel-title pull-left pad-no">{{ __('Contact Forms') }}</h3>
            <div class="pull-right clearfix">
                <form class="" id="sort_contact_forms" action="" method="GET">
                    <div class="box-inline pad-rgt pull-left">
                        <div class="" style="min-width: 200px;">
                            <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ __('Search by name, email or message') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>
                        @php
                            $nameSortUrl = $sort_field == 'name' && $sort_direction == 'asc' 
                                ? request()->fullUrlWithQuery(['sort_field' => 'name', 'sort_direction' => 'desc'])
                                : ($sort_field == 'name' && $sort_direction == 'desc' 
                                    ? request()->fullUrlWithQuery(['sort_field' => null, 'sort_direction' => null])
                                    : request()->fullUrlWithQuery(['sort_field' => 'name', 'sort_direction' => 'asc']));
                        @endphp
                        <a href="{{ $nameSortUrl }}" class="sort-link">
                            {{ __('Name') }}
                            @if($sort_field == 'name')
                                @if($sort_direction == 'asc')
                                    <i class="fa fa-sort-asc sort-icon"></i>
                                @elseif($sort_direction == 'desc')
                                    <i class="fa fa-sort-desc sort-icon"></i>
                                @endif
                            @else
                                <i class="fa fa-sort sort-icon inactive"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        @php
                            $emailSortUrl = $sort_field == 'email' && $sort_direction == 'asc' 
                                ? request()->fullUrlWithQuery(['sort_field' => 'email', 'sort_direction' => 'desc'])
                                : ($sort_field == 'email' && $sort_direction == 'desc' 
                                    ? request()->fullUrlWithQuery(['sort_field' => null, 'sort_direction' => null])
                                    : request()->fullUrlWithQuery(['sort_field' => 'email', 'sort_direction' => 'asc']));
                        @endphp
                        <a href="{{ $emailSortUrl }}" class="sort-link">
                            {{ __('Email') }}
                            @if($sort_field == 'email')
                                @if($sort_direction == 'asc')
                                    <i class="fa fa-sort-asc sort-icon"></i>
                                @elseif($sort_direction == 'desc')
                                    <i class="fa fa-sort-desc sort-icon"></i>
                                @endif
                            @else
                                <i class="fa fa-sort sort-icon inactive"></i>
                            @endif
                        </a>
                    </th>
                    <th>{{ __('Message') }}</th>
                    <th>
                        @php
                            $readSortUrl = $sort_field == 'is_read' && $sort_direction == 'asc' 
                                ? request()->fullUrlWithQuery(['sort_field' => 'is_read', 'sort_direction' => 'desc'])
                                : ($sort_field == 'is_read' && $sort_direction == 'desc' 
                                    ? request()->fullUrlWithQuery(['sort_field' => null, 'sort_direction' => null])
                                    : request()->fullUrlWithQuery(['sort_field' => 'is_read', 'sort_direction' => 'asc']));
                        @endphp
                        <a href="{{ $readSortUrl }}" class="sort-link">
                            {{ __('Status') }}
                            @if($sort_field == 'is_read')
                                @if($sort_direction == 'asc')
                                    <i class="fa fa-sort-asc sort-icon"></i>
                                @elseif($sort_direction == 'desc')
                                    <i class="fa fa-sort-desc sort-icon"></i>
                                @endif
                            @else
                                <i class="fa fa-sort sort-icon inactive"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        @php
                            $createdAtSortUrl = $sort_field == 'created_at' && $sort_direction == 'asc' 
                                ? request()->fullUrlWithQuery(['sort_field' => 'created_at', 'sort_direction' => 'desc'])
                                : ($sort_field == 'created_at' && $sort_direction == 'desc' 
                                    ? request()->fullUrlWithQuery(['sort_field' => null, 'sort_direction' => null])
                                    : request()->fullUrlWithQuery(['sort_field' => 'created_at', 'sort_direction' => 'asc']));
                        @endphp
                        <a href="{{ $createdAtSortUrl }}" class="sort-link">
                            {{ __('Submitted At') }}
                            @if($sort_field == 'created_at')
                                @if($sort_direction == 'asc')
                                    <i class="fa fa-sort-asc sort-icon"></i>
                                @elseif($sort_direction == 'desc')
                                    <i class="fa fa-sort-desc sort-icon"></i>
                                @endif
                            @else
                                <i class="fa fa-sort sort-icon inactive"></i>
                            @endif
                        </a>
                    </th>
                    <th width="10%">{{ __('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($contactForms as $key => $contactForm)
                    <tr>
                        <td>{{ ($key+1) + ($contactForms->currentPage() - 1)*$contactForms->perPage() }}</td>
                        <td>{{ $contactForm->name }}</td>
                        <td>{{ $contactForm->email }}</td>
                        <td>{{ Str::limit($contactForm->message, 50) }}</td>
                        <td>
                            @if($contactForm->is_read)
                                <span class="badge badge-success">{{ __('Read') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('Unread') }}</span>
                            @endif
                        </td>
                        <td>{{ $contactForm->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('contact_forms.show', $contactForm->id) }}" class="btn btn-info btn-xs" title="{{ __('View Details') }}">
                                <i class="fa fa-eye"></i>
                            </a>
                            <form id="delete-form-{{ $contactForm->id }}" action="{{ route('contact_forms.destroy', $contactForm->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-xs" onclick="confirmDelete({{ $contactForm->id }})" title="{{ __('Delete') }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">
                    {{ $contactForms->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this contact form?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection


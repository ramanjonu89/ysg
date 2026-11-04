@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{ route('contact_forms.index') }}" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> {{ __('Back to List') }}
            </a>
        </div>
    </div>
    <br>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Contact Form Details') }}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 200px;">{{ __('Name') }}</th>
                        <td>{{ $contactForm->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Email') }}</th>
                        <td>{{ $contactForm->email }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Message') }}</th>
                        <td>{{ $contactForm->message }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Status') }}</th>
                        <td>
                            @if($contactForm->is_read)
                                <span class="badge badge-success">{{ __('Read') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('Unread') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Submitted At') }}</th>
                        <td>{{ $contactForm->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="mar-top text-right">
                @if(!$contactForm->is_read)
                    <button type="button" class="btn btn-success" onclick="markAsRead({{ $contactForm->id }})">
                        <i class="fa fa-check"></i> {{ __('Mark as Read') }}
                    </button>
                @else
                    <button type="button" class="btn btn-warning" onclick="markAsUnread({{ $contactForm->id }})">
                        <i class="fa fa-times"></i> {{ __('Mark as Unread') }}
                    </button>
                @endif
                
                <form id="delete-form-{{ $contactForm->id }}" action="{{ route('contact_forms.destroy', $contactForm->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $contactForm->id }})">
                        <i class="fa fa-trash"></i> {{ __('Delete') }}
                    </button>
                </form>
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

        function markAsRead(id) {
            $.ajax({
                url: '/tifpwysne/contact-forms/' + id + '/mark-as-read',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }

        function markAsUnread(id) {
            $.ajax({
                url: '/tifpwysne/contact-forms/' + id + '/mark-as-unread',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }
    </script>
@endsection


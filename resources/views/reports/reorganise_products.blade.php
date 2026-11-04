@extends('layouts.app')
@section('content')
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{ __('Reorganise Products Export') }}</h3>
        </div>
        <div class="panel-body">
            <br>
            <div class="">
                <a href="{{ route('reorganise_products_report') }}"><button class="btn btn-primary">{{ __('Download Excel') }}</button></a>
            </div>
            <br>
        </div>
    </div>
    @if (session()->has('failed'))
        <div class="panel">
            <div class="panel-heading">
                <div class="bg-green" style="border-radius:3px">
                    <h5 class="mt-0 total-row-successed">Total Rows Import Successed: {{ intval(session()->get('total')) - intval(session()->get('failed')) }}</h5>
                </div>
                <div class="bg-red" style="border-radius:3px">
                    <h5 class="mb-1 total-row-failed">Total Rows Import Failed: {{ intval(session()->get('failed')) }}</h5>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('failures'))
        <div id="table-scroll" style="height:150px;overflow:auto;">
            <table class="table table-sm" style="background-color: #f5c6cb;">
                <thead>
                    <th>Row</th>
                    <th>Attribute</th>
                    <th>Error</th>
                    <th>Value</th>
                </thead>
                @foreach (session()->get('failures') as $validation)
                    <tr>
                        <td class="table-danger">{{ $validation->row() }}</td>
                        <td class="table-danger">{{ $validation->attribute() }}</td>
                        <td class="table-danger">
                            <ul>
                                @foreach ($validation->errors() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="table-danger">{{ $validation->values()[$validation->attribute()] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
    <div class="panel">
        <div class="panel-heading">
            <h1 class="panel-title"><strong>{{ __('Upload Product File') }}</strong></h1>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" action="{{ route('reorganise_products_upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <input type="file" class="form-control" name="reorganise_products" required>
                </div>
                <div class="form-group">
                    <div class="col-lg-12">
                        <button class="btn btn-primary" type="submit">{{ __('Upload Excel') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


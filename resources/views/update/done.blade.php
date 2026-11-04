@extends('layouts.blank')

@section('content')

    <div class="mar-ver pad-btm text-center">

        <h1 class="h3">{{ __('Congratulations') }}!!!</h1>

        <p>{{ __('You have successfully completed the updating process. Please Login to continue') }}.</p>

    </div>

    <div class="text-center">

        <a href="{{ env('APP_URL') }}" class="btn btn-primary">{{ __('Go to Home') }}</a>

        <a href="{{ env('APP_URL') }}/admin" class="btn btn-success">{{ __('Login to Admin panel') }}</a>

    </div>

@endsection


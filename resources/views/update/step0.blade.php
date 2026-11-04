@extends('layouts.blank')

@section('content')

    <div class="mar-ver pad-btm text-center">

        <h1 class="h3">{{ __('Active eCommerce CMS Update Process') }}</h1>

        <p>{{ __('You will need to know the following items before

        proceeding') }}.</p>

    </div>

    <ol class="list-group">

        <li class="list-group-item text-semibold"><i class="fa fa-check"></i> {{ __('Codecanyon purchase code') }}</li>

        <li class="list-group-item text-semibold"><i class="fa fa-check"></i> {{ __('Database Name') }}</li>

        <li class="list-group-item text-semibold"><i class="fa fa-check"></i> {{ __('Database Username') }}</li>

        <li class="list-group-item text-semibold"><i class="fa fa-check"></i> {{ __('Database Password') }}</li>

        <li class="list-group-item text-semibold"><i class="fa fa-check"></i> {{ __('Database Hostname') }}</li>

    </ol>

    <br>

    <div class="text-center">

        <a href="{{ route('step1') }}" class="btn btn-info text-light">

            {{ __('Update Now') }}

        </a>

    </div>

@endsection


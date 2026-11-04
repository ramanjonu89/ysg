@extends('layouts.app')
@section('content')


  <noscript>
    <strong>{{ __("We're sorry but Seller Center doesn't work properly without JavaScript enabled. Please enable it to continue.") }}</strong>
  </noscript>

  <script>
    localStorage.setItem('sct', "{{ $data['token'] }}");
    localStorage.setItem('user', '@json($data["user"])');
  </script>
  <div id="loading-bg">
    <div class="loading-logo">
      <img src="{{ my_asset($generalSetting->logo) }}" alt="Logo" />
    </div>
    <div class="loading">
      <div class="effect-1 effects"></div>
      <div class="effect-2 effects"></div>
      <div class="effect-3 effects"></div>
    </div>
  </div>
  <div id="app">
  </div>

  {{-- <script src="{{ asset(mix('js/app.js')) }}" defer></script> --}}

@endsection
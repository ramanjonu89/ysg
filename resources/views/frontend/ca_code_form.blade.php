@extends('frontend.layouts.app')
@section('content')
    <section class="gry-bg py-5">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            <div class="text-center px-5 pt-5 ">
                                <h1 class="heading heading-4 strong-500">
                                    {{ $title }}
                                </h1>
                            </div>
                            <div class="px-5 py-3 py-lg-4">
                                <div class="">
                                    <form class="form-default" role="form" action="{{ $actionUrl }}" method="GET">
                                        <div class="form-group">
                                            <input type="text" class="form-control h-auto form-control-lg"
                                                   placeholder="{{ __('Code') }}" name="access_code" id="access_code" required>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-styled btn-base-1 btn-md w-100">{{  __('Proceed') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script type="text/javascript">
    </script>
@endsection

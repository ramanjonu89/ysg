@extends('layouts.app')

@section('content')

    <div class="tab-base">
        <!--Nav Tabs-->
        <ul class="nav nav-tabs">
            <li>
                <a href="#home-slider" aria-expanded="false">{{ __('Home slider') }}</a>
            </li>
            <li class="">
                <a href="#home-banner-1" aria-expanded="false">{{ __('Home banner 1') }}</a>
            </li>
            <li class="">
                <a href="#home-categories" aria-expanded="false">{{ __('Home categories') }}</a>
            </li>
            <li class="">
                <a href="#home-banner-2" aria-expanded="false">{{ __('Home banner 2') }}</a>
            </li>
            <li class="">
                <a href="#top-10" aria-expanded="false">{{ __('Top 10') }}</a>
            </li>
            <li class="">
                <a href="#home-blocks" aria-expanded="false">{{ __('Home Blocks') }}</a>
            </li>
        </ul>
        <!--Tabs Content-->
        <div class="tab-content">

            @component('frontend_settings.home.home_slider', ['sliderList' => $sliderList])
                <strong>Whoops!</strong> home_slider!
            @endcomponent

            @component('frontend_settings.home.home_banner_1', ['listBanner' => $homeBanner1List])
                <strong>Whoops!</strong> home_banner_1!
            @endcomponent

            @component('frontend_settings.home.home_categories')
                <strong>Whoops!</strong> home_categories!
            @endcomponent

            @component('frontend_settings.home.home_banner_2', ['listBanner' => $homeBanner2List])
                <strong>Whoops!</strong> home_banner_2!
            @endcomponent

            @component('frontend_settings.home.top_10')
                <strong>Whoops!</strong> top_10!
            @endcomponent

            @component('frontend_settings.home.blocks', ['page' => $page, 'allBlocks' => $allBlocks, 'blocks' => $blocks, 'blockTypes' => $blockTypes])
                <strong>Whoops!</strong> blocks!
            @endcomponent

        </div>
    </div>
@endsection

@section('script')
    <script src="{{ static_asset('js/loading-overlay.min.js') }}"></script>

    <script type="text/javascript">

      $(function(){
        let hash = window.location.hash || '#home-slider';
        hash && $('ul.nav a[href="' + hash + '"]').tab('show');
        $('.nav-tabs a').click(function (e) {
          $(this).tab('show');
          window.location.hash = this.hash;
        });
      });

        function toggle_publish(el, url){
            $.post(url, {_token:'{{ csrf_token() }}'}, function(data){
                if (data.status === true) {
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message);
                    $(el).prop('checked', false);
                }
            });
        }

    </script>
@endsection


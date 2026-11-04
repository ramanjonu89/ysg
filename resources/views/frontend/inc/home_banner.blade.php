<section class="sticky-top-item home-banner-area home-menu" style="background: {{ getMenuBarAreaColor() }}; color: #fff !important; border-bottom: 1px solid #e9e9e9; max-height:46px;">
    <div class="container">
        <div class="row no-gutters position-relative">
            <div class="col-lg-3 col-sm-6">
                <div class="category-sidebar">
                    <div class="all-category d-none d-lg-block" @if (isMicrosite()) style="background: {{ getMenuBarAreaColor() }}" @endif>
                        <div class="dropdown-toggle " id="category-menu-icon">

                            {{ __('Categories') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-sm-6">
                @if ($generalSetting->show_css_banner && !isMicrosite())
                <a href="{{ $generalSetting->css_url }}" class="pull-right">
                    <img style="max-height:45px;" src="{{ asset($generalSetting->css_banner) }}" />
                </a>
                @endif
            </div>
        </div>
    </div>
</section>
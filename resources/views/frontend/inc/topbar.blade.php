<!-- Top Bar -->
<div class="top-navbar">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col">
                @if (!isMicrosite())
                    <div class="nav-bar inline-links d-lg-inline-block d-flex justify-content-between">
                        <ul class="inline-links" style="font-weight:600">
                            <li class="dropdown" id="currency-change" style="font-weight:600">
                            <li>
                                <a href="/shops/create" class="top-bar-item">{{ __('Be a seller') }}</a>
                            </li>
                            <li>
                                <span class="top-bar-item" style="color:#000; font-weight:600">{{ __('Follow us') }}</span>
                            </li>
                            @if ($generalSetting->facebook != null)
                                <li>
                                    <a href="{{ $generalSetting->facebook }}" class="facebook top-bar-item"
                                       target="_blank" data-toggle="tooltip" data-original-title="Facebook">
                                        <i class="fa fa-facebook"></i>
                                    </a>
                                </li>
                            @endif
                            @if ($generalSetting->instagram != null)
                                <li>
                                    <a href="{{ $generalSetting->instagram }}" class="instagram top-bar-item"
                                       target="_blank" data-toggle="tooltip" data-original-title="Instagram">
                                        <i class="fa fa-instagram"></i>
                                    </a>
                                </li>
                            @endif
                            @if ($generalSetting->twitter != null)
                                <li>
                                    <a href="{{ $generalSetting->twitter }}" class="twitter top-bar-item"
                                       target="_blank" data-toggle="tooltip" data-original-title="Twitter">
                                        <i class="fa fa-twitter"></i>
                                    </a>
                                </li>
                            @endif
                            @if ($generalSetting->youtube != null)
                                <li>
                                    <a href="{{ $generalSetting->youtube }}" class="youtube top-bar-item"
                                       target="_blank" data-toggle="tooltip" data-original-title="Youtube">
                                        <i class="fa fa-youtube"></i>
                                    </a>
                                </li>
                            @endif
                            @if ($generalSetting->google_plus != null)
                                <li>
                                    <a href="{{ $generalSetting->google_plus }}" class="google-plus top-bar-item"
                                       target="_blank" data-toggle="tooltip" data-original-title="Google Plus">
                                        <i class="fa fa-google-plus"></i>
                                    </a>
                                </li>
                            @endif
                            @if ($generalSetting->telegram != null)
                                <li>
                                    <a href="{{ $generalSetting->telegram }}" class="telegram top-bar-item"
                                       target="_blank" data-toggle="tooltip" data-original-title="Telegram">
                                        <i class="fa fa-telegram" aria-hidden="true"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
            <div class="col-5 text-right d-none d-lg-block">
                <ul class="inline-links">
                    <li>
                        <a href="{{ route('orders.track') }}" class="top-bar-item"
                           style="font-weight:600">{{ __('Track Order') }}</a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('dashboard') }}" class="top-bar-item"
                               style="font-weight:600">{{ __('Hello') }} {{auth()->user()->name}}</a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" id="user-logout" class="top-bar-item"
                               style="font-weight:600">{{ __('Logout') }}</a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('user.login') }}" class="top-bar-item"
                               style="font-weight:600">{{ __('Login') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('user.registration') }}" class="top-bar-item"
                               style="font-weight:600">{{ __('Registration') }}</a>
                        </li>
                    @endauth
                    @if(isset($languages) && count($languages) > 0)
                    <li style="display: none">
                        <select name="languages" id="languages" style="border: none;padding: 7px 0;color: #000 !important;font-weight:bold;width: auto;outline: none;">
                            @foreach($languages as $language)
                            <option value="{{$language->code}}" {{ getSessionLanguageCode() ==  $language->code ? 'selected' : ''}}>{{$language->name}}</option>
                            @endforeach
                        </select>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- END Top Bar -->

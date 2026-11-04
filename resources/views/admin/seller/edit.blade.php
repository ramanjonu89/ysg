@extends('layouts.app')
@section('content')
    <div class="col-lg-6 col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading text-center">
                <h3 class="panel-title">{{ __('Seller Information') }}</h3>
            </div>
            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" action="{{ route('sellers.update', $seller->id) }}" method="POST"
                  enctype="multipart/form-data">
                @method('PATCH')
                @csrf
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="company_id">{{ __('OMS Company ID') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ __('OMS Company ID') }}" id="company_id"
                                   name="company_id" class="form-control" value="{{$seller->user->company_id}}">
                            <small>Please obtain the company ID from OMS Team*</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ __('OMS Sync Settings') }}</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="switch">
                                            <input type="checkbox" name="sync_products_enabled" value="1"
                                                    @if($seller->sync_products_enabled) checked @endif>
                                            <span class="slider round"></span>
                                        </label>
                                        <span class="ml-2">{{ __('Sync Products') }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="switch">
                                            <input type="checkbox" name="sync_stocks_enabled" value="1"
                                                    @if($seller->sync_stocks_enabled) checked @endif>
                                            <span class="slider round"></span>
                                        </label>
                                        <span class="ml-2">{{ __('Sync Stocks') }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="switch">
                                            <input type="checkbox" name="sync_orders_enabled" value="1"
                                                    @if($seller->sync_orders_enabled) checked @endif>
                                            <span class="slider round"></span>
                                        </label>
                                        <span class="ml-2">{{ __('Sync Orders') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="name">{{ __('Name') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ __('Name') }}" id="name" name="name"
                                   class="form-control" value="{{$seller->user->name}}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="email">{{ __('Email Address') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ __('Email Address') }}" id="email" name="email"
                                   class="form-control" value="{{$seller->user->email}}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="phone">{{ __('Phone') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{__('Phone')}}" id="phone" name="phone"
                                   class="form-control" value="{{$seller->user->phone}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="password">{{ __('Password') }}</label>
                        <div class="col-sm-9">
                            <input type="password" placeholder="{{ __('Password') }}" id="password" name="password"
                                   class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="name">{{ __('Photo') }}</label>
                        <div class="col-sm-9">
                            <div class="img-upload-preview">
                                <img loading="lazy" src="{{ my_asset($seller->user->avatar_original) }}" alt="" class="img-fluid">
                            </div>
                            <input type="file" name="photo" id="file-photo"
                                   class="form-control mb-3 custom-input-file custom-input-file--4"
                                   data-multiple-caption="{count} files selected" accept="image/*"/>
                            <label for="file-photo" class="mw-100 mb-3">
                                <span></span>
                                <strong>
                                    <i class="fa fa-upload"></i>
                                    {{ __('Choose image') }}
                                </strong>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"
                               for="company_shipping">{{ __('Shipping Option') }}</label>
                        <div class="col-sm-9">
                            <select class="form-control " data-placeholder="{{ __('Select a Shipping option') }}"
                                    id="company_shipping" name="company_shipping">
                                <option value="1" @if($seller->user->company_shipping == 1) selected @endif>YSG Shipping</option>
                                <option value="2" @if($seller->user->company_shipping == 2) selected @endif>External Shipping</option>
                            </select>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="company_type">{{ __('Company Type') }}</label>
                        <div class="col-sm-9">
                            <select class="form-control " data-placeholder="{{ __('Select a Company Type') }}"
                                    id="company_type" name="company_type">
                                <option value="1" @if($seller->user->company_type == 1) selected @endif>Flag Ship
                                </option>
                                <option value="2" @if($seller->user->company_type == 2) selected @endif>Private Event
                                </option>
                            </select>
                        </div>
                    </div>

                    <div id="setting-access">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label class="switch pull-right">
                                    <input type="checkbox" name="active_corporate_access_code" id="active_corporate_access_code" value="1"
                                           @if($seller->active_corporate_access_code) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <strong>{{ __('Corporate Access Code') }}</strong>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="corporate_access_code"></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Unique Corporate Access Code') }}"
                                       id="corporate_access_code" name="corporate_access_code" class="form-control"
                                       value="{{$seller->corporate_access_code}}"
                                >
                                <div class="mt-2">
                                    <a href="{{ route('shop.visit', ['slug' => $seller->user->shop->slug, 'access_code' => $seller->corporate_access_code]) }}">
                                        <small>
                                        {{ route('shop.visit', ['slug' => $seller->user->shop->slug, 'access_code' => $seller->corporate_access_code]) }}
                                        </small>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-3">
                                <label class="switch pull-right">
                                    <input type="checkbox" name="enable_access_code" id="enable_access_code" value="1"
                                           @if($seller->user->enable_access_code == 1) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="col-sm-9">
                                <strong>{{ __('Access Code (Private Sales)') }}</strong>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="access_code"></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('Access Code') }}"
                                       id="company_password" name="company_password" class="form-control"
                                       value="{{$seller->user->company_password}}"
                                >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <strong>{{ __('Message Content') }}</strong>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="Message Content"></label>
                            <div class="col-sm-9">
                            <textarea name="message_content" placeholder="{{ __('Message Content') }}"
                                      id="message_content" class="form-control"
                                      rows="3">{{ $seller->user->message_content }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="switch pull-right">
                                            <input type="checkbox" name="enable_corporate_domains" value="1"
                                                   @if($seller->user->enable_corporate_domains == 1) checked @endif>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <strong>{{ __('Corporate Domains') }}</strong>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="file" id="file_domain_upload"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="col-sm-12">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button class="btn btn-default pull-right" id="import_domain"
                                                type="button">{{ __('Import') }}</button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button class="btn btn-default pull-right" type="button"
                                                id="export_domain">{{ __('Export') }}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <span>{{ __('Domain List') }}</span>
                                    </div>
                                </div>
                                <div class="form-group" style="height: 100px; overflow: overlay">
                                    <div class="col-sm-12" id="domain_file_content"></div>
                                    <input type="hidden" name="domains">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="file" id="file_email_upload"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="col-sm-12">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button class="btn btn-default pull-right" id="import_email"
                                                type="button">{{ __('Import') }}</button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button class="btn btn-default pull-right" type="button"
                                                id="export_email">{{ __('Export') }}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <span>{{ __('Email List') }}</span>
                                    </div>
                                </div>
                                <div class="form-group" style="height: 100px; overflow: overlay">
                                    <div class="col-sm-12" id="email_file_content"></div>
                                    <input type="hidden" name="emails">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" hidden>
                            <table id="old-domains">
                                @foreach ($oldCorporateDomains as $domain)
                                    <tr>
                                        <td>{{ $domain->domain }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="form-group" hidden>
                            <table id="old-emails">
                                @foreach ($emailCoporateAcces as $email)
                                    <tr>
                                        <td>{{ $email->email }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="company_start_date">{{__('Private Event Date')}}</label>


                            <div class="col-sm-9">


                                <input type="text" class="form-control datepicker" id="company_start_date"
                                       name="company_start_date"
                                       value="{{date("Y-m-d", strtotime($seller->user->company_start_date))}}">

                                <span class="input-group-addon">{{ __('to') }}</span>
                                <input type="text" class="form-control datepicker" id="company_end_date"
                                       name="company_end_date"
                                       value="{{date("Y-m-d", strtotime($seller->user->company_end_date))}}">

                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="switch pull-right">
                                <input type="checkbox" name="sale_report_enabled" value="1"
                                       @if($seller->user->sale_report_enabled == 1) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="col-sm-3">
                            <strong>{{__('Sale Report')}}</strong>
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control mb-3 frequency-select" data-placeholder="{{ __('Frequency')}}" name="sale_report_frequency">
                                @foreach(\App\Models\UserTask::FREQUENCY_LIST as $value => $title)
                                    <option value="{{ $value }}" @if($seller->user->sale_report_frequency === $value) selected @endif>{{ __($title) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <input placeholder="Options" class="form-control" type="text" name="sale_frequency_options" value="{{ $seller->user->sale_frequency_options }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="switch pull-right">
                                <input type="checkbox" name="fulfilment_report_enabled" value="1"
                                       @if($seller->user->fulfilment_report_enabled == 1) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>{{__('Fulfilment Report')}}</strong>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-control mb-3 frequency-select"
                                            data-placeholder="{{ __('Frequency')}}"
                                            name="fulfilment_report_frequency">
                                        @foreach(\App\Models\UserTask::FREQUENCY_LIST as $value => $title)
                                            <option value="{{ $value }}"
                                                    @if($seller->user->fulfilment_report_frequency === $value) selected @endif>{{ __($title) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <input placeholder="Options" class="form-control" type="text" name="fulfilment_frequency_options" value="{{ $seller->user->fulfilment_frequency_options }}" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <small>
                                        <p>Example of options (empty options will take a default schedule):</p>
                                        <ul>
                                            <li>Daily: 18:00</li>
                                            <li>Weekly: Monday, 13:00 (must have comma)</li>
                                            <li>Monthly: leave empty</li>
                                        </ul>
                                        <p>Default schedule:</p>
                                        <ul>
                                            <li>Daily: Run everyday at 00:00</li>
                                            <li>Weekly: Run every Sunday at 00:00</li>
                                            <li>Monthly: Run on the first day of every month at 00:00</li>
                                        </ul>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="email_notification">{{__('Email Notification')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{__('E.g. shawn@ysg.com.sg')}}"
                                   id="email_notification" name="email_notification" class="form-control"
                                   value="{{$seller->user->email_notification}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="photo">{{ __('Image for CSS Page') }}</label>
                        <div class="col-sm-9">
                            @if ($seller->image != null)
                                <div class="col-md-4 col-sm-4 col-xs-6">
                                    <div class="img-upload-preview">
                                        <img loading="lazy"  src="{{ my_asset($seller->image) }}" alt="" class="img-responsive">
                                        <input type="hidden" name="previous_photo" value="{{ $seller->image }}">
                                        <button type="button" class="btn btn-danger close-btn remove-files"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            @endif
                            <div id="photo">

                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="api_key">{{ __('API Key') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ __('API Key') }}" id="api_key" name="api_key"
                                   class="form-control" value="{{ $seller->user->api_key }}">
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
            <!--===================================================-->
            <!--End Horizontal Form-->
        </div>
    </div>
    <div class="col-lg-6 col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title text-center">{{ __('Shop settings') }}</h3>
            </div>
            <!--Horizontal Form-->
            <!--===================================================-->
            <form class="form-horizontal" action="{{ route('admin.sellers.shops.update', $shop->id) }}" method="POST"
                  enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                @csrf
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="shop_name">{{ __('Shop Name') }} <span
                                class="required-star">*</span></label>
                        <div class="col-sm-9">
                            <input id="shop_name" type="text" class="form-control mb-3"
                                   placeholder="{{ __('Shop Name') }}" name="name" value="{{ $shop->name }}">
                            @if ($errors->has('name'))
                                <span class="invalid-feedback d-block">
                                    {{ $errors->first('name') }}
                                </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group @if ($errors->has('address_required')) has-error @endif">
                        <label class="col-sm-3 control-label">{{ __('Addresses') }}</label>
                        <div class="col-sm-9 addresses">
                            @foreach ($addresses as $key => $address)
                                <div class="col-md-12 address-item" id="address-item-{{ $address->id }}">
                                    <div class="border p-3 pr-5 rounded mb-3 position-relative">
                                        <div>
                                            <span class="alpha-6">{{ __('Title') }}:</span>
                                            <span class="strong-600 ml-2">{{ $address->title }}</span>
                                        </div>
                                        <div>
                                            <span class="alpha-6">{{ __('Address') }}:</span>
                                            <span class="strong-600 ml-2">{{ $address->address }}</span>
                                        </div>
                                        <div>
                                            <span class="alpha-6">{{ __('Postal Code') }}:</span>
                                            <span class="strong-600 ml-2">{{ $address->postal_code }}</span>
                                        </div>
                                        <div>
                                            <span class="alpha-6">{{ __('City') }}:</span>
                                            <span class="strong-600 ml-2">{{ $address->city }}</span>
                                        </div>
                                        <div>
                                            <span class="alpha-6">{{ __('Country') }}:</span>
                                            <span class="strong-600 ml-2">{{ $address->country }}</span>
                                        </div>
                                        <div>
                                            <span class="alpha-6">{{ __('Phone') }}:</span>
                                            <span class="strong-600 ml-2">{{ $address->fullPhone() }}</span>
                                        </div>
                                        @if ($address->set_default)
                                            <div class="position-absolute right-0 bottom-0 pr-2 pb-3">
                                                <span class="badge badge-primary bg-base-1">{{ __('Default') }}</span>
                                            </div>
                                        @endif

                                        <div class="dropdown position-absolute right-0 top-0">
                                            <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                                <i class="la la-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                @if (!$address->set_default)
                                                <a class="dropdown-item" href="{{ route('addresses.set_default', [$address->id, $seller->user->id]) }}">{{ __('Make This Default') }}</a>
                                                @endif
                                                <a class="dropdown-item d-block" href="#" data-toggle="modal" onclick="add_new_address({{ json_encode($address) }})">
                                                    {{ __('Edit') }}
                                                </a>
                                                <a class="dropdown-item" href="{{ route('addresses.destroy', $address->id) }}">{{ __('Delete') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-12 mx-auto" onclick="add_new_address()" id="add-address-box">
                                <div class="border p-3 rounded mb-3 c-pointer text-center bg-light">
                                    <i class="la la-plus la-2x"></i>
                                    <div class="alpha-7">{{ __('Add New Address') }}</div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                @if ($errors->has('address_required'))
                                    <span class="help-block">
                                        {{ $errors->first('address_required') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group @if ($errors->has('order_type')) has-error @endif">
                        <label class="col-sm-3 control-label" for="order_type">
                            {{ __('Order type') }} <span class="required-star">*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="mb-3">
                                <label class="switch">
                                    <input type="checkbox" id="checkbox_delivery" name="order_type[]" value="1"
                                        @if($shop->hasDeliveryOrderType()) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                                {{ __('Delivery') }}
                            </div>
                            <div>
                                <label class="switch">
                                    <input type="checkbox" id="checkbox_self_collection" name="order_type[]" value="2"
                                        @if($shop->hasSelfCollectionOrderType()) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                                {{ __('Self Collection') }}
                            </div>
                            <div class="col-md-12">
                                @if ($errors->has('order_type'))
                                    <span class="help-block">
                                        {{ $errors->first('order_type') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping')
                        <div id="shipping_cost_setting" @if($shop->order_type == \App\Models\Shop::ORDER_TYPE_SELF_COLLECTION) style="display: none" @endif>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="shipping_cost">{{ __('Shipping Cost') }}
                                    <span class="required-star">*</span></label>
                                <div class="col-sm-9">
                                    <input id="shipping_cost" type="number" min="0" class="form-control mb-3"
                                        placeholder="{{ __('Shipping Cost') }}" name="shipping_cost"
                                        value="{{ $shop->shipping_cost }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"
                                    for="shipping_cost_above">{{ __('Free Shipping above') }} <span
                                        class="required-star">*</span></label>
                                <div class="col-sm-9">
                                    <input id="shipping_cost_above" type="number" min="0" class="form-control mb-3"
                                        placeholder="{{ __('e.g 100') }}" name="shipping_cost_above"
                                        value="{{ $shop->shipping_cost_above }}" required>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="logo">{{ __('Logo') }}
                            <small>({{ __('120x120') }})</small></label>
                        <div class="col-sm-9">
                            <div class="img-upload-preview">
                                <img loading="lazy" src="{{ my_asset($shop->logo) }}" alt="" class="img-fluid">
                            </div>
                            <input type="file" name="logo" id="file-2"
                                   class="form-control mb-3 custom-input-file custom-input-file--4"
                                   data-multiple-caption="{count} files selected" accept="image/*"/>
                            <label for="file-2" class="mw-100 mb-3">
                                <span></span>
                                <strong>
                                    <i class="fa fa-upload"></i>
                                    {{ __('Choose image') }}
                                </strong>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="meta_title">{{ __('Meta Title') }} <span
                                class="required-star">*</span></label>
                        <div class="col-sm-9">
                            <input id="meta_title" type="text" class="form-control mb-3"
                                   placeholder="{{ __('Meta Title') }}" name="meta_title"
                                   value="{{ $shop->meta_title }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="meta_description">{{ __('Meta Description') }}
                            <span class="required-star">*</span></label>
                        <div class="col-sm-9">
                            <textarea id="meta_description" class="form-control mb-3" rows="6" name="meta_description"
                                      required>{{ $shop->meta_description }}</textarea>
                        </div>
                    </div>

                    <!-- Feature Products -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="meta_title">{{ translate('Feature Product IDs')}}</label>
                        <div class="col-sm-9">
                            <input id="feature-products" type="text" class="form-control mb-3"
                                   placeholder="{{ translate('123,456,789')}}" name="featured_products"
                                   value="{{ $shop->featured_products }}">
                            <small>{{ __('Min 6, max 10 ids') }}</small>
                        </div>
                    </div>


                    <!-- Assign product attributes -->
                    <div class="form-group">
                        <label class="col-lg-3 control-label">{{translate('Allowed product attributes')}}</label>
                        <div class="col-lg-9">
                            <select name="allowed_attributes[]" id="allowed_attributes" class="form-control demo-select2" multiple data-placeholder="{{ translate('Choose Attributes') }}">
								@foreach ($attributes as $key => $attribute)
									<option value="{{ $attribute->id }}" @if(in_array($attribute->id, $selectedAttributes)) selected @endif>{{ $attribute->name }}</option>
								@endforeach
	                        </select>
                            <small>{{ __('These values will be display in Seller Center when seller create a product variant') }}</small>
                        </div>
                    </div>

                    <!-- social accounts -->

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="facebook">{{ __('Facebook') }} <i
                                class="line-height-1_8 size-24 mr-2 fa fa-facebook bg-facebook c-white text-center"></i></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-3" placeholder="{{ __('Facebook') }}"
                                   name="facebook" value="{{ $shop->facebook }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="twitter">{{ __('Twitter') }} <i
                                class="line-height-1_8 size-24 mr-2 fa fa-twitter bg-twitter c-white text-center"></i></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-3" placeholder="{{ __('Twitter') }}"
                                   name="twitter" value="{{ $shop->twitter }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="google">{{ __('Google') }} <i
                                class="line-height-1_8 size-24 mr-2 fa fa-google bg-google c-white text-center"></i></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-3" placeholder="{{ __('Google') }}"
                                   name="google" value="{{ $shop->google }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="youtube">{{ __('Youtube') }} <i
                                class="line-height-1_8 size-24 mr-2 fa fa-youtube bg-youtube c-white text-center"></i></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-3" placeholder="{{ __('Youtube') }}"
                                   name="youtube" value="{{ $shop->youtube }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="instagram">{{ __('Instagram') }} <i
                                class="line-height-1_8 size-24 mr-2 fa fa-instagram bg-instagram c-white text-center"></i></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-3" placeholder="{{ __('Instagram') }}"
                                   name="instagram" value="{{ $shop->instagram }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="linkedin">{{ __('Linkedin') }} <i
                                class="line-height-1_8 size-24 mr-2 fa fa-linkedin bg-linkedin c-white text-center"></i></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-3" placeholder="{{ __('Linkedin') }}"
                                   name="linkedin" value="{{ $shop->linkedin }}">
                        </div>
                    </div>
                </div>

                <div class="panel-footer text-right">
                    <button class="btn btn-purple" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
            <!--===================================================-->
            <!--End Horizontal Form-->
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="panel panel-primary">
            @include('admin.custom_page.page.blocks', ['page' => $shop, 'title' => __('Seller blocks'), 'pageType' => 'shop'])
        </div>
    </div>

    <div class="modal fade" id="new-address-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ __('New Address') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-default" role="form" action="" method="POST" id="address-form">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $seller->user->id }}" />
                    <input type="hidden" name="_method" value="">
                    <div class="modal-body">
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Title') }}</label>
                                </div>
                                <div class="col-md-10 mb-3">
                                    <input type="text" class="form-control"
                                                placeholder="{{ __('Title') }}" name="title" required>
                                    <small class="text-danger error-text title_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Address') }}</label>
                                </div>
                                <div class="col-md-10 mb-3">
                                    <textarea class="form-control textarea-autogrow"
                                                placeholder="{{ __('Your Address') }}" rows="1" name="address"
                                                required></textarea>
                                    <small class="text-danger error-text address_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>{{ __('Unit No.') }}</label>
                                </div>
                                <div class="col-sm-10 mb-3">
                                    <input type="text" class="form-control" placeholder="{{ __('e.g. 09-11') }}"
                                        name="unitno" value="" required>
                                    <small class="text-danger error-text unitno_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Unit No.') }}</label>
                                </div>
                                <div class="col-md-10 mb-3">
                                    <input type="text" class="form-control" placeholder="{{ __('e.g. 09-11') }}"
                                        name="unitno" value="" required>
                                <small class="text-danger error-text unitno_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Country') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <select class="form-control mb-3" id="select-country"
                                                data-placeholder="{{ __('Select your country') }}" name="country_id"
                                                required>
                                            @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger error-text country_id_error"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('City') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3" id="select-city"
                                            data-placeholder="{{ __('Select your country') }}" name="city_id"
                                            required>
                                        @if (isset($cities) && !empty($cities))
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <small class="text-danger error-text city_id_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Postal code') }}</label>
                                </div>
                                <div class="col-md-10 mb-3">
                                    <input type="text" class="form-control"
                                            placeholder="{{ __('Your Postal Code') }}" name="postal_code" value=""
                                            required>
                                    <small class="text-danger error-text postal_code_error"></small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ __('Phone') }}</label>
                                </div>
                                <div class="col-md-10 phone-number">
                                    <input type="hidden" class="country_code" name="country_code" value="">
                                    <input type="tel" id="phone-code" class="h-auto form-control phone-init"
                                            value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off" required>
                                    <small class="text-danger error-text phone_error"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-base-1 btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- intlTelInput -->
    <link type="text/css" href="{{ static_asset_with_version('frontend/css/intlTelInput.min.css') }}" rel="stylesheet">
    <script src="{{ static_asset('frontend/js/intlTelInput.min.js') }}"></script>
    @include('frontend.inc.js.phone-input')

    <script>
        $('.frequency-select').on('change', function() {
        })
        function add_new_address(address = null) {
            if (address) {
                $('input[name=id]').val(address.id);
                $('input[name=title]').val(address.title);
                const fullAddress = address.address;
                const lastSpaceIndex = fullAddress.lastIndexOf(' ');
                const mainAddress = fullAddress.substring(0, lastSpaceIndex);
                const unitNo = fullAddress.substring(lastSpaceIndex + 1);

                $('textarea[name=address]').val(mainAddress);
                $('input[name=unitno]').val(unitNo);

                $('select[name=country_id]').val(address.country_id).trigger('change', address.city_id);

                $('input[name=postal_code]').val(address.postal_code);
                $('input[name=country_code]').val(address.country_code ?? '+65');
                $('input[name=phone]').val(address.phone);

                $('input[name=_method]').val('PATCH');
                const addressUpdateUrl = "{{ route('addresses.update', ':id') }}";
                $('#address-form').attr('action', addressUpdateUrl.replace(':id', address.id));
            }
            else {
                $('#address-form')[0].reset();
                $('input[name=country_code]').val('+65');
                $('input[name=_method]').val('');
                $('#address-form').attr('action', '{{ route('addresses.store') }}');
            }

            if (itiMain) {
                const dial = $('input[name=country_code]').val().replace('+', '');
                const matched = countryData.find(c => c.dialCode === dial);
                if (matched) {
                    itiMain.setCountry(matched.iso2);
                }
            }

            $('#new-address-modal').modal('show');
        }

        $(document).on('change', '#select-country', function (e, selectedCityId = null) {
            getCities($(this).val(), '#select-city', selectedCityId);
        });

        function getCities(countryId, citySelector, selectedCityId = null) {
            $.ajax({
                url: '/get-cities/' + countryId,
                success: function (res) {
                    if (res.status !== 200) return;

                    const $city = $(citySelector);

                    $city.html(res.html);

                    if (selectedCityId) {
                        $city.val(selectedCityId).trigger('change');
                    } else {
                        $city.val(null).trigger('change');
                    }
                }
            });
        }

        $(function () {
            $(".datepicker").datepicker(
                {
                    format: 'yyyy-mm-dd',
                }
            );

            $(document).ready(function () {
                var companyType = $('#company_type').val();
                var settingAccess = $('#setting-access');
                var domainFileContent = $("#domain_file_content");
                var emailFileContent = $("#email_file_content");

                if (companyType === '2') {
                    settingAccess.show();
                    processTableData($('#old-domains'), domainFileContent);
                    processTableData($('#old-emails'), emailFileContent);
                } else {
                    settingAccess.hide();
                }

                var $corporateAccessCode = $('#active_corporate_access_code');
                var $enableAccessCode = $('#enable_access_code');

                $corporateAccessCode.on('change', function() {
                    if ($(this).is(':checked')) {
                        $enableAccessCode.prop('checked', false);
                    }
                });

                $enableAccessCode.on('change', function() {
                    if ($(this).is(':checked')) {
                        $corporateAccessCode.prop('checked', false);
                    }
                });
            });

            function processTableData(table, targetElement) {
                var data = [];
                var csvData = "";

                table.find('tr').each(function () {
                    data.push($(this));
                });

                data.forEach(function (item) {
                    var td = item[0].children;
                    csvData += td[0].innerText + "<br />";
                });

                targetElement.html(csvData);
            }


            $('#company_type').change(function () {
                if ($(this).val() == 2) {
                    $('#setting-access').show();
                } else {
                    $('#setting-access').hide();
                }
            });

            $(function () {
                function importData(fileUploadId, targetElementId, hiddenInputName) {
                    var values = [];
                    if ($(fileUploadId).val().toLowerCase().includes('.csv')) {
                        if (typeof (FileReader) != "undefined") {
                            var reader = new FileReader();
                            reader.onload = function (event) {
                                var table = $("<table />");
                                var rows = event.target.result.split("\n");
                                for (var i = 0; i < rows.length; i++) {
                                    var row = $("<tr />");
                                    var cells = rows[i].split(",");
                                    for (var j = 0; j < cells.length; j++) {
                                        var cell = $("<td />");
                                        cell.html(cells[j]);
                                        values.push(cells[j]);
                                        row.append(cell);
                                    }
                                    table.append(row);
                                }

                                $(hiddenInputName).val(JSON.stringify(values));

                                $(targetElementId).html('');
                                $(targetElementId).append(table);
                            }

                            reader.readAsText($(fileUploadId)[0].files[0]);
                        }
                    }
                }

                function exportData(dataElementId, fileName) {
                    var data = [];
                    var csvData = "";

                    $(dataElementId + " tr").each(function () {
                        data.push($(this));
                    });

                    data.forEach(function (item) {
                        var td = item[0].children;
                        csvData += td[0].innerText + "\n";
                    })

                    var downloadLink = document.createElement("a");
                    var blob = new Blob(["\ufeff", csvData]);
                    downloadLink.href = URL.createObjectURL(blob);
                    downloadLink.download = fileName;
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }

                $("#import_domain").bind("click", function () {
                    importData("#file_domain_upload", "#domain_file_content", 'input:hidden[name=domains]');
                });

                $('#export_domain').click(function () {
                    exportData("#old-domains", "existed_corporate_domains.csv");
                })

                $("#import_email").bind("click", function () {
                    importData("#file_email_upload", "#email_file_content", 'input:hidden[name=emails]');
                });

                $('#export_email').click(function () {
                    exportData("#old-emails", "existed_corporate_emails.csv");
                })
            });

            $(document).ready(function () {
                $("#photo").spartanMultiImagePicker({
                    fieldName:          'image',
                    maxCount:           1,
                    rowHeight:          '200px',
                    groupClassName:     'col-md-4 col-sm-9 col-xs-6',
                    maxFileSize:        '',
                    dropFileLabel :     "Drop Here",
                    onExtensionErr : function(index, file){
                        console.log(index, file,  'extension err');
                        alert('Please only input png or jpg type file')
                    },

                    onSizeErr : function(index, file){
                        console.log(index, file,  'file size too big');
                        alert('File size too big');
                    }
                });
            });

            $('.remove-files').on('click', function(){
                $(this).parents(".col-md-4").remove();
            });

            $('#address-form').on('submit', function(e) {
                e.preventDefault();

                let form = this;
                let formData = new FormData(form);

                $('.error-text').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: form.action,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            $('#new-address-modal').modal('hide');
                            showAlert('success', res.message);
                            form.reset();

                            const $addressHtml = normalizeAddressHtml(res.html);

                            if (res.address_id && $('#address-item-' + res.address_id).length) {
                                $('#address-item-' + res.address_id).replaceWith($addressHtml);
                                return;
                            }
                            if ($('#add-address-box').length) {
                                $($addressHtml).insertBefore('#add-address-box');
                            } else {
                                $('.addresses').append($addressHtml);
                            }
                            if (res.count >= 6) {
                                $('#add-address-box').remove();
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('.' + key + '_error').text(value[0]);
                                $('[name="' + key + '"]').addClass('is-invalid');
                            });
                        }
                    }
                });
            });

            function normalizeAddressHtml(html) {
                return $(html)
                    .removeClass('col-lg-6')
                    .addClass('col-md-12 px-3');
            }
        });
    </script>

@endsection
<style>
    #address-form .is-invalid {
        border-color: #dc3545 !important;
    }

    .d-block {
        display: block !important;
    }

    #photo i {
        margin-top: 8px;
    }
</style>

@section('script')

    <script>
        imageInputInitialize();

        function imageInputInitialize() {
            $('.custom-input-file').each(function () {
                var $input = $(this),
                $label = $input.next('label'),
                labelVal = $label.html();
                $input.on('change', function (e) {
                    var fileName = '';
                    if (this.files && this.files.length > 1)
                        fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                    else if (e.target.value)
                        fileName = e.target.value.split('\\').pop();

                    if (fileName)
                        $label.find('span').html(fileName);
                    else
                        $label.html(labelVal);
                });

                // Firefox bug fix
                $input
                    .on('focus', function () {
                        $input.addClass('has-focus');
                    })
                    .on('blur', function () {
                        $input.removeClass('has-focus');
                    });
            });
        }

        function delete_this_row(em) {

            $(em).closest('.addtitional-row').remove();

        }

        $(document).ready(function () {
            $('.remove-files').on('click', function () {
                $(this).parents(".col-md-6").remove();
            });

            $('#allowed_attributes').on('change', function () {
                //$('#customer_choice_options').html(null);
                $.each($("#allowed_attributes option:selected"), function (j, attribute) {
                    flag = false;
                    $('input[name="allowed_attributes[]"]').each(function (i, choice_no) {
                        if ($(attribute).val() == $(choice_no).val()) {
                            flag = true;
                        }
                    });
                    if (!flag) {
                        add_more_customer_choice_option($(attribute).val(), $(attribute).text());
                    }
                });
            });
            var str = @php echo json_encode($selectedAttributes) @endphp;
			$.each(str, function(index, value){
				flag = false;
				$.each($("#allowed_attributes option:selected"), function(j, attribute){
					if(value == $(attribute).val()){
						flag = true;
					}
				});
				if(!flag){
					//console.log();
					$('input[name="allowed_attributes[]"][value="'+value+'"]').parent().parent().remove();
				}
			});

        });

        $(document).on("click", ".remove-panel-button", function(){
            $(this).closest( ".panel" ).remove();
        });

    </script>

@endsection

@extends('layouts.app')



@section('content')


    <div class=" col-md-12">

        <div class="panel">

            <!--Panel heading-->

            <div class="panel-heading">

                <h3 class="panel-title">{{ __('Coupon Usage Report') }} </h3>

            </div>


            <div class="pad-all text-center">

                <form class="" action="{{ route('coupon_report.index') }}" method="GET">


                    <div class="box-inline mar-btm pad-rgt">

                        {{ __('Seller Name') }}:

                        <div class="select">
                            <select class=" form-control input-sm" name="shop_name" required>
                                @foreach ($sellers as $key => $seller)
                                    <option value="{{ $seller->id }}"
                                        @isset($_GET['shop_name']) @if ($_GET['shop_name'] == $seller->id) selected @endif @endisset>
                                        {{ __($seller->name) }}</option>
                                @endforeach
                                <option value="0"
                                    @isset($_GET['shop_name']) @if ($_GET['shop_name'] == 0) selected @endif @endisset>
                                    All</option>
                            </select>

                        </div>

                    </div>


                    <div class="box-inline mar-btm pad-rgt">

                        {{ __('Start Date') }}:

                        <input class="form-control datepicker input-sm" type="text" name="start_date"
                            placeholder="{{ __('Start Date') }}"
                            @isset($_GET['start_date']) value="{{ $_GET['start_date'] }}" @endisset required>

                    </div>
                    <div class="box-inline mar-btm pad-rgt">

                        {{ __('End Date') }}:

                        <input class="form-control datepicker input-sm" type="text" name="end_date"
                            placeholder="{{ __('End Date') }}"
                            @isset($_GET['end_date']) value="{{ $_GET['end_date'] }}" @endisset required>

                    </div>
                    <button class="btn btn-default" type="submit">{{ __('Search') }}</button>

                </form>

            </div>

            @if (isset($_GET['shop_name']))
                <!--Panel body-->

                <div class="panel-body">

                    <div class="table-responsive">

                        <table class="table table-striped mar-no demo-dt-basic4">

                            <thead>

                                <tr>
                                    <th>No.</th>
                                    <th>{{ __('Coupon Used Date') }}</th>
                                    <th>{{ __('Order Number') }}</th>
                                    <th>{{ __('Seller Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Discount Type') }}</th>
                                    <th>{{ __('Discount Value') }}</th>
                                </tr>

                            </thead>

                            <tbody>
                                @foreach ($couponusages as $key => $couponusage)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $couponusage->couponused }}</td>
                                        <td>{{ $couponusage->order_id }}</td>
                                        <td>{{ isset($couponusage->shop_name) ? $couponusage->shop_name : 'YSG' }}</td>
                                        <td>{{ $couponusage->type }}</td>
                                        <td>{{ $couponusage->code }}</td>
                                        <td>{{ $couponusage->discount }}</td>
                                        <td>{{ $couponusage->discount_type }}</td>
                                        <td>{{ $couponusage->discount_value }}</td>
                                    </tr>
                                @endforeach

                            <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>{{ __('Coupon Used Date') }}</th>
                                    <th>{{ __('Order Number') }}</th>
                                    <th>{{ __('Seller Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Discount Type') }}</th>
                                    <th>{{ __('Discount Value') }}</th>
                                </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <script>
        $(function() {
            $(".datepicker").datepicker({
                format: 'mm/dd/yyyy',
            });
        });
    </script>

@endsection

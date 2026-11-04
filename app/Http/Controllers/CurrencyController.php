<?php
namespace App\Http\Controllers;

use App\Currency;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected $service;

    public function __construct(CurrencyService $currencyService)
    {
        $this->service = $currencyService;
    }

    public function changeCurrency(Request $request)
    {
        if ($model = $this->service->changeCurrency($request->currency_code)) {
            $request->session()->put('currency_code', $model->code);
            flash(__('Currency changed to ') . $model->name)->success();
        } else {
            flash(__('Has error when change currency'))->error();
        }
    }

    public function currency(Request $request)
    {
        $currencies = Currency::all();
        $active_currencies = Currency::where('status', 1)->get();

        return view('business_settings.currency', compact('currencies', 'active_currencies'));
    }



    // public function updateCurrency(Request $request)
    // {
    //     $currency = Currency::findOrFail($request->id);
    //     $currency->exchange_rate = $request->exchange_rate;
    //     $currency->status = $request->status;
    //     if($currency->save()){
    //         flash(__('Currency updated successfully'))->success();
    //         return '1';
    //     }
    //     flash(__('Something went wrong'))->error();
    //     return '0';
    // }
    public function updateYourCurrency(Request $request)
    {
        $currency = Currency::findOrFail($request->id);
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->code = $request->code;
        $currency->exchange_rate = $request->exchange_rate;
        $currency->status = $currency->status;
        if ($currency->save()) {
            flash(__('Currency updated successfully'))->success();

            return redirect()->route('currency.index');
        } else {
            flash(__('Something went wrong'))->error();

            return redirect()->route('currency.index');
        }
    }

    public function create()
    {
        return view('partials.currency_create');
    }

    public function edit(Request $request)
    {
        $currency = Currency::findOrFail($request->id);

        return view('partials.currency_edit', compact('currency'));
    }

    public function store(Request $request)
    {
        $currency = new Currency;
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->code = $request->code;
        $currency->exchange_rate = $request->exchange_rate;
        $currency->status = '0';
        if ($currency->save()) {
            flash(__('Currency updated successfully'))->success();

            return redirect()->route('currency.index');
        } else {
            flash(__('Something went wrong'))->error();

            return redirect()->route('currency.index');
        }
    }

    public function update_status(Request $request)
    {
        $currency = Currency::findOrFail($request->id);
        $currency->status = $request->status;
        if ($currency->save()) {
            return 1;
        }

        return 0;
    }
}


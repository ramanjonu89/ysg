<?php
namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use App\Services\LanguageService;


class LocaleSettingsController extends Controller
{
    /**
     * @param LanguageService;
     */
    protected $languageService;

    public function __construct( LanguageService $languageService ) {
        $this->languageService      = $languageService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $localeSettings = GeneralSetting::first();
        $languages      = $this->languageService->getAll();
        $languageIds    = $localeSettings->language_ids != null ? json_decode($localeSettings->language_ids) : [];
        return view("locale_settings.index", compact('localeSettings','languages','languageIds'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $localeSettings                     = GeneralSetting::first();
        $localeSettings->language_ids       = $request->language_ids ? json_encode($request->language_ids) : null;
        $localeSettings->default_language   = $request->default_language ? $request->default_language: null;

        if ($localeSettings->save()) {
            flash(__('localeSettings has been updated successfully'))->success();
            return redirect()->route('localesettings.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }
}


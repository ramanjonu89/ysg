<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\GeneralSetting;
use App\Services\LanguageService;
class Language
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
     /**
     * @param LanguageService;
     */
    protected $languageService;

    public function __construct( LanguageService $languageService ) {
        $this->languageService  = $languageService;
    }
    public function handle($request, Closure $next)
    {
        $generalSetting     = GeneralSetting::first();
        $defaultLanguage    = isset($generalSetting->default_language) ? $generalSetting->default_language : NULL;
        $languageIds        = $generalSetting->language_ids ? json_decode($generalSetting->language_ids) : [];
        $languages          = $this->languageService->getLanguages();

        $languageCodes      = !empty($languageIds) ? array_column($languages,'code') : [];

        if(!session('locale') || (!empty($languageCodes) && !in_array(session('locale'),$languageCodes))){
            session(['locale' => isset($defaultLanguage) ? $this->languageService->get($defaultLanguage)->code : (isset($languageIds) && !empty($languageIds) ? $this->languageService->get($languageIds[0])->code : 'en')]);
        }
        app()->setLocale(session('locale','en'));

        $sessionLanguageId = @$this->languageService->findBy('code',session('locale'))->id;
        session(['sessionLanguageId'=> $sessionLanguageId]);
        return $next($request);
    }
}


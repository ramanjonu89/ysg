<?php
namespace App\Services;

use App\Repositories\LanguageRepository;

class LanguageService extends AbstractService
{

    protected $repo;

    public function __construct(LanguageRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }

    public function getLanguages(){
        $generalSetting         = $this->repo->getGeneralSetting();
        $languageIds            = json_decode(@$generalSetting->language_ids);
        $languageCodes          = isset($languageIds) && !empty($languageIds) ? $this->repo->getLanguagesWithIds($languageIds) : $this->repo->getDataWithFields(['id','name','code']);
        return $languageCodes;
    }

    public function getLanguageCode($code = ''){
        $languages              = $this->getLanguages();
        $languageCodes          = array_column($languages, 'code');
        $generalSetting         = $this->repo->getGeneralSetting();
        $languageCodeDefault    = isset($generalSetting->default_language) ? $this->repo->find($generalSetting->default_language)->code : 'en';

        return in_array($code, $languageCodes) ? $code : $languageCodeDefault;
    }

    public function getAllEnabledLanguages()
    {
        $generalSetting         = $this->repo->getGeneralSetting();
        $languageIds            = json_decode(@$generalSetting->language_ids);

        return $this->repo->getLanguagesByIds($languageIds);
    }

    public function getDefaultLanguage()
    {
        $generalSetting = $this->repo->getGeneralSetting();

        return $generalSetting->default_language;
    }
}

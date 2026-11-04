<?php
namespace App\Repositories;

use App\Models\GeneralSetting;
use App\Models\Language;

class LanguageRepository extends AbstractRepo
{

    public function getModel(): string
    {
        return Language::class;
    }

    public function getLanguagesByIds(array $ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    public function getLanguagesWithIds($ids){
        return $this->model->whereIn('id', $ids)->select('id','name','code')->get()->toArray();
    }

    public function getDataWithFields(array $fields){
        return $this->model->select($fields)->get()->toArray();
    }

    public function getGeneralSetting(){
        return GeneralSetting::first();
    }
}

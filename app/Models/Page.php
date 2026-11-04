<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = [];

    public function pageTranslation($languageId = 1){
        return @$this->hasOne(PageTranslation::class)->where(['language_id' => $languageId])->first();
    }

    public function translation()
    {
        $languageId = session('sessionLanguageId');
        return $this->hasMany(PageTranslation::class)->where('language_id', $languageId);
    }

    public function getTranslationTitleAttribute()
    {
        return $this->translation->isEmpty() ? $this->title : $this->translation->first()->title;
    }

    public function getTranslationContentAttribute()
    {
        return $this->translation->isEmpty() ? $this->content : $this->translation->first()->content;
    }

    public function getTranslationMetaTitleAttribute()
    {
        return $this->translation->isEmpty() ? $this->meta_title : $this->translation->first()->meta_title;
    }

    public function getTranslationMetaDescriptionAttribute()
    {
        return $this->translation->isEmpty() ? $this->meta_description : $this->translation->first()->meta_description;
    }

    public function getTranslationKeywordAttribute()
    {
        return $this->translation->isEmpty() ? $this->keywords : $this->translation->first()->keywords;
    }
}

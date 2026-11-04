<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Log;
use App\Models\ProductTranslation;
use App\Services\LanguageService;
use App\Models\Interfaces\TranslatableInterface;

trait TranslatableEvents
{
  protected $translatableFields = [];
  protected $idField;

  protected static function bootTranslatableEvents()
  {
      static::created(function ($entity) {
          $entity->createTranslations();
      });

      static::updated(function ($entity) {
          $entity->updateTranslations();
      });
  }

  public function createTranslations()
  {
      $languageService = app()->make(LanguageService::class);
      $enabledLanguages = $languageService->getAllEnabledLanguages();

      if (count($this->translations) === 0) {
        foreach ($enabledLanguages as $language) {
          $translation = [];
          foreach ($this->translatableFields as $field) {
              $translation[$field] = $this->{$field};
          }
          $translation['language_id'] = $language->id;
          $translation[$this->idField] = $this->id;

          $this->translations()->create($translation);
          Log::info('Translation ' . $language->name . ' for ' . class_basename($this) . ' is created');
        }
      }
  }

  public function updateTranslations()
  {
      $languageService = app()->make(LanguageService::class);
      $defaultLanguageId = $languageService->getDefaultLanguage();
      $translation = $this->translationByLangId($defaultLanguageId);

      if ($translation) {
        foreach ($this->translatableFields as $field) {
          if ($this->{$field} !== $translation->{$field}) {
              $translation->{$field} = $this->{$field};
          }
        }
        $translation->save();
      } else {
          $this->createTranslations();
      }

      Log::info("Updated event fired");
  }
}

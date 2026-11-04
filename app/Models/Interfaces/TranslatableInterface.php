<?php
namespace App\Models\Interfaces;

interface TranslatableInterface
{
  public function translations();
  public function translation();
  public function translationByLangId($languageId);
}
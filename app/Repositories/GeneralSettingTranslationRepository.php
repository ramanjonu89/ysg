<?php
namespace App\Repositories;

use App\Models\GeneralSettingTranslation;
use Exception;

class GeneralSettingTranslationRepository extends AbstractRepo
{

    const PER_PAGE = 15;

    public function getModel(): string
    {
        return GeneralSettingTranslation::class;
    }

    public function updateOrCreateMultiple(array $data)
    {
        try {
            foreach ($data as $item) {
                $itemUpdate = $this->model->where(['general_setting_id' => $item['general_setting_id'], 'language_id' => $item['language_id']])->first();
                if(!isset($itemUpdate)){
                    $itemUpdate = new $this->model;
                }
                $itemUpdate->general_setting_id = $item['general_setting_id'];
                $itemUpdate->language_id        = $item['language_id'];
                $itemUpdate->site_name          = $item['site_name'];
                $itemUpdate->annoucement        = $item['annoucement'];
                $itemUpdate->description        = $item['description'];
                $itemUpdate->save();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

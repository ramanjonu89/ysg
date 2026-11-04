<?php

namespace App\Repositories;

use App\Models\SubSubCategoryTranslation;
use Exception;

class SubSubCategoryTranslationRepository extends AbstractRepo
{

    const PER_PAGE = 15;

    public function getModel(): string
    {
        return SubSubCategoryTranslation::class;
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    public function updateOrCreateMultiple(array $data)
    {
        try {
            foreach ($data as $item) {
                $itemUpdate = $this->model->where(['sub_sub_category_id' => $item['sub_sub_category_id'], 'language_id' => $item['language_id']])->first();
                if(!isset($itemUpdate)){
                    $itemUpdate = new $this->model;
                }
                $itemUpdate->sub_sub_category_id    = $item['sub_sub_category_id'];
                $itemUpdate->language_id            = $item['language_id'];
                $itemUpdate->name                   = $item['name'];
                $itemUpdate->meta_title             = $item['meta_title'];
                $itemUpdate->meta_description       = $item['meta_description'];
                $itemUpdate->save();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

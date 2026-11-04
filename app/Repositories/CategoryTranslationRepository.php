<?php

namespace App\Repositories;

use App\Models\CategoryTranslation;
use Exception;
class CategoryTranslationRepository extends AbstractRepo
{

    const PER_PAGE = 15;

    public function getModel(): string
    {
        return CategoryTranslation::class;
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    public function updateOrCreateMultiple(array $data)
    {
        try {
            foreach ($data as $item) {
                $itemUpdate = $this->model->where(['category_id' => $item['category_id'], 'language_id' => $item['language_id']])->first();
                if(!isset($itemUpdate)){
                    $itemUpdate = new $this->model;
                }
                $itemUpdate->category_id    = $item['category_id'];
                $itemUpdate->language_id    = $item['language_id'];
                $itemUpdate->name           = $item['name'];
                $itemUpdate->meta_title     = $item['meta_title'];
                $itemUpdate->meta_description = $item['meta_description'];
                $itemUpdate->save();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

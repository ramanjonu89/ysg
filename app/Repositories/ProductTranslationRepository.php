<?php
namespace App\Repositories;

use App\Models\ProductTranslation;
use Exception;

class ProductTranslationRepository extends AbstractRepo
{
    use SortableTrait;

    const PER_PAGE = 16;

    public function getModel(): string
    {
        return ProductTranslation::class;
    }


    public function updateOrCreateMultiple(array $data)
    {
        try {
            foreach ($data as $item) {
                $itemUpdate = $this->model->where(['product_id' => $item['product_id'], 'language_id' => $item['language_id']])->first();
                if(!isset($itemUpdate)){
                    $itemUpdate = new $this->model;
                }
                $itemUpdate->product_id         = $item['product_id'];
                $itemUpdate->language_id        = $item['language_id'];
                $itemUpdate->name               = $item['name'];
                $itemUpdate->description        = $item['description'];
                $itemUpdate->meta_title         = $item['meta_title'];
                $itemUpdate->meta_description   = $item['meta_description'];
                $itemUpdate->save();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

<?php
namespace App\Repositories\CustomPage;

use App\Models\CustomPage\YsgPageTranslation;
use App\Repositories\AbstractRepo;
use Exception;

class YsgPageTranslationRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return YsgPageTranslation::class;
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    public function updateOrCreateMultiple(array $data)
    {
        try {
            foreach ($data as $item) {
                $itemUpdate = $this->model->where(['ysg_page_id' => $item['ysg_page_id'], 'language_id' => $item['language_id']])->first();
                if(!isset($itemUpdate)){
                    $itemUpdate = new $this->model;
                }
                $itemUpdate->ysg_page_id    = $item['ysg_page_id'];
                $itemUpdate->language_id    = $item['language_id'];
                $itemUpdate->title          = $item['title'];
                $itemUpdate->description    = $item['description'];
                $itemUpdate->content        = $item['content'];
                $itemUpdate->meta_title         = $item['meta_title'];
                $itemUpdate->meta_description   = $item['meta_description'];
                $itemUpdate->meta_keywords      = $item['meta_keywords'];
                $itemUpdate->save();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

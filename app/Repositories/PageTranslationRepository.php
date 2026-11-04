<?php
namespace App\Repositories;

use App\Models\PageTranslation;
use Exception;

class PageTranslationRepository extends AbstractRepo
{

    const PER_PAGE = 15;

    public function getModel(): string
    {
        return PageTranslation::class;
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }


    public function updateOrCreateMultiple(array $data)
    {
        try {
            foreach ($data as $item) {
                $itemUpdate = $this->model->where(['page_id' => $item['page_id'], 'language_id' => $item['language_id']])->first();
                if(!isset($itemUpdate)){
                    $itemUpdate = new $this->model;
                }
                $itemUpdate->page_id            = $item['page_id'];
                $itemUpdate->language_id        = $item['language_id'];
                $itemUpdate->title              = $item['title'];
                $itemUpdate->content            = $item['content'];
                $itemUpdate->meta_title         = $item['meta_title'];
                $itemUpdate->meta_description   = $item['meta_description'];
                $itemUpdate->keywords           = $item['keywords'];
                $itemUpdate->save();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}

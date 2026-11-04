<?php
namespace App\Repositories\CustomPage;

use App\Models\CustomPage\YsgBlockTranslation;
use App\Repositories\AbstractRepo;
use Exception;

class YsgBlockTranslationRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return YsgBlockTranslation::class;
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    public function updateOrCreateMultiple(array $data)
    {
        try {
            foreach ($data as $item) {
                $itemUpdate = $this->model->where(['ysg_block_id' => $item['ysg_block_id'], 'language_id' => $item['language_id']])->first();
                if(!isset($itemUpdate)){
                    $itemUpdate = new $this->model;
                }
                $itemUpdate->ysg_block_id   = $item['ysg_block_id'];
                $itemUpdate->language_id    = $item['language_id'];
                $itemUpdate->title          = $item['title'];
                $itemUpdate->description    = $item['description'];
                $itemUpdate->content        = $item['content'];
                $itemUpdate->save();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

<?php
namespace App\Services;

use App\Repositories\SubSubCategoryTranslationRepository;


class SubSubCategoryTranslationService extends AbstractService
{

    protected $repo;

    public function __construct(SubSubCategoryTranslationRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }

    public function formatDataInputs(object $request, $subSubCategoryId){
        $data = [];
        foreach($request->language_id as $key => $languageId){
            $item = [];
            $item['sub_sub_category_id']    = $subSubCategoryId;
            $item['language_id']            = $languageId;
            $item['name']                   = $request->name[$key] != null ? $request->name[$key] : $request->name[0];
            $item['meta_title']             = $request->meta_title[$key] != null ? $request->meta_title[$key] : $request->meta_title[0];
            $item['meta_description']       = $request->meta_description[$key] != null ? $request->meta_description[$key] : $request->meta_description[0];
            $item['created_at']             = date('Y-m-d H:i:s');
            $item['updated_at']             = date('Y-m-d H:i:s');
            
            array_push($data,$item);
        }
        return $data;
    }

    public function insert($data){
        return $this->repo->insert($data);
    }

    public function updateOrCreateMultiple($data){
        return $this->repo->updateOrCreateMultiple($data);
    }
}

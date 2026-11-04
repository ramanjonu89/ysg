<?php
namespace App\Services;

use App\Repositories\GeneralSettingTranslationRepository;

class GeneralSettingTranslationService extends AbstractService
{

    protected $repo;

    public function __construct(GeneralSettingTranslationRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }

    public function formatDataInputs(object $request, $id){
        $data = [];

        foreach($request->language_id as $key => $languageId){
            $item = [];
            $item['general_setting_id']     = $id;
            $item['language_id']            = $languageId;
            $item['site_name']              = $request->name[$key] ?? $request->name[0] ?? '';
            $item['annoucement']            = $request->annoucement[$key] ?? $request->annoucement[0] ?? '';
            $item['description']            = $request->description[$key] ?? $request->description[0] ?? '';
            $item['created_at']             = date('Y-m-d H:i:s');
            $item['updated_at']             = date('Y-m-d H:i:s');

            array_push($data,$item);
        }
        return $data;
    }

    public function updateOrCreateMultiple($data){
        return $this->repo->updateOrCreateMultiple($data);
    }
}

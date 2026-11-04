<?php
namespace App\Services\CustomPage;

use App\Repositories\CustomPage\YsgBlockContentTranslationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Services\AbstractService;

class YsgBlockContentTranslationService extends AbstractService
{

    protected $repo;

    public function __construct(YsgBlockContentTranslationRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }

    public function formatDataInputs(object $request, $ysgBlockContentId){
        $data = [];
        foreach($request->language_id as $key => $languageId){
            $item = [];
            $item['ysg_block_content_id']       = $ysgBlockContentId;
            $item['language_id']        = $languageId;
            $item['title']              = isset($request->title[$key]) ? $request->title[$key] : (isset($request->title[0]) ? $request->title[0] : '' );
            $item['description']        = isset($request->description[$key]) ? $request->description[$key] : (isset($request->description[0])  ? $request->description[0] : '');
            $item['content']            = isset($request->content[$key]) ? $request->content[$key] : (isset($request->content[0]) ? $request->content[0] : '');
            $item['created_at']         = date('Y-m-d H:i:s');
            $item['updated_at']         = date('Y-m-d H:i:s');

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

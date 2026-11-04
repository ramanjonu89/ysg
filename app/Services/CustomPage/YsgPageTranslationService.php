<?php
namespace App\Services\CustomPage;

use App\Repositories\CustomPage\YsgPageTranslationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Services\AbstractService;

class YsgPageTranslationService extends AbstractService
{

    protected $repo;

    public function __construct(YsgPageTranslationRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }

    public function formatDataInputs(object $request, $ysgPageId){
        $data = [];
        foreach($request->language_id as $key => $languageId){
            $item = [];
            $item['ysg_page_id']        = $ysgPageId;
            $item['language_id']        = $languageId;
            $item['title']              = isset($request->title[$key]) ? $request->title[$key] :( isset($request->title[0]) ?  $request->title[0] : '') ;
            $item['description']        = isset($request->description[$key]) ? $request->description[$key] : ( isset($request->description[0]) ?  $request->description[0] : '');
            $item['content']            = isset($request->content[$key]) ? $request->content[$key] : (isset($request->content[0]) ? $request->content[0] : '');
            $item['meta_title']         = isset($request->meta_title[$key]) ? $request->meta_title[$key] : (isset($request->meta_title[0]) ? $request->meta_title[0] : '');
            $item['meta_description']   = isset($request->meta_description[$key]) ? $request->meta_description[$key] : (isset($request->meta_description[0]) ? $request->meta_description[0] : '');
            $item['meta_keywords']      = isset($request->meta_keywords[$key]) ? $request->meta_keywords[$key] : (isset($request->meta_keywords[0]) ? $request->meta_keywords[0] : '');
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

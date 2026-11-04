<?php
namespace App\Services;

use App\Repositories\PageTranslationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class PageTranslationService extends AbstractService
{

    protected $repo;

    public function __construct(PageTranslationRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }


    public function formatDataInputs(object $request, $pageId){
        $data = [];
        foreach($request->language_id as $key => $languageId){
            $item = [];
            $item['page_id']            = $pageId;
            $item['language_id']        = $languageId;
            $item['title']              = $request->title[$key] != null ? $request->title[$key] : $request->title[0];
            $item['content']            = $request->content[$key] != null ? $request->content[$key] : $request->content[0];
            $item['meta_title']         = $request->meta_title[$key] != null ? $request->meta_title[$key] : $request->meta_title[0];
            $item['meta_description']   = $request->meta_description[$key] != null ? $request->meta_description[$key] : $request->meta_description[0];
            $item['keywords']           = $request->keywords[$key] != null ? $request->keywords[$key] : $request->keywords[0];
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

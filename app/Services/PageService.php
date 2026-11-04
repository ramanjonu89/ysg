<?php
namespace App\Services;

use App\Repositories\PageRepository;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class PageService extends AbstractService
{

    protected $repo;

    public function __construct(PageRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }


    public function formatDataInputs(object $request){
        $inputs['title']             = @$request->title[0];
        $inputs['content']           = @$request->content[0];
        $inputs['meta_title']        = @$request->meta_title[0];
        $inputs['meta_description']  = @$request->meta_description[0];
        $inputs['keywords']          = @$request->keywords[0];
        $inputs['slug']              =  preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));

        if ($request->hasFile('meta_image')) {
            $inputs['meta_image'] = $request->meta_image->store('uploads/custom-pages');;
        }

        return $inputs;
    }
}

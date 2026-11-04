<?php
namespace App\Services;

use App\Repositories\GeneralSettingRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class GeneralSettingService extends AbstractService
{

    protected $repo;

    public function __construct(GeneralSettingRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }
    public function formatDataInputs(object $request, $generalsetting){
        $inputs['site_name']        = @$request->name[0];
        $inputs['annoucement']      = @$request->annoucement[0];
        $inputs['description']      = @$request->description[0];
        $inputs['address']          = $request->address;
        $inputs['phone']            = $request->phone;
        $inputs['email']            = $request->email;
        $inputs['facebook']         = $request->facebook;
        $inputs['instagram']        = $request->instagram;
        $inputs['twitter']          = $request->twitter;
        $inputs['youtube']          = $request->youtube;
        $inputs['google_plus']      = $request->google_plus;
        $inputs['telegram']         = $request->telegram;
        $inputs['show_css_banner']  = isset($request->show_css_banner) && $request->show_css_banner == 'on' ? true : false;

        if ($request->hasFile('css_banner')) {
            // delete old file
            if (File::exists($generalsetting->css_banner)) {
                File::delete($generalsetting->css_banner);
            }
            $inputs['css_banner']    = $request->file('css_banner')->store('uploads');
        }
        $inputs['css_url']      = $request->css_url;

        return $inputs;
    }
}

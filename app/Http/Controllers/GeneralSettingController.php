<?php

namespace App\Http\Controllers;

use App\Services\SellerCenterSettingService;
use App\Models\GeneralSetting;
use App\Services\GeneralSettingService;
use App\Services\GeneralSettingTranslationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ImageOptimizer;

class GeneralSettingController extends Controller
{
    /**
     * @param sellerCenterSettingService
     */
    protected $sellerCenterSettingService;
    
    /**
     * @param GeneralSettingService
     */
    
    protected $generalSettingService;

    /**
     * @param GeneralSettingTranslationService
     */
    protected $generalSettingTranslationService;

    public function __construct(
        GeneralSettingService $generalSettingService,
        GeneralSettingTranslationService $generalSettingTranslationService,
        SellerCenterSettingService $sellerCenterSettingService
    )
    {
        $this->generalSettingService            = $generalSettingService;
        $this->generalSettingTranslationService = $generalSettingTranslationService;
        $this->sellerCenterSettingService = $sellerCenterSettingService;
    }
    public function index()
    {
        $generalsetting = $this->generalSettingService->getFirst();
        $sellerCenterMasterPassword = $this->sellerCenterSettingService->get('masterPassword');

        return view("general_settings.index", compact(
            "generalsetting",
            'sellerCenterMasterPassword'
        ));
    }

    public function logo()
    {
        $generalsetting = $this->generalSettingService->getFirst();
        return view("general_settings.logo", compact("generalsetting"));
    }

    //updates the logo and favicons of the system
    public function storeLogo(Request $request)
    {
        $generalsetting = GeneralSetting::first();
        if ($request->hasFile('logo')) {
            $generalsetting->logo = $request->file('logo')->store('uploads/logo');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->logo);
        }
        if ($request->hasFile('footer_logo')) {
            $generalsetting->footer_logo = $request->file('footer_logo')->store('uploads/logo');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->logo);
        }
        if ($request->hasFile('admin_logo')) {
            $generalsetting->admin_logo = $request->file('admin_logo')->store('uploads/admin_logo');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->admin_logo);
        }
        if ($request->hasFile('favicon')) {
            $generalsetting->favicon = $request->file('favicon')->store('uploads/favicon');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->favicon);
        }
        if ($request->hasFile('admin_login_background')) {
            $generalsetting->admin_login_background = $request->file('admin_login_background')->store('uploads/admin_login_background');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->admin_login_background);
        }
        if ($request->hasFile('admin_login_sidebar')) {
            $generalsetting->admin_login_sidebar = $request->file('admin_login_sidebar')->store('uploads/admin_login_sidebar');
            //ImageOptimizer::optimize(base_path('public/').$generalsetting->admin_login_sidebar);
        }
        if ($generalsetting->save()) {
            flash(__('Logo settings has been updated successfully'))->success();

            return redirect()->route('generalsettings.logo');
        } else {
            flash(__('Something went wrong'))->error();

            return back();
        }
    }

    public function color()
    {
        $defaultColors = [
            '#e62e04', '#1abc9c', '#3498db', '#72bf40', '#F79F1F', '#12CBC4', '#4F136D', '#ED4C67'
        ];

        $generalSetting = \App\Models\GeneralSetting::first();

        return view("general_settings.color", compact("generalSetting", 'defaultColors'));
    }

    //updates system ui color
    public function storeColor(Request $request)
    {
        $generalsetting = GeneralSetting::first();
        $generalsetting->frontend_color = $request->frontend_color;
        $generalsetting->frontend_color_hover = $request->frontend_color_hover;
        $generalsetting->frontend_secondary_color = $request->frontend_secondary_color;
        $generalsetting->color_menu_bar_area = $request->color_menu_bar_area;
        $generalsetting->color_menu_bar_main = $request->color_menu_bar_main;
        $generalsetting->color_menu_bar_text = $request->color_menu_bar_text;
        if ($generalsetting->save()) {
            flash(__('Color settings has been updated successfully'))->success();

            return redirect()->route('generalsettings.color');
        } else {
            flash(__('Something went wrong'))->error();

            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $generalSetting = $this->generalSettingService->get($id);
            if (!$generalSetting) return abort(404);

            $inputs = $this->generalSettingService->formatDataInputs($request, $generalSetting);
            $result = $this->generalSettingService->update($id, $inputs);
            $this->sellerCenterSettingService->set('masterPassword', $request->seller_center_master_password);

            if ($result) {
                $inputsGeneralSettings  = $this->generalSettingTranslationService->formatDataInputs($request, $id);
                $this->generalSettingTranslationService->updateOrCreateMultiple($inputsGeneralSettings);

                DB::commit();
                flash(__('GeneralSetting has been updated successfully'))->success();
                return redirect()->route('generalsettings.index');
            } else {
                flash(__('Something went wrong'))->error();
                return back();
            }
        } catch (Exception $e) {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

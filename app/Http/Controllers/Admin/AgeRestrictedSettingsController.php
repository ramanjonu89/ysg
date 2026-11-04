<?php

namespace App\Http\Controllers\Admin;

use App\Models\AgeRestrictedSetting;
use App\Services\{
    AgeRestrictedSettingService,
    CategoryService,
    ProductService,
    ShopService
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AgeRestrictedSettingsController extends Controller
{
    protected $service;
    protected $categoryService;
    protected $productService;
    protected $shopService;

    public function __construct(
        AgeRestrictedSettingService $service,
        CategoryService $categoryService,
        ProductService $productService,
        ShopService $shopService
    ) {
        $this->service = $service;
        $this->categoryService = $categoryService;
        $this->productService = $productService;
        $this->shopService = $shopService;
    }
    public function index() {
        $content_eighteen = $this->service->getFirstWithConditions(['age' => AgeRestrictedSetting::EIGHTEEN_AND_ABOVE]);
        $content_twentyone = $this->service->getFirstWithConditions(['age' => AgeRestrictedSetting::TWENTYONE_AND_ABOVE]);

        return view('admin.age_restricted_setting.add_content', compact('content_eighteen', 'content_twentyone'));
    }
    
    public function addContent(Request $request) {
        try {
            $data['age'] = $request->age;
            $data['top_content'] = $request->top_content;
            $data['body_content'] = $request->body_content;
            $content = $this->service->getFirstWithConditions(['age' => $request->age]);
            if (isset($content)) {
                $this->service->update($content->id, $data);
            } else {
                $this->service->create($data);
            }
            flash(__('Content has been inserted successfully'))->success();
            return redirect()->route('age_restricted.index');
        } catch (\Throwable $th) {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function getContent(Request $request) {
        $content = $this->service->getFirstWithConditions(['age' => $request->value]);
        return array(
            'top_content'  => $content->top_content,
            'body_content' => $content->body_content
        );
    }

    public function getFormConfirmAgeRestricted(Request $request) {
        $slug = $request->get('slug');
        $shop = $request->get('shop');
        $content = $this->getContent($request);
        $isConfirmAge = session()->get('is_confirm_age');

        if (isset($isConfirmAge)) {
            if (isset($shop)) {
                return redirect()->route('shop.visit', $slug);
            }
            return redirect()->route('product', $slug);
        }

        return view('frontend.form_confirm_age_restricted', compact('content', 'slug', 'shop'));
    }

    public function confirmAgeRestricted(Request $request) {
        $slug = $request->get('slug');
        $shop = $request->get('shop');

        Session::put(['is_confirm_age' => AgeRestrictedSetting::TWENTYONE_AND_ABOVE]);

        if(isset($shop)) {
            return redirect()->route('shop.visit', $slug);
        }

        return redirect()->route('product', $slug);
    }
}

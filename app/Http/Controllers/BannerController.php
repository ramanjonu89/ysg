<?php
namespace App\Http\Controllers;

use App\Models\Banner;
use App\Services\BannerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
     /**
     * @param BannerService
     */
    protected $bannerService;

    public function __construct(BannerService $bannerService) {
        $this->bannerService = $bannerService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banners = Banner::all();

        return view('banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($position)
    {
        $templates = Banner::getTemplateList();
        return view('banners.create', compact('position', 'templates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$this->validateUpdateRequest($request)) {
            if ($request->position == Banner::BANNER_POSITION_1) {
                return redirect()->to(route('frontend_settings.home.index') . '#home-banner-1')->withInput();
            }
            return redirect()->to(route('frontend_settings.home.index') . '#home-banner-2')->withInput();
        }

        if ($request->hasFile('photo')) {
            $data = $this->formatDataInputs($request);
            $banner = $this->bannerService->create($data);

            flash(__('Banner has been inserted successfully'))->success();
            if ($banner->position == Banner::BANNER_POSITION_1) {
                return redirect()->to(route('admin.custom_page.block.index') . '#home-banner-1');
            }

            return redirect()->to(route('frontend_settings.home.index') . '#home-banner-2');
        }

        flash(__('Something went wrong'))->error();
        if ($request->position == Banner::BANNER_POSITION_1) {
            return redirect()->to(route('admin.custom_page.block.index') . '#home-banner-1');
        }
        return redirect()->to(route('frontend_settings.home.index') . '#home-banner-2');

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
        $banner = Banner::findOrFail($id);
        $templates = Banner::getTemplateList();
        $daterange = '';
        if (!empty($banner->start_date) && !empty($banner->end_date)) {
            $daterange = date('d/m/Y H:i', $banner->start_date).' - '.date('d/m/Y H:i', $banner->end_date);
        }

        return view('banners.edit', compact('banner', 'templates', 'daterange'));
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
        try {
            $banner = Banner::find($id);
            if (!$this->validateUpdateRequest($request)) {
                if ($request->position == 1) {
                    return redirect()->to(route('frontend_settings.home.index') . '#home-banner-1')->withInput();
                }
                return redirect()->to(route('frontend_settings.home.index') . '#home-banner-2')->withInput();
            }

            $data = $this->formatDataInputs($request, $banner);
            $banner = $this->bannerService->update($banner->id, $data);

            flash(__('Banner has been updated successfully'))->success();
            if ($banner->position == Banner::BANNER_POSITION_1) {
                return redirect()->to(route('frontend_settings.home.index') . '#home-banner-1');
            }

            return redirect()->to(route('frontend_settings.home.index') . '#home-banner-2');
        } catch (\Throwable $th) {
            flash(__('Something went wrong'))->error();
            if ($request->position == Banner::BANNER_POSITION_1) {
                return redirect()->to(route('frontend_settings.home.index') . '#home-banner-1');
            }

            return redirect()->to(route('frontend_settings.home.index') . '#home-banner-2');
        }
    }

    public function update_status(Request $request)
    {
        $banner = Banner::find($request->id);
        $banner->published = $request->status;
        if ($request->status == 1) {
            if (count(Banner::where('published', 1)->where('position', $banner->position)->get()) < 4) {
                if ($banner->save()) {
                    return '1';
                } else {
                    return '0';
                }
            }
        } else {
            if ($banner->save()) {
                return '1';
            } else {
                return '0';
            }
        }

        return '0';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        if (Banner::destroy($id)) {
            flash(__('Banner has been deleted successfully'))->success();
        } else {
            flash(__('Something went wrong'))->error();
        }

        return redirect()->route('frontend_settings.home.index');
    }

    private function validateUpdateRequest(Request $request): bool
    {
        $regex = "/^https?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/";

        $rules = [
            'url' => 'nullable|regex:'.$regex
        ];

        $messages = [
            'url.regex' => 'Error Invalid url'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            flash($validator->errors()->first())->error();
            return false;
        }

        return true;
    }

    private function formatDataInputs($request, $banner = null)
    {
        if ($request->date_timer_type === Banner::START_DATE_ONLY) {
            $start = $request->start_date;
        } else if ($request->date_timer_type === Banner::END_DATE_ONLY) {
            $end = $request->end_date;
        } else if(isset($request->daterange)) {
            list($start, $end) = explode(' - ', $request->daterange);
        }

        $photo = $request->hasFile('photo') 
                    ? $request->photo->store('uploads/banners') 
                    : (isset($banner) ? $request->previous_photo : $request->photo->store('uploads/banners'));
        $data = [
            'url'           => $request->url ?? null,
            'position'      => $request->position ?? null,
            'template'      => $request->template ?? null,
            'photo'         => $photo ?? null,
            'published'     => $request->published ?? 0,
            'start_date'    => isset($start) ? Carbon::createFromFormat('d/m/Y H:i:s', $start)->timestamp : 0,
            'end_date'      => isset($end)   ? Carbon::createFromFormat('d/m/Y H:i:s', $end)->timestamp : 0
        ];

        return $data;
    }

}


<?php
/** Deployed by BeeBee 15 Nov 2020**/
/** Edit slider **/
namespace App\Http\Controllers;

use App\Models\Slider;
use App\Services\SliderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
     /**
     * @param SliderService
     */
    protected $sliderService;

    public function __construct(SliderService $sliderService) {
        $this->sliderService = $sliderService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = Slider::all();
        return view('sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sliders.create');
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
            return redirect()->route('frontend_settings.home.index')->withInput();
        }

        if ($request->hasFile('photos')) {
            foreach ($request->photos as $key => $photo) {
                $photo = $photo->store('uploads/sliders');
                $data = $this->formatDataInputs($request, null, $photo);

                $slider = $this->sliderService->create($data);
            }
            flash(__('Slider has been inserted successfully'))->success();
        }

        return redirect()->route('frontend_settings.home.index');
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
        $slider = Slider::findOrFail($id);
        $daterange = '';
        if (!empty($slider->start_date) && !empty($slider->end_date)) {
            $daterange = date('d/m/Y H:i', $slider->start_date).' - '.date('d/m/Y H:i', $slider->end_date);
        }
        return view('sliders.edit', compact('slider', 'daterange'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request)
    {
        $slider = Slider::find($request->id);
        $slider->published = $request->status;
        if ($slider->save()) {
            return '1';
        } else {
            return '0';
        }
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::find($id);
        if (!$this->validateUpdateRequest($request)) {
            
            return redirect()->route('frontend_settings.home.index');
        }

        $data = $this->formatDataInputs($request, $slider, null);
        $slider = $this->sliderService->update($slider->id, $data);
        flash(__('Slider has been updated successfully'))->success();

        return redirect()->route('frontend_settings.home.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);
        if (Slider::destroy($id)) {
            //unlink($slider->photo);
            flash(__('Slider has been deleted successfully'))->success();
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

    private function formatDataInputs($request, $slider = null , $photo = null)
    {

        if ($request->date_timer_type === Slider::START_DATE_ONLY) {
            $start = $request->start_date;
        } else if ($request->date_timer_type === Slider::END_DATE_ONLY) {
            $end = $request->end_date;
        } else if(isset($request->daterange)) {
            list($start, $end) = explode(' - ', $request->daterange);
        }

        if (isset($slider)) {
            $photo = $request->hasFile('photo') 
                        ? $request->photo->store('uploads/sliders') 
                        : $request->previous_photo;
        }

        $data = [
            'link'          => $request->url ?? null,
            'photo'         => $photo ?? null,
            'published'     => $request->published ?? 0,
            'start_date'    => isset($start) ? Carbon::createFromFormat('d/m/Y H:i:s', $start)->timestamp : 0,
            'end_date'      => isset($end)   ? Carbon::createFromFormat('d/m/Y H:i:s', $end)->timestamp : 0
        ];

        return $data;
    }
}


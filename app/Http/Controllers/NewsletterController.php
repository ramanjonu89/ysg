<?php
namespace App\Http\Controllers;

use App\Models\NewSletter;
use App\Models\Subscriber;
use App\Rules\NewsletterDateRange;
use App\Services\NewSletterService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NewsletterController extends Controller
{
    protected $service;

    public function __construct(NewSletterService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        $newsletters = $this->service->getList();
        $users = User::all();
        $subscribers = Subscriber::orderBy('created_at', 'desc')->paginate(20);

        return view('newsletters.index', compact('newsletters', 'users', 'subscribers'));
    }

    public function store(Request $request)
    {
        try {
            $dates = $this->normalizeDateInput($request);
            $request->merge($dates);

            $request->validate([
                'daterange' => ['required', new NewsletterDateRange()],
                'description' => 'nullable',
                'photo'      => 'required'
            ]);

            $inputs = $this->formatDataInputs($request);
            $this->service->create($inputs);
            flash(__('Newsletter has been created successfully'))->success();

            return redirect()->route('newsletters.index');
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first();

            flash($message)->error();
            return redirect()
                ->back()
                ->withInput();
        }
    }

    public function create(Request $request)
    {
        $daterange = '';
        return view('newsletters.create', compact('daterange'));
    }

    public function edit(Request $request, $id)
    {
        $newsletter = $this->service->getFirstWithConditions(['id' => $id]);
        $daterange = '';
        if (!empty($newsletter->start_date) && !empty($newsletter->end_date)) {
            $daterange = date('d/m/Y H:i', $newsletter->start_date).' - '.date('d/m/Y H:i', $newsletter->end_date);
        }

        return view('newsletters.edit', compact('newsletter', 'daterange'));
    }

    public function update(Request $request , newsletter $newsletter)
    {
        try {
            $dates = $this->normalizeDateInput($request);
            $request->merge($dates);

            $request->validate([
                'daterange' => ['required', new NewsletterDateRange($newsletter->id)],
                'description' => 'nullable',
            ]);

            $inputs = $this->formatDataInputs($request, $newsletter);
            $this->service->update($newsletter->id,$inputs);
            flash(__('Newsletter settings has been updated successfully'))->success();

            return redirect()->route('newsletters.index');
        } catch (ValidationException  $e) {
            $message = collect($e->errors())->flatten()->first();

            flash($message)->error();
            return redirect()
                ->back()
                ->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $this->service->destroy($id);
            flash(__('Newsletter has been deleted successfully'))->success();
            return redirect()->route('newsletters.index');
        } catch (\Exception $e) {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $newsletter = $this->service->getFirstWithConditions(['id' => $request->id]);

            if ($newsletter->status == NewSletter::STATUS_INACTIVE) {
                if ($this->service->hasConflict($newsletter->start_date, $newsletter->end_date, $newsletter->id))
                {
                    return json_encode([
                        'status'  => 0,
                        'message' => 'Selected Date/Time Overlap with Existing Promotion Banner'
                    ]);
                }
            }

            $status = (boolean)$this->service->updateStatus($request->id);
            if ($status) {
                $response = [
                    'status'  => 1,
                    'message' => 'Published newsletter updated successfully!'
                ];
            } else {
                $response = [
                    'status'  => 0,
                    'message' => 'Something went wrong!'
                ];
            }
            return json_encode($response);
        } catch (\Exception $e) {
            return json_encode(['status' => 0, 'message' => 'Something went wrong!']);
        }
    }

    private function formatDataInputs($request, $newsletter = null)
    {
        $inputs = [];

        $inputs['start_date']   = $request->start_date ?? 0;
        $inputs['end_date']     = $request->end_date ?? 0;
        $inputs['url']          = $request->url;
        $inputs['status']       = $request->status ? 1 : 0;
        $inputs['show_subbox']  = $request->subbox;
        $inputs['description']  = $request->description;
        $inputs['photo']        = $request->hasFile('photo')
                                ? $request->file('photo')->store('newsletter/photo')
                                : (isset($newsletter) ? $request->previous_photo : $request->file('photo')->store('newsletter/photo'));

        return $inputs;
    }

    private function normalizeDateInput(Request $request): array
    {
        $start = null;
        $end   = null;

        if ($request->date_timer_type) {
            switch ($request->date_timer_type) {
                case NewSletter::START_DATE_ONLY:
                    $start = $request->start_date;
                    break;

                case NewSletter::END_DATE_ONLY:
                    $end = $request->end_date;
                    break;

                default:
                    if ($request->daterange) {
                        [$start, $end] = explode(' - ', $request->daterange);
                    }
                    break;
            }
        }

        return [
            'start_date' => $start
                ? Carbon::createFromFormat('d/m/Y H:i:s', trim($start))->timestamp
                : null,

            'end_date' => $end
                ? Carbon::createFromFormat('d/m/Y H:i:s', trim($end))->timestamp
                : null,
        ];
    }

}

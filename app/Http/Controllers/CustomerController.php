<?php
namespace App\Http\Controllers;

use App\Customer;
use App\Excel\CustomerExport;
use App\Mail\CustomerExportMail;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Services\UserOtpService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    /**
     * @param UserOtpService
     */
    protected $userOtpService;

    /**
     * @param UserService
     */
    protected $userService;

    public function __construct(
        UserOtpService $userOtpService,
        UserService $userService
    ) {
        $this->userOtpService = $userOtpService;
        $this->userService    = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_field = $request->get('sort_field');
        $sort_direction = $request->get('sort_direction', 'desc');
        $filters = $request->get('filters', []);

        $customers = Customer::query()
            ->with(['user', 'user.addresses'])
            ->leftJoin('users', 'customers.user_id', '=', 'users.id')
            ->select('customers.*');

        $customers->selectSub(
            DB::table('orders as o3')
                ->select('o3.payment_status')
                ->whereColumn('o3.user_id', 'users.id')
                ->orderBy('o3.created_at', 'desc')
                ->limit(1),
            'last_payment_status'
        );

        $customers->selectSub(
            DB::table('orders as o2')
                ->select('o2.created_at')
                ->whereColumn('o2.user_id', 'users.id')
                ->orderBy('o2.created_at', 'desc')
                ->limit(1),
            'last_order_date'
        );

        $customers->selectSub(
            DB::table('orders as o')
                ->selectRaw('COUNT(*)')
                ->whereColumn('o.user_id', 'users.id')
                ->where('o.payment_status', 'paid')
                ->whereIn('o.id', function ($q) {
                    $q->select('od.order_id')
                        ->from('order_details as od')
                        ->where('od.delivery_status', '<>', 'cancelled');
                }),
            'total_successful_orders'
        );

        if (!empty($filters)) {
            foreach ($filters as $index => $filter) {
                if (empty($filter['keyword'])) {
                    continue;
                }

                $column = $filter['column'] ?? 'name';
                $condition = $filter['condition'] ?? 'contains';
                $keyword = $filter['keyword'];

                if (in_array($column, ['name', 'email', 'phone'])) {
                    $customers->whereHas('user', function (Builder $query) use ($keyword, $column, $condition) {
                        if ($column == 'name') {
                            $this->applySearchCondition($query, 'users.name', $keyword, $condition);
                        } elseif ($column == 'email') {
                            $this->applySearchCondition($query, 'users.email', $keyword, $condition);
                        } elseif ($column == 'phone') {
                            $this->applySearchCondition($query, 'users.phone', $keyword, $condition);
                        }
                    });
                }

                if (in_array($column, ['delivery_address', 'delivery_contact'])) {
                    $customers->whereHas('user.addresses', function (Builder $query) use ($keyword, $column, $condition) {
                        if ($column == 'delivery_address') {
                            $this->applySearchCondition($query, 'addresses.address', $keyword, $condition);
                        } elseif ($column == 'delivery_contact') {
                            $this->applySearchCondition($query, 'addresses.phone', $keyword, $condition);
                        }
                    });
                }
            }
        }

        if ($sort_field) {
            switch ($sort_field) {
                case 'name':
                case 'email':
                case 'last_login':
                    $customers->orderBy('users.' . $sort_field, $sort_direction);
                    break;

                case 'created_at':
                    $customers->orderBy('customers.created_at', $sort_direction);
                    break;

                case 'last_order_date':
                case 'total_successful_orders':
                    $customers->orderBy($sort_field, $sort_direction);
                    break;

                default:
                    $customers->orderBy('customers.created_at', 'desc');
            }
        } else {
            $customers->orderBy('customers.created_at', 'desc');
        }

        $customers = $customers->paginate(20);

        return view('customers.index', compact('customers', 'sort_field', 'sort_direction', 'filters'));
    }

    private function applySearchCondition($query, $column, $value, $condition)
    {
        switch ($condition) {
            case 'contains':
                $query->where($column, 'like', '%' . $value . '%');
                break;
            case 'not_contains':
                $query->where($column, 'not like', '%' . $value . '%');
                break;
            case 'starts_with':
                $query->where($column, 'like', $value . '%');
                break;
            case 'ends_with':
                $query->where($column, 'like', '%' . $value);
                break;
            case 'equals':
                $query->where($column, '=', $value);
                break;
            case 'not_equals':
                $query->where($column, '!=', $value);
                break;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $user          = User::findOrFail(Customer::findOrFail($id)->user->id);
            $emailNameOld  = explode('@', $user->email);
            $newEmail      = reset($emailNameOld).'_deleted_'.time().'@'.end($emailNameOld);
            $this->userService->update($user->id, ['email' => $newEmail]);
            $this->userService->destroy($user->id);
            DB::commit();
            flash(__('Customer has been deleted successfully'))->success();
            return redirect()->route('customers.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            flash(__('Something went wrong'))->error();
            return back();
        }

    }

    public function login($id)
    {
        $customer = Customer::findOrFail(decrypt($id));
        $user = $customer->user;
        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

    public function ban($id)
    {
        $customer = Customer::findOrFail($id);
        if ($customer->user->banned == 1) {
            $customer->user->banned = 0;
        } else {
            $customer->user->banned = 1;
        }
        $customer->user->save();

        return back();
    }

    public function exportAll(Request $request)
    {
        try {
            $params['filters'] = $request->get('filters', []);

            $user = auth()->user();
            $email = optional($user->staff)->export_email_to;

            if (!$email) {
                flash(__('Please set email to export'))->error();
                return back();
            }

            $fileName = 'customer_export_' . date('Ymd_His') . '.xlsx';
            $storePath = 'exports/' . $fileName;

            Excel::store(
                new CustomerExport($params),
                $storePath
            );

            $filePath = public_path($storePath);

            $mailData = [
                'file' => $filePath,
                'file_name' => $fileName,
                'subject' => 'YSG Customer List Report',
            ];

            Mail::to($email)->queue(new CustomerExportMail($mailData));

            flash(__('Email has been sent successfully'))->success();
            return back();
        } catch (\Exception $e) {
            flash(__('Something went wrong'))->error();
            Log::error("export customer error: " . $e->getMessage());
            return back();
        }
    }
}


<?php

namespace App\Services;

use App\Exports\FulfilmentReport;
use App\Exports\SaleReport;
use App\Mail\FulfilmentReportManager;
use App\Mail\SaleReportManager;
use App\Models\EmailCorporateAccess;
use App\Models\UserTask;
use App\Repositories\UserRepository;
use App\SellerCorporateDomains;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UserService extends AbstractService
{
    public function __construct(UserRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Exception
     */
    public function updateByRequest($id, Request $request)
    {
        $user = $this->repo->find($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->company_id = $request->company_id;
        $user->enable_access_code = 0;
        $user->company_password = null;
        $user->message_content = null;
        $user->enable_corporate_domains = 0;

        $sellerCorporateDomainService = app()->make(SellerCorporateDomainService::class);
        $sellerCorporateEmailService = app()->make(SellerCorporateEmailService::class);
        if (!$request->has('enable_corporate_domains') || (int)$request->company_type === User::COMPANY_TYPE_FLAG_SHIP) {
            $sellerCorporateDomainService->deleteBySellerId($user->seller->id);
        }
        if ((int)$request->company_type === User::COMPANY_TYPE_PRIVATE_EVENT) {
            if ($request->has('enable_access_code')) {
                $user->enable_access_code = 1;
                $user->company_password = $request->company_password;
                $user->message_content = $request->message_content;
            }
            if ($request->has('enable_corporate_domains')) {
                $user->enable_corporate_domains = 1;
                if ($request->domains) {
                    $sellerCorporateDomainService->deleteBySellerId($user->seller->id);
                    $domains = array_unique(json_decode($request->domains, true));
                    foreach ($domains as $domain) {
                        if (strlen(trim($domain)) > 0) {
                            SellerCorporateDomains::firstOrCreate([
                                'seller_id' => $user->seller->id,
                                'domain' => trim($domain)
                            ]);
                        }
                    }
                }

                if ($request->emails) {
                    $sellerCorporateEmailService->deleteBySellerId($user->seller->id);
                    $emails = array_unique(json_decode($request->emails, true));
            
                    foreach ($emails as $email) {
                        $trimmedEmail = trim($email);
            
                        if (strlen($trimmedEmail) > 0) {
                            EmailCorporateAccess::firstOrCreate([
                                'seller_id' => $user->seller->id,
                                'email' => $trimmedEmail
                            ]);
                        }
                    }
                }
            }
        }
        $user->company_type = $request->company_type;
        $user->company_shipping = $request->company_shipping;
        $user->company_start_date = date("Y-m-d", strtotime($request->company_start_date));
        $user->company_end_date = date("Y-m-d", strtotime($request->company_end_date));
        $user->email_notification = $request->email_notification;
        $user->fulfilment_report_enabled = $request->fulfilment_report_enabled;
        $user->sale_report_enabled = $request->sale_report_enabled;
        $user->api_key = $request->api_key;

        $user->tasks()->delete();
        if ($request->sale_report_enabled) {
            $task = new UserTask();
            $task->type = User::SALE_REPORT_TYPE;
            $task->frequency = $request->sale_report_frequency;
            $task->options = $request->sale_frequency_options;
            $user->tasks()->save($task);
        }
        if ($request->fulfilment_report_enabled) {
            $task = new UserTask();
            $task->type = User::FULFILMENT_REPORT_TYPE;
            $task->frequency = $request->fulfilment_report_frequency;
            $task->options = $request->fulfilment_frequency_options;
            $user->tasks()->save($task);
        }
        if (strlen($request->password) > 0) {
            $user->password = Hash::make($request->password);
        }
        if ($request->hasFile('photo')) {
            $user->avatar_original = $request->photo->store('uploads');
        }
        if ($user->save()) {
            return $user;
        }

        throw new \Exception(__('There is an error when saving'));
    }

    public function getFlagShipUserIdsArray()
    {
        return $this->repo->getFlagShipUserIdsArray();
    }

    public function handleSchedule(UserTask $task)
    {
        echo 'running';
        $user = $task->user;
        $task->type === User::SALE_REPORT_TYPE ? $this->sendSaleReport($user, $task->frequency) : $this->sendFulfilmentReport($user, $task->frequency);
    }

    public function sendSaleReport(User $user, string $frequency)
    {
        $today = Carbon::now();
        if (UserTask::DAILY_FREQUENCY === $frequency) {
            $time = Carbon::now()->subDay()->toDateTimeString();
        }

        if (UserTask::WEEKLY_FREQUENCY === $frequency) {
            $time = Carbon::now()->subWeek()->toDateTimeString();
        }

        if (UserTask::MONTHLY_FREQUENCY === $frequency) {
            $time = Carbon::now()->subMonth()->toDateTimeString();
        }
        //$todaydate = "2020-12-11";
        $orderDetails = \App\Models\OrderDetail::where('payment_status', 'paid')->whereBetween('created_at', [$time, $today])->where('seller_id', $user->id)->get();

        if (count($orderDetails) > 0) {
            $todaydate = Carbon::today()->toDateString();
            $todayMonth = Carbon::today()->month;
            $todayYear = Carbon::today()->year;
            if (UserTask::DAILY_FREQUENCY === $frequency) {
                $ytddate = Carbon::yesterday()->toDateString();
            }

            if (UserTask::WEEKLY_FREQUENCY === $frequency) {
                $ytddate = Carbon::now()->subWeek()->toDateString();
            }

            if (UserTask::MONTHLY_FREQUENCY === $frequency) {
                $ytddate = Carbon::now()->subMonth()->toDateString();
            }
            /*hack date*/
            // $todaydate = "2022-06-27";
            // $ytddate = "2022-05-27";
            // $todayMonth = 6;
            $totalorder_1 = 0;
            $email_addresses = $user->email_notification;
            $user_id = $user->id;
            echo "today month:" . $todayMonth;
            $todayItems = \App\Models\OrderDetail::with('order')->where('seller_id', $user_id)->whereDate('created_at', $todaydate)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->get();
            $dailytotalsales = $this->calculateTotalSales($todayItems);

            $totalorders = DB::select(DB::raw("select count(id) as totalorders from orders where id in (SELECT order_id FROM `order_details` where seller_id = " . $user_id . " and payment_status = 'paid' and delivery_status != 'cancelled' and created_at like '" . $todaydate . "%' group by (order_id))"));
            foreach ($totalorders as $totalorder) {
                $totalorder_1 = $totalorder->totalorders;
            }
            $totalitemunitsold = \App\Models\OrderDetail::where('seller_id', $user_id)->whereDate('created_at', $todaydate)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('quantity');
            $monthtodateItems = \App\Models\OrderDetail::where('seller_id', $user_id)->whereYear('created_at', $todayYear)->whereMonth('created_at', $todayMonth)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->get();
            $monthtodatetotalordersItems = \App\Models\OrderDetail::where('seller_id', $user_id)->whereYear('created_at', $todayYear)->whereMonth('created_at', $todayMonth)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->groupBy('order_id')->get();
            $monthtodatetotalorders = count($monthtodatetotalordersItems);
            $monthtodatetotalsales = $this->calculateTotalSales($monthtodateItems);
            $monthtodateitemsold = \App\Models\OrderDetail::where('seller_id', $user_id)->whereYear('created_at', $todayYear)->whereMonth('created_at', $todayMonth)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('quantity');

            $dailytotalsalesytd = \App\Models\OrderDetail::where('seller_id', $user_id)->whereDate('created_at', $ytddate)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('price');
            $salesdiff = $dailytotalsales - $dailytotalsalesytd;
            //if ytd sales is more than 0 and today sales is more than 0
            if (($dailytotalsalesytd > 0) && ($dailytotalsales > 0)) {
                //kick in formula
                $salesstats = number_format((float)(($salesdiff) / $dailytotalsalesytd) * 100, 2, '.', '');
            } else if (($dailytotalsalesytd == 0) && ($dailytotalsales > 0)) {
                //if ytd sales is 0 and today sales is more than 0
                $salesstats = "100";
            } else if (($dailytotalsalesytd > 0) && ($dailytotalsales == 0)) {
                //if ytd sales is more than 0 and today sales is 0
                $salesstats = "-100";
            } else {
                $salesstats = "0";
            }
            if ($monthtodatetotalorders > 0) {
                $averge_order_value = number_format((float)$monthtodatetotalsales / $monthtodatetotalorders, 2, '.', '');
            } else {
                $averge_order_value = 'NIL';
            }
            //lets send some email//
            $array['averge_order_value'] = $averge_order_value;
            $array['salesstats'] = $salesstats;
            $array['dailytotalsalesytd'] = $dailytotalsalesytd;
            $array['monthtodatetotalorder_1'] = $monthtodatetotalorders;
            $array['totalorder_1'] = $totalorder_1;
            $array['monthtodateitemsold'] = $monthtodateitemsold;
            $array['monthtodatetotalsales'] = $monthtodatetotalsales;
            $array['totalitemunitsold'] = $totalitemunitsold;
            $array['dailytotalsales'] = $dailytotalsales;
            //lets do excel now
            Excel::store(new SaleReport($orderDetails), 'invoices/salereport' . $user_id . '.xlsx');
            $excelFile = public_path() . '/invoices/salereport' . $user_id . '.xlsx';
            $excelFile_name = 'sale_report_' . $user_id . '.xlsx';
            //end of excel
            $array['file'] = $excelFile;
            $array['file_name'] = $excelFile_name;
            $array['view'] = 'emails.sale_report';
            $array['subject'] = 'YSG Sale Report for ' . $user->shop->name . ' ' . Carbon::now()->format('d-m-Y');
            $array['from'] = env('MAIL_FROM_ADDRESS');
            if (env('MAIL_FROM_ADDRESS') != null) {
                try {
                    $email_address = explode(";", $email_addresses);
                    foreach ($email_address as $email_address_send) {
                        Mail::to(trim($email_address_send))->queue(new SaleReportManager($array));
                    }
                    // Mail::to('thinh1011@gmail.com')->queue(new SaleReportManager($array));
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                    echo $e;
                }
            }
        }
    }

    public function calculateTotalSales($items)
    {
        $total = 0;
        foreach($items as $item) {
            $total += $item->price;
        }

        return number_format($total, 2, '.', '');
    }

    public function sendFulfilmentReport(User $user, string $frequency)
    {
        $email_addresses = $user->email_notification;
        $user_id = $user->id;
        $today = Carbon::now();
        if (UserTask::DAILY_FREQUENCY === $frequency) {
            $time = $today->clone()->subDay();
        }

        if (UserTask::WEEKLY_FREQUENCY === $frequency) {
            $time = $today->clone()->subWeek();
        }

        if (UserTask::MONTHLY_FREQUENCY === $frequency) {
            $time = $today->clone()->subMonth();
        }

        $orderDetails = \App\Models\OrderDetail::where('payment_status', 'paid')->whereBetween('created_at', [$time, $today])->where('seller_id', $user_id)->get();

        if (count($orderDetails) > 0) {
            Excel::store(new FulfilmentReport($orderDetails), 'invoices/fulfilmentreport' . $user_id . '.xlsx');
            $excelFile = public_path() . '/invoices/fulfilmentreport' . $user_id . '.xlsx';
            $excelFile_name = 'fulfilment_report_' . $user_id . '.xlsx';
            //end of excel
            $array['file'] = $excelFile;
            $array['file_name'] = $excelFile_name;
            $array['view'] = 'emails.fulfilment_report';
            $array['subject'] = 'YSG Fulfilment Report for ' . $user->shop->name . ' ' . Carbon::now()->format('d-m-Y');
            $array['from'] = env('MAIL_FROM_ADDRESS');
            if (env('MAIL_FROM_ADDRESS') != null) {
                try {
                    $email_address = explode(";", $email_addresses);
                    foreach ($email_address as $email_address_send) {
                        Mail::to(trim($email_address_send))->queue(new FulfilmentReportManager($array));
                    }
                    // Mail::to('thinh1011@gmail.com')->queue(new FulfilmentReportManager($array));
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                    echo $e;
                }
            }
        }
        
    }

    public function getSellerFilter($validProducts)
    {
        return $this->repo->getSellerFilter($validProducts);
    }

    /**
     * Get users by IDs in the same order as provided in the array
     */
    public function getByIdsInOrder($ids)
    {
        return $this->repo->getByIdsInOrder($ids);
    }
    
}

<?php

namespace App\Console\Commands;

use App\Exports\DailyReport;
use Mail;
use Illuminate\Console\Command;
use App\Mail\DailyReportManager;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB;

class DailyReportCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ysg:dailyreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Report for User has email notification enabled';

    public function calculateTotalSales($items)
    {
        $total = 0;
        foreach($items as $item) {
            $total += $item->price;
            $order = $item->order;
            if (count($order->coupons)) {
                $coupon = $order->coupons->first();
                // check only coupon apply for seller and type is percentage
                if ($coupon->discount_type == 'percent' && $coupon->type = 'cart_base') {
                    $total -= $coupon->getDiscountAmount($item->price);
                }
            }
        }

        return number_format($total, 2, '.', '');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = DB::select(DB::raw("select *, u.id as uid, s.name as shopName from users u left join shops s on s.user_id = u.id where u.email_notification_enabled = 1 "));
        $i = 0;
        $current_deliverystatus = '';
        $statusdelivery = '';
        $orderid = '';
        // CHANGED: command run at 00:01 a.m to get data form yesterday, before command run at 23:59 to get data from today
        $todaydate = Carbon::yesterday()->toDateString();
        $todayMonth = Carbon::yesterday()->month;
        $todayYear = Carbon::yesterday()->year;
        $ytddate = Carbon::yesterday()->subDays(1)->toDateString();
        /*hack date*/
        // $todaydate = "2021-09-23";
        // $ytddate = "2021-09-22";
        $totalorder_1 = 0;
        $totalitemunitsold = 0;
        $monthtodatetotalsales = 0;
        $monthtodateitemsold = 0;
        $monthtodatetotalorder_1 = 0;
        $salesstats = 0;
        $averge_order_value = 0;
        foreach ($users as $user) {
            $dailytotalsales = 0;
            $email_addresses = $user->email_notification;
            $user_id = $user->uid;
            // if ($user_id !== 2077) {
            //     continue;
            // }
            echo "today month:" . $todayMonth;
            // $dailytotalsales = \App\Models\OrderDetail::where('seller_id', $user_id)->whereDate('created_at', $todaydate)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('price');
            $todayItems = \App\Models\OrderDetail::with('order')->where('seller_id', $user_id)->whereDate('created_at', $todaydate)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->get();
            $dailytotalsales = $this->calculateTotalSales($todayItems);
            

            $totalorders = DB::select(DB::raw("select count(id) as totalorders from orders where id in (SELECT order_id FROM `order_details` where seller_id = $user_id and payment_status = 'paid' and delivery_status != 'cancelled' and created_at like '$todaydate%' group by (order_id))"));
            foreach ($totalorders as $totalorder) {
                $totalorder_1 = $totalorder->totalorders;
            }
            $totalitemunitsold = \App\Models\OrderDetail::where('seller_id', $user_id)->whereDate('created_at', $todaydate)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('quantity');
            $monthtodateItems = \App\Models\OrderDetail::where('seller_id', $user_id)->whereYear('created_at', $todayYear)->whereMonth('created_at', $todayMonth)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->get();
            $monthtodatetotalordersItems = \App\Models\OrderDetail::where('seller_id', $user_id)->whereYear('created_at', $todayYear)->whereMonth('created_at', $todayMonth)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->groupBy('order_id')->get();
            $monthtodatetotalorders = count($monthtodatetotalordersItems);
            $monthtodatetotalsales = $this->calculateTotalSales($monthtodateItems);
            $monthtodateitemsold = \App\Models\OrderDetail::where('seller_id', $user_id)->whereYear('created_at', $todayYear)->whereMonth('created_at', $todayMonth)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('quantity');
            // $monthtodatetotalorders = DB::select(DB::raw("select count(id) as totalorders from orders where id in (SELECT order_id FROM `order_details` where seller_id = $user_id and payment_status = 'paid' and delivery_status != 'cancelled' and  created_at like '$todayMonth%' group by (order_id))"));
            
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
            $type = 'csv';
            Excel::store(new DailyReport($user_id), 'invoices/dailyreport' . $user_id . '.xlsx');
            $excelFile = public_path() . '/invoices/dailyreport' . $user_id . '.xlsx';
            $excelFile_name = 'dailyreport' . $user_id . '.xlsx';
            //end of excel
            $array['file'] = $excelFile;
            $array['file_name'] = $excelFile_name;
            $array['view'] = 'emails.daily_report';
            $array['subject'] = 'YSG Daily Summary Report for ' . $user->shopName . ' ' . Carbon::yesterday()->format('d-m-Y');
            $array['from'] = env('MAIL_FROM_ADDRESS');
            if (env('MAIL_FROM_ADDRESS') != null) {
                try {
                    echo "hello";
                    $email_address = explode(";", $email_addresses);
                    foreach ($email_address as $email_address_send) {
                        Mail::to(trim($email_address_send))->queue(new DailyReportManager($array));
                    }
                    // Mail::to('thinh1011@gmail.com')->queue(new DailyReportManager($array));
                    //Mail::to('shizku@gmail.com')->queue(new DailyReportManager($array));
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                    echo $e;
                }
            }
            echo "dailytotalsales";
            var_dump($dailytotalsales);
            echo "<br>";
            echo "monthtodatetotalsales";
            var_dump($monthtodatetotalsales);
            echo "monthtodatetotalorders";
            var_dump($monthtodatetotalorders);
            //
            /*
                'averge_order_value' => $this->array['averge_order_value'],

                'monthtodateitemsold' => $this->array['monthtodateitemsold'],

                'monthtodatetotalorder' => $this->array['monthtodatetotalorder'],

                'monthtodatetotalsales' => $this->array['monthtodatetotalsales'],

                'totalitemunitsold' => $this->array['totalitemunitsold'],
                'totalorders' => $this->array['totalorders'],
                'salesstats' => $this->array['salesstats'],
                'dailytotalsales' => $this->array['dailytotalsales'],
            */
        }
    }
}

<?php
namespace App\Services;

use App\Repositories\ReportRepository;
use App\Repositories\SplitOrderRepository;
use Auth;
use Carbon\Carbon;

class ReportService extends AbstractService
{
    protected $repo;
    protected $splitOrdersRepo;

    public function __construct(
        ReportRepository $repo,
        SplitOrderRepository $splitOrdersRepo
    ) {
        parent::__construct($repo);
        $this->repo = $repo;
        $this->splitOrdersRepo = $splitOrdersRepo;
    }

    public function getSummaryReport()
    {
        $user = Auth::user();
        return $this->repo->getSummaryReport($user);
    }

    public function getBusinessReport()
    {
        $user = Auth::user();
        return $this->repo->getBusinessReport($user);
    }

    public function getOrdersChartData(array $params): array
    {
        $fromDate = Carbon::createFromFormat('Y-m-d', $params['fromDate'])->startOfDay();
        $toDate = Carbon::createFromFormat('Y-m-d', $params['toDate'])->endOfDay();

        $chartData = [];
        $user = Auth::user();
        $data = $this->repo->getOrdersChartData($user, $fromDate, $toDate)->keyBy('date')->toArray();

        $toDate->startOfDay();
        while ($fromDate <= $toDate) {
            $date = $fromDate->toDateString();
            if (isset($data[$date])) {
                $chartData[$date] = $data[$date]->total;
            } else {
                $chartData[$date] = 0;
            }
            $fromDate->addDay();
        }

        return $chartData;
    }

    public function getListCouponsReport($request)
    {
        $from      = isset($request->start_date) ? $request->start_date : null;
        $to        = isset($request->end_date)   ? $request->end_date   : null;
        $sellerId  = isset($request->shop_name)  ? $request->shop_name  : null;

        $from = date('Y-m-d', strtotime($from));
        $to   = date('Y-m-d', strtotime('+1 day', strtotime($to)));

        return $this->repo->getListCouponsReport($from, $to, $sellerId);
        
    }
}
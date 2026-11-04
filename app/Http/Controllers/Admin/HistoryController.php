<?php
namespace App\Http\Controllers\Admin;

use App\Services\HistoryService;
use Illuminate\Http\Request;
use App\Excel\HistoriesExport;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    protected $service;
    public function __construct(HistoryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $histories = $this->service->getList();

        return view('admin.histories.index', compact('histories'));
    }

    public function exportExcel()
    {
        return Excel::download(new HistoriesExport, 'history_changes_report.xlsx');
    }
}
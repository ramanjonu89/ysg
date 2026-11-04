<?php

namespace App\Http\Controllers\Admin\Ajax;

use App\Models\History;
use App\Services\HistoryService;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller;

class HistoryController extends Controller
{
  protected $service;
  public function __construct(HistoryService $service)
  {
    $this->service = $service;
  }

  public function get(History $history)
  {
    return [
      'title' => __('Changes detail: ') . "$history->name ($history->historiable_id)",
      'body' => view('admin.ajax.histories.get', compact('history'))->render()
    ];
  }
}

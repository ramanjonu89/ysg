<?php

namespace App\Excel;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Services\HistoryService;

class HistoriesExport implements FromCollection, WithMapping, WithHeadings
{
  const MAX_RECORD = 3000;

  public function collection()
  {
    $service = app()->make(HistoryService::class);
    return $service->getListForExport([
      'perPage' => self::MAX_RECORD
    ]);
  }

  public function headings(): array
  {
    return [
      'User',
      'Seller',
      'Action',
      'Object',
      'Object ID',
      'Name',
      'Fields change',
      'Date change',
    ];
  }

  /**
   * @var History $entity
   */
  public function map($entity): array
  {
    return [
      $entity->user_name,
      $entity->user_type == 'seller' ? $entity->user_name : 'admin',
      $entity->action,
      $entity->module_name,
      $entity->historiable_id,
      $entity->name,
      getHistoryUpdatedFields($entity->new_value),
      $entity->created_at,
    ];
  }
}

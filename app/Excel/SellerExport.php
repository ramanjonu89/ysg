<?php

namespace App\Excel;

use App\Services\SellerService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class SellerExport implements FromCollection, WithMapping, WithHeadings
{
    const MAX_RECORD = 3000;
    protected $params;

    public function __construct($params)
    {
        $this->params = $params; 
    }

    public function collection()
    {
        return $this->prepareCollections($this->params);
    }

    private function prepareCollections($params) {

        $results = $this->buildQuery($params);

        return $results;
    }

    public function buildQuery($params)
    {
        $service = app()->make(SellerService::class);
        $query = $service->getListForExport([
            'perPage' => self::MAX_RECORD,
            'keyword' => $params['keyword'],
            'verification_status' => $params['verification_status'],
        ]);

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Phone',
            'Email',
            'Approval',
            'Display',
            'Created at',
            'Num. of Products',
            'Total sale (Excl. Shipping)',
        ];
    }

    /**
     * @var $entity
     */
    public function map($entity): array
    {
        return [
            $entity->id,
            $entity->name,
            $entity->phone,
            $entity->email,
            $this->getApproval($entity->verify),
            $this->getDisplay($entity->display),
            $entity->created_at,
            number_format($entity->numOfProducts ?? 0),
            single_price($entity->total_sales),
        ];
    }

    private function getApproval($verify) {
        if ($verify === 1) {
            return 'Approval';
        } else {
            return 'Disapproval';
        }
    }

    private function getDisplay($display) {
        if ($display === 1) {
            return 'Display';
        } else {
            return 'Hide';
        }
    }
}

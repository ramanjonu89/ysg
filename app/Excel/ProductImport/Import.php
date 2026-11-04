<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 28/04/2022
 * Time: 01:41
 */

namespace App\Excel\ProductImport;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class Import
 * @package App\Services\ProductImport
 * @property
 */
class Import implements WithHeadingRow, WithMapping
{

    /**
     * @var $entity
     */
    public function map($row): array
    {
        foreach ($row as $key => $value) {
            if ($key == 'product_sku') {
                $row[$key] = trim((string)$value);
            } else if (is_string($value)) {
                $row[$key] = trim($value);
            }
        }
        return $row;
    }
}
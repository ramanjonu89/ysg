<?php

namespace App\Models\Imports;

use App\Models\FlashDealProduct as Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FlashDealProduct implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Model([
            'id' => $row['id'],
            'name' => $row['name'],
            'discount' => $row['discount'],
            'discount_type' => $row['discount_type'],
        ]);
    }


    public function headingRow(): int
    {
        return 1;
    }
}
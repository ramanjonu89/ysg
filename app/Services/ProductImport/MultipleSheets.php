<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 28/04/2022
 * Time: 01:41
 */

namespace App\Services\ProductImport;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Class MultipleSheets
 * @package App\Services\ProductImport
 * @property
 */
class MultipleSheets implements WithMultipleSheets
{
    protected $sheetHandlers;

    public function __construct(
        array $sheetHandlers
    ) {
        $this->sheetHandlers = $sheetHandlers;
    }

    public function sheets(): array
    {
        return $this->sheetHandlers;
    }
}
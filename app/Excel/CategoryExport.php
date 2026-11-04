<?php

namespace App\Excel;

use App\Models\Product;
use App\Models\ProductStock;
use App\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class CategoryExport extends DefaultValueBinder implements FromCollection, WithMapping, WithHeadings
{
    public function __construct(
    ) {
    }


    public function collection()
    {
        return $this->prepareCollections();
    }

    private function prepareCollections() {

        $query = $this->buildQuery();

//        $collections = $query->get();
//
//        dd($collections);

        return $query->get();
    }



    public function buildQuery()
    {
        $query = DB::table('categories')

            ->select(
                'categories.id as cat_id',
                'categories.name as cat_name',
                'categories.featured as cat_featured',
                'categories.status as cat_status',

                'sub_categories.id as subcat_id',
                'sub_categories.name as subcat_name',
                'sub_categories.status as subcat_status',

                'sub_sub_categories.id as subsubcat_id',
                'sub_sub_categories.name as subsubcat_name',
                'sub_sub_categories.status as subsubcat_status',
            )

            ->leftJoin('sub_categories', 'categories.id', '=', 'sub_categories.category_id')
            ->leftJoin('sub_sub_categories', 'sub_categories.id', '=', 'sub_sub_categories.sub_category_id')
            ->orderBy('cat_id')
        ;


        return $query;
    }


    public function headings(): array
    {
        return [
            'ID',
            'Category',
            'Category Featured',
            'Category Status',
            'Sub Category ID',
            'Sub Category',
            'Sub Category Status',
            'Sub Sub Category ID',
            'Sub Sub Category',
            'Sub Sub Category Status',
        ];
    }

    /**
     * @var $entity
     */
    public function map($entity): array
    {
        return [
            $entity->cat_id,
            $entity->cat_name,
            $this->getFeatured($entity->cat_featured),
            $this->getStatus($entity->cat_status),
            $entity->subcat_id??'---',
            $entity->subcat_name??'---',
            $this->getStatus($entity->subcat_status),
            $entity->subsubcat_id??'---',
            $entity->subsubcat_name??'---',
            $this->getStatus($entity->subsubcat_status),
        ];
    }

    private function getFeatured($featured) {
        return $featured ? 'Yes' : 'No';
    }

    private function getStatus($status) {
        if ($status === 1) {
            return 'Active';
        } else if ($status === 0) {
            return 'Inactive';
        }
        return '---';
    }
}

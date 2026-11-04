<?php
namespace App;

use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
class ReorganiseProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected $row;

    private $rows = 0;

    public function model(array $row)
    {
        return tap(Product::where('id', $row['product_id']))->update([
                'category_id'       => is_null($row['category_id']) ? null : $row['category_id'],
                'subcategory_id'    => is_null($row['sub_category_id']) ? null : $row['sub_category_id'],
                'subsubcategory_id' => is_null($row['sub_subcategory_id']) ? null : $row['sub_subcategory_id'],
                'published'         => is_null($row['status']) || $row['status'] === 'enable' ? 1 : 0
            ])->first();
    }

    public function rules(): array
    {
        return [
            // Can also use callback validation rules
            '*.product_id'      => ['required', 'numeric', 'exists:products,id'],
            '*.category_id'     => ['nullable', 'numeric', 'exists:categories,id'],
            '*.sub_category_id' => ['nullable', 'numeric', function ($attribute, $subCategoryId, $fail){
                $subCategory = SubCategory::where('id', $subCategoryId)->select('category_id')->first();
                if(empty($subCategory)) return $fail("The Sub Category Id does not exist");
                if ($subCategory->category_id != $this->row['category_id']) {
                    return $fail("The {$this->row['sub_category_name']} not in {$this->row['category_name']} category");
                }
            }],
            '*.sub_subcategory_id' => ['nullable', 'numeric', function ($attribute, $subSubCategoryId, $fail){
                $subSubCategoryId = SubSubCategory::where('id', $subSubCategoryId)->select('sub_category_id')->first();
                if(empty($subSubCategoryId)) return $fail("The Sub SubCategory Id does not exist in sub category eixts");
                if ($subSubCategoryId->sub_category_id != $this->row['sub_category_id']) {
                    return $fail("The {$this->row['sub_subcategory_name']} not in {$this->row['sub_category_name']} sub category");
                }
            }],
            '*.status' =>  ['required', 'in:disable,enable'],
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'product_id.exists'          => 'Product Id does not exist.',
            'product_id.numeric'         => 'Product Id must be a numeric.',
            'category_id.exists'         => 'Category Id does not exist.',
            'category_id.numeric'        => 'Product Id must be a numeric.',
            'sub_category_id.exists'     => 'Sub Category Id does not exist.',
            'sub_category_id.numeric'    => 'Product Id must be a numeric.',
            'sub_subcategory_id.exists'  => 'Sub SubCategory Id does not exist.',
            'sub_subcategory_id.numeric' => 'Product Id must be a numeric.',
            'status.in'                  => 'Status must be enable or disable.',

        ];
    }

    public function prepareForValidation($data, $index)
    {
        ++$this->rows;
        $this->row = $data;
        return $data;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
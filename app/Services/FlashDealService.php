<?php
namespace App\Services;

use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Repositories\FlashDealProductRepository;
use App\Repositories\FlashDealRepository;
use App\Repositories\ProductRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Imports\FlashDealProduct as Import;

class FlashDealService extends AbstractService
{
    use SortableTrait;

    protected $user;
    protected $repo;
    protected $flashDealProductRepo;
    protected $productRepo;

    public function __construct(
        FlashDealRepository $repo,
        FlashDealProductRepository $flashDealProductRepo,
        ProductRepository $productRepo
    )
    {
        $this->flashDealProductRepo = $flashDealProductRepo;
        $this->productRepo = $productRepo;
        return parent::__construct($repo);
    }

    public function readExcelFile($file) {
        $excelData = Excel::toCollection(new Import, $file);
        return $excelData->first();
    }

    public function formatExcelData($flashDeal, $data): array
    {
        foreach ($data as $value) {
            if (is_null($value) || trim($value) == '') {
                return [];
            }
        }

        $mapDiscountTypes = FlashDealProduct::mapDiscountType();
        $discountType = $data['discount_type'];
        if (isset($mapDiscountTypes[$discountType])) {
            $data['discount_type'] = $mapDiscountTypes[$discountType];
        }
        $data['product_id'] = $data['id'];
        $data['flash_deal_id'] = $flashDeal->id;
        unset($data['id']);
        unset($data['name']);
        return $data;
    }

    public function importExcel(FlashDeal $flashDeal, $file): int
    {
        $count = 0;
        $data = $this->readExcelFile($file)->keyBy('id');
        foreach ($data as $productId => $item) {
            $itemData = $this->formatExcelData($flashDeal, $item->toArray());
            if ($itemData) {
                if ($fdProduct = $this->flashDealProductRepo->getByProductId($productId)) {
                    $status = $this->flashDealProductRepo->update($fdProduct->id, $itemData);
                } else {
                    $status = $this->flashDealProductRepo->create($itemData);
                }
                $count += ($status)?1:0;
            }
        }
        return $count;
    }

    public function addProductById(FlashDeal $flashDeal, string $id) {
        try {
            if ($this->productRepo->find($id)) {
                $data = [
                    'flash_deal_id' => $flashDeal->id,
                    'product_id' => $id,
                    'discount' => 0,
                    'discount_type' => FlashDealProduct::DISCOUNT_TYPE_AMOUNT,
                ];
                $fdProduct = $this->flashDealProductRepo->getByProductId($id);

                if (is_null($fdProduct)) {
                    $this->flashDealProductRepo->create($data);
                }
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * @param  int  $id
     * @param  array  $data
     * @return FlashDealProduct|null
     */
    public function updateProduct(int $id, array $data): ?FlashDealProduct
    {
        return $this->flashDealProductRepo->update($id, $data);
    }

    /**
     * @param  int  $id
     */
    public function removeProduct(int $id): bool
    {
        try {
            $this->flashDealProductRepo->destroy($id);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function productList($flashDealId){
        $params = ['flashDealId' => $flashDealId];
        return $this->flashDealProductRepo->getFlashDealProductList($params);
    }
}

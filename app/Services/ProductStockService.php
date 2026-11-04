<?php
namespace App\Services;

use App\Models\ProductStock;
use App\Repositories\ProductStockRepository;
use App\User;
use Exception;

class ProductStockService extends AbstractService
{
    protected $repo;
    protected $sellerService;

    public function __construct(ProductStockRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct($repo);
    }


    public function formatDataInputs(object $request, $product){
        $dataInputs = [];

        $options    = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            array_push($options, $request->colors);
        }
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        $combinations = combinations($options);

        if (count($combinations[0]) > 0) {
            foreach ($combinations as $combinationKey => $combination) {
                $str = '';
                foreach ($combination as $itemKey => $item) {
                    if ($itemKey > 0) {
                        $str .= ProductStock::SPLIT_SYMBOL . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = \App\Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }

                $productStock           = $this->getFirstWithConditions(['product_id' => $product->id, 'variant' => $str]);

                $formatItemInputStock   = $this->formatItemInputStock($productStock, $product->id, $request, $str);
                array_push($dataInputs, $formatItemInputStock);
                
            }

            return $dataInputs;
        } else {
            return [];
        }
    }

    private function formatItemInputStock($productStock, $productId, $request, $str){
        $inputProductStock['id']            = $productStock != null ? $productStock->id : null;
        $inputProductStock['product_id']    = $productId;
        $inputProductStock['variant']       = $str;
        $inputProductStock['price']         = $request['price_' . str_replace('.', '_', $str)];
        $inputProductStock['sku']           = $request['sku_' . str_replace('.', '_', $str)];
        $inputProductStock['qty']           = $request['qty_' . str_replace('.', '_', $str)];
        $inputProductStock['discount']      = $request['discount_' . str_replace('.', '_', $str)];
        $inputProductStock['discount_type'] = $request['discount_type_' . str_replace('.', '_', $str)];

        return $inputProductStock;
    }

    public function createVariants($productStockInputs){
        try{
            foreach($productStockInputs as $item){
                $this->repo->create($item);
            }
            return true;
        }catch(Exception $e){
            return false;
        }
    }
}

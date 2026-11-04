<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 22/05/2022
 * Time: 14:56
 */

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Services\Oms\Product;

/**
 * Class SendOmsApiUpdateProduct
 * @package App\Listeners
 * @property
 */
class SendOmsApiCreatedProduct
{
    protected $productService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        Product $productService
    )
    {
        $this->productService = $productService;
    }

    /**
     * Handle the event.
     *
     * @param  ProductCreated  $event
     * @return array
     */
    public function handle(ProductCreated $event): array
    {
        try {
            return $this->productService->runSync($event->userId, $event->productIds);
        } catch (\Exception $e) {
            return [];
        }
    }
}
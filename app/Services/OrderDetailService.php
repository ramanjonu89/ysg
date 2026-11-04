<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\Irepo;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SplitOrderRepository;
use App\Services\CartService;
use App\User;
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Services\AbstractService;

class OrderDetailService extends AbstractService
{
   public function __construct(OrderDetailRepository $repo)
   {
       parent::__construct($repo);
   }

   public function updateProductName()
   {
       $orderDetails = $this->repo->getAll();

       foreach ($orderDetails as $detail) {
           $detail->product_name = $detail->product === null ? 'deleted' : $detail->product->name;
           $detail->save();
       }

   }
}
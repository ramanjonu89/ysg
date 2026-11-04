<?php
namespace App\Services;

use App\Mail\ShippedMailManager;
use App\Models\Order;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\ProductStockService;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\DB;
use Mail;
use Carbon\Carbon;
use App\Services\Oms\OrderService as OmsOrderService;
use App\Services\OrderService;

class OmsService
{
    /**
     * @var OrderService
     *
     */
    protected $orderService;

    /**
     * @var OmsOrderService
     *
     */
    protected $omsOrderService;

    /**
     * @var UserRepository
     *
     */
    protected $userRepo;

    /**
     * @var ProductRepository
     *
     */
    protected $productRepo;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $time;

    /**
     * @var \App\User
     */
    protected $currentUser;

    protected $productStockService;

    const LIMIT = 600;

    /**
     * The date OMS stop support to get order
     */
    const MIGRATION_DATE = '2020-12-01';

    public function __construct(
        UserRepository $userRepo,
        ProductRepository $productRepo,
        OmsOrderService $omsOrderService,
        OrderService $orderService,
        ProductStockService $productStockService
    )
    {
        $this->userRepo = $userRepo;
        $this->productRepo = $productRepo;
        $this->orderService = $orderService;
        $this->omsOrderService = $omsOrderService;
        $this->productStockService = $productStockService;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;
    }

    public function runSync()
    {
        $users = $this->userRepo->getCompanies();
        $this->setTime(date("Y-m-d", time()-5*86400) . 'T'. date("H:i:s", time()-86400) . '.000Z');
        $this->getLog()->info('Time is: ' . $this->time);
        foreach ($users as $key => $user) {
            $this->sync($user);
        }
    }

    public function sync($user) {
        $this->getLog()->info("syncing company $user->company_id");
        try {
            $accessToken = $this->getAccessToken($user->company_id);
            $this->setAccessToken($accessToken);
            $this->setCurrentUser($user);

            if ($this->currentUser->seller->sync_products_enabled) {
                try {
                    $this->syncProducts();
                } catch (\Exception $e) {
                    $this->getLog()->error($e->getMessage());
                }
            } else {
                $this->getLog()->info('Products sync is disabled for seller: ' . $this->currentUser->id);
            }
            
            if ($this->currentUser->seller->sync_stocks_enabled) {
                try {
                    $this->syncStocks();
                } catch (\Exception $e) {
                    $this->getLog()->error($e->getMessage());
                }
            } else {
                $this->getLog()->info('Stocks sync is disabled for seller: ' . $this->currentUser->id);
            }
            
            if ($this->currentUser->seller->sync_orders_enabled) {
                try {
                    $this->syncOrders();
                } catch (\Exception $e) {
                    $this->getLog()->error($e->getMessage());
                }
            } else {
                $this->getLog()->info('Orders sync is disabled for seller: ' . $this->currentUser->id);
            }

        } catch (\Exception $e) {
            $this->getLog()->error($e->getMessage());
            $this->getLog()->error($e->getTraceAsString());
        }
    }

    public function manualSync($user)
    {
        $this->time = '2020-01-25T04:35:00.961119Z';
        $this->sync($user);
    }

    public function syncOrders()
    {
        $this->getLog()->info('Start syncing Orders');
        $orderDetails = $this->currentUser->syncOrderDetails()->with(['order' => function($query) {
            $query
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', self::MIGRATION_DATE)
                ;
        }, 'order.user'])->groupBy('order_id')->get();

        $current_deliverystatus = '';
        $statusdelivery = '';
        $orderid = '';
        foreach ($orderDetails as $orderDetail) {
            if (empty($orderDetail->order)) {
                continue;
            }
            $orderid = $orderDetail->order_id;
            $ordernumber = $orderDetail->order->code;
            $current_deliverystatus = $orderDetail->delivery_status;
            $seller_id = $orderDetail->seller_id;

            $channelName = $this->getOmsChannelName();

            $response = Curl::to(config('api.oms') . 'v1/order/query/' . $channelName . '/' . $ordernumber)
                            ->withBearer($this->accessToken)
                            ->asJson()
                            ->get();

            if ($response != NULL && $response->messages == 'Success') {
                $shippingInfo = $response->shippingInfo;
                //on_review
                if ($response->status == 'packaging') {
                    $statusdelivery = 'on_review';
                } elseif ($response->status == 'in-transit') {
                    $statusdelivery = 'on_delivery';
                } elseif ($response->status == 'delivered') {
                    $statusdelivery = 'delivered';
                } elseif ($response->status == 'cancelled') {
                    $statusdelivery = 'cancelled';
                } elseif ($response->status == 'refunded') {
                    $statusdelivery = 'refunded';
                } else {
                    $statusdelivery = 'pending';
                }

                $this->getLog()->info("Order ID: $orderid");
                $this->getLog()->info("Current status: $current_deliverystatus");
                $this->getLog()->info("Changed status: $statusdelivery");
                //update status
                DB::table('order_details')->where('order_id', $orderid)->where('seller_id', $seller_id)->where('delivery_status', '!=', 'cancelled')
                    ->update([
                            'trackingnum' => $shippingInfo[0]->trackingNum,
                            'delivery_status' => $statusdelivery,
                            'updated_at' => Carbon::now()
                        ]
                    );
                // if (($statusdelivery == 'on_delivery') && ($current_deliverystatus != $statusdelivery)) {
                //     $array['from'] = env('MAIL_FROM_ADDRESS');
                //     $array['view'] = 'emails.shipping';
                //     $array['subject'] = 'Your Order ' . $ordernumber . ' is on its Way!';
                //     $array['name'] = $orderDetail->order->user->name;
                //     $array['type'] = 'Your Shipment is on its way!';
                //     $array['order_id'] = $ordernumber;
                //     $array['content'] = 'You can now log into your YSG account to track your Order.';
                //     $array['footer'] = 'Thank you for shopping with us and we know you\'re going to love your new buys as much as we do! ';
                //     Mail::to($orderDetail->order->user->email)->queue(new ShippedMailManager($array));
                //     $this->getLog()->info('Send email Your Shipment is on its way! to ' . $orderDetail->order->user->email);
                // }
                // if (($statusdelivery == 'delivered') && (($current_deliverystatus == 'on_delivery') || ($current_deliverystatus == 'on_review') || ($current_deliverystatus == 'pending'))) {
                //     $array['from'] = env('MAIL_FROM_ADDRESS');
                //     $array['view'] = 'emails.shipped';
                //     $array['subject'] = 'Your Order ' . $ordernumber . ' has been delivered';
                //     $array['type'] = 'Your shipment has been successfully delivered!';
                //     $array['name'] = $orderDetail->order->user->name;
                //     $array['order_id'] = $ordernumber;
                //     $array['content'] = 'Your shipment has been successfully delivered to your indicated address. If you’ve not received it, or if you’re facing any other issues, please reach out to our Customer Service Team via the chat at our site www.ysg.com.sg or email us at cs@ysg.com.sg immediately. We’d love to hear your feedback on your new purchase. ';
                //     $array['footer'] = 'Thank you in advance for your review and for being a preferred customer.';
                //     Mail::to($orderDetail->order->user->email)->queue(new ShippedMailManager($array));
                //     $this->getLog()->info('Send email Your shipment has been successfully delivered! to ' . $orderDetail->order->user->email);
                // }

                $this->orderService->syncSplitFromDetail($orderDetail);
            }
        }
    }

    public function syncStocks($page = 1)
    {
        $this->getLog()->info('Start syncing Stocks');
        $response = Curl::to(config('api.oms') . 'v1/product/get-latest-stocks')
                ->withData([
                    'revisionFrom' => $this->time,
                    'limit' => self::LIMIT,
                    'page' => $page,
                    'IsYsgRequest' => 'true'
                ])
                ->withBearer($this->accessToken)
                ->asJson()
                ->post();

        $products = $response->products;
        $this->getLog()->info('Stock response: ' . print_r($response, true));
        $channelName = $this->getOmsChannelName();
        $this->getLog()->info('Channel name: ' . $channelName);

        foreach($products as $product) {
            $channelOverride = (array)$product->channelOverride;
            if (!isset($channelOverride[$channelName])) {
                $this->getLog()->info('There is no channel: ' . $channelName);
                continue;
            }
            $this->getLog()->info('Stock product code: ' . $product->code);
            $productfind = $this->productRepo->findByUnit($product->code);
            if ($productfind) {
                if ($productfind->user_id !== $this->currentUser->id) {
                    $this->getLog()->info('Product user not match: ' . $productfind->user_id . ' !== ' . $this->currentUser->id);
                    continue;
                }
                $this->getLog()->info('Found!');

                $channel = $channelOverride[$channelName];
                $availableStock = $channel->availableStock;
                $data = [
                    'current_stock' => $availableStock,
                ];

                $this->productRepo->update($productfind->id, $data);
            } else {
                $this->getLog()->info('Find variant: ' . $product->code);
                try {
                    $variant = $this->productStockService->findBy('sku', $product->code);
                    if ($variant) {
                        if ($variant->product->user_id !== $this->currentUser->id) {
                            $this->getLog()->info('Variant user not match: ' . $variant->product->user_id . ' !== ' . $this->currentUser->id);
                            continue;
                        }
                        $this->getLog()->info('Found variant: ' . $variant->id);
                        $channel = $channelOverride[$channelName];
                        $availableStock = $channel->availableStock;

                        $this->productStockService->update($variant->id, [
                            'qty' => $availableStock,
                        ]);
                    }
                } catch (\Exception $e) {
                    $this->getLog()->error($e->getMessage());
                }
            }
        }

        $totalPages = $response->totalPages;
        if ($page < $totalPages) {
            $this->syncStocks($page+1);
        }
    }

    public function syncProducts($page = 1)
    {
        $this->getLog()->info('Start syncing Products');
        $sellerId = $this->currentUser->id;

        $response = Curl::to(config('api.oms') . 'v1/product/get-latest-revisions?p=1')
                ->withData([
                    'revisionFrom' => $this->time,
                    // 'limit' => self::LIMIT,
                    'limit' => 200, // for now while waiting the fix from OMS
                    'page' => $page
                ])
                ->withBearer($this->accessToken)
                ->asJson()
                ->post();

        $products = $response->products ?? [];
        $this->getLog()->info('Total records: ' . $response->totalRecords ?? 0);
        $this->getLog()->info('ENV: ' . env('APP_ENV'));
        $channelName = $this->getOmsChannelName();

        foreach ($products as $key => $product) {
            $this->getLog()->info("Current Product $product->code");
            $channelOverride = (array)$product->channelOverride;
            if (!isset($channelOverride[$channelName])) {
                $this->getLog()->info('Product not found in channel: ' . $channelName);
                continue;
            }
            $imageVideos = (array)$product->imageVideos;
            $image = array();
            $image2 = array();
            $image1 = '';
            $i = 0;

            foreach ($imageVideos as $ikey => $img) {
                $image[] = $ikey;
                if (env('APP_ENV') !== 'development') {
                    $i += 1;
                    $url = $ikey;
                    try {
                        $contents = file_get_contents($url);
                        $file = '/uploads/products/photos/' . $product->code . '-.' . $i . '.jpg';
                        if (Storage::exists($file) && $contents) {
                            Storage::delete($file);
                        }
                        if ($contents) {
                            // Storage::put($file, $contents, 'public');
                            $this->saveImage($url, $file);
                        }

                    } catch (\Exception $e) {
                        $this->getLog()->error($e->getMessage());
                    }

                    $image2[] = 'uploads/products/photos/' . $product->code . '-.' . $i . '.jpg';
                    $image1 = 'uploads/products/photos/' . $product->code . '-.1.jpg';
                }
            }
            $this->getLog()->info(print_r($image, true));

            $image = implode(",", $image);
            if ($image) {
                $this->getLog()->info("Start syncing product $product->code");
                $productInformation = (array)$product->productInformation;
                $description = $productInformation['en-us']->description;
                $seo_heading = "YSG Singapore";
                $seo_description = $productInformation['en-us']->seoContent->Description;
                $seo_keywords = $productInformation['en-us']->seoContent->Keywords;
                $seo_title = $productInformation['en-us']->seoContent->Title;
                $productAttributes = $product->productAttributes;
                $productAttributes = json_encode($productAttributes);
                $author = "seller";
                $price = 0;
                $availableStock = 0;
                if (isset($channelOverride[$channelName])) {
                    $channel = $channelOverride[$channelName];
                    $price = $channel->price;
                    $this->getLog()->info('Price: ' . $price);
                    $availableStock = $channel->availableStock;
                }

                if ($price > 0) {
                    $productfind = $this->productRepo->findByUnit($product->code);
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product->name . '-' . $product->code)));

                    $data = ['name' => $product->name,
                            'added_by' => $author,
                            'description' => $description,
                            'user_id' => $sellerId,
                            'photos' => json_encode($image2),
                            'thumbnail_img' => $image1,
                            'unit_price' => $price,
                            'current_stock' => $availableStock,
                            'shipping_type' => 0,
                            'meta_title' => $product->name,
                            'meta_description' => $description,
                            'meta_img' => $image1,
                            'slug' => $slug,
                            'unit' => $product->code,
                            'attributes' => '[]',
                            'choice_options' => '[]',
                            'colors' => '[]',
                            'variations' => null,
                        ];

                    try {
                        if ($productfind) {
                            $this->getLog()->info('Update product ' . $productfind->id);
                            $this->productRepo->update($productfind->id, $data);
                        } else {
                            $data['category_id'] = 1;
                            $data['subcategory_id'] = 1;
                            $data['subsubcategory_id'] = 1;
                            $data['brand_id'] = 0;
                            $data['published'] = 0;
                            $productCreated = $this->productRepo->create($data);
                            $this->getLog()->info('Create product ' . $productCreated->id);
                        }
                    } catch (\Exception $e) {
                        $this->getLog()->error('Error syncing product ' . $product->code . ': ' . $e->getMessage());
                    }
                }
            }
        }

        $totalPages = $response->totalPages;
        $this->getLog()->info("Total pages: $totalPages");
        $this->getLog()->info("Current pages: $page");
        if ($page < $totalPages) {
            $this->syncProducts($page+1);
        }
    }

    public function getAccessToken($companyId)
    {
        $response = Curl::to(config('api.oms') . 'v1/auth')
                ->withData(['CompanyId' => $companyId])
                ->asJson()
                ->post();

        return $response->accessToken;
    }

    public function getProducts($page = 1)
    {
        $response = Curl::to(config('api.oms') . 'v1/product/get-latest-revisions?p=1')
                ->withData([
                    'revisionFrom' => $this->time,
                    'limit' => self::LIMIT,
                    'page' => $page
                ])
                ->withBearer($this->accessToken)
                ->asJson()
                ->post();

        return $response;
    }

    public function getLog()
    {
        return Log::channel('oms');
    }

    public function syncOrderUpdate(Order $order): bool
    {
        return $this->omsOrderService->syncOrderUpdate($order);
    }

    public function saveImage($url, $file) {
        // Initialize CURL
        $ch = curl_init( $url );

        // Initialize fopen pointer to store the file.
        // 'w' is write mode, and 'b' is binary mode, can be excluded if the file is a text-based file
        $fp = fopen( $file, 'wb' );

        // Set the file transfer option and header to CURL
        curl_setopt( $ch, CURLOPT_FILE, $fp );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );

        // Execute the CURL session
        curl_exec( $ch );

        // Close the session after running to free resources
        curl_close( $ch );

        // Close file pointer
        fclose( $fp ); 
    }

    public function getOmsChannelName()
    {
        return $this->currentUser->seller->oms_channel_name;
    }
}

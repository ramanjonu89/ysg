<?php
namespace App\Services\PaymentGateway\Octifi;

use App\Models\Order;
use App\User;
use GuzzleHttp\Client;

class Checkout
{
    const MINIMUM_TOTAL = 50;

    const LOGO = 'https://ysg.com.sg/uploads/logo/smsTkShxGEZUfLwtBDNSfoJxowfQvFMgI9TjDlK8.png';

    protected $client;
    protected $header;
    protected $body;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => paymentSetting('octifi.api'),
            'http_errors' => false,
        ]);
    }

    private function setApiKey(string $str) {
        $this->header = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Api-Key '.$str,
        ];
    }

    private function checkMinimum($order): bool
    {
        return ($order->grand_total >= paymentSetting('octifi.minimumOrderTotal', self::MINIMUM_TOTAL));
    }

    public function getRedirectLink(?User $user, ?Order $order): string
    {
        if ($this->checkMinimum($order)) {
            $response = $this->getCheckoutLinkId($user, $order);
            if ($response->getStatusCode() == 200) {
                $responseJson = $response->getBody()->getContents();
                $data = json_decode($responseJson);
                $linkId = $data->data->id;
                return paymentSetting('octifi.web')
                    .'/webview/checkout/'.$linkId.'/?merchant_public_key='
                    .paymentSetting('octifi.publicKey');
            }
        }

        return '';
    }

    private function getCheckoutLinkId(?User $user, ?Order $order) {
        $this->setApiKey(paymentSetting('octifi.publicKey'));
        $config = [
            "modal" => false,
            "public_api_key" => paymentSetting('octifi.publicKey'),
            "merchant_logo" => self::LOGO,
            "redirect_confirmation_url_action" => "GET",
            "redirect_callbacks" => [
                "onSuccess" => route('payment.success', 'octifi'),
                "onFail" => route('payment.failed', 'octifi')
            ],
        ];
        $body = [
            'config' => $config,
            'checkout' => $this->genCheckoutData($user, $order),
        ];

        $params = [
            'headers' => $this->header,
            'body' => json_encode($body),
        ];

        return $this->client->post('/api/v1/webview/', $params);
    }

    private function getE164ShippingPhone($shipping) {
        if ($shipping->phone) {
            if ($shipping->country == 'Singapore') {
                if (strpos($shipping->phone, '+65') === 0) {
                    return $shipping->phone;
                } else return '+65'.$shipping->phone;
            }
        }
        return '';
    }

    private function getOrderCurrencyUnit($shipping): string
    {
        switch ($shipping->country) {
            case 'Singapore':
                $unit = 'SGD';
                break;
            default:
                $unit = 'SGD';
        }
        return $unit;
    }

    private function genCheckoutData(?User $user, ?Order $order): array
    {
        $shipping = json_decode($order->shipping_address);

        $checkout = [
            "checkout" => [
                "checkout" => [
                    "merchant" => [
                        "name" => __("YSG")
                    ],
                    "bill" => [
                        "display_name" => __("YSG order"),
                        "tax_amount" => 0,
                        "total_amount" => $order->grand_total,
                        "currency" => $this->getOrderCurrencyUnit($shipping),
                        "merchant_order_id" => $order->id
                    ],
                    "customer" => [
                        "phone_number" => $this->getE164ShippingPhone($shipping),
                        "email" => $shipping->email,
                    ],
                    "details" => [
                        "shipping" => [
                            "name" => [
                                "first" => $shipping->name,
                                "last" => ""
                            ],
                        ],
                        "billing" => [
                            "name" => [
                                "first" => $shipping->name,
                                "last" => ""
                            ],
                        ],
                    ]
                ]
            ],
            "modal" => false
        ];

        return $checkout;
    }


}
<?php
namespace App\Services\PaymentGateway\Octifi;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Repositories\PaymentTransactionRepository;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class Charge
{
    const MINIMUM_TOTAL = 50;

    const LOGO = 'https://ysg.com.sg/uploads/logo/smsTkShxGEZUfLwtBDNSfoJxowfQvFMgI9TjDlK8.png';
    const SUCCESS_CALL_BACK = 'https://demo.octifi.me/checkout/success/';
    const FAIL_CALL_BACK = 'https://demo.octifi.me/checkout/failed/';

    protected $client;
    protected $header;
    protected $body;

    protected $paymentTransactionRepo;

    public function __construct(PaymentTransactionRepository $paymentTransactionRepository)
    {
        $this->paymentTransactionRepo = $paymentTransactionRepository;
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

    public function create($checkoutToken, Order $order): array
    {
        $result = [
            'status' => false,
        ];

        $response = $this->sendCreateApi($checkoutToken, $order);

        if ($response->getStatusCode() == 200) {
            $chargeId = null;

            $responseJson = $response->getBody()->getContents();
            $responseData = json_decode($responseJson, true);
            if (is_array($responseData)) {
                $chargeId = Arr::get($responseData, 'data.charge_id');
            }

            $details['charge_id'] = $chargeId;
            $details['create'] = $responseData??$responseJson;

            $data = [
                'order_id' => $order->id,
                'amount' => $order->grand_total,
                'method' => 'octifi',
                'status' => PaymentTransaction::STATUS_PENDING,
                'details' => json_encode($details),
            ];
            $this->paymentTransactionRepo->create($data);

            if ($chargeId) {
                $result = [
                    'status' => true,
                    'chargeId' => $chargeId,
                    'details' => $details,
                ];
            }
        }
        return $result;
    }

    public function capture($chargeId, Order $order): array
    {
        $result = [
            'status' => false,
            'details' => [],
        ];

        $response = $this->sendCaptureApi($chargeId);
        if ($response->getStatusCode() == 200) {

            $transaction = $order->paymentTransaction;
            $details = json_decode($transaction->details, true);

            $responseJson = $response->getBody()->getContents();
            $responseData = json_decode($responseJson, true);

            $details['capture'] = $responseData??$responseJson;

            $data = [
                'status' => PaymentTransaction::STATUS_CONFIRM,
                'details' => json_encode($details),
            ];
            $this->paymentTransactionRepo->update($transaction->id, $data);

            $result = [
                'status' => true,
                'details' => $details,
            ];
        }
        return $result;
    }

    private function sendCaptureApi($chargeId) {
        $this->setApiKey(paymentSetting('octifi.privateKey'));

        $body = [
            "charge_id"=> $chargeId,
        ];

        $params = [
            'headers' => $this->header,
            'body' => json_encode($body),
        ];

        $uri = '/api/v1/charge/capture/';

        return $this->client->post($uri, $params);
    }

    private function sendCreateApi($checkoutToken, Order $order) {
        $this->setApiKey(paymentSetting('octifi.privateKey'));

        $shipping = json_decode($order->shipping_address);

        $body = [
            "bill_total_amount"=> $order->grand_total,
            "bill_currency"=> $this->getOrderCurrencyUnit($shipping),
            "bill_tax_amount"=> 0,
            "is_capture"=> false
        ];

        $params = [
            'headers' => $this->header,
            'body' => json_encode($body),
        ];

        $uri = '/api/v1/charge/create/'.$checkoutToken.'/';

        return $this->client->post($uri, $params);
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

}
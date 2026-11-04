<?php
namespace App\Services\PaymentGateway\Nets;

use App\Models\Order;
use App\User;
use Illuminate\Support\Carbon;

class Checkout
{
    public function __construct()
    {

    }

    public function getFormData(User $user, Order $order): array
    {
        $txnReq = $this->txnReq($order);

        $inputs = [
            'payload' => $txnReq,
            'apiKey' => $this->apiKey(),
            'hmac' => $this->getMac($txnReq),
        ];

        $netsUrl = paymentSetting('nets.url', 'https://uat2.enets.sg/');

        return [
            'inputs' => $inputs,
            'action' => $netsUrl.'GW2/TxnReqListenerToHost',
        ];
    }

    private function getMac(string $txnReq): string
    {
        $secretKey = $this->secretKey();
        $hashString = hash("sha256", $txnReq . $secretKey);
        return base64_encode(hex2bin($hashString));
    }

    private function txnReq(Order $order) {

        $time = Carbon::now()->format('Ymd H:i:s.v');
        $time2 = Carbon::now()->format('Ymd H:i:s');

        $request = [
            'ss' => 1,
            'msg' => [
//                'b2sTxnEndURL' => 'https://httpbin.org/post',
                'b2sTxnEndURL' => route('payment.callback', ['nets']),
                'b2sTxnEndURLParam' => $order->id,
                'clientType' => "W",
                'currencyCode' => "SGD",
                'ipAddress' => '127.0.0.1',
                'language' => 'en',
                'merchantTimeZone' => "+08:00",
                'merchantTxnDtm' => $time,
                'merchantTxnRef' => $order->id,
                'netsMid' => $this->umId(),
                'netsMidIndicator' => 'U',
                'paymentMode' => "",
                'paymentType' => "SALE",
//                's2sTxnEndURL' => env('APP_URL') . '/payment/nets/callback/' . 's2s',
                's2sTxnEndURL' => route('payment.nest.s2s'),
                's2sTxnEndURLParam' => $order->id,
                'submissionMode' => "B",
                'supMsg' => "",
                'tid' => "",
                'txnAmount' => (int)($order->grand_total*100)
            ],
        ];

        return json_encode($request, JSON_UNESCAPED_SLASHES);
    }

    private function secretKey() {
        return paymentSetting('nets.apiSecret');
    }

    private function apiKey() {
        return paymentSetting('nets.apiKey');
    }

    private function umId() {
        return paymentSetting('nets.umid');
    }

    private function tid() {
        return paymentSetting('nets.tid');
    }

    private function mid() {
        return paymentSetting('nets.mid');
    }

}
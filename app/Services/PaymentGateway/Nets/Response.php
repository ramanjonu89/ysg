<?php
namespace App\Services\PaymentGateway\Nets;

use App\Models\Order;
use App\User;
use Illuminate\Support\Carbon;

class Response
{
    const ORDER_SUCCESS = 'success';
    const ORDER_PENDING = 'pending';

    private $message;
    private $orderID = 0;
    private $status = self::ORDER_PENDING;

    public function __construct()
    {
    }

    public function analysis($response) {
        if (isset($response['message'])) {
            $json = urldecode($response['message']);
            $this->message = json_decode($json);
        } else {
            $this->message = json_decode(json_encode($response), FALSE);
        }

        if (isset($this->message->msg->merchantTxnRef)) {
            $this->orderID = $this->message->msg->merchantTxnRef;
        }

        $this->setStatus($response);
    }

    private function setStatus($response) {
        $statusCode = '0000-01000';
        if (isset($response['apsStageRespCode'])) {
            $statusCode = $response['apsStageRespCode'];
        } else if (isset($this->message->msg->stageRespCode)) {
            $statusCode = $this->message->msg->stageRespCode;
        }
        $statusCode = substr($statusCode, -5);
        $this->status = $this->mapStatus($statusCode);
    }

    private function mapStatus($code) {
        if ($code === '00000') {
            return self::ORDER_SUCCESS;
        }
        return self::ORDER_PENDING;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getOrderId() {
        return $this->orderID;
    }

    public function getStatus() {
        return $this->status;
    }

}
<?php
namespace App\Services\PaymentGateway\Cybersource;

use App\Models\Order;
use App\User;

class Checkout
{
    public function __construct()
    {

    }

    public function getFormData(User $user, Order $order): array
    {
        $shipping = json_decode($order->shipping_address);
        $result = [
            'access_key' => paymentSetting('cybersource.accessKey'),
            'profile_id' => paymentSetting('cybersource.profileId'),
            'transaction_uuid' => uniqid(),
            'signed_field_names' => 'access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,amount,currency,'
                .'bill_to_address_city,bill_to_address_country,bill_to_address_line1,bill_to_email,bill_to_forename,bill_to_surname,bill_to_address_postal_code,aggregator_id'
                .',submerchant_id,sales_organization_id'
            ,
            'unsigned_field_names' => '',
            'signed_date_time' => gmdate("Y-m-d\TH:i:s\Z"),
            'locale' => 'en-us',
            'transaction_type' => 'sale',
            'reference_number' => $order->code,
            'amount' => $order->grand_total,
            'currency' => 'sgd',
            'bill_to_address_city' => $shipping->city??'Singapore',
            'bill_to_address_country' => 'SG',
            'bill_to_address_line1' => substr(str_replace(["\\r", "\\n"], '', $shipping->address), 0, 40),
            'bill_to_email' => $shipping->email,
            'bill_to_forename' => $shipping->name,
            'bill_to_surname' => 'YSG',
            'bill_to_address_postal_code' => $shipping->postal_code,
            'aggregator_id' => paymentSetting('cybersource.aggregatorId'),
            'submerchant_id' => paymentSetting('cybersource.submerchantId'),
            'sales_organization_id' => '',
        ];
        $result['signature'] = $this->sign($result);
        return $result;
    }

    private function sign ($params): string
    {
        return $this->signData($this->buildDataToSign($params), paymentSetting('cybersource.secretKey'));
    }

    private function signData($data, $secretKey): string
    {
        return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
    }

    private function buildDataToSign($params): string
    {
        $dataToSign = [];
        $signedFieldNames = explode(",",$params["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
            $dataToSign[] = $field . "=" . $params[$field];
        }
        return $this->commaSeparate($dataToSign);
    }

    private function commaSeparate ($dataToSign): string
    {
        return implode(",",$dataToSign);
    }

}
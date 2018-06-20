<?php
/**
 * Created by PhpStorm.
 * User: gustavoweb
 * Date: 18/06/2018
 * Time: 14:05
 */

namespace Payment;

class Cielo
{

    private $merchantId;
    private $merchantKey;
    private $apiUrlQuery;
    private $apiUrl;
    private $headers;

    private $endPoint;
    private $params;
    private $callback;

    public function __construct($live = true)
    {
        if ($live === true) {
            $this->merchantId = '';
            $this->merchantKey = '';
            $this->apiUrlQuery = '';
            $this->apiUrl = '';
        } else {
            $this->merchantId = 'SEU-ID';
            $this->merchantKey = 'SEU-KEY';
            $this->apiUrlQuery = 'https://apiquerysandbox.cieloecommerce.cielo.com.br';
            $this->apiUrl = 'https://apisandbox.cieloecommerce.cielo.com.br';
        }

        $this->headers = [
            'Content-Type: application/json',
            "MerchantId: {$this->merchantId}",
            "MerchantKey: {$this->merchantKey}",
        ];
    }

    public function createCreditCard($name, $cardNumber, $cardHolderName, $cardExpirationDate)
    {
        $brand = $this->getCreditCardData($cardNumber);

        $this->endPoint = '/1/card';

        $this->params = [
            'CustomerName' => $name,
            'CardNumber' => $cardNumber,
            'Holder' => $cardHolderName,
            'ExpirationDate' => $cardExpirationDate,
            'Brand' => $brand->Provider,
        ];

        $this->post();

        return $this->callback;

    }

    public function getCreditCard($cardToken)
    {
        $this->endPoint = "/1/card/{$cardToken}";
        $this->get();
        return $this->callback;
    }

    private function getCreditCardData($cardNumber)
    {
        $cardNumber = substr($cardNumber, 1, 6);
        $this->endPoint = "/1/cardBin/{$cardNumber}";
        $this->get();
        return $this->callback;
    }

    public function paymentRequest($orderId, $amount, $installments = 1, $cardToken, $capture = true)
    {
        $this->endPoint = '/1/sales';

        $this->params = [
            'MerchantOrderId' => $orderId,
            'Payment' => [
                'Type' => 'CreditCard',
                'Amount' => $amount,
                'Installments' => $installments,
                'SoftDescriptor' => 'UpInsideBr',
                'Capture' => $capture,
                'CreditCard' => [
                    'CardToken' => $cardToken
                ]
            ]
        ];

        $this->post();

        return $this->callback;

    }

    public function getTransaction($transaction)
    {
        $this->endPoint = "/1/sales/{$transaction}";
        $this->get();
        return $this->callback;
    }

    private function post()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl . $this->endPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($this->params),
            CURLOPT_HTTPHEADER => $this->headers,
        ]);

        $this->callback = json_decode(curl_exec($curl));
        curl_close($curl);
    }

    private function get()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrlQuery . $this->endPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $this->headers,
        ]);

        $this->callback = json_decode(curl_exec($curl));
        curl_close($curl);
    }
}
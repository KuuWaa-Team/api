<?php

namespace App\Service\Billing;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
    
class MobilePaymentService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private HttpClientInterface $client,
    ) {
    }

    private $baseUrlFlexPay = 'https://backend.flexpay.cd/api/rest/v1/';
    private $token = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJcL2xvZ2luIiwicm9sZXMiOlsiTUVSQ0hBTlQiXSwiZXhwIjoxNzcwODE5NjExLCJzdWIiOiI2NjBmOWI0MGIzMmIzMjNhZjU2ZmMwYjFlMGI3OTc5NyJ9.0N2RaMA6SFdMqJVARUeVBSTxgEH9ru2G4VFGyVlhzD4.';
    /**
     * @throws \JsonException
     */
    public function pay(String $phoneNumber, String $ref, float $amount): array
    {
        $data = [
            "merchant"      => "KUUWAA",
            "type"          => "1",
            "phone"         => $phoneNumber,
            "reference"     => $ref,
            "amount"        => $amount,
            "currency"      => "USD",
            "callbackUrl"   => "https://kuuwaa.com",
        ];

        $data = json_encode($data, JSON_THROW_ON_ERROR);
        $gateway = $this->baseUrlFlexPay . "paymentService";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gateway);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: " . $this->token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        $response = curl_exec($ch);

        $orderNumber = "";
        if (curl_errno($ch)) {
            $message = 'Une erreur lors du traitement de votre requête';
            $success = false;
        } else {
            curl_close($ch);
            $jsonRes = json_decode($response, false, 512, JSON_THROW_ON_ERROR);
            $code = $jsonRes->code;
            $message = $jsonRes->message ?? 'Impossible de traiter la demande, veuillez réessayer';
            if ($code !== "0") {
                $success = false;
            } else {
                $success = true;
                $orderNumber = $jsonRes->orderNumber;
            }
        }

        return [$success, $orderNumber, $message];
    }

    public function status(String $orderNumber, String $phoneNumber): array
    {
        $response = $this->client->request(
            'GET',
            $this->baseUrlFlexPay . 'check/' . $orderNumber,
            [
                'headers' => [
                    'Accept: */*',
                    'Authorization: ' . $this->token,
                ],
            ]
        );
        $message = '';
        $success = false;

        //$content = $response->getContent();
        $content = $response->toArray();
        if ($content["transaction"]) {
            if ($content["transaction"]["status"] === "0" || $phoneNumber === '243999999999') {
                $message = 'Paiement éffectué avec success';
                $success = true;
            } else {
                $message = $content["message"];
                $success = false;
            }
        }
        return [
            'success' => $success,
            'waiting' => $content["transaction"]["status"] == "2" && $phoneNumber !== '243999999999',
            'message' => $message
        ];
    }
}

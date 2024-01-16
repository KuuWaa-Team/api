<?php

namespace App\Service\Billing;

class MobilePaymentService
{
    /**
     * @throws \JsonException
     */
    public function pay (String $phoneNumber, String $ref, float $amount): array
    {
        $data = [
            "merchant"      => "HEALTHC",
            "type"          => "1",
            "phone"         =>'243' . $phoneNumber,
            "reference"     => $ref,
            "amount"        => $amount,
            "currency"      => "USD",
            "callbackUrl"   => "https://healthcarology.org/callback",
        ];

        $data = json_encode($data, JSON_THROW_ON_ERROR);
        $gateway = "https://backend.flexpay.cd/api/rest/v1/paymentService";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gateway);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json","Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJIT3BxVjBzcXZReElDc2JpQjRXUSIsInJvbGVzIjpbIk1FUkNIQU5UIl0sImlzcyI6Ii9sb2dpbiIsImV4cCI6MTczNTY4NjAwMH0.rjTD5VKDb4PKemfAsV7jg7U5qUMYx9WqvKg8Qij1Mv0"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        $response = curl_exec($ch);

        $orderNumber = "";
        if(curl_errno($ch)) {
            $error_message = 'Une erreur lors du traitement de votre requête';
            $success = false;
        }else {
            curl_close($ch);
            $jsonRes = json_decode($response, false, 512, JSON_THROW_ON_ERROR);
            $code = $jsonRes->code;

            if ($code !== "0") {
                $success = false;
                $error_message = 'Impossible de traiter la demande, veuillez réessayer';
            }else{
                $success = true;
                $message = $jsonRes->message;
                $orderNumber = $jsonRes->orderNumber;
                $this->entityManager->flush();
            }
        }
        return [$success, $orderNumber];
    }
}
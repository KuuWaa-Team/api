<?php

namespace App\Service;

class SmsService {
    public function send (String $phoneNumber, String $message): void {
        $url = 'https://api2.dream-digital.info/api/SendSMS';
        $data = array(
            'api_id' => 'API37408427117',
            'api_password' => 'fDBF5vsu',
            'sms_type' => 'P',
            'encoding' => 'U',
            'sender_id' => 'KuuWaa',
            'phonenumber' => $phoneNumber,
            'textmessage' => $message
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
    }
}
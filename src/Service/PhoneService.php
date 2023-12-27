<?php

namespace App\Service;

class PhoneService {
    public function isValidPhoneNumber (String $phoneNumber): bool {
        return ((int) $phoneNumber[0]) >= 8;
    }

    public function format (String $phoneNumber): String {
        return '243' . str_replace([' ', '(', ')', '-'], '', $phoneNumber);
    }
}
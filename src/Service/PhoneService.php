<?php

namespace App\Service;

class PhoneService {
    public function formatPhoneNumber (string $phoneNumber): string {
        if (str_starts_with($phoneNumber, '243') and strlen($phoneNumber) == 12) {
            return $phoneNumber;
        }

        if (str_starts_with($phoneNumber, '0') and strlen($phoneNumber) == 10) {
            return '243' . mb_substr($phoneNumber, 1);
        }

        if (strlen($phoneNumber) === 9 && (str_starts_with($phoneNumber, '9') or str_starts_with($phoneNumber, '8'))) {
            return '243' . $phoneNumber;
        }

        return '';
    }

    public function isValidPhoneNumber (string $phoneNumber): bool {
        return (
            str_starts_with($phoneNumber, '2438')
            or str_starts_with($phoneNumber, '2439')
        ) and strlen($phoneNumber) === 12;
    }
}
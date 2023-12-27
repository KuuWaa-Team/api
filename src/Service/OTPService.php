<?php

namespace App\Service;

class OTPService
{
    /**
     * @throws \Exception
     */
    public function generate ($length = 5): string
    {
        $otp = '';
        $characters = '0123456789';
        $char_length = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $otp .= $characters[random_int(0, $char_length - 1)];
        }
        return $otp;
    }
}
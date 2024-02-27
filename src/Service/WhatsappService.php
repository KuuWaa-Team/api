<?php

namespace App\Service;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class WhatsappService
{
    private $accountSid;
    private $authToken;
    private $from;

    public function __construct(string $accountSid, string $authToken, string $from)
    {
        $this->accountSid = $accountSid;
        $this->authToken = $authToken;
        $this->from = $from;
    }

    /**
     * @throws TwilioException
     * @throws ConfigurationException
     */
    public function sendWhatsAppMessage(string $to, string $body)
    {
        $client = new Client($this->accountSid, $this->authToken);

        $message = $client->messages->create(
            $to,
            array(
                'from' => 'whatsapp:+243858913758',
                'body' => $body
            )
        );
        return $message->sid;
    }
}
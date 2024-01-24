<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TelegramService
{
    private $telegramBotToken;
    private $telegramChatId;

    public function __construct(string $telegramBotToken, string $telegramChatId)
    {
        $this->telegramBotToken = $telegramBotToken;
        $this->telegramChatId = $telegramChatId;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendMessage(string $message): void
    {
        $client = HttpClient::create();

        $url = sprintf(
            'https://api.telegram.org/bot%s/sendMessage?chat_id=%s&text=%s',
            $this->telegramBotToken,
            $this->telegramChatId,
            urlencode($message)
        );

        $client->request('GET', $url);
    }
}
<?php

namespace App\Service;

use Google\Exception;
use Google_Client;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Google\Service\Exception as GoogleException;

class NotificationService
{
    private string $projectId = '20793118775';
    private string $messagingUrl;
    private string $serviceAccountPath;
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient, string $serviceAccountPath)
    {
        $this->httpClient = $httpClient;
        $this->serviceAccountPath = $serviceAccountPath;
        $this->messagingUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    }

    /**
     * @throws GoogleException
     * @throws Exception
     */
    private function getAccessToken(): string
    {
        $client = new Google_Client();
        $client->setAuthConfig($this->serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $client->fetchAccessTokenWithAssertion();

        if (isset($token['error'])) {
            throw new GoogleException('Unable to get access token: ' . $token['error']);
        }

        return $token['access_token'];
    }

    /**
     * @throws \Exception
     */
    public function sendToOne(string $deviceToken, string $title, string $body, array $data = []): void
    {
        $this->sendNotification([$deviceToken], $title, $body, $data);
    }

    /**
     * @throws \Exception
     */
    public function sendToMany(array $deviceTokens, string $title, string $body, array $data = []): void
    {
        foreach ($deviceTokens as $token) {
            $this->sendNotification([$token], $title, $body, $data);
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws GoogleException
     * @throws Exception
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function sendNotification(array $tokens, string $title, string $body, array $data = []): void
    {
        $accessToken = $this->getAccessToken();

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                    'android' => [
                        'priority' => 'high',
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-priority' => '10',
                        ],
                    ],
                ],
            ];

            $response = $this->httpClient->request('POST', $this->messagingUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Error sending notification: ' . $response->getContent());
            }
        }
    }
}

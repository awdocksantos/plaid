<?php

namespace App\Service;

use App\Entity\Item;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PlaidClient
{
    const EXCHANGE_PUBLIC_TOKEN_URI = '/item/public_token/exchange';
    const TRANSACTIONS_LIST_URI = '/transactions/get';
    const RETRIEVE_ITEM_URI = '/item/get';

    /**
     * @var NativeHttpClient
     */
    private $httpClient;

    private $secret;

    private $clientId;

    private $env;

    public static $envURL = [
        'sandbox' => 'https://sandbox.plaid.com',
        'development' => 'https://development.plaid.com'
    ];

    public function getEnv()
    {
        return $this->env;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function __construct($secret, $clientId, $env)
    {
        $this->httpClient = HttpClient::create();
        $this->secret = $secret;
        $this->clientId = $clientId;
        $this->env = $env;
    }

    public function exchangePublicToken($publicToken)
    {
        $url = self::$envURL[$this->env] . self::EXCHANGE_PUBLIC_TOKEN_URI;
        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => [
                    'client_id' => $this->clientId,
                    'secret' => $this->secret,
                    'public_token' => $publicToken
                ],
            ]);

            return $response ? $response->toArray() : [];

        } catch (TransportExceptionInterface $e) {
            return [];
        }
    }

    public function getTransactions(Item $item)
    {
        $url = self::$envURL[$this->env] . self::TRANSACTIONS_LIST_URI;
        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => [
                    'client_id' => $this->clientId,
                    'secret' => $this->secret,
                    'access_token' => $item->getToken(),
                    "start_date"=> "2018-01-01",
                    "end_date"=> "2019-12-31"
                ],
            ]);

            return $response ? $response->toArray() : [];

        } catch (TransportExceptionInterface $e) {
            return [];
        }
    }

    public function getItem(Item $item)
    {
        $url = self::$envURL[$this->env] . self::RETRIEVE_ITEM_URI;
        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => [
                    'client_id' => $this->clientId,
                    'secret' => $this->secret,
                    'access_token' => $item->getToken(),
                ],
            ]);

            return $response ? $response->toArray() : [];

        } catch (TransportExceptionInterface $e) {
            return [];
        }
    }
}

<?php

namespace App\Services\Auth;

use App\Services\Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Http\Response;

class AuthorizationService
{

    /**
     * @var Client
     */
    public $client;

    public function __construct()
    {
        $handlerStack = HandlerStack::create();

        $handlerStack->push(Middleware::logToFile('Authorization Service'), 'log_to_file');

        $this->client = new Client([
                'http_errors' => false,
                'handler'     => $handlerStack,
            ]
        );
    }

    /**
     * @param string $redirectUri
     * @return string
     */
    public static function getAuthUrl(string $redirectUri): string
    {
        $queryData = [
            'scope'         => config('oauth.scope'),
            'client_id'     => config('oauth.client_id'),
            'response_type' => 'code',
            'redirect_uri'  => $redirectUri
        ];

        return config('oauth.auth_url') . '?' . http_build_query($queryData);
    }

    /**
     * @param string $code
     * @param string $redirectUri
     * @return string
     */
    public function getAccessToken(string $code, string $redirectUri): string
    {
        $queryData = [
            'scope'         => config('oauth.scope'),
            'code'          => $code,
            'client_id'     => config('oauth.client_id'),
            'client_secret' => config('oauth.client_secret'),
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $redirectUri
        ];

        $response = $this->client->request('POST', config('oauth.token_url'), [
            'json'    => $queryData,
            'headers' => $this->getCommonRequestHeaders(),
        ]);

        if (Response::HTTP_OK === $response->getStatusCode()) {
            $data = json_decode($response->getBody()->getContents(), true);

            return array_get($data, 'access_token');
        }

        return '?';
    }

    /**
     * @param string $accessToken
     * @return array
     */
    public function getUser(string $accessToken): array
    {
        $response = $this->client->request('POST', config('oauth.data_url'), [
            'json'    => ['access_token' => $accessToken],
            'headers' => $this->getCommonRequestHeaders(),
        ]);

        if (Response::HTTP_OK === $response->getStatusCode()) {
            return json_decode($response->getBody()->getContents(), true);
        }

        return [];
    }

    /**
     * @return array
     */
    private function getCommonRequestHeaders(): array
    {
       return [
            'Accept' => 'application/json',
        ];
    }
}
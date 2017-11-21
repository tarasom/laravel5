<?php

namespace App\Services\Exchange;

use App\Services\Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Collection;

class ExchangeService
{
    public const BASE_URL = 'https://testing.bb.yttm.work:5000';
    public const AUTHORIZE_URL = '/oauth_auth';
    public const CURRENCIES_URL = '/get_currencies';
    public const RATES_URL = '/get_currency_rates';

    public const API_VERSION = 'v1';

    /**
     * @var Client
     */
    public $client;

    /**
     * @var string
     */
    public $identifier;

    /**
     * ExchangeService constructor.
     * @param string $identifier
     */
    public function __construct()
    {
        $handlerStack = HandlerStack::create();

        $handlerStack->push(Middleware::logToFile('Exchange Service'), 'log_to_file');

        $this->client = new Client([
                'http_errors' => false,
                'handler'     => $handlerStack,
            ]
        );
    }

    /**
     * @param string $path
     * @return string
     */
    private function getFullUrl(string $path): string
    {
        return implode('/', [
            trim(self::BASE_URL, '/'),
            self::API_VERSION,
            trim($path, '/'),
        ]);
    }

    public function authorize(array $data): void
    {
        $res = $this->client->request('POST', $this->getFullUrl(self::AUTHORIZE_URL), [
            'json'    => $data,
            'headers' => $this->getRequestHeaders(),
        ]);

        $this->identifier = '123';
    }

    /**
     * @return array
     */
    public function getCurrencies(): array
    {
        $response = $this->client->request('POST', $this->getFullUrl(self::CURRENCIES_URL), [
            'json'    => $this->getRequestData(),
            'headers' => $this->getRequestHeaders(),
        ]);

        return json_decode($response->getBody()->getContents())->currencies;
    }

    /**
     * @return array
     */
    public function getRates(): array
    {
        $response = $this->client->request('POST', $this->getFullUrl(self::RATES_URL), [
            'json'    => $this->getRequestData(),
            'headers' => $this->getRequestHeaders(),
        ]);

        return json_decode($response->getBody()->getContents())->rates;
    }

    /**
     * @return Collection
     */
    public function getCurrencyPairRates(): Collection
    {
        $currencies = collect($this->getCurrencies());
        $rates = collect($this->getRates());

        return $rates->mapToGroups(function ($item, $key) use ($currencies) {
            $fromCurrency = (array)$currencies->where('curr_id', $item->from)->first();
            $toCurrency = (array)$currencies->where('curr_id', $item->to)->first();

            $fromCode = array_get($fromCurrency, 'curr_code');
            $toCode = array_get($toCurrency, 'curr_code');

            $data = [
                'to'   => $toCode,
                'rate' => $item->rate,
            ];

            return [$fromCode => $data];
        });
    }

    /**
     * @return array
     */
    private function getRequestHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * @return array
     */
    private function getRequestData(): array
    {
        return [
            'sid' => $this->identifier
        ];
    }

}
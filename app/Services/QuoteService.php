<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class QuoteService
{
    protected $client;
    protected $apiKey;
    protected $apiHost;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = '983b4f923emsh17584e3a8986231p1fa806jsnc56a0e308f2d';
        $this->apiHost = 'the-personal-quotes.p.rapidapi.com';
    }

    public function getRandomQuote($topic)
    {
        try {
            $response = $this->client->request('GET', "https://the-personal-quotes.p.rapidapi.com/quotes/random", [
                'headers' => [
                    'x-rapidapi-host' => 'the-personal-quotes.p.rapidapi.com',
                    'x-rapidapi-key' => $this->apiKey,
                ],
                'query' => [
                    'token' => 'ipworld.info',
                ],
                'verify' => false
            ]);

            $data = json_decode($response->getBody(), true);
            if (!empty($data['quote'])) {
                return ['quote' => $data['quote']];
            } else {
                throw new \Exception('Quote not found for the specified topic');
            }
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $message = json_decode($response->getBody()->getContents(), true)['message'];

            throw new \Exception("Error fetching quote data: $message", $statusCode);
        } catch (\Exception $e) {
            throw new \Exception('Error fetching quote data: ' . $e->getMessage(), 500);
        }
    }

    public function getQuotesByCategory($categoryName)
    {
        try {
            $response = $this->client->request('GET', "https://{$this->apiHost}/category/{$categoryName}", [
                'headers' => [
                    'x-rapidapi-host' => $this->apiHost,
                    'x-rapidapi-key' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $message = json_decode($response->getBody()->getContents(), true)['message'];

            throw new \Exception("Error fetching quotes by category: $message", $statusCode);
        } catch (\Exception $e) {
            throw new \Exception('Error fetching quotes by category: ' . $e->getMessage(), 500);
        }
    }
}

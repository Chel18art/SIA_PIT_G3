<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class WeatherService
{
    protected $client;
    protected $apiKey;
    protected $apiHost;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('WEATHERAPI_KEY', '983b4f923emsh17584e3a8986231p1fa806jsnc56a0e308f2d');
        $this->apiHost = 'open-weather13.p.rapidapi.com';
    }

    public function getWeather($city)
    {
        try {
            $response = $this->client->request('GET', "https://{$this->apiHost}/city/{$city}/EN", [
                'headers' => [
                    'x-rapidapi-host' => $this->apiHost,
                    'x-rapidapi-key' => $this->apiKey,
                ],
                'verify' => false
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['main']) && isset($data['wind']) && isset($data['sys']) && isset($data['name'])) {
                return [
                    'temperature' => $data['main']['temp'],
                    'humidity' => $data['main']['humidity'],
                    'windSpeed' => $data['wind']['speed'],
                    'country' => $data['sys']['country'],
                    'city' => $data['name']
                ];
            } else {
                throw new \Exception('Required weather data not found');
            }
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $message = json_decode($response->getBody()->getContents(), true)['message'];

            throw new \Exception("Error fetching weather data: $message", $statusCode);
        } catch (\Exception $e) {
            throw new \Exception('Error fetching weather data: ' . $e->getMessage(), 500);
        }
    }
}

<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;

class NewsService
{
    protected $client;
    protected $apiKey;
    protected $apiHost;
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->client = new Client();
        $this->apiKey = env('RAPIDAPI_KEY', '983b4f923emsh17584e3a8986231p1fa806jsnc56a0e308f2d');
        $this->apiHost = 'news67.p.rapidapi.com';
        $this->logger = $logger;
    }

    public function getNews($category = 'news')
    {
        try {
            $response = $this->client->request('GET', "https://news67.p.rapidapi.com/v2/trending", [
                'headers' => [
                    'x-rapidapi-host' => $this->apiHost,
                    'x-rapidapi-key' => $this->apiKey,
                ],
                'query' => [
                    'category' => $category,
                    'country' => 'us',
                    'language' => 'en',
                    'pageSize' => 10,
                ],
                'verify' => false
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody()->getContents(), true);

            // Log the entire response body for debugging
            $this->logger->error('API response error', ['body' => $responseBody]);

            $message = isset($responseBody['message']) ? $responseBody['message'] : 'An error occurred';

            throw new \Exception("Error fetching news data: $message", $statusCode);
        } catch (\Exception $e) {
            throw new \Exception('Error fetching news data: ' . $e->getMessage(), 500);
        }
    }
}

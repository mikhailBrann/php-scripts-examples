<?php
namespace Bin\Classes;

class CloudClass
{
    private $token = false;
    private $diskType = false;
    private $url = false;

    function __construct($diskType, $url, $urlModificate='?path=')
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '../../..');
        $dotenv->load();

        if (!empty($_ENV["TOKEN"])) {
            $this->token = $_ENV["TOKEN"];
        }

        $this->diskType = $diskType;
        $this->url = $url . $urlModificate;
    }

    protected function diskRequest($path='%2F', $method="GET", $body=false, $contentType='application/json')
    {
        $response = false;
        $guzzle = new \GuzzleHttp\Client();

        try {

            if ($body) {
                $response = $guzzle->request($method, $this->url . $path, [
                    'headers' => [
                        'Authorization' => $this->token
                    ],
                    'body' => $body
                ]);
            } else {
                $response = $guzzle->request($method, $this->url . $path, [
                    'headers' => [
                        'Authorization' => $this->token
                    ],
                ]);
            }

            return [
                'status' => $response->getStatusCode(),
                'data' => $response->getBody()->getContents()
            ];

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();

            return [
                'status' => $response->getStatusCode(),
                'error' => $response->getBody()->getContents()
            ];
        }
    }

    protected function sendFile($url, $method, $pathToFile)
    {
        $response = false;
        $guzzle = new \GuzzleHttp\Client();

        try {
            $file =  fopen($pathToFile,'r');
            $response = $guzzle->request($method, $url, [
                'body' => $file,
            ]);

            return [
                'status' => $response->getStatusCode(),
                'data' => $response->getBody()->getContents()
            ];

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();

            return [
                'status' => $response->getStatusCode(),
                'error' => $response->getBody()->getContents()
            ];
        }
    }
}
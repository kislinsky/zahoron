<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ZvonokService
{
    protected $client;
    protected $apiKey;
    protected $campaignId;

    public function __construct()
    {
        $this->apiKey = env('ZVONOK_PUBLIC_KEY');
        $this->campaignId = env('CAMPAIGN_ID');
        $this->client = new Client();
    }

    public function addCall($phoneNumber, $code)
    {
        if (env('API_WORK') == 'true') {
            try {
                // Сначала делаем запрос на инициализацию звонка с кодом
                $tellCodeUrl = 'https://zvonok.com/manager/cabapi_external/api/v1/phones/tellcode/' . '?' . http_build_query([
                    'public_key' => $this->apiKey,
                    'phone' => normalizePhone($phoneNumber),
                    'campaign_id' => $this->campaignId,
                    'pincode' => $code,
                ]);

                $response = $this->client->get($tellCodeUrl);
                $tellCodeResult = json_decode($response->getBody()->getContents(), true);

                // Затем проверяем статус звонка
                $statusUrl = 'https://zvonok.com/manager/cabapi_external/api/v1/phones/calls_by_phone/?' . http_build_query([
                    'campaign_id' => $this->campaignId,
                    'phone' => normalizePhone($phoneNumber),
                    'public_key' => $this->apiKey,
                ]);

                $statusResponse = $this->client->get($statusUrl);
                $statusResult = json_decode($statusResponse->getBody()->getContents(), true);

                return [
                    'tell_code_result' => $tellCodeResult,
                    'call_status' => $statusResult,
                ];

            } catch (RequestException $e) {
                return [
                    'error' => true,
                    'message' => $e->getMessage(),
                ];
            }
        }
        
        return [
            'error' => true,
            'message' => 'API is disabled',
        ];
    }
}

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
        if(env('API_WORK')=='true'){
            try {
                $url = 'https://zvonok.com/manager/cabapi_external/api/v1/phones/tellcode/' . '?' . http_build_query([
                    'public_key' => $this->apiKey,
                    'phone' => normalizePhone($phoneNumber),
                    'campaign_id' => $this->campaignId,
                    'pincode'=> $code,
                ]);
    
                $response = $this->client->get($url);
    
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                return [
                    'error' => true,
                    'message' => $e->getMessage(),
                ];
            }
        }
        
    }
}

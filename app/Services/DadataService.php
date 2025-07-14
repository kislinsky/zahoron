<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class DadataService
{
    private $token;
    private $secret;
    private $suggest_url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs";

    public function __construct()
    {
        $this->token = env('DADATA_API_KEY');
        $this->secret = env('DADATA_SECRET_KEY');
        
        if (empty($this->token) || empty($this->secret)) {
            throw new Exception('Dadata credentials not configured');
        }
    }

    /**
     * Получить координаты по адресу
     * 
     * @param string $address Адрес для поиска
     * @param int $count Количество возвращаемых результатов (по умолчанию 1)
     * @return array|null Массив с координатами или null, если адрес не найден
     * @throws Exception
     */
    public function getCoordinatesByAddress(string $address, int $count = 1): ?array
    {
        $url = $this->suggest_url . "/suggest/address";
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Token " . $this->token,
            'X-Secret' => $this->secret,
        ])->post($url, [
            'query' => $address,
            'count' => $count,
            'language' => 'ru',
        ]);

        if ($response->status() === 429) {
            throw new Exception('Too many requests to Dadata API');
        }

        if (!$response->successful()) {
            throw new Exception('Dadata API request failed: ' . $response->body());
        }

        $data = $response->json();

        if (empty($data['suggestions'])) {
            return null;
        }

        // Возвращаем первый результат с координатами
        $firstSuggestion = $data['suggestions'][0];
        
        return [
            'lat' => $firstSuggestion['data']['geo_lat'] ?? null,
            'lon' => $firstSuggestion['data']['geo_lon'] ?? null,
            'address' => $firstSuggestion['unrestricted_value'] ?? $address,
            'precision' => $firstSuggestion['data']['qc_geo'] ?? null,
        ];
    }
}
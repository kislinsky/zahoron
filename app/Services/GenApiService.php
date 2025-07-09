<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenApiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.gen-api.ru/api/v1';
    
    public function __construct()
    {
        $this->apiKey = env('GEN_API_API_KEY');
        
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Gen-API API key is not configured');
        }
    }
    
    /**
     * Отправить запрос к нейросети
     */
    public function queryNetwork(string $networkId, array $messages, array $params = []): array
    {
        try {
            // Валидация структуры messages
            $validatedMessages = array_map(function($message) {
                if (!isset($message['role']) || !in_array($message['role'], ['system', 'user', 'assistant'])) {
                    throw new \InvalidArgumentException(
                        "Each message must have a 'role' (system, user or assistant)"
                    );
                }
                if (!isset($message['content'])) {
                    throw new \InvalidArgumentException(
                        "Each message must have a 'content' field"
                    );
                }
                return [
                    'role' => $message['role'],
                    'content' => $message['content']
                ];
            }, $messages);

            $data = array_merge([
                'messages' => $validatedMessages,
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ], $params);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post("{$this->baseUrl}/networks/{$networkId}", $data);

            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? $response->body();
                throw new \Exception("API Error {$response->status()}: {$errorMessage}");
            }

            return $response->json();
            
        } catch (\Exception $e) {
            Log::error('GenAPI Error: ' . $e->getMessage(), [
                'networkId' => $networkId,
                'params' => $params
            ]);
            throw $e;
        }
    }
}
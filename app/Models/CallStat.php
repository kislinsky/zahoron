<?php

namespace App\Models;

use App\Models\Edge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'uid',
        'ga_cid',
        'ya_cid',
        'rs_cid',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'country_code',
        'region_code',
        'city',
        'device',
        'ip',
        'url',
        'first_url',
        'custom_params',
        'is_duplicate',
        'is_quality',
        'is_new',
        'call_id',
        'webhook_type',
        'last_group',
        'record_url',
        'date_start',
        'caller_number',
        'call_type',
        'date_end',
        'call_status',
        'duration',
        'number_hash',
        'wait_time',
        'called_number'
    ];

    protected $casts = [
        'date_start' => 'datetime',
        'date_end' => 'datetime',
        'is_duplicate' => 'boolean',
        'is_quality' => 'boolean',
        'is_new' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

   public static function callback(Request $request)
    {
        // Валидация входящих данных
        $validated = $request->validate([
            'callId'=> 'nullable',
            // Параметры коллтрекинга
            'uid' => 'nullable|string',
            'gaCid' => 'nullable|string',
            'yaCid' => 'nullable|string',
            'rsCid' => 'nullable|string',
            'utmSource' => 'nullable|string',
            'utmMedium' => 'nullable|string',
            'utmCampaign' => 'nullable|string',
            'utmContent' => 'nullable|string',
            'utmTerm' => 'nullable|string',
            
            // Гео данные
            'countryCode' => 'nullable|string',
            'regionCode' => 'nullable|string',
            'city' => 'nullable|string',
            
            // Данные устройства
            'device' => 'nullable|string|in:desktop,tablet,mobile',
            'ip' => 'nullable|ip',
            
            // URL данные
            'url' => 'nullable|url',
            'firstUrl' => 'nullable|url',
            'customParam' => 'nullable|string',
            
            // Флаги
            'isDuplicate' => 'nullable|boolean',
            'isQuality' => 'nullable|boolean',
            'isNew' => 'nullable|boolean',
            
            // Данные звонка
            'webhookType' => 'nullable|string',
            'lastGroup' => 'nullable|string',
            'recordUrl' => 'nullable|url',
            'dateStart' => 'nullable|date',
            'callerNumber' => 'nullable|string',
            'callType' => 'nullable|string',
            'dateEnd' => 'nullable|date',
            'callStatus' => 'nullable|string',
            'duration' => 'nullable|integer',
            'numberHash' => 'nullable|string',
            'waitTime' => 'nullable|integer',
            'number' => 'nullable|string',
        ]);

        try {
            // Определяем organization_id из различных источников
            $organizationId = self::extractOrganizationId($validated);

            if($organizationId!=null){
                self::updateLimitCalls($organizationId);
                if(str_starts_with($validated['call_status'], '11')){
                    $organization=Organization::find($organizationId);
                    sendSms($organization->phone,'Вам поступал звонок с сайта zahoron.ru');
                }
            }

            // Обрабатываем запись звонка, если есть URL
            $localRecordPath = null;
            if (!empty($validated['recordUrl'])) {
                $localRecordPath = self::downloadCallRecord($validated['recordUrl']);
            }

            // Создаем запись о звонке
            $callStat = self::create([
                'call_id' => $validated['callId'] ?? null,
                'organization_id' => $organizationId,
                
                // Параметры коллтрекинга
                'uid' => $validated['uid'] ?? null,
                'ga_cid' => $validated['gaCid'] ?? null,
                'ya_cid' => $validated['yaCid'] ?? null,
                'rs_cid' => $validated['rsCid'] ?? null,
                'utm_source' => $validated['utmSource'] ?? null,
                'utm_medium' => $validated['utmMedium'] ?? null,
                'utm_campaign' => $validated['utmCampaign'] ?? null,
                'utm_content' => $validated['utmContent'] ?? null,
                'utm_term' => $validated['utmTerm'] ?? null,
                
                // Гео данные
                'country_code' => $validated['countryCode'] ?? null,
                'region_code' => $validated['regionCode'] ?? null,
                'city' => $validated['city'] ?? null,
                
                // Данные устройства
                'device' => $validated['device'] ?? null,
                'ip' => $validated['ip'] ?? null,
                
                // URL данные
                'url' => $validated['url'] ?? null,
                'first_url' => $validated['firstUrl'] ?? null,
                'custom_params' => $validated['customParam'] ?? null,
                
                // Флаги
                'is_duplicate' => $validated['isDuplicate'] ?? false,
                'is_quality' => $validated['isQuality'] ?? false,
                'is_new' => $validated['isNew'] ?? false,
                
                // Данные звонка
                'webhook_type' => $validated['webhookType'] ?? null,
                'last_group' => $validated['lastGroup'] ?? null,
                'record_url' => $localRecordPath,
                'date_start' => $validated['dateStart'] ?? null,
                'caller_number' => $validated['callerNumber'] ?? null,
                'call_type' => $validated['callType'] ?? null,
                'date_end' => $validated['dateEnd'] ?? null,
                'call_status' => $validated['callStatus'] ?? null,
                'duration' => $validated['duration'] ?? null,
                'number_hash' => $validated['numberHash'] ?? null,
                'wait_time' => $validated['waitTime'] ?? null,
                'called_number' => $validated['number'] ?? null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Call stat saved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Call stat save error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save call stat'
            ], 500);
        }
    }

    /**
     * Скачивает запись звонка по URL и сохраняет локально
     */
    private static function downloadCallRecord(string $recordUrl): ?string
{
    try {
        $storagePath = storage_path('app/public/files_calls');
        
        // Создаем директорию с проверкой прав
        if (!file_exists($storagePath)) {
            if (!mkdir($storagePath, 0755, true) && !is_dir($storagePath)) {
                Log::error('Failed to create directory', ['path' => $storagePath]);
                return null;
            }
        }

        // Проверяем права на запись
        if (!is_writable($storagePath)) {
            Log::error('Directory not writable', ['path' => $storagePath]);
            return null;
        }

        // Получаем расширение файла
        $urlPath = parse_url($recordUrl, PHP_URL_PATH);
        $extension = pathinfo($urlPath, PATHINFO_EXTENSION);
        
        if (empty($extension)) {
            $extension = self::detectFileExtension($recordUrl) ?? 'mp3';
        }

        $filename = 'call_record_' . uniqid() . '_' . time() . '.' . $extension;
        $filePath = $storagePath . DIRECTORY_SEPARATOR . $filename;

        // Скачиваем файл
        $client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);

        $response = $client->get($recordUrl, [
            'sink' => $filePath,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ],
        ]);

        // Проверяем успешность
        if ($response->getStatusCode() === 200 && 
            file_exists($filePath) && 
            filesize($filePath) > 0) {
            
            Log::info('Call record downloaded successfully', [
                'url' => $recordUrl,
                'local_path' => $filePath,
                'file_size' => filesize($filePath)
            ]);
            
            return 'files_calls/' . $filename;
        }

        // Если файл пустой или не существует, удаляем его
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return null;

    } catch (\Exception $e) {
        Log::error('Failed to download call record', [
            'url' => $recordUrl,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return null;
    }
}

private static function detectFileExtension(string $url): ?string
{
    try {
        $client = new \GuzzleHttp\Client();
        $response = $client->head($url);
        $contentType = $response->getHeaderLine('Content-Type');
        
        $mimeToExt = [
            'audio/mpeg' => 'mp3',
            'audio/wav' => 'wav',
            'audio/ogg' => 'ogg',
            'audio/x-wav' => 'wav',
        ];
        
        return $mimeToExt[$contentType] ?? null;
    } catch (\Exception $e) {
        return null;
    }
}

    /**
     * Извлекает organization_id из различных источников
     */
    protected static function extractOrganizationId(array $validated): ?int
    {
        // 1. Проверяем, если organization_id уже передан напрямую
        if (!empty($validated['organization_id'])) {
            return (int) $validated['organization_id'];
        }

        // 2. Проверяем customParam на наличие organization_id
        if (!empty($validated['customParam'])) {
            $organizationIdFromCustomParam = self::extractOrganizationIdFromCustomParam($validated['customParam']);
            if ($organizationIdFromCustomParam) {
                return $organizationIdFromCustomParam;
            }
        }

        // 3. Проверяем URL на наличие slug организации
        if (!empty($validated['url'])) {
            $organizationIdFromUrl = self::extractOrganizationIdFromUrl($validated['url']);
            if ($organizationIdFromUrl) {
                return $organizationIdFromUrl;
            }
        }

        // 4. Если ничего не найдено, возвращаем null
        return null;
    }

    /**
     * Извлекает organization_id из customParam
     */
    protected static function extractOrganizationIdFromCustomParam(?string $customParam): ?int
    {
        if (empty($customParam)) {
            return null;
        }

        // Ищем паттерн organization_id=число
        if (preg_match('/organization_id=(\d+)/', $customParam, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Извлекает organization_id из URL путем поиска по slug
     */
    protected static function extractOrganizationIdFromUrl(?string $url): ?int
    {
        if (empty($url)) {
            return null;
        }

        // Парсим URL
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['path'])) {
            return null;
        }

        // Разбиваем путь на части
        $pathParts = explode('/', trim($parsedUrl['path'], '/'));
        
        $organizationIndex = array_search('organization', $pathParts);
        
        if ($organizationIndex !== false && isset($pathParts[$organizationIndex + 1])) {
            $slug = $pathParts[$organizationIndex + 1];
            
            // Ищем организацию по slug
            $organization = Organization::where('slug', $slug)->first();
            
            if ($organization) {
                return $organization->id;
            }
        }

        return null;
    }

    protected static function updateLimitCalls($organizationId){
        $organization=Organization::find($organizationId);

        if($organization->user!=null && $organization->user->app_organization==1){
            return true;
        }
        elseif($organization->calls=='unlimited'){
            return true;
        }
        elseif($organization->calls>0){
            $organization->update(['calls'=>$organization->calls-1]);
        }
        
    }


    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($call) {

            if($call->organization_id!=null){
                // Для организации о новом звонке
                Notification::create([
                    'user_id' => null,
                    'organization_id' => $call->organization_id,
                    'type' => 'call',
                    'title' => 'Новый звонок',
                    'message' => "Зафиксирован новый входящий звонок",
                    'is_read' => false
                ]);
            }
        });
    }

}
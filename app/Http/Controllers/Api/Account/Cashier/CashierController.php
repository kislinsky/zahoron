<?php

namespace App\Http\Controllers\Api\Account\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CallStat;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Mortuary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function getCemeteries(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user->organizationBranch) {
            return response()->json(['data' => []]);
        }

        $cemeteryIds = json_decode($user->organizationBranch->cemetery_ids, true) ?? [];
        
        if (empty($cemeteryIds)) {
            return response()->json(['data' => []]);
        }

        $cemeteries = Cemetery::whereIn('id', $cemeteryIds)
            ->get(['id', 'title', 'city_id', 'adres'])
            ->map(function ($cemetery) {
                return [
                    'id' => (string)$cemetery->id,
                    'title' => $cemetery->title,
                    'city_id' => (string)$cemetery->city_id,
                    'adres' => $cemetery->adres
                ];
            });

        return response()->json(['data' => $cemeteries]);
    }

    public function getMorgues(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user->organizationBranch) {
            return response()->json(['data' => []]);
        }

        // Получаем area_id из города организации пользователя
        $organizationCity = $user->organizationBranch->city;
        
        if (!$organizationCity || !$organizationCity->area) {
            return response()->json(['data' => []]);
        }

        $areaId = $organizationCity->area->id;

        // Получаем все города в этом районе
        $citiesInArea = City::where('area_id', $areaId)
            ->pluck('id')
            ->toArray();

        if (empty($citiesInArea)) {
            return response()->json(['data' => []]);
        }

        // Получаем морги в этих городах
        $morgues = Mortuary::whereIn('city_id', $citiesInArea)
            ->get(['id', 'name', 'city_id', 'adres'])
            ->map(function ($morgue) {
                return [
                    'id' => (string)$morgue->id,
                    'name' => $morgue->name,
                    'city_id' => (string)$morgue->city_id,
                    'adres' => $morgue->adres
                ];
            });

        return response()->json(['data' => $morgues]);
    }

    public function getCallStats(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user->organizationBranch) {
            return response()->json(['data' => [], 'meta' => [
                'total_calls' => 0,
                'avg_duration' => 0
            ]]);
        }

        $organizationId = $user->organizationBranch->organization_id;
        
        // Базовый запрос
        $query = CallStat::where('organization_id', $organizationId)
            ->with(['city' => function($query) {
                $query->select('id', 'name as city_name');
            }])
            ->select([
                'id',
                'city_id',
                'call_status',
                'duration',
                'created_at'
            ]);

        // Фильтрация по дате
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Получаем статистику
        $callStats = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($call) {
                return [
                    'id' => (string)$call->id,
                    'city' => $call->city ? $call->city->city_name : 'Не указан',
                    'call_status' => $call->call_status,
                    'duration' => $call->duration,
                    'created_at' => $call->created_at
                ];
            });

        // Получаем агрегированные данные для мета информации
        $aggregatedData = CallStat::where('organization_id', $organizationId)
            ->select([
                DB::raw('COUNT(*) as total_calls'),
                DB::raw('AVG(duration) as avg_duration')
            ])
            ->first();

        return response()->json([
            'data' => $callStats,
            'meta' => [
                'total_calls' => (int) $aggregatedData->total_calls,
                'avg_duration' => round((float) $aggregatedData->avg_duration, 2)
            ]
        ]);
    }

     public function getCemetery($id)
    {
        try {
            $cemetery = Cemetery::with('city')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'title' => $cemetery->title,
                    'phone' => $cemetery->phone,
                    'address' => $this->formatCemeteryAddress($cemetery)
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Кладбище не найдено'
            ], 404);
        }
    }

    /**
     * Получение морга по ID
     */
    public function getMortuary($id)
    {
        try {
            $mortuary = Mortuary::with('city')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'title' => $mortuary->title,
                    'phone' => $mortuary->phone,
                    'address' => $this->formatMortuaryAddress($mortuary),
                    'working_hours' => $mortuary->workingHours
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Морг не найден'
            ], 404);
        }
    }

    /**
     * Форматирование адреса кладбища
     */
    private function formatCemeteryAddress(Cemetery $cemetery)
    {
        $addressParts = [];
        
        if ($cemetery->adres) {
            $addressParts[] = $cemetery->adres;
        }
        
        if ($cemetery->city && $cemetery->city->title) {
            $addressParts[] = $cemetery->city->title;
        }
        
        if ($cemetery->city && $cemetery->city->area) {
            $addressParts[] = $cemetery->city->area->title;
        }
        
        if ($cemetery->city && $cemetery->city->edge) {
            $addressParts[] = $cemetery->city->edge->title;
        }
        
        $addressParts[] = 'Россия';
        
        return implode(', ', $addressParts);
    }

    /**
     * Форматирование адреса морга
     */
    private function formatMortuaryAddress(Mortuary $mortuary)
    {
        $addressParts = [];
        
        if ($mortuary->adres) {
            $addressParts[] = $mortuary->adres;
        }
        
        if ($mortuary->city && $mortuary->city->title) {
            $addressParts[] = $mortuary->city->title;
        }
        
        if ($mortuary->city && $mortuary->city->area) {
            $addressParts[] = $mortuary->city->area->title;
        }
        
        if ($mortuary->city && $mortuary->city->edge) {
            $addressParts[] = $mortuary->city->edge->title;
        }
        
        $addressParts[] = 'Россия';
        
        return implode(', ', $addressParts);
    }
}
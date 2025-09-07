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
            ->get(['id', 'title', 'city_id', 'adres']);

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
            ->get(['id', 'name', 'city_id', 'adres']);

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
                    'id' => $call->id,
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

}
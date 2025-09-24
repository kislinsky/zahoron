<?php

namespace App\Http\Controllers\Api\Account\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CallStat;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Mortuary;
use App\Models\OrderProduct;
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

    // Используем explode вместо json_decode для строки с разделителями-запятыми
    $cemeteryIds = explode(',', $user->organizationBranch->cemetery_ids);
    
    // Фильтруем пустые значения и преобразуем к числам
    $cemeteryIds = array_filter(array_map('intval', $cemeteryIds));
    
    if (empty($cemeteryIds)) {
        return response()->json(['data' => []]);
    }

    $cemeteries = Cemetery::whereIn('id', $cemeteryIds)
        ->get(['id', 'title', 'city_id', 'adres', 'width', 'longitude'])
        ->map(function ($cemetery) {
            return [
                'id' => (string)$cemetery->id,
                'title' => $cemetery->title,
                'city_id' => (string)$cemetery->city_id,
                'adres' => $cemetery->adres,
                'width' => $cemetery->width ? (string)$cemetery->width : null,
                'longitude' => $cemetery->longitude ? (string)$cemetery->longitude : null
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
        ->get(['id', 'title', 'city_id', 'adres', 'width', 'longitude'])
        ->map(function ($morgue) {
            return [
                'id' => (string)$morgue->id,
                'title' => $morgue->title,
                'city_id' => (string)$morgue->city_id,
                'adres' => $morgue->adres,
                'width' => $morgue->width ? (string)$morgue->width : null,
                'longitude' => $morgue->longitude ? (string)$morgue->longitude : null
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
        
        $query = CallStat::where('organization_id', $organizationId)
    ->leftJoin('cities', 'cities.title', '=', 'call_stats.city') // Предполагаем, что есть поле city_name
    ->select([
        'call_stats.id',
        'cities.id as city_id', // Получаем ID из таблицы cities
        'call_stats.call_status',
        'call_stats.duration',
        'call_stats.created_at',
        'cities.title as city'
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
                    'city' => $call->city ?? 'Не указан',
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



    public static function orderProducts(Request $request)
    {
        $user = Auth::user();
        
        // Получаем заказы через связь пользователя с организацией
        $orders = OrderProduct::with([
                'product.getImages' => function($query) {
                    $query->orderBy('created_at', 'asc')->limit(1);
                },
                'cemetery',
                'city',
                'mortuary',
                'user'
            ])
            ->whereHas('user.organizationBranch', function($query) use ($user) {
                $query->where('organization_id', $user->organizationBranch->organization_id);
            })
            ->when($request->has('category_id'), function($query) use ($request) {
                $query->whereHas('product', function($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            })
            ->when($request->has('subcategory_id'), function($query) use ($request) {
                $query->whereHas('product', function($q) use ($request) {
                    $q->where('category_parent_id', $request->subcategory_id);
                });
            })
            ->when($request->has('status'), function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Преобразуем данные для ответа
        $result = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'created_at' => $order->created_at,
                'product' => [
                    'id' => $order->product->id,
                    'title' => $order->product->title,
                    'first_image' => $order->product->getImages->first() ? 
                        $order->product->getImages->first()->title : null
                ],
                'cemetery' => $order->cemetery ? $order->cemetery->title : null,
                'city' => $order->city ? $order->city->title : null,
                'mortuary' => $order->mortuary ? $order->mortuary->title : null,
                'customer' => $order->user->name,
                'count' => $order->count,
                'price' => $order->price,
                'status' => $order->status,
                'date' => $order->date,
                'time' => $order->time,
                'customer_comment' => $order->customer_comment,
                'additional' => $order->additionals()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result,
            'total' => $orders->count()
        ]);
    }
}
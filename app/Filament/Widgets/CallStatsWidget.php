<?php

namespace App\Filament\Widgets;

use App\Models\CallStat;
use App\Models\Organization;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\DB;

class CallStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            // Всего звонков
            Stat::make('Всего звонков', CallStat::count())
                ->description('За все время')
                ->descriptionIcon('heroicon-o-phone')
                ->color('primary')
                ->chart($this->getCallsTrendData())
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition-shadow duration-300'
                ]),

            // Звонки сегодня
            Stat::make('Сегодня', CallStat::whereDate('date_start', today())->count())
                ->description($this->getTodayChange() . '% вчера')
                ->descriptionIcon($this->getTodayChange() >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($this->getTodayChange() >= 0 ? 'success' : 'danger')
                ->chart($this->getTodayHourlyData())
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition-shadow duration-300'
                ]),


          

            // Средняя длительность
            Stat::make('Средняя длительность', $this->getAverageDuration())
                ->description('среднее время разговора')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->chart($this->getDurationTrendData())
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition-shadow duration-300'
                ]),

        ];
    }

    /**
     * Получаем данные о звонках по дням (30 дней)
     */
    private function getCallsTrendData(): array
    {
        $data = Trend::model(CallStat::class)
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        return $data->map(fn (TrendValue $value) => $value->aggregate)->toArray();
    }

    /**
     * Получаем почасовые данные за сегодня
     */
    private function getTodayHourlyData(): array
    {
        $data = [];
        $currentHour = now()->hour;
        
        for ($i = max(0, $currentHour - 11); $i <= $currentHour; $i++) {
            $hour = now()->setHour($i)->startOfHour();
            $count = CallStat::whereBetween('date_start', [
                $hour,
                $hour->copy()->addHour()
            ])->count();
            
            $data[] = $count;
        }
        
        // Дополняем до 12 точек если нужно
        while (count($data) < 12) {
            array_unshift($data, 0);
        }
        
        return $data;
    }

    /**
     * Получаем данные по принятым звонкам
     */
    private function getAcceptedTrendData(): array
    {
        $data = Trend::query(
            CallStat::where('call_status', 'like', '11%')
        )
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return $data->map(fn (TrendValue $value) => $value->aggregate)->toArray();
    }

    /**
     * Получаем данные по пропущенным звонкам
     */
    private function getMissedTrendData(): array
    {
        $data = Trend::query(
            CallStat::whereNotNull('call_status')->whereNot('call_status', 'like', '11%')
        )
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return $data->map(fn (TrendValue $value) => $value->aggregate)->toArray();
    }

    /**
     * Получаем процент принятых звонков
     */
    private function getAcceptedPercentage(): string
    {
        $total = CallStat::count();
        $accepted = CallStat::where('call_status', 'like', '11%')->count();
        
        return $total > 0 ? number_format(($accepted / $total) * 100, 1) : '0.0';
    }

    /**
     * Получаем процент пропущенных звонков
     */
    private function getMissedPercentage(): string
    {
        $total = CallStat::count();
        $missed = CallStat::whereNotNull('call_status')->whereNot('call_status', 'like', '11%')->count();
        
        return $total > 0 ? number_format(($missed / $total) * 100, 1) : '0.0';
    }

    /**
     * Получаем среднюю длительность звонков
     */
    private function getAverageDuration(): string
    {
        $average = CallStat::where('duration', '>', 0)->average('duration');
        
        if (!$average) return '0:00';
        
        $minutes = floor($average / 60);
        $seconds = $average % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Получаем изменение по сравнению со вчера
     */
    private function getTodayChange(): float
    {
        $today = CallStat::whereDate('date_start', today())->count();
        $yesterday = CallStat::whereDate('date_start', today()->subDay())->count();
        
        if ($yesterday == 0) {
            return $today > 0 ? 100.0 : 0.0;
        }
        
        return round((($today - $yesterday) / $yesterday) * 100, 1);
    }

    /**
     * Получаем количество активных организаций сегодня
     */
    private function getActiveOrganizationsCount(): int
    {
        return CallStat::whereDate('date_start', today())
            ->distinct('organization_id')
            ->count('organization_id');
    }

    /**
     * Получаем данные по организациям
     */
    private function getOrganizationsTrendData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = CallStat::whereDate('date_start', $date)
                ->distinct('organization_id')
                ->count('organization_id');
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Получаем данные по длительности
     */
    private function getDurationTrendData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $average = CallStat::whereDate('date_start', $date)
                ->where('duration', '>', 0)
                ->average('duration');
            $data[] = $average ? round($average / 60, 1) : 0; // в минутах
        }
        return $data;
    }

    /**
     * Получаем топ городов по звонкам
     */
    private function getTopCities(): array
    {
        return CallStat::select('city', DB::raw('count(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'city' => $item->city ?: 'Не указан',
                'count' => $item->total,
                'percentage' => CallStat::count() > 0 
                    ? round(($item->total / CallStat::count()) * 100, 1) 
                    : 0
            ])
            ->toArray();
    }

    /**
     * Получаем распределение по устройствам
     */
    private function getDeviceDistribution(): array
    {
        return CallStat::select('device', DB::raw('count(*) as total'))
            ->whereNotNull('device')
            ->groupBy('device')
            ->orderByDesc('total')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->device => [
                    'count' => $item->total,
                    'percentage' => CallStat::count() > 0 
                        ? round(($item->total / CallStat::count()) * 100, 1) 
                        : 0,
                    'color' => match($item->device) {
                        'desktop' => 'blue',
                        'mobile' => 'green',
                        'tablet' => 'yellow',
                        default => 'gray'
                    }
                ]
            ])
            ->toArray();
    }

    /**
     * Получаем статистику по времени суток
     */
    private function getTimeOfDayStats(): array
    {
        $morning = CallStat::whereTime('date_start', '>=', '06:00:00')
            ->whereTime('date_start', '<', '12:00:00')
            ->count();
        
        $afternoon = CallStat::whereTime('date_start', '>=', '12:00:00')
            ->whereTime('date_start', '<', '18:00:00')
            ->count();
        
        $evening = CallStat::whereTime('date_start', '>=', '18:00:00')
            ->whereTime('date_start', '<', '24:00:00')
            ->count();
        
        $night = CallStat::whereTime('date_start', '>=', '00:00:00')
            ->whereTime('date_start', '<', '06:00:00')
            ->count();
        
        $total = $morning + $afternoon + $evening + $night;
        
        return [
            'morning' => [
                'count' => $morning,
                'percentage' => $total > 0 ? round(($morning / $total) * 100, 1) : 0,
                'label' => 'Утро (6:00-12:00)'
            ],
            'afternoon' => [
                'count' => $afternoon,
                'percentage' => $total > 0 ? round(($afternoon / $total) * 100, 1) : 0,
                'label' => 'День (12:00-18:00)'
            ],
            'evening' => [
                'count' => $evening,
                'percentage' => $total > 0 ? round(($evening / $total) * 100, 1) : 0,
                'label' => 'Вечер (18:00-24:00)'
            ],
            'night' => [
                'count' => $night,
                'percentage' => $total > 0 ? round(($night / $total) * 100, 1) : 0,
                'label' => 'Ночь (0:00-6:00)'
            ]
        ];
    }

    /**
     * Получаем статистику по UTM источникам
     */
    private function getUtmSourceStats(): array
    {
        return CallStat::select('utm_source', DB::raw('count(*) as total'))
            ->whereNotNull('utm_source')
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(fn ($item) => [
                'source' => $item->utm_source,
                'count' => $item->total,
                'percentage' => CallStat::count() > 0 
                    ? round(($item->total / CallStat::count()) * 100, 1) 
                    : 0
            ])
            ->toArray();
    }

    /**
     * Получаем статистику по качественным звонкам
     */
    private function getQualityStats(): array
    {
        $total = CallStat::count();
        $quality = CallStat::where('is_quality', true)->count();
        
        return [
            'quality' => $quality,
            'non_quality' => $total - $quality,
            'quality_percentage' => $total > 0 ? round(($quality / $total) * 100, 1) : 0
        ];
    }

    /**
     * Получаем статистику по типам звонков
     */
    private function getCallTypeStats(): array
    {
        return CallStat::select('call_type', DB::raw('count(*) as total'))
            ->whereNotNull('call_type')
            ->groupBy('call_type')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->call_type => [
                    'count' => $item->total,
                    'label' => match($item->call_type) {
                        '1' => 'Динамический',
                        '2' => 'Статический',
                        '3' => 'Дефолтный',
                        default => 'Неизвестный'
                    },
                    'color' => match($item->call_type) {
                        '1' => 'blue',
                        '2' => 'green',
                        '3' => 'purple',
                        default => 'gray'
                    }
                ]
            ])
            ->toArray();
    }

    /**
     * Получаем последние звонки
     */
    private function getRecentCalls(int $limit = 5): array
    {
        return CallStat::with('organization')
            ->latest('date_start')
            ->limit($limit)
            ->get()
            ->map(fn ($call) => [
                'id' => $call->id,
                'caller_number' => $this->formatPhoneNumber($call->caller_number),
                'city' => $call->city,
                'duration' => $call->duration ? gmdate('i:s', $call->duration) : '0:00',
                'status' => $call->call_status,
                'status_color' => str_starts_with($call->call_status, '11') ? 'success' : 'danger',
                'organization' => $call->organization?->title ?? 'Не указано',
                'time_ago' => $call->date_start ? $call->date_start->diffForHumans() : '',
                'has_recording' => !empty($call->record_url),
                'is_quality' => $call->is_quality,
            ])
            ->toArray();
    }

    /**
     * Форматируем номер телефона
     */
    private function formatPhoneNumber(?string $number): string
    {
        if (!$number) return 'Не указан';
        
        // Убираем все нецифровые символы
        $cleaned = preg_replace('/\D/', '', $number);
        
        if (strlen($cleaned) === 11 && $cleaned[0] === '7') {
            return '+7 ' . substr($cleaned, 1, 3) . ' ' . substr($cleaned, 4, 3) . ' ' . 
                   substr($cleaned, 7, 2) . ' ' . substr($cleaned, 9, 2);
        }
        
        if (strlen($cleaned) === 10) {
            return '+7 ' . substr($cleaned, 0, 3) . ' ' . substr($cleaned, 3, 3) . ' ' . 
                   substr($cleaned, 6, 2) . ' ' . substr($cleaned, 8, 2);
        }
        
        return $number;
    }

    /**
     * Получаем данные для графика распределения по часам
     */
    private function getHourlyDistributionData(): array
    {
        $data = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $count = CallStat::whereRaw('HOUR(date_start) = ?', [$hour])->count();
            $data[] = [
                'hour' => sprintf('%02d:00', $hour),
                'count' => $count
            ];
        }
        return $data;
    }

    /**
     * Получаем статистику по географии
     */
    private function getGeographyStats(): array
    {
        return CallStat::select('city', 'country_code', DB::raw('count(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city', 'country_code')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'city' => $item->city,
                'country' => $item->country_code ?: 'Не указано',
                'count' => $item->total,
                'flag' => $this->getCountryFlag($item->country_code)
            ])
            ->toArray();
    }

    /**
     * Получаем флаг страны по коду
     */
    private function getCountryFlag(?string $code): string
    {
        if (!$code) return '🌐';
        
        $flags = [
            'RU' => '🇷🇺',
            'UA' => '🇺🇦',
            'KZ' => '🇰🇿',
            'BY' => '🇧🇾',
            'US' => '🇺🇸',
            'DE' => '🇩🇪',
            'GB' => '🇬🇧',
            'FR' => '🇫🇷',
            'CN' => '🇨🇳',
            'JP' => '🇯🇵',
        ];
        
        return $flags[strtoupper($code)] ?? '🌐';
    }

    /**
     * Получаем ключевые метрики для дашборда
     */
    public function getKeyMetrics(): array
    {
        return [
            'total_calls' => CallStat::count(),
            'today_calls' => CallStat::whereDate('date_start', today())->count(),
            'avg_duration' => $this->getAverageDuration(),
            'acceptance_rate' => $this->getAcceptedPercentage(),
            'top_city' => CallStat::select('city')
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderByRaw('COUNT(*) DESC')
                ->value('city') ?? 'Не указан',
            'busiest_hour' => $this->getBusiestHour(),
            'quality_calls' => CallStat::where('is_quality', true)->count(),
            'unique_cities' => CallStat::whereNotNull('city')->distinct('city')->count('city'),
        ];
    }

    /**
     * Определяем самый загруженный час
     */
    private function getBusiestHour(): string
    {
        $busiest = CallStat::selectRaw('HOUR(date_start) as hour, COUNT(*) as count')
            ->whereNotNull('date_start')
            ->groupByRaw('HOUR(date_start)')
            ->orderByDesc('count')
            ->first();
        
        return $busiest ? sprintf('%02d:00', $busiest->hour) : 'Не определен';
    }

    /**
     * Получаем прогноз на сегодня
     */
    public function getTodayForecast(): array
    {
        $currentHour = now()->hour;
        $callsSoFar = CallStat::whereDate('date_start', today())->count();
        
        if ($currentHour < 1) return ['forecast' => 0, 'confidence' => 'low'];
        
        $avgPerHour = $callsSoFar / $currentHour;
        $hoursLeft = 24 - $currentHour;
        $forecast = round($callsSoFar + ($avgPerHour * $hoursLeft));
        
        $confidence = match(true) {
            $currentHour >= 18 => 'high',
            $currentHour >= 12 => 'medium',
            default => 'low'
        };
        
        return [
            'forecast' => $forecast,
            'confidence' => $confidence,
            'current' => $callsSoFar,
            'avg_per_hour' => round($avgPerHour, 1)
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
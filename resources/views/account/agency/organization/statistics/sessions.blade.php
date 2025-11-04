@extends('account.agency.components.page')
@section('title', "Статистика визитов")

@section('content')
    <div class="title_middle_black_bold">Визиты на страницу компании</div>

    <div class="statistics_sessions">
        <div class="block_statistics_session">
            <div class="text_black">За 12 месяцев</div>
            <div class="count_sessions">
                <div class="title_middle_black_bold">{{ $statistics['year']['count'] ?? 0 }}</div>
                <div class="procent_sessions {{ $statistics['year']['trend'] ?? 'up' }}">
                    {{ ($statistics['year']['trend'] ?? 'up') == 'up' ? '+' : '' }}{{ $statistics['year']['percentage'] ?? 0 }}%
                    <img src="{{ asset('storage/uploads/Icon_up_green.svg') }}" alt="" class="up">
                    <img src="{{ asset('storage/uploads/Icon_down_red.svg') }}" alt="" class="down">
                </div>
            </div>
        </div>
        
        <div class="block_statistics_session">
            <div class="text_black">За месяц</div>
            <div class="count_sessions">
                <div class="title_middle_black_bold">{{ $statistics['month']['count'] ?? 0 }}</div>
                <div class="procent_sessions {{ $statistics['month']['trend'] ?? 'up' }}">
                    {{ ($statistics['month']['trend'] ?? 'up') == 'up' ? '+' : '' }}{{ $statistics['month']['percentage'] ?? 0 }}%
                    <img src="{{ asset('storage/uploads/Icon_up_green.svg') }}" alt="" class="up">
                    <img src="{{ asset('storage/uploads/Icon_down_red.svg') }}" alt="" class="down">
                </div>
            </div>
        </div>
        
        <div class="block_statistics_session">
            <div class="text_black">За неделю</div>
            <div class="count_sessions">
                <div class="title_middle_black_bold">{{ $statistics['week']['count'] ?? 0 }}</div>
                <div class="procent_sessions {{ $statistics['week']['trend'] ?? 'up' }}">
                    {{ ($statistics['week']['trend'] ?? 'up') == 'up' ? '+' : '' }}{{ $statistics['week']['percentage'] ?? 0 }}%
                    <img src="{{ asset('storage/uploads/Icon_up_green.svg') }}" alt="" class="up">
                    <img src="{{ asset('storage/uploads/Icon_down_red.svg') }}" alt="" class="down">
                </div>
            </div>
        </div>
        
        <div class="block_statistics_session">
            <div class="text_black">За 24 часа</div>
            <div class="count_sessions">
                <div class="title_middle_black_bold">{{ $statistics['day']['count'] ?? 0 }}</div>
                <div class="procent_sessions {{ $statistics['day']['trend'] ?? 'up' }}">
                    {{ ($statistics['day']['trend'] ?? 'up') == 'up' ? '+' : '' }}{{ $statistics['day']['percentage'] ?? 0 }}%
                    <img src="{{ asset('storage/uploads/Icon_up_green.svg') }}" alt="" class="up">
                    <img src="{{ asset('storage/uploads/Icon_down_red.svg') }}" alt="" class="down">
                </div>
            </div>
        </div>
    </div>

    <div class="chart-container">
        <canvas id="viewsChart"></canvas>
    </div>

    <div class="stats">
        <div class="stat-card total-views-stat">
            <h3>Всего просмотров</h3>
            <div class="stat-value total-views-value" id="totalViews">0</div>
            <p>За последние 12 месяцев</p>
        </div>
        <div class="stat-card unique-views-stat">
            <h3>Уникальные просмотры</h3>
            <div class="stat-value unique-views-value" id="uniqueViews">0</div>
            <p>За последние 12 месяцев</p>
        </div>
    </div>

    <!-- Новый блок для просмотров товаров -->
    <div class="title_middle_black_bold" style="margin-top: 40px;">Просмотры товаров компании</div>

    <div class="statistics_sessions">
        <div class="block_statistics_session">
            <div class="text_black">За 12 месяцев</div>
            <div class="count_sessions">
                <div class="title_middle_black_bold">{{ $productStatistics['year']['count'] ?? 0 }}</div>
                <div class="procent_sessions {{ $productStatistics['year']['trend'] ?? 'up' }}">
                    {{ ($productStatistics['year']['trend'] ?? 'up') == 'up' ? '+' : '' }}{{ $productStatistics['year']['percentage'] ?? 0 }}%
                    <img src="{{ asset('storage/uploads/Icon_up_green.svg') }}" alt="" class="up">
                    <img src="{{ asset('storage/uploads/Icon_down_red.svg') }}" alt="" class="down">
                </div>
            </div>
        </div>
        
        <div class="block_statistics_session">
            <div class="text_black">За месяц</div>
            <div class="count_sessions">
                <div class="title_middle_black_bold">{{ $productStatistics['month']['count'] ?? 0 }}</div>
                <div class="procent_sessions {{ $productStatistics['month']['trend'] ?? 'up' }}">
                    {{ ($productStatistics['month']['trend'] ?? 'up') == 'up' ? '+' : '' }}{{ $productStatistics['month']['percentage'] ?? 0 }}%
                    <img src="{{ asset('storage/uploads/Icon_up_green.svg') }}" alt="" class="up">
                    <img src="{{ asset('storage/uploads/Icon_down_red.svg') }}" alt="" class="down">
                </div>
            </div>
        </div>
        
        <div class="block_statistics_session">
            <div class="text_black">За неделю</div>
            <div class="count_sessions">
                <div class="title_middle_black_bold">{{ $productStatistics['week']['count'] ?? 0 }}</div>
                <div class="procent_sessions {{ $productStatistics['week']['trend'] ?? 'up' }}">
                    {{ ($productStatistics['week']['trend'] ?? 'up') == 'up' ? '+' : '' }}{{ $productStatistics['week']['percentage'] ?? 0 }}%
                    <img src="{{ asset('storage/uploads/Icon_up_green.svg') }}" alt="" class="up">
                    <img src="{{ asset('storage/uploads/Icon_down_red.svg') }}" alt="" class="down">
                </div>
            </div>
        </div>
        
        <div class="block_statistics_session">
            <div class="text_black">За 24 часа</div>
            <div class="count_sessions">
                <div class="title_middle_black_bold">{{ $productStatistics['day']['count'] ?? 0 }}</div>
                <div class="procent_sessions {{ $productStatistics['day']['trend'] ?? 'up' }}">
                    {{ ($productStatistics['day']['trend'] ?? 'up') == 'up' ? '+' : '' }}{{ $productStatistics['day']['percentage'] ?? 0 }}%
                    <img src="{{ asset('storage/uploads/Icon_up_green.svg') }}" alt="" class="up">
                    <img src="{{ asset('storage/uploads/Icon_down_red.svg') }}" alt="" class="down">
                </div>
            </div>
        </div>
    </div>

    <div class="chart-container">
        <canvas id="productViewsChart"></canvas>
    </div>

    <div class="stats">
        <div class="stat-card total-product-views-stat">
            <h3>Всего просмотров товаров</h3>
            <div class="stat-value total-product-views-value" id="totalProductViews">0</div>
            <p>За последние 12 месяцев</p>
        </div>
        <div class="stat-card unique-product-views-stat">
            <h3>Уникальные просмотры товаров</h3>
            <div class="stat-value unique-product-views-value" id="uniqueProductViews">0</div>
            <p>За последние 12 месяцев</p>
        </div>
    </div>

    <script>
        // Данные для графика просмотров организации из PHP
        const months = @json($chartData['labels'] ?? []);
        const totalViews = @json($chartData['total_views'] ?? []);
        const uniqueViews = @json($chartData['unique_views'] ?? []);

        // Данные для графика просмотров товаров из PHP
        const productTotalViews = @json($productChartData['total_views'] ?? []);
        const productUniqueViews = @json($productChartData['unique_views'] ?? []);

        // Расчет общих сумм для организации
        const totalViewsSum = totalViews.reduce((sum, value) => sum + value, 0);
        const uniqueViewsSum = uniqueViews.reduce((sum, value) => sum + value, 0);

        // Расчет общих сумм для товаров
        const totalProductViewsSum = productTotalViews.reduce((sum, value) => sum + value, 0);
        const uniqueProductViewsSum = productUniqueViews.reduce((sum, value) => sum + value, 0);

        // Обновление статистики организации
        document.getElementById('totalViews').textContent = totalViewsSum.toLocaleString('ru-RU');
        document.getElementById('uniqueViews').textContent = uniqueViewsSum.toLocaleString('ru-RU');

        // Обновление статистики товаров
        document.getElementById('totalProductViews').textContent = totalProductViewsSum.toLocaleString('ru-RU');
        document.getElementById('uniqueProductViews').textContent = uniqueProductViewsSum.toLocaleString('ru-RU');

        // Создание графика просмотров организации
        const ctx = document.getElementById('viewsChart').getContext('2d');
        const viewsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Все просмотры',
                        data: totalViews,
                        borderColor: '#579C00',
                        backgroundColor: 'rgba(87, 156, 0, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#579C00',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#579C00',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    },
                    {
                        label: 'Уникальные просмотры',
                        data: uniqueViews,
                        borderColor: '#0080D7',
                        backgroundColor: 'rgba(0, 128, 215, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#0080D7',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#0080D7',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                family: 'Arial'
                            },
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        titleFont: {
                            size: 14,
                            family: 'Arial'
                        },
                        bodyFont: {
                            size: 14,
                            family: 'Arial'
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const datasetLabel = context.dataset.label || '';
                                const value = context.parsed.y;
                                return `${datasetLabel}: ${value.toLocaleString('ru-RU')}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            callback: function(value) {
                                return value.toLocaleString('ru-RU');
                            }
                        },
                        title: {
                            display: true,
                            text: 'Количество просмотров',
                            font: {
                                size: 14,
                                family: 'Arial',
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Месяцы',
                            font: {
                                size: 14,
                                family: 'Arial',
                                weight: 'bold'
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                hover: {
                    animationDuration: 0
                }
            }
        });

        // Создание графика просмотров товаров
        const productCtx = document.getElementById('productViewsChart').getContext('2d');
        const productViewsChart = new Chart(productCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Все просмотры товаров',
                        data: productTotalViews,
                        borderColor: '#FF6B00',
                        backgroundColor: 'rgba(255, 107, 0, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#FF6B00',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#FF6B00',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    },
                    {
                        label: 'Уникальные просмотры товаров',
                        data: productUniqueViews,
                        borderColor: '#8E44AD',
                        backgroundColor: 'rgba(142, 68, 173, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#8E44AD',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#8E44AD',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                family: 'Arial'
                            },
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        titleFont: {
                            size: 14,
                            family: 'Arial'
                        },
                        bodyFont: {
                            size: 14,
                            family: 'Arial'
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const datasetLabel = context.dataset.label || '';
                                const value = context.parsed.y;
                                return `${datasetLabel}: ${value.toLocaleString('ru-RU')}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            },
                            callback: function(value) {
                                return value.toLocaleString('ru-RU');
                            }
                        },
                        title: {
                            display: true,
                            text: 'Количество просмотров',
                            font: {
                                size: 14,
                                family: 'Arial',
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Месяцы',
                            font: {
                                size: 14,
                                family: 'Arial',
                                weight: 'bold'
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                hover: {
                    animationDuration: 0
                }
            }
        });

        // Функции для кастомных подсказок (для обоих графиков)
        function initializeCustomTooltip(chart, chartContainer) {
            const customTooltip = document.createElement('div');
            customTooltip.className = 'tooltip';
            customTooltip.style.display = 'none';
            chartContainer.appendChild(customTooltip);

            chartContainer.addEventListener('mousemove', function(event) {
                const points = chart.getElementsAtEventForMode(
                    event, 
                    'nearest', 
                    { intersect: true }, 
                    true
                );

                if (points.length > 0) {
                    const point = points[0];
                    const datasetIndex = point.datasetIndex;
                    const index = point.index;
                    
                    const dataset = chart.data.datasets[datasetIndex];
                    const value = dataset.data[index];
                    const month = chart.data.labels[index];
                    const label = dataset.label;

                    const rect = chartContainer.getBoundingClientRect();
                    const x = event.clientX - rect.left;
                    const y = event.clientY - rect.top;

                    customTooltip.innerHTML = `
                        <strong>${month}</strong><br>
                        ${label}: ${value.toLocaleString('ru-RU')}
                    `;
                    customTooltip.style.display = 'block';
                    customTooltip.style.left = x + 'px';
                    customTooltip.style.top = y + 'px';
                } else {
                    customTooltip.style.display = 'none';
                }
            });

            chartContainer.addEventListener('mouseleave', function() {
                customTooltip.style.display = 'none';
            });
        }

        // Инициализация кастомных подсказок для обоих графиков
        initializeCustomTooltip(viewsChart, document.querySelector('#viewsChart').closest('.chart-container'));
        initializeCustomTooltip(productViewsChart, document.querySelector('#productViewsChart').closest('.chart-container'));
    </script>
@endsection
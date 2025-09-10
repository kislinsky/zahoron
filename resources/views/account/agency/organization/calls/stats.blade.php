@extends('account.agency.components.page')
@section('title', "Входящие звонки")

@section('content')
    <div class="text_black margin_top_down_20">{{ $organization->name_type }} {{ $organization->title }} на {{ $organization->adres }} в {{ $organization->city->title }}</div>
    
    <form class="filters_calls" method="GET" action="{{ route('account.agency.organization.calls.stats') }}">
        @csrf
        <input type="hidden" name="organization_id" value="{{ $organization->id }}">
        
        <button type="submit" name="period" value="month" class="{{ request('period') == 'month' ? 'blue_btn' : 'btn_gray' }}">За месяц</button>
        <button type="submit" name="period" value="week" class="{{ request('period') == 'week' ? 'blue_btn' : 'btn_gray' }}">За неделю</button>
       
        <div class="filter-container">
            <button type="button" class="btn_gray toggle-period-btn" id="togglePeriodBtn">
                📅 Выбранный период
            </button>
            
            <div class="custom-period hidden" id="customPeriod">
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="От" class="date-input">
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="До" class="date-input">
                <button type="submit" name="period" value="custom" class="blue_btn">Применить</button>
            </div>
        </div>
        
        <div class="btn_gray margin_left_auto">Осталось звонков: {{ $remaining_calls ?? 0 }}</div>
        <div class="blue_btn">Добавить звонки</div>
    </form>

    <table class='call_stats'>
        <thead>
            <tr>
                <td class='text_black'>Телефон</td>
                <td class='text_black date-header' style="cursor: pointer;">
                    Дата 
                    <img src="{{ asset('storage/uploads/arrow-down_calls.svg') }}" alt="" 
                         id="dateSortIcon"
                         data-sort="{{ request('sort', 'desc') }}">
                </td>
                <td class='text_black'>Продолжительность</td>
                <td class='text_black'>Прослушать запись</td>
            </tr>
        </thead>
        <tbody id="callsTableBody">
            @if($calls->count() > 0)
                @foreach ($calls as $call)
                    <tr>
                        <td>
                            <div class="text_black">
                                <div class="phone_icon_black">
                                    <img src="{{ asset('/storage/uploads/Vector_phone_black.svg') }}" alt="">
                                </div>
                                +7 {{ substr($call->caller_number, 1, 3) }} {{ substr($call->caller_number, 4, 3) }} {{ substr($call->caller_number, 7, 2) }} {{ substr($call->caller_number, 9, 2) }}
                            </div>
                        </td>
                        <td>
                            <div class="text_black">
                                {{ $call->created_at->translatedFormat('d M Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="text_black">
                                @php
                                    $minutes = floor($call->duration / 60);
                                    $seconds = $call->duration % 60;
                                @endphp
                                {{ sprintf('%02d:%02d', $minutes, $seconds) }} мин
                            </div>
                        </td>
                        <td>
                            <div class="text_black">
                                @if($call->record_url)
                                    <audio controls style="width: 150px; height: 30px;">
                                        <source src="{{ $call->record_url }}" type="audio/mpeg">
                                    </audio>
                                @else
                                    Запись отсутствует
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="text_black text-center">Звонки не найдены</td>
                </tr>
            @endif
        </tbody>
    </table>
        
    <div id="paginationContainer">
        {{ $calls->appends(request()->except('page'))->links() }}
    </div>

    <style>
    .filter-container {
        position: relative;
        display: inline-block;
    }
    
    .custom-period {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        border: 1px solid #e0e0e0;
        z-index: 1000;
        min-width: 300px;
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        visibility: hidden;
    }
    
    .custom-period.visible {
        opacity: 1;
        transform: translateY(0);
        visibility: visible;
    }
    
    .custom-period.hidden {
        display: flex !important;
        opacity: 0;
        visibility: hidden;
    }
    
    .date-header {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .sort-asc {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
    }
    
    .sort-desc {
        transform: rotate(0deg);
        transition: transform 0.3s ease;
    }
    
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Функция для открытия/закрытия кастомного периода
        const toggleBtn = document.getElementById('togglePeriodBtn');
        const customPeriod = document.getElementById('customPeriod');
        let isOpen = false;

        function toggleCustomPeriod() {
            isOpen = !isOpen;
            if (isOpen) {
                customPeriod.classList.remove('hidden');
                customPeriod.classList.add('visible');
                setTimeout(() => document.addEventListener('click', closeOnClickOutside), 0);
            } else {
                customPeriod.classList.remove('visible');
                customPeriod.classList.add('hidden');
                document.removeEventListener('click', closeOnClickOutside);
            }
        }

        function closeOnClickOutside(event) {
            if (!customPeriod.contains(event.target) && event.target !== toggleBtn) {
                toggleCustomPeriod();
            }
        }

        toggleBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            toggleCustomPeriod();
        });

        customPeriod.addEventListener('click', function(event) {
            event.stopPropagation();
        });

        @if(request('period') == 'custom')
            toggleCustomPeriod();
        @endif

        // Функция для сортировки по дате
        const dateHeader = document.querySelector('.date-header');
        const dateSortIcon = document.getElementById('dateSortIcon');
        let currentSort = dateSortIcon.dataset.sort;

        dateHeader.addEventListener('click', function() {
            // Меняем направление сортировки
            currentSort = currentSort === 'asc' ? 'desc' : 'asc';
            dateSortIcon.dataset.sort = currentSort;
            
            // Анимация стрелки
            if (currentSort === 'asc') {
                dateSortIcon.classList.remove('sort-desc');
                dateSortIcon.classList.add('sort-asc');
            } else {
                dateSortIcon.classList.remove('sort-asc');
                dateSortIcon.classList.add('sort-desc');
            }
            
            // Выполняем AJAX запрос
            sortCallsByDate(currentSort);
        });

        function sortCallsByDate(sortOrder) {
            const tableBody = document.getElementById('callsTableBody');
            const paginationContainer = document.getElementById('paginationContainer');
            
            // Показываем индикатор загрузки
            tableBody.classList.add('loading');
            
            // Получаем текущие параметры URL
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', sortOrder);
            urlParams.set('page', 1); // Сбрасываем на первую страницу при сортировке
            
            // Выполняем AJAX запрос
            fetch(`{{ route('account.agency.organization.calls.stats') }}?${urlParams.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновляем таблицу
                    tableBody.innerHTML = data.html;
                    
                    // Обновляем пагинацию
                    if (data.pagination) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                tableBody.classList.remove('loading');
            });
        }
    });

    // Обработчик для пагинации (чтобы сохранялась сортировка)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('.pagination a').href;
            
            const tableBody = document.getElementById('callsTableBody');
            tableBody.classList.add('loading');
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    tableBody.innerHTML = data.html;
                    const paginationContainer = document.getElementById('paginationContainer');
                    if (data.pagination) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                    
                    // Прокрутка к верху таблицы
                    tableBody.scrollIntoView({ behavior: 'smooth' });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.href = url; // Fallback
            })
            .finally(() => {
                tableBody.classList.remove('loading');
            });
        }
    });
    </script>

@endsection
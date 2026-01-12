@extends('account.agency.components.page')
@section('title', "Входящие звонки")

@section('content')
    <div class="text_black margin_top_down_20">{{ $organization->name_type }} {{ $organization->title }} на {{ $organization->adres }} в {{ $organization->city->title }}</div>
    
    <!-- Desktop version -->
    <div class="desktop-only">
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
                                    {{ $call->date_start->translatedFormat('d M Y') }}
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
                                        <audio controls style="width: 100%; height: 30px;">
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
    </div>

    <!-- Mobile version -->
    <div class="mobile-only">
        <div class="block_mobile_calls">
            <form class="filters_calls" method="GET" action="{{ route('account.agency.organization.calls.stats') }}">
                @csrf
                <input type="hidden" name="organization_id" value="{{ $organization->id }}">
                
                <button type="submit" name="period" value="today" class="{{ request('period') == 'today' ? 'blue_btn' : 'btn_gray' }}">Сегодня</button>
                <button type="submit" name="period" value="yesterday" class="{{ request('period') == 'yesterday' ? 'blue_btn' : 'btn_gray' }}">Вчера</button>
                
                <div class="filter-container-mobile">
                    <button type="button" class="btn_gray toggle-period-btn-mobile" id="togglePeriodBtnMobile">
                        📅 Период
                    </button>
                    
                    <div class="custom-period-mobile hidden" id="customPeriodMobile">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="От" class="date-input">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="До" class="date-input">
                        <button type="submit" name="period" value="custom" class="blue_btn">Применить</button>
                    </div>
                </div>
                
               
            </form>

            <div class="ul_mobile_calls">
                @if($calls->count() > 0)
                    @foreach ($calls as $call)
                        <div class="li_mobile_calls">
                            <div class='mini_info_call'>
                                <div class="text_block">
                                    @php
                                        // Форматирование телефона: 8-914-780-56-67
                                        $phone = $call->caller_number;
                                        if (strlen($phone) >= 11) {
                                            $formatted_phone = '8-' . substr($phone, 2, 3) . '-' . substr($phone, 5, 3) . '-' . substr($phone, 8, 2) . '-' . substr($phone, 10, 2);
                                        } else {
                                            $formatted_phone = $phone;
                                        }
                                    @endphp
                                    {{ $formatted_phone }}
                                </div>
                                <div class="text_block_gray">
                                    @php
                                        // Форматирование даты: 15:00 24.09.24
                                        $formatted_date = $call->created_at->format('H:i d.m.y');
                                    @endphp
                                    {{ $formatted_date }}
                                </div>
                                <img src="{{ asset('storage/uploads/Переключатель (2).svg') }}" alt="" class="open_more_info_call">
                            </div>
                            
                            <div class="audio-container" style="display: none;">
                                @if($call->record_url)
                                    <audio controls style="width: 100%; height: 30px;">
                                        <source src="{{ $call->record_url }}" type="audio/mpeg">
                                    </audio>
                                @else
                                    <div class="text_gray">Запись отсутствует</div>
                                @endif
                            </div>
                            
                            <div class="more_info_call" style="display: none;">
                                <div class="li_more_info_call">
                                    <div class="text_black_bold">Статус:</div>
                                    <div class="text_gray {{ strpos($call->status ?? '', '11') === 0 ? 'status-success' : '' }}">
                                        {{ $call->status ?? 'Не указан' }}
                                        @if(strpos($call->status ?? '', '11') === 0)
                                            <span class="status-indicator">✓</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="li_more_info_call">
                                    <div class="text_black_bold">Город:</div>
                                    <div class="text_gray">{{ $organization->city->title }}</div>
                                </div>
                                <div class="li_more_info_call">
                                    <div class="text_black_bold">Продолжительность:</div>
                                    <div class="text_gray">
                                        @php
                                            // Форматирование продолжительности: 60 сек
                                            echo ($call->duration ?? 0) . ' сек';
                                        @endphp
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text_black text-center">Звонки не найдены</div>
                @endif
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Desktop functionality
        const toggleBtn = document.getElementById('togglePeriodBtn');
        if (toggleBtn) {
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

            // Date sorting functionality
            const dateHeader = document.querySelector('.date-header');
            if (dateHeader) {
                const dateSortIcon = document.getElementById('dateSortIcon');
                let currentSort = dateSortIcon.dataset.sort;

                dateHeader.addEventListener('click', function() {
                    currentSort = currentSort === 'asc' ? 'desc' : 'asc';
                    dateSortIcon.dataset.sort = currentSort;
                    
                    if (currentSort === 'asc') {
                        dateSortIcon.classList.remove('sort-desc');
                        dateSortIcon.classList.add('sort-asc');
                    } else {
                        dateSortIcon.classList.remove('sort-asc');
                        dateSortIcon.classList.add('sort-desc');
                    }
                    
                    sortCallsByDate(currentSort);
                });
            }
        }

        // Mobile functionality
        const toggleButtonsMobile = document.querySelectorAll('.open_more_info_call');
        toggleButtonsMobile.forEach(button => {
            button.addEventListener('click', function() {
                const callItem = this.closest('.li_mobile_calls');
                const audioContainer = callItem.querySelector('.audio-container');
                const moreInfo = callItem.querySelector('.more_info_call');
                
                if (audioContainer.style.display === 'none') {
                    audioContainer.style.display = 'block';
                    moreInfo.style.display = 'block';
                } else {
                    audioContainer.style.display = 'none';
                    moreInfo.style.display = 'none';
                }
            });
        });

        // Mobile period filter
        const togglePeriodMobile = document.getElementById('togglePeriodBtnMobile');
        if (togglePeriodMobile) {
            const customPeriodMobile = document.getElementById('customPeriodMobile');
            let isMobileOpen = false;

            function toggleCustomPeriodMobile() {
                isMobileOpen = !isMobileOpen;
                if (isMobileOpen) {
                    customPeriodMobile.classList.remove('hidden');
                    customPeriodMobile.classList.add('visible');
                    setTimeout(() => document.addEventListener('click', closeMobileOnClickOutside), 0);
                } else {
                    customPeriodMobile.classList.remove('visible');
                    customPeriodMobile.classList.add('hidden');
                    document.removeEventListener('click', closeMobileOnClickOutside);
                }
            }

            function closeMobileOnClickOutside(event) {
                if (!customPeriodMobile.contains(event.target) && event.target !== togglePeriodMobile) {
                    toggleCustomPeriodMobile();
                }
            }

            togglePeriodMobile.addEventListener('click', function(event) {
                event.stopPropagation();
                toggleCustomPeriodMobile();
            });

            customPeriodMobile.addEventListener('click', function(event) {
                event.stopPropagation();
            });

            @if(request('period') == 'custom')
                toggleCustomPeriodMobile();
            @endif
        }

        function sortCallsByDate(sortOrder) {
            const tableBody = document.getElementById('callsTableBody');
            const paginationContainer = document.getElementById('paginationContainer');
            
            tableBody.classList.add('loading');
            
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', sortOrder);
            urlParams.set('page', 1);
            
            fetch(`{{ route('account.agency.organization.calls.stats') }}?${urlParams.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    tableBody.innerHTML = data.html;
                    
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

        // Pagination handling
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
                        
                        tableBody.scrollIntoView({ behavior: 'smooth' });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = url;
                })
                .finally(() => {
                    tableBody.classList.remove('loading');
                });
            }
        });
    });
    </script>

@endsection
<?php 
use Illuminate\Support\Str;
?>
@extends('account.user.components.page')
@section('title', 'Мои тикеты')

@section('content')
<div class="container-fluid" style="max-width: 900px; margin-top:30px;">
    <div class="row">
        <div class="col-12">
            <!-- Заголовок -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 style="font-size: 24px; font-weight: 600; color: #050505; margin: 0;">
                    Мои обращения
                </h1>
                <a href="{{ route('account.tickets.create') }}" class="btn btn-primary" 
                   style="background-color: #0080D7; border-color: #0080D7; border-radius: 8px; padding: 10px 20px; font-size: 16px; font-weight: 400;">
                    + Создать тикет
                </a>
            </div>

            <!-- Карточка с тикетами -->
            <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #e6e6e6;">
                <div class="card-body p-4">
                    @if($tickets->count() > 0)
                        <!-- Список тикетов -->
                        <div class="tickets-list">
                            @foreach($tickets as $ticket)
                                <div class="ticket-item" style="border-bottom: 1px solid #f0f0f0; padding: 20px 0; transition: all 0.2s ease;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <span style="font-size: 14px; color: #999; font-weight: 400;">
                                                #{{ $ticket->id }}
                                            </span>
                                            <h3 style="font-size: 18px; font-weight: 600; color: #050505; margin: 0;">
                                                {{ Str::limit($ticket->subject, 60) }}
                                            </h3>
                                        </div>
                                        <span class="status-badge" style="
                                            background-color: {{ $ticket->isClosed() ? '#f0f0f0' : '#e6f7ff' }};
                                            color: {{ $ticket->isClosed() ? '#999' : '#0080D7' }};
                                            padding: 4px 12px;
                                            border-radius: 16px;
                                            font-size: 14px;
                                            font-weight: 400;
                                        ">
                                            {{ $ticket->isClosed() ? 'Закрыт' : 'Активен' }}
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center gap-3 mb-3" style="flex-wrap: wrap;">
                                        <span class="category-badge" style="
                                            background-color: {{ $ticket->category->color }}20;
                                            color: {{ $ticket->category->color }};
                                            padding: 4px 12px;
                                            border-radius: 16px;
                                            font-size: 14px;
                                            font-weight: 400;
                                            border: 1px solid {{ $ticket->category->color }}40;
                                        ">
                                            {{ $ticket->category->name }}
                                        </span>
                                        
                                        <span class="priority-badge" style="
                                            background-color: {{ 
                                                $ticket->priority->name == 'Высокий' ? '#ff475720' : 
                                                ($ticket->priority->name == 'Средний' ? '#ffa50020' : '#00c85120') 
                                            }};
                                            color: {{ 
                                                $ticket->priority->name == 'Высокий' ? '#ff4757' : 
                                                ($ticket->priority->name == 'Средний' ? '#ffa500' : '#00c851') 
                                            }};
                                            padding: 4px 12px;
                                            border-radius: 16px;
                                            font-size: 14px;
                                            font-weight: 400;
                                            border: 1px solid {{ 
                                                $ticket->priority->name == 'Высокий' ? '#ff475740' : 
                                                ($ticket->priority->name == 'Средний' ? '#ffa50040' : '#00c85140') 
                                            }};
                                        ">
                                            {{ $ticket->priority->name }}
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="font-size: 14px; color: #999; font-weight: 400;">
                                            Создан: {{ $ticket->created_at->format('d.m.Y в H:i') }}
                                        </span>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('account.tickets.show', $ticket) }}" class="btn btn-sm" 
                                               style="background-color: #f8f9fa; color: #0080D7; border: 1px solid #e6e6e6; border-radius: 6px; padding: 8px 16px; font-size: 14px; font-weight: 400;">
                                                Посмотреть
                                            </a>
                                            
                                            @if(!$ticket->isClosed())
                                                <form action="{{ route('account.tickets.close', $ticket) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm" 
                                                            style="background-color: #fff; color: #ff4757; border: 1px solid #ff4757; border-radius: 6px; padding: 8px 16px; font-size: 14px; font-weight: 400;"
                                                            onclick="return confirm('Закрыть тикет?')">
                                                        Закрыть
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('account.tickets.reopen', $ticket) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm" 
                                                            style="background-color: #fff; color: #00c851; border: 1px solid #00c851; border-radius: 6px; padding: 8px 16px; font-size: 14px; font-weight: 400;"
                                                            onclick="return confirm('Открыть тикет?')">
                                                        Открыть
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Пагинация -->
                        <div class="mt-4" style="border-top: 1px solid #f0f0f0; padding-top: 20px;">
                            {{ $tickets->links('vendor.pagination.simple-default') }}
                        </div>

                    @else
                        <!-- Пустой state -->
                        <div class="text-center py-5" style="padding: 60px 0;">
                            <div style="margin-bottom: 24px;">
                                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <h4 style="font-size: 18px; font-weight: 600; color: #050505; margin-bottom: 8px;">
                                Обращений пока нет
                            </h4>
                            <p style="font-size: 16px; color: #999; margin-bottom: 24px; line-height: 1.5;">
                                Создайте первое обращение в службу поддержки
                            </p>
                            <a href="{{ route('account.tickets.create') }}" class="btn btn-primary" 
                               style="background-color: #0080D7; border-color: #0080D7; border-radius: 8px; padding: 12px 24px; font-size: 16px; font-weight: 400;">
                                Создать обращение
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.ticket-item:hover {
    background-color: #fafafa;
    margin: 0 -16px;
    padding: 20px 16px !important;
    border-radius: 8px;
}

.btn {
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 128, 215, 0.2);
}

.status-badge, .category-badge, .priority-badge {
    transition: all 0.2s ease;
}

.ticket-item:hover .status-badge,
.ticket-item:hover .category-badge,
.ticket-item:hover .priority-badge {
    transform: scale(1.05);
}

.pagination {
    justify-content: center;
}

.page-item .page-link {
    color: #0080D7;
    border: 1px solid #e6e6e6;
    border-radius: 6px;
    margin: 0 4px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 400;
}

.page-item.active .page-link {
    background-color: #0080D7;
    border-color: #0080D7;
    color: white;
}

.page-item.disabled .page-link {
    color: #999;
    border-color: #e6e6e6;
}

.card {
    transition: box-shadow 0.2s ease;
}

.card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}
</style>
@endsection
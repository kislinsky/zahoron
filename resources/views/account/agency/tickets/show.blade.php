@extends('account.agency.components.page')
@section('title', 'Обращение #' . $ticket->id)

@section('content')
<div class="container-fluid" style="max-width: 1200px;  margin-top: 30px; padding: 0 16px;">
    <div class="row">
        <!-- Левая часть - переписка -->
        <div class="col-lg-8 mb-4">
            <!-- Заголовок -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('account.tickets.index') }}" class="btn btn-sm me-3" 
                   style="background-color: #f8f9fa; border: 1px solid #e6e6e6; border-radius: 8px; padding: 8px 16px; color: #050505;">
                    ← Назад
                </a>
                <h1 style="font-size: 24px; font-weight: 600; color: #050505; margin: 0;">
                    Обращение #{{ $ticket->id }}
                </h1>
                <span class="ms-3" style="
                    background-color: {{ $ticket->isClosed() ? '#f0f0f0' : '#e6f7ff' }};
                    color: {{ $ticket->isClosed() ? '#999' : '#0080D7' }};
                    padding: 6px 16px;
                    border-radius: 20px;
                    font-size: 14px;
                    font-weight: 400;
                ">
                    {{ $ticket->isClosed() ? 'Закрыт' : 'Активен' }}
                </span>
            </div>

            <!-- Карточка переписки -->
            <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #e6e6e6;">
                <div class="card-header p-4" style="background-color: #fafafa; border-bottom: 1px solid #e6e6e6;">
                    <h2 style="font-size: 18px; font-weight: 600; color: #050505; margin: 0;">
                        {{ $ticket->subject }}
                    </h2>
                </div>

                <!-- История переписки -->
                <div class="card-body p-0">
                    <div class="chat-messages" style="padding: 20px; max-height: 500px; overflow-y: auto;">
                        @foreach($ticket->replies->sortBy('created_at') as $reply)
                            <div class="message {{ $reply->user_id == auth()->id() ? 'message-right' : 'message-left' }}" 
                                 style="margin-bottom: 20px;">
                                <div class="message-header" style="margin-bottom: 8px;">
                                    <span style="font-size: 14px; font-weight: 600; color: #050505;">
                                        {{ $reply->user->name }}
                                        @if($reply->is_internal)
                                            <small style="color: #999; font-weight: 400;">(внутренний)</small>
                                        @endif
                                    </span>
                                    <span style="font-size: 12px; color: #999; margin-left: 12px;">
                                        {{ $reply->created_at->format('d.m.Y в H:i') }}
                                    </span>
                                </div>
                                <div class="message-content" >
                                    {!! nl2br(e($reply->message)) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Форма ответа -->
                @if(!$ticket->isClosed())
                <div class="card-footer p-4" style="background-color: #fafafa; border-top: 1px solid #e6e6e6;">
                    <form action="{{ route('account.tickets.reply', $ticket) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-3">
                            <textarea name="message" class="form-control" placeholder="Введите ваш ответ..." rows="3" required
                                      style="border-radius: 8px; border: 1px solid #e6e6e6; padding: 16px; font-size: 16px; resize: vertical;"></textarea>
                            <button type="submit" class="btn align-self-end" 
                                    style="background-color: #0080D7; color: white; border: none; border-radius: 8px; padding: 16px 24px; font-size: 16px; white-space: nowrap;">
                                Отправить
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div class="card-footer p-4 text-center" style="background-color: #fafafa; border-top: 1px solid #e6e6e6;">
                    <p style="font-size: 16px; color: #999; margin-bottom: 16px;">
                        Обращение закрыто. Новые ответы не принимаются.
                    </p>
                    <form action="{{ route('account.tickets.reopen', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn" 
                                style="background-color: #00c851; color: white; border: none; border-radius: 8px; padding: 12px 24px; font-size: 14px;"
                                onclick="return confirm('Открыть обращение?')">
                            Открыть обращение
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Правая часть - информация -->
        <div class="col-lg-4">
            <!-- Информация о тикете -->
            <div class="card mb-4" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #e6e6e6;">
                <div class="card-header p-4" style="background-color: #fafafa; border-bottom: 1px solid #e6e6e6;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #050505; margin: 0;">
                        Информация об обращении
                    </h3>
                </div>
                <div class="card-body p-4">
                    <!-- Категория -->
                    <div class="info-item mb-3">
                        <span style="font-size: 14px; color: #999; display: block; margin-bottom: 4px;">Категория</span>
                        <span class="badge" style="
                            background-color: {{ $ticket->category->color }}20;
                            color: {{ $ticket->category->color }};
                            padding: 6px 12px;
                            border-radius: 16px;
                            font-size: 14px;
                            border: 1px solid {{ $ticket->category->color }}40;
                        ">
                            {{ $ticket->category->name }}
                        </span>
                    </div>

                    <!-- Приоритет -->
                    <div class="info-item mb-3">
                        <span style="font-size: 14px; color: #999; display: block; margin-bottom: 4px;">Приоритет</span>
                        <span class="badge" style="
                            background-color: {{ 
                                $ticket->priority->name == 'Высокий' ? '#ff475720' : 
                                ($ticket->priority->name == 'Средний' ? '#ffa50020' : '#00c85120') 
                            }};
                            color: {{ 
                                $ticket->priority->name == 'Высокий' ? '#ff4757' : 
                                ($ticket->priority->name == 'Средний' ? '#ffa500' : '#00c851') 
                            }};
                            padding: 6px 12px;
                            border-radius: 16px;
                            font-size: 14px;
                            border: 1px solid {{ 
                                $ticket->priority->name == 'Высокий' ? '#ff475740' : 
                                ($ticket->priority->name == 'Средний' ? '#ffa50040' : '#00c85140') 
                            }};
                        ">
                            {{ $ticket->priority->name }}
                        </span>
                    </div>

                    <!-- Статус -->
                    <div class="info-item mb-3">
                        <span style="font-size: 14px; color: #999; display: block; margin-bottom: 4px;">Статус</span>
                        <span class="badge" style="
                            background-color: {{ 
                                $ticket->status->name == 'Закрыт' ? '#f0f0f0' : 
                                ($ticket->status->name == 'Решен' ? '#00c85120' : 
                                ($ticket->status->name == 'В работе' ? '#ffa50020' : '#0080D720')) 
                            }};
                            color: {{ 
                                $ticket->status->name == 'Закрыт' ? '#999' : 
                                ($ticket->status->name == 'Решен' ? '#00c851' : 
                                ($ticket->status->name == 'В работе' ? '#ffa500' : '#0080D7')) 
                            }};
                            padding: 6px 12px;
                            border-radius: 16px;
                            font-size: 14px;
                            border: 1px solid {{ 
                                $ticket->status->name == 'Закрыт' ? '#e6e6e6' : 
                                ($ticket->status->name == 'Решен' ? '#00c85140' : 
                                ($ticket->status->name == 'В работе' ? '#ffa50040' : '#0080D740')) 
                            }};
                        ">
                            {{ $ticket->status->name }}
                        </span>
                    </div>

                    <!-- Даты -->
                    <div class="info-item mb-3">
                        <span style="font-size: 14px; color: #999; display: block; margin-bottom: 4px;">Создан</span>
                        <span style="font-size: 14px; color: #050505; font-weight: 400;">
                            {{ $ticket->created_at->format('d.m.Y в H:i') }}
                        </span>
                    </div>

                    @if($ticket->closed_at)
                    <div class="info-item mb-3">
                        <span style="font-size: 14px; color: #999; display: block; margin-bottom: 4px;">Закрыт</span>
                        <span style="font-size: 14px; color: #050505; font-weight: 400;">
                            {{ $ticket->closed_at->format('d.m.Y в H:i') }}
                        </span>
                    </div>
                    @endif

                    @if($ticket->assignedTo)
                    <div class="info-item mb-3">
                        <span style="font-size: 14px; color: #999; display: block; margin-bottom: 4px;">Назначен</span>
                        <span style="font-size: 14px; color: #050505; font-weight: 400;">
                            {{ $ticket->assignedTo->name }}
                        </span>
                    </div>
                    @endif

                    <!-- Действия -->
                    @if(!$ticket->isClosed())
                    <form action="{{ route('account.tickets.close', $ticket) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn w-100" 
                                style="background-color: #ff4757; color: white; border: none; border-radius: 8px; padding: 12px; font-size: 14px;"
                                onclick="return confirm('Закрыть обращение?')">
                            Закрыть обращение
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Быстрые действия -->
            <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #e6e6e6;">
                <div class="card-header p-4" style="background-color: #fafafa; border-bottom: 1px solid #e6e6e6;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #050505; margin: 0;">
                        Действия
                    </h3>
                </div>
                <div class="card-body p-4">
                    <a href="{{ route('account.tickets.index') }}" class="btn w-100 mb-3" 
                       style="background-color: #f8f9fa; color: #050505; border: 1px solid #e6e6e6; border-radius: 8px; padding: 12px; font-size: 14px; text-align: center; display: block;">
                        К списку обращений
                    </a>
                    
                    @if($ticket->isClosed())
                    <form action="{{ route('account.tickets.reopen', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn w-100" 
                                style="background-color: #00c851; color: white; border: none; border-radius: 8px; padding: 12px; font-size: 14px;"
                                onclick="return confirm('Открыть обращение?')">
                            Открыть обращение
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.chat-messages {
    scroll-behavior: smooth;
    padding: 20px;
    max-height: 500px;
    overflow-y: auto;
}

.message {
    margin-bottom: 24px;
    display: flex;
    flex-direction: column;
}

.message-right {
    margin-left: auto;
    max-width: 75%;
}

.message-left {
    margin-right: auto;
    max-width: 75%;
}

.message-header {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    font-size: 14px;
    line-height: 1.4;
}

.message-content {
    padding: 16px;
    border-radius: 16px;
    border: 1px solid #a8a8a8
    line-height: 1.5;
    white-space: pre-wrap;
    word-wrap: break-word;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    max-width: 100%;
}

.message-right .message-content {
    color: black;
    border-bottom-right-radius: 4px;
}

.message-left .message-content {
    background: #f8f9fa;
    color: #050505;
    border: 1px solid #e6e6e6;
    border-bottom-left-radius: 4px;
}

.message-time {
    font-size: 12px;
    color: #999;
    margin-left: 12px;
    font-weight: 400;
}

.message-username {
    font-weight: 600;
    color: #050505;
}

.message-internal {
    font-size: 12px;
    color: #999;
    font-weight: 400;
    margin-left: 6px;
}

/* Стили для скроллбара */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Анимации */
.message {
    animation: messageAppear 0.3s ease-out;
}

@keyframes messageAppear {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Адаптивность */
@media (max-width: 768px) {
    .message-right,
    .message-left {
        max-width: 85%;
    }
    
    .chat-messages {
        padding: 16px;
        max-height: 400px;
    }
    
    .message-content {
        padding: 12px;
    }
}

@media (max-width: 576px) {
    .message-right,
    .message-left {
        max-width: 90%;
    }
    
    .message-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .message-time {
        margin-left: 0;
    }
}

</style>
@endsection
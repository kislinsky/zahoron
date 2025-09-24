@extends('account.user.components.page')
@section('title', 'Создание обращения')

@section('content')
<div class="container-fluid" style="max-width: 900px; margin-top: 30px; padding: 0 16px;">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Заголовок -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('account.tickets.index') }}" class="btn btn-sm me-3" 
                   style="background-color: #f8f9fa; border: 1px solid #e6e6e6; border-radius: 8px; padding: 8px 16px; color: #050505;">
                    ← Назад
                </a>
                <h1 style="font-size: 24px; font-weight: 600; color: #050505; margin: 0;">
                    Новое обращение
                </h1>
            </div>

            <!-- Форма -->
            <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #e6e6e6;">
                <form action="{{ route('account.tickets.store') }}" method="POST">
                    @csrf
                    <div class="card-body p-4">
                        <!-- Тема -->
                        <div class="mb-4">
                            <label for="subject" style="display: block; font-size: 16px; font-weight: 600; color: #050505; margin-bottom: 8px;">
                                Тема обращения *
                            </label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject') }}" required
                                   style="border-radius: 8px; border: 1px solid #e6e6e6; padding: 12px 16px; font-size: 16px; color: #050505;"
                                   placeholder="Кратко опишите проблему">
                            @error('subject')
                                <div class="invalid-feedback" style="font-size: 14px; color: #ff4757;">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Описание -->
                        <div class="mb-4">
                            <label for="description" style="display: block; font-size: 16px; font-weight: 600; color: #050505; margin-bottom: 8px;">
                                Подробное описание *
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="6" required
                                      style="border-radius: 8px; border: 1px solid #e6e6e6; padding: 16px; font-size: 16px; color: #050505; resize: vertical;"
                                      placeholder="Опишите проблему подробно, укажите шаги воспроизведения, приложите скриншоты если необходимо">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback" style="font-size: 14px; color: #ff4757;">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Категория и Приоритет -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" style="display: block; font-size: 16px; font-weight: 600; color: #050505; margin-bottom: 8px;">
                                    Категория *
                                </label>
                                <select class="form-control @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required
                                        style="border-radius: 8px; border: 1px solid #e6e6e6; padding: 12px 16px; font-size: 16px; color: #050505; height: 48px;">
                                    <option value="">Выберите категорию</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback" style="font-size: 14px; color: #ff4757;">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="priority_id" style="display: block; font-size: 16px; font-weight: 600; color: #050505; margin-bottom: 8px;">
                                    Приоритет *
                                </label>
                                <select class="form-control @error('priority_id') is-invalid @enderror" 
                                        id="priority_id" name="priority_id" required
                                        style="border-radius: 8px; border: 1px solid #e6e6e6; padding: 12px 16px; font-size: 16px; color: #050505; height: 48px;">
                                    <option value="">Выберите приоритет</option>
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority->id }}" 
                                            {{ old('priority_id') == $priority->id ? 'selected' : '' }}>
                                            {{ $priority->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority_id')
                                    <div class="invalid-feedback" style="font-size: 14px; color: #ff4757;">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Футер -->
                    <div class="card-footer p-4" style="background-color: #fafafa; border-top: 1px solid #e6e6e6; border-radius: 0 0 12px 12px;">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn" 
                                    style="background-color: #0080D7; color: white; border-radius: 8px; padding: 12px 24px; font-size: 16px; font-weight: 400; border: none; flex: 1;">
                                Отправить обращение
                            </button>
                            <a href="{{ route('account.tickets.index') }}" class="btn" 
                               style="background-color: #f8f9fa; color: #050505; border: 1px solid #e6e6e6; border-radius: 8px; padding: 12px 24px; font-size: 16px; font-weight: 400; flex: 1;">
                                Отмена
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Подсказки -->
            <div class="mt-4" style="background-color: #f8f9fa; border-radius: 12px; padding: 20px;">
                <h4 style="font-size: 16px; font-weight: 600; color: #050505; margin-bottom: 12px;">
                    💡 Советы для быстрого решения:
                </h4>
                <ul style="font-size: 14px; color: #666; line-height: 1.6; margin: 0; padding-left: 20px;">
                    <li>Опишите проблему максимально подробно</li>
                    <li>Укажите шаги, которые привели к ошибке</li>
                    <li>Приложите скриншоты или видео</li>
                    <li>Укажите модель устройства и браузер, если это уместно</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const description = document.getElementById('description');
    const prioritySelect = document.getElementById('priority_id');
    
    description.addEventListener('input', function() {
        const text = this.value.toLowerCase();
        if (text.includes('срочно') || text.includes('важно') || text.includes('не работает') || text.includes('критич')) {
            setPriority('Высокий');
        } else if (text.includes('проблема') || text.includes('вопрос') || text.includes('ошибка')) {
            setPriority('Средний');
        }
    });

    function setPriority(priorityName) {
        const options = prioritySelect.options;
        for (let i = 0; i < options.length; i++) {
            if (options[i].text === priorityName) {
                prioritySelect.value = options[i].value;
                break;
            }
        }
    }
});
</script>
@endsection
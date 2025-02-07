@php
    $record = $getRecord(); // Получаем текущую запись
@endphp

@if($record->href_img==1 && !empty($record->logo) )
    <img src="{{ $record->logo }}" alt="Изображение" style="max-height: 150px; display: block;">
@else
    Изображение отсутствует
@endif
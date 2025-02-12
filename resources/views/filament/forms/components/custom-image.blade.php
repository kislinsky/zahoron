@php
    $record = $getRecord(); // Получаем текущую запись
@endphp

@if(isset($record->href_im) && $record->href_img==1 && !empty($record->img_url) )
    <img src="{{ $record->img_url }}" alt="Изображение" style="max-height: 150px; display: block;">
@else
    Изображение отсутствует
@endif
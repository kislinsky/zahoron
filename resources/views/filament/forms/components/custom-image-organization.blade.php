@php
    $record = $getRecord(); // Получаем текущую запись
@endphp

@if($record->href_img==1 && !empty($record->urlImgMain()) )
    <img src="{{ $record->urlImgMain()}}" alt="Изображение" style="max-height: 150px; display: block;">
@else
    Изображение отсутствует
@endif
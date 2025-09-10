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
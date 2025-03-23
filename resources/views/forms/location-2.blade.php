<?php 

use App\Models\Edge;
$edges=Edge::orderBy('title','asc')->get();
?>

<div class="modal fade" id="location_form_2" tabindex="-1" aria-labelledby="location_form_2" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="title_middle">Выберите регион и кладбище</div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <div class="block_location">
                    <div class="title_news">Область</div>
                    <!-- Кнопка "Назад" (изначально скрыта) -->
                    <button id="backButton" class="btn btn-secondary mb-2" style="display: none;">Назад</button>
                    <div class="ul_location ul_location_main">
                        @foreach ($edges as $edge)
                            <div id_edge="{{ $edge->id }}" class="li_location edge_li">{{ $edge->title }}</div>
                            @php $cities = $edge->cities; @endphp
                            @if(count($cities) > 0)
                                <div id_edge_ul="{{ $edge->id }}" class="ul_location cities_ul" style="display: none;">
                                    @foreach ($cities as $city)
                                        <div id_city="{{ $city->id }}" class="li_location city_li">{{ $city->title }}</div>
                                        @php $cemeteries = $city->cemeteries; @endphp
                                        @if(count($cemeteries) > 0)
                                            <div id_city_ul="{{ $city->id }}" class="ul_location cemetery_ul" style="display: none;">
                                                @foreach ($cemeteries as $cemetery)
                                                    <div id_cemetery="{{ $cemetery->id }}" class="li_location li_cemetery_3">{{ $cemetery->title }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 

use App\Models\Edge;
use App\Models\City;
use App\Models\Cemetery;
$edges=Edge::orderBy('title','asc')->get();
?>

<div class="modal fade" id="location_form" tabindex="-1" aria-labelledby="location_form" aria-hidden="true">
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
                    <div class="ul_location">
                        @foreach ($edges as $edge)

                            <div id_edge={{ $edge->id }} class="li_location edge_li">{{ $edge->title }}</div>

                            <?php    $cities=City::orderBy('id','desc')->where('edge_id',$edge->id)->get();?>

                            @if(count($cities)>0)

                                <div id_edge_ul={{ $edge->id }} class="ul_location cities_ul">

                                    @foreach ($cities as $city)
                                    
                                        <div id_city={{ $city->id }} class="li_location city_li">{{ $city->title }}</div>

                                        <?php $cemeteries=Cemetery::orderBy('id','desc')->where('city_id',$city->id)->get();?>

                                        @if(count($cemeteries)>0)

                                            <div id_city_ul={{ $city->id }} class="ul_location cemetery_ul">
                                                @foreach ($cemeteries as $cemetery)

                                                    <div id_cemetery={{ $cemetery->id }} data-bs-dismiss="modal" class="li_location li_cemetery_2">{{ $cemetery->title }}</div>

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


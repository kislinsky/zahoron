<?php 

use App\Models\Edge;
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
                        @endforeach
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>


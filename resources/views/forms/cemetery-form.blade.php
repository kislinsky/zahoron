<?php 

use App\Models\Cemetery;
$cemeteries=Cemetery::orderBy('title','asc')->where('city_id',selectCity()->id)->get();
?>

<div class="modal fade" id="cemetery_form" tabindex="-1" aria-labelledby="cemetery_form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="title_middle">Выберите  кладбище</div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <div class="block_location">
                    <div class="title_news">Кладбища</div>
                    <div class="ul_location">
                        @foreach ($cemeteries as $cemetery)
                            <div id_cemetery={{ $cemetery->id }} class="li_location cemetery_form_li">{{ $cemetery->title }}</div>
                        @endforeach
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
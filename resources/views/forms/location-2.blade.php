<?php 

use App\Models\Edge;
$objects = Edge::whereHas('area', function($query) {
    $query->whereHas('cities', function($query) {
        $query->whereHas('cemeteries');
    });
})
->orderBy('title', 'asc')
->get();

$type='edge';

?>

<div class="modal fade" id="location_form_2" tabindex="-1" aria-labelledby="location_form_2" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="title_middle">Удобный выбор кладбищ</div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <div class="text_block">Область-округ-город(село/поселок)-кладбище</div>
                <div class="html_geo">
                    {{view('components.components_form.ul-location',compact('objects','type'))}}
                </div>

                
            </div>
        </div>
    </div>
</div>





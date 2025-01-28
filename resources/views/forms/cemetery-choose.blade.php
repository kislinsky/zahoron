<?php 
$edges=edges();
?>
<div class="modal fade" id="cemetery_choose_form"  tabindex="-1" aria-labelledby="cemetery_choose_form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="text_center">
                        <div class="title_middle">Удобный выбор кладбищ</div>
                        <div class="text_block">Край-район-город(село/поселок)-кладбище</div>
                    </div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <div class="ul_objects">
                    @foreach ($edges as $edge)
                        <div class="li_object">
                            <span class='text_black open_children_object'>{{ $edge->title }} <img src="{{ asset('storage/uploads/Vector 9 (1).svg') }}" alt="" ></span>
                            <ul class="ul_objects ul_objects_children">
                                @foreach ($edge->area as $area)
                                    <li class="li_object">
                                        <span class='text_black open_children_object'>{{ $area->title }} <img src="{{ asset('storage/uploads/Vector 9 (1).svg') }}" alt=""></span>
                                        <ul class="ul_objects ul_objects_children">
                                            @foreach ($area->cities as $city)
                                                <li class="li_object">
                                                    <span class='text_black open_children_object'>{{ $city->title }} <img src="{{ asset('storage/uploads/Vector 9 (1).svg') }}" alt=""></span>
                                                    <ul class="ul_objects ul_objects_children">
                                                        @foreach ($city->cemeteries as $cemetery)
                                                            <li class="li_object">
                                                                <span class='text_black'>{{ $cemetery->title }} </span>
                                                            </li>    
                                                        @endforeach
                                                    </ul>
                                                </li>    
                                            @endforeach
                                        </ul>
                                    </li>    
                                @endforeach
                            </ul>
                        </div>    
                    @endforeach
                    
                </div>
            </div>
        </div>
    </div>
</div>



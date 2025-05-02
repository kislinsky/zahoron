<?php 
$cities=mainCities();
?>


<div class="modal fade" id="city_form" tabindex="-1" aria-labelledby="city_form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="title_middle">Выберите город</div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <div class="block_location">
                    <div class="form_city_select">
                        <input type="text" class='city_form' placeholder="Найти город">
                        <img class='icon_search' src="{{ asset('storage/uploads/IconPack.svg') }}" alt="">
                    
                    </div>
                    <div class="title_news">Город</div>
                        @if(count($cities)>0)
                            <div  class="ul_location">
                                @foreach ($cities as $city)
                                    <a href='{{ route('city.select',$city->id) }}'  class="li_location city_li">{{ $city->title }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    $( ".form_city_select input" ).on( "input", function() {
        $('.abs_cities').remove()
        let filters  = {
            'city_id':$(this).val(),
        };
        $.ajax({
            type: 'GET',
            url: '{{route('city.ajax')}}',
            data: filters,
            success: function (result) {
                $('.abs_cities').remove()
               $('#city_form .ul_location').append(result)
            },
            error: function () {
                $('.abs_cities').remove()
            }
        });
    });
</script>

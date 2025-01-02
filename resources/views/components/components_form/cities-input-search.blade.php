<div class="abs_cities_input_search">
    @foreach($cities as $city)
        <a val_id='{{ $city->id }}' class="city_li_input_seacrh">{{$city->title}}</a>
    @endforeach
    <script>
        $( ".city_li_input_seacrh" ).on( "click", function() {
            let title=$(this).html()
            let res=$(this).attr('val_id')
            $(this).parent().siblings('.input_search_cities').val(title)
            $(this).parent().siblings('.city_id_input').val(res)
            let search=$(this).parent().siblings('.input_search_cities').attr('search')

            $('.abs_cities_input_search').remove()
            

            if(search!='false'){
                let data  = {
                    "_token": "{{ csrf_token() }}",
                    'city_id':res,
                };
    
                $.ajax({
                    type: 'GET',
                    url: '{{route('dead.ajax.mortuary')}}',
                    data:  data,
                    success: function (result) {
                        $( "#dead_form select[name='mortuary_dead']" ).html(result)
                    },
                    error: function () {
                        alert('Ошибка');
                    }
                });
                $.ajax({
                    type: 'GET',
                    url: '{{route('beautification.ajax.cemetery')}}',
                    data:  data,
                    success: function (result) {
                        $( "#beautification_form select[name='cemetery_beautification']" ).html(result)
                    },
                    error: function () {
                        alert('Ошибка');
                    }
                });
                $.ajax({
                    type: 'GET',
                    url: '{{route('funeral-service.ajax.mortuary')}}',
                    data:  data,
                    success: function (result) {
                        $( "#funeral_services_form select[name='mortuary_funeral_service']" ).html(result)
                    },
                    error: function () {
                        alert('Ошибка');
                    }
                });
                $.ajax({
                    type: 'GET',
                    url: '{{route('funeral-service.ajax.cemetery')}}',
                    data:  data,
                    success: function (result) {
                        $( "#funeral_services_form select[name='cemetery_funeral_service']" ).html(result)
                    },
                    error: function () {
                        alert('Ошибка');
                    }
                });
                $.ajax({
                    type: 'GET',
                    url: '{{route('memorial.ajax.district')}}',
                    data:  data,
                    success: function (result) {
                        $( "#memorial_form select[name='district_memorial']" ).html(result)
                    },
                    error: function () {
                        alert('Ошибка');
                    }
                });
            }
            
        });
    </script>
</div>



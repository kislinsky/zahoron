@if(count($cities)>0)
    <div class="abs_cities_input">
        @foreach($cities as $city)
            <a class="city_li_input">{{$city->title}}</a>
        @endforeach
    </div>
@endif

<script>
    $( ".city_li_input" ).on( "click", function() {
        let res=$(this).html()
        $(this).parent('.abs_cities_input').siblings('input').val(res)
        $('.abs_cities_input').remove()
    });
</script>
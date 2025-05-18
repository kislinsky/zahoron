
<div class="load_block_2">
    <div class="load-9">
        <div class="spinner">
        <div class="bubble-1"></div>
        <div class="bubble-2"></div>
        </div>
    </div>
</div>

<div class="block_location">
    @if($type!='edge')
        <div  id_object={{ $objects[0]->id }}  class="blue_btn li_location" type_object='{{ $type }}' type_request='parent'>Назад</div>
    @endif
    @if($type=='edge')
        <div class="title_news">Область</div>
    @elseif ($type=='area')
        <div class="title_news">Округ</div>
    @elseif ($type=='city')
        <div class="title_news">Город</div>
    @elseif ($type=='cemetery')
        <div class="title_news">Кладбище</div>
    @endif
    <div class="ul_location">
        @foreach ($objects as $object)
            <div id_object={{ $object->id }} class="li_location {{ $type }}_li" type_object='{{ $type }}' type_request='children' >{{ $object->title }}</div>
        @endforeach
    </div>
</div>
<script>

 $( ".li_location" ).on( "click", function() {
    $('.load_block_2').show()
    let id=$(this).attr('id_object')
    let type_request=$(this).attr('type_request')
    let type_object=$(this).attr('type_object')
    let html_old=$('.html_geo').html()
    $('.html_geo .block_location').remove()

    let data  = {
        "type_request": type_request,
        'type_object':type_object,
        'id':id,
    };

    console.log(data)
    $.ajax({
        type: 'GET',
        url: '{{route('geo.ajax')}}',
        data:  data,
        success: function (result) {
            $('.load_block_2').hide()
            $( ".html_geo" ).html(result)
        },
        error: function () {
            $('.load_block_2').hide()
            $( ".html_geo" ).html(html)
        }
    });
});




</script>

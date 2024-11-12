@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Колумабрии в г. {{$city->title}}</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>


<div class="container">
    <div id="map" style="width: 100%; height: 600px"></div>
</div>

<section class="cemetery">
    <div class="container">
        <div class="block_places">
            <div class="ul_places">
                @if (isset($columbariums) && $columbariums->count()>0)
                    @foreach ($columbariums as $columbarium)
                        <div  class="li_place">
                            <a  href="{{ $columbarium->route() }}"  class="img_place"> <img src="{{$columbarium->urlImg()}}" alt=""> </a>
                            <div class="content_place_mini">
                                <a href="{{ $columbarium->route() }}" class="title_blue">{{$columbarium->title}}</a>
                                <div class="text_black">г.{{$city->title}}</div>
                            </div>
                            <div class="btn_border_gray">{{$columbarium->openOrNot()}}</div>
                        </div>
                    @endforeach
                @endif
                {{ $columbariums->withPath(route('columbariums'))->appends($_GET)->links() }}

            </div>
            {{view('columbarium.components.sidebar',compact('products'))}}
        </div>


        <div class="block_info_place">
            <div class="title_middle">Информация о колумбариях в г. {{$city->title}}</div>
            <div class="text_black">
                {!!str_replace('city',$city->title,$city->content_mortuary)!!}
            </div>
        </div>
    </div>
</section>

{{view('components.useful',compact('usefuls'))}}


@include('components.monuments-grave')

@include('components.rating-funeral-agencies-prices')

@include('components.rating-uneral-bureaus-raves-prices')


@include('columbarium.components.cities-places') 

<script>
    ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map", {
            center: ['{{$city->width}}', '{{$city->longitude}}'],
            zoom: 12
        }, {
            searchControlProvider: 'yandex#search'
        });
@if (isset($columbariums_map) )
    @foreach($columbariums_map as $columbarium)
      myMap.geoObjects
        .add(new ymaps.Placemark(['{{$columbarium->width}}', '{{$columbarium->longitude}}'], {
            balloonContent: '{!!$columbarium->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$columbarium->rating.'<br>'.$columbarium->countReviews().' отзывов' !!}',

            iconCaption: '{{$columbarium->title}}'
        },{
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/mdi_grave-stone (2).svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));
    @endforeach
@endif
}
</script>
@include('footer.footer') 

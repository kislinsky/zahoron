@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Кладбища в г. {{$city->title}}</div>    
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
                @if (isset($cemeteries) && $cemeteries->count()>0)
                    @foreach ($cemeteries as $cemetery)
                        <div  class="li_place">
                            <a  href="{{ $cemetery->route() }}"  class="img_place"> <img src="{{$cemetery->urlImg()}}" alt=""> </a>
                            <div class="content_place_mini">
                                <a href="{{ $cemetery->route() }}" class="title_blue">{{$cemetery->title}}</a>
                                <div class="text_black">г.{{$city->title}}</div>
                            </div>
                            <div class="btn_border_gray">{{$cemetery->openOrNot()}}</div>
                        </div>
                    @endforeach
                @endif
                {{ $cemeteries->withPath(route('cemeteries'))->appends($_GET)->links() }}

            </div>
            {{view('cemetery.components.sidebar',compact('products'))}}
        </div>


        <div class="block_info_place">
            <div class="title_middle">Информация о кладбищах в г. {{$city->title}}</div>
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

@include('cemetery.components.cities-places') 

<script >
    ymaps.ready(init);

    
    
function init() {
    var myMap = new ymaps.Map("map", {
            center: ['{{$city->width}}', '{{$city->longitude}}'],
            zoom: 12
        }, {
            searchControlProvider: 'yandex#search'
        });
        
@if (isset($cemeteries_map) )
    @foreach($cemeteries_map as $cemetery)
      myMap.geoObjects
        .add(new ymaps.Placemark(['{{$cemetery->width}}', '{{$cemetery->longitude}}'], {
            balloonContent: '{!!$cemetery->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$cemetery->rating.'<br>'.$cemetery->countReviews().' отзывов' !!}',
            iconCaption: '{{$cemetery->title}}'
         }, {
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/mdi_grave-stone (1).svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));
    @endforeach
@endif
}
</script>
@include('footer.footer') 
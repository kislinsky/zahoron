@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title">Церкви в г. {{$city->title}}</h1>    
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>

{{view('components.navigation',compact('pages_navigation'))}}

<div class="block_ritual_objects">

    <div class="container">
        <h2 class="title_middle mobile_title_ritual_object">Церкви на карте в г. {{$city->title}}</h2>
        <div id="map" style="width: 100%; height: 600px"></div>
        <div class="mobile_sidebar_ritual_object">
            <h2 class="title_middle">Организация похорон в г. {{ $city->title }}</h2>
            {{view('mortuary.components.sidebar',compact('products'))}}
        </div>
    </div>

<section class="cemetery">
    <div class="container">
        <div class="block_places">
            <div class="ul_places">
                @if (isset($churches) && $churches->count()>0)
                    @foreach ($churches as $church)
                        <div  class="li_place">
                            <a  href="{{ $church->route() }}"  class="img_place"> 
                                <img class='white_img_org' src="{{$church->defaultImg()[0]}}" alt="">   
                                <img class='black_img_org' src="{{$church->defaultImg()[1]}}" alt="">    
                            </a>
                            <div class="content_place_mini">
                                <a href="{{ $church->route() }}" class="title_blue">{{$church->title}}</a>
                                <div class="text_black">г.{{$city->title}}</div>
                            </div>
                            <div class="btn_border_gray">{{$church->openOrNot()}}</div>
                        </div>
                    @endforeach
                @endif
                {{ $churches->withPath(route('churches'))->appends($_GET)->links() }}

            </div>
            <div class="dekstop_sidebar_ritual_object">
                {{view('mortuary.components.sidebar',compact('products'))}}
            </div>
        </div>


        <div class="block_info_place">
            <h2 class="title_middle">Информация о церквях в г. {{$city->title}}</h2>
            <div class="text_black">
                {!!str_replace('{city}',$city->title,get_acf(5,'content_1'))!!}
            </div>
        </div>
    </div>
</section>
</div>


@include('components.monuments-grave')

@include('components.rating-funeral-agencies-prices')

@include('components.rating-uneral-bureaus-raves-prices')

@include('church.components.cities-places') 

<script>
    ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map", {
            center: ['{{$city->width}}', '{{$city->longitude}}'],
            zoom: 12
        }, {
            searchControlProvider: 'yandex#search'
        });
@if (isset($churches_map) )
    @foreach($churches_map as $church)
    myMap.geoObjects
        .add(new ymaps.Placemark(['{{$church->latitude}}', '{{$church->longitude}}'], {
            balloonContent: '{!! "<a style=\"color:#1A1A1A !important; text-decoration:none;\" href=\"{$church->route()}\">{$church->title}</a><br> <img src=\"".asset('storage/uploads/Frame 334.svg')."\" alt=\"\">  {$church->rating}<br>{$church->countReviews()} отзывов" !!}',            iconCaption: '{{$church->title}}'
        },{
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/game-icons_morgue-feet (2).svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));
    @endforeach
@endif
}
</script>


@include('components.ritual-objects') 

@include('footer.footer') 
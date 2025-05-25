@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Морги в г. {{$city->title}}</div>    
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>

{{view('components.navigation',compact('pages_navigation'))}}

<div class="block_ritual_objects">

    <div class="container">
        <div class="title_middle mobile_title_ritual_object">Морги на карте в г. {{$city->title}}</div>
        <div id="map" style="width: 100%; height: 600px"></div>
        <div class="mobile_sidebar_ritual_object">
            <div class="title_middle">Организация похорон в г. {{ $city->title }}</div>
            {{view('mortuary.components.sidebar',compact('products'))}}
        </div>
    </div>

<section class="cemetery">
    <div class="container">
        <div class="block_places">
            <div class="ul_places">
                @if (isset($mortuaries) && $mortuaries->count()>0)
                    @foreach ($mortuaries as $mortuary)
                        <div  class="li_place">
                            <a  href="{{ $mortuary->route() }}"  class="img_place"> 
                                <img class='white_img_org' src="{{$mortuary->defaultImg()[0]}}" alt="">   
                                <img class='black_img_org' src="{{$mortuary->defaultImg()[1]}}" alt="">    
                            </a>
                            <div class="content_place_mini">
                                <a href="{{ $mortuary->route() }}" class="title_blue">{{$mortuary->title}}</a>
                                <div class="text_black">г.{{$city->title}}</div>
                            </div>
                            <div class="btn_border_gray">{{$mortuary->openOrNot()}}</div>
                        </div>
                    @endforeach
                @endif
                {{ $mortuaries->withPath(route('mortuaries'))->appends($_GET)->links() }}

            </div>
            <div class="dekstop_sidebar_ritual_object">
                {{view('mortuary.components.sidebar',compact('products'))}}
            </div>
        </div>


        <div class="block_info_place">
            <div class="title_middle">Информация о моргах в г. {{$city->title}}</div>
            <div class="text_black">
                {!!str_replace('{city}',$city->title,get_acf(5,'content_1'))!!}
            </div>
        </div>
    </div>
</section>
</div>
{{view('components.useful',compact('usefuls'))}}


@include('components.monuments-grave')

@include('components.rating-funeral-agencies-prices')

@include('components.rating-uneral-bureaus-raves-prices')

@include('mortuary.components.cities-places') 

<script>
    ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map", {
            center: ['{{$city->width}}', '{{$city->longitude}}'],
            zoom: 12
        }, {
            searchControlProvider: 'yandex#search'
        });
@if (isset($mortuaries_map) )
    @foreach($mortuaries_map as $mortuary)
    myMap.geoObjects
        .add(new ymaps.Placemark(['{{$mortuary->width}}', '{{$mortuary->longitude}}'], {
            balloonContent: '{!! "<a style=\"color:#1A1A1A !important; text-decoration:none;\" href=\"{$mortuary->route()}\">{$mortuary->title}</a><br> <img src=\"".asset('storage/uploads/Frame 334.svg')."\" alt=\"\">  {$mortuary->rating}<br>{$mortuary->countReviews()} отзывов" !!}',            iconCaption: '{{$mortuary->title}}'
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
@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Крематории в г. {{$city->title}}</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>


<div class="block_ritual_objects">

    <div class="container">
        <div class="title_middle mobile_title_ritual_object">Крематории на карте в г. {{$city->title}}</div>
        <div id="map" style="width: 100%; height: 600px"></div>
        <div class="mobile_sidebar_ritual_object">
            <div class="title_middle">Организация кремации в г. {{ $city->title }}</div>
            {{view('crematorium.components.sidebar',compact('products'))}}
        </div>
    </div>

<section class="cemetery">
    <div class="container">
        <div class="block_places">
            <div class="ul_places">
                @if (isset($crematoriums) && $crematoriums->count()>0)
                    @foreach ($crematoriums as $crematorium)
                        <div  class="li_place">
                            <a  href="{{ $crematorium->route() }}"  class="img_place"> <img src="{{$crematorium->urlImg()}}" alt=""> </a>
                            <div class="content_place_mini">
                                <a href="{{ $crematorium->route() }}" class="title_blue">{{$crematorium->title}}</a>
                                <div class="text_black">г.{{$city->title}}</div>
                            </div>
                            <div class="btn_border_gray">{{$crematorium->openOrNot()}}</div>
                        </div>
                    @endforeach
                @endif
                {{ $crematoriums->withPath(route('crematoriums'))->appends($_GET)->links() }}

            </div>
            <div class="dekstop_sidebar_ritual_object">
                {{view('crematorium.components.sidebar',compact('products'))}}
            </div>
        </div>


        <div class="block_info_place">
            <div class="title_middle">Информация о крематориях в г. {{$city->title}}</div>
            <div class="text_black">
                {!!str_replace('city',$city->title,$city->content_mortuary)!!}
            </div>
        </div>
    </div>
</section>
</div>
{{view('components.useful',compact('usefuls'))}}


@include('components.monuments-grave')

@include('components.rating-funeral-agencies-prices')

@include('components.rating-uneral-bureaus-raves-prices')

@include('crematorium.components.cities-places') 

<script>
    ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map", {
            center: ['{{$city->width}}', '{{$city->longitude}}'],
            zoom: 12
        }, {
            searchControlProvider: 'yandex#search'
        });
@if (isset($crematoriums_map) )
    @foreach($crematoriums_map as $crematorium)
      myMap.geoObjects
        .add(new ymaps.Placemark(['{{$crematorium->width}}', '{{$crematorium->longitude}}'], {
            balloonContent: '{!!$crematorium->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$crematorium->rating.'<br>'.$crematorium->countReviews().' отзывов' !!}',
            iconCaption: '{{$crematorium->title}}'
        },{
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/emojione-monotone_funeral-urn.svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));
    @endforeach
@endif
}
</script>

@include('components.ritual-objects') 

@include('footer.footer') 
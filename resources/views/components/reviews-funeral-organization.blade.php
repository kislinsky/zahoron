<?php 

$city=selectCity();
$reviews_organization=reviewsOrganization($city->id);
?>
@if($reviews_organization->count()>0)
<section class='reviews_organizations'>
    <div class="container">
        <div class="title">Отзывы клиентов о ритуальных агентствах в г. {{$city->title}}</div>

            <div class="swiper reviews_funeral_agencies_swiper">
                <div class="swiper-wrapper">
                @foreach($reviews_organization as $review_organization)
                    <div class="swiper-slide">
                        <div class="li_review_organization">
                            <div class='name_organization'>
                                <?php 
                                    $organization=$review_organization->organization;
                                ?>
                                <img src="{{$organization->urlImg()}}" alt="">
                                <a href='{{$organization->route()}}' class="title_organization">Ритуальное агентство 
                                    "{{$organization->title}}"</a>
                            </div>
                            <div class="raiting_memorial_dinner">
                                <img src="{{asset('storage/uploads/Star 1 copy.svg')}}" alt="">{{$review_organization->rating}}
                            </div>
                            <div class="content_block">
                                <div class="content_not_all">{!!custom_echo($review_organization->content,200)!!}</div>
                                <div class="content_all">{!!$review_organization->content!!}</div>
                            </div>
                            <div class="text_li">
                                <?php $city_user=$review_organization->city;?>
                                {{$review_organization->name}} г. {{$city_user->title}}
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>

            <div class="swiper-button-next swiper_button_next_reviews_funeral_agencies"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
            <div class="swiper-button-prev swiper_button_prev_reviews_funeral_agencies"><img src='{{asset('storage/uploads/Переключатель (1).svg')}}'></div>
    </div>
</section>
@endif

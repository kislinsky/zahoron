<?php 
use App\Models\User;
use App\Models\City;
use App\Models\Organization;

$city=selectCity();
$reviews_organization=reviewsOrganization($city->id);
?>
@if(count($reviews_organization)>0)
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
                                    $organization=Organization::find($review_organization->organization_id);
                                    $user_city=User::find($review_organization->user_id);
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
                                <?php $city_user=City::find($review_organization->city_id);?>
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

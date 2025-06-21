<?php 
use App\Models\Organization;

$city=selectCity();
$sorted_organizations=organizationratingEstablishmentsProvidingHallsHoldingCommemorations($city->id);?>

@if($sorted_organizations->count()>0 && $sorted_organizations->first()!=null)

<section class="raiting raiting_2">
    <div class="container">
        <h2 class="title_our_works">Рейтинг заведений, предоставляющих залы для проведения поминок в г. {{$city->title}}.</h2>
        <div class="text_block">* Цены являются приблизительными. Уточняйте стоимость, позвонив в агентство.</div>
        <div class="table_rating_block">
            <table class="raiting_table">
                <thead>
                    <tr>
                        <th>Ритуальное бюро</th>
                        <th>Район города</th>
                        <th>Поминальное меню / персона</th>
                    </tr>
                </thead>
                <tbody>
                        @foreach($sorted_organizations as $sorted_organization)
                        <?php $organization=Organization::find($sorted_organization->organization_id);?>
                            <tr>
                                <td class='name_organization'>
                                    @if($organization->urlImg()=='default')
                                        <img class='white_img_org img_logo_organization' src="{{$organization->defaultLogoImg()[0]}}" alt="">   
                                        <img class='black_img_org img_logo_organization' src="{{$organization->defaultLogoImg()[1]}}" alt="">   
                                    @else
                                        <img  src="{{$organization->urlImg()}}" alt="">   
                                    @endif
                                    <a href='{{$organization->route()}}'class="title_organization">Ритуальное агентство 
                                        "{{$organization->title}}"
                                    </a>
                                </td>
                                <?php $district=$organization->district;?>
                                <td><div class="text_black">{{$district->title}}</div></td>
                                <td><div class="text_black">от {{$sorted_organization->priceHtml()}} р</div></td>
                            </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
</section>
@endif

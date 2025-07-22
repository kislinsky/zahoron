<?php 
use App\Models\ActivityCategoryOrganization;


$city=selectCity();
$organizations=organizationRatingUneralBureausRavesPrices($city->id);?>
@if($organizations!=null && $organizations->count()>0 && $organizations->first()!=null)
<section class="raiting raiting_2">
    <div class="container">
        <h2 class="title_our_works">Рейтинг ритуальных бюро по облагораживанию могил с самыми низкими ценами г. {{$city->title}}</h2>
        <div class="text_block">* Цены являются приблизительными. Уточняйте стоимость, позвонив в агентство.</div>
        
        <div class="table_rating_block">
            <table class="raiting_table">
            <thead>
                <tr>
                    <th>Ритуальное бюро</th>
                    <th>Памятник</th>
                    <th>Оградка</th>
                    <th>Плитка на могилу п.м.</th>
                </tr>
            </thead>
            <tbody>
                    @foreach($organizations as $organization)
                        @if($organization!=null)
                            <?php 
                                $price_1=ActivityCategoryOrganization::where('category_children_id',29)->where('organization_id',$organization->id)->get()->first()->priceHtml();
                                $price_2=ActivityCategoryOrganization::where('category_children_id',30)->where('organization_id',$organization->id)->get()->first()->priceHtml();
                                $price_3=ActivityCategoryOrganization::where('category_children_id',39)->where('organization_id',$organization->id)->get()->first()->priceHtml();
                            ?>
                            <tr>
                                <td class='name_organization'>
                                    @if($organization->urlImg()=='default')
                                        <img class='white_img_org img_logo_organization' src="{{$organization->defaultLogoImg()[0]}}" alt="">   
                                        <img class='black_img_org img_logo_organization' src="{{$organization->defaultLogoImg()[1]}}" alt="">   
                                    @else
                                        <img  src="{{$organization->urlImg()}}" alt="">   
                                    @endif
                                    <a href='{{$organization->route()}}'class="title_organization">{{$organization->name_type}}: 
                                        "{{$organization->title}}"</a>
                                </td>
                                <td><div class="text_black"> {{$price_1}} </div></td>
                                <td><div class="text_black"> {{$price_2}} </div></td>
                                <td><div class="text_black"> {{$price_3}} </div></td>
                            </tr>
                         @endif
                    @endforeach
            </tbody>
            </table>
        </div>
    </div>
</section>
@endif

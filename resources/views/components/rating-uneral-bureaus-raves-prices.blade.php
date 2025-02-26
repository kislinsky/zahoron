<?php 
use App\Models\ActivityCategoryOrganization;


$city=selectCity();
$organizations=organizationRatingUneralBureausRavesPrices($city->id);?>
@if($organizations!=null && $organizations->count()>0)
<section class="raiting raiting_2">
    <div class="container">
        <div class="title_our_works">Рейтинг ритуальных бюро по облагораживанию могил с самыми низкими ценами г. {{$city->title}}</div>
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
                                $price_1=ActivityCategoryOrganization::where('category_children_id',29)->where('organization_id',$organization->id)->get()->first()->price;
                                $price_2=ActivityCategoryOrganization::where('category_children_id',30)->where('organization_id',$organization->id)->get()->first()->price;
                                $price_3=ActivityCategoryOrganization::where('category_children_id',39)->where('organization_id',$organization->id)->get()->first()->price;
                            ?>
                            <tr>
                                <td class='name_organization'>
                                    <img src="{{$organization->urlImg()}}" alt="">
                                    <a href='{{$organization->route()}}'class="title_organization">Ритуальное агентство 
                                        "{{$organization->title}}"</a>
                                </td>
                                <td><div class="text_black">от {{$price_1}} р</div></td>
                                <td><div class="text_black">от {{$price_2}} р</div></td>
                                <td><div class="text_black">от {{$price_3}} р</div></td>
                            </tr>
                         @endif
                    @endforeach
            </tbody>
            </table>
        </div>
    </div>
</section>
@endif

<?php 
use App\Models\ActivityCategoryOrganization;

$city=selectCity();
$organizations=organizationRatingFuneralAgenciesPrices($city->id);
?>

@if($organizations!=null && count($organizations)>0)
<section class="raiting">
    <div class="container">
        <div class="title_our_works">Рейтинг ритуальных агентств в г. {{$city->title}}: 10 лучших предложений по ценам</div>
        <div class="text_block">* Цены являются приблизительными. Уточняйте стоимость, позвонив в агентство.</div>
        <table class="raiting_table">
            <thead>
                <tr>
                    <th>Агентство</th>
                    <th>Похороны</th>
                    <th>Кремация</th>
                    <th>Копка могил</th>
                    <th>Отправка груз 200</th>
                </tr>
            </thead>
            <tbody>
                    @foreach($organizations as $organization)
                        @if($organization!=null)
                            <?php 
                                $price_1=ActivityCategoryOrganization::where('category_children_id',32)->where('organization_id',$organization->id)->get()->first()->price;
                                $price_2=ActivityCategoryOrganization::where('category_children_id',33)->where('organization_id',$organization->id)->get()->first()->price;
                                $price_3=ActivityCategoryOrganization::where('category_children_id',34)->where('organization_id',$organization->id)->get()->first()->price;
                                $price_4=ActivityCategoryOrganization::where('category_children_id',35)->where('organization_id',$organization->id)->get()->first()->price;
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
                                <td><div class="text_black">от {{$price_4}} р</div></td>
                            </tr>
                        @endif
                    @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif
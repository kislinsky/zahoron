<?php 
use App\Models\Organization;

$city = selectCity();
$sorted_organizations = organizationratingEstablishmentsProvidingHallsHoldingCommemorations($city->id);
?>

@if($sorted_organizations->count() > 0 && $sorted_organizations->first() != null)
<?php
// Предзагрузка всех организаций для второго блока
$organizationIds2 = $sorted_organizations->pluck('organization_id');
$organizations2 = Organization::whereIn('id', $organizationIds2)->get()->keyBy('id');
?>
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
                        <?php $organization = $organizations2[$sorted_organization->organization_id] ?? null; ?>
                        @if($organization)
                            <tr>
                                <td class='name_organization'>
                                    @if($organization->urlImg() == 'default')
                                        <img class='white_img_org img_logo_organization' src="{{$organization->defaultLogoImg()[0]}}" alt="">   
                                        <img class='black_img_org img_logo_organization' src="{{$organization->defaultLogoImg()[1]}}" alt="">   
                                    @else
                                        <img src="{{$organization->urlImg()}}" alt="">   
                                    @endif
                                    <a href='{{$organization->route()}}' class="title_organization">{{$organization->name_type}}: 
                                        "{{$organization->title}}"
                                    </a>
                                </td>
                                <?php $district = $organization->district; ?>
                                <td><div class="text_black">{{$district->title}}</div></td>
                                <td>
                                    <div class="text_black">
                                        от 
                                        @if(strpos($sorted_organization->priceHtml(), 'уточняйте') !== false || strpos($sorted_organization->priceHtml(), 'Уточняйте') !== false)
                                            @if($organization->city->edge->call_mango_office)
                                                <a href='javascript:void(0)' class="mgo-call-button price-link" 
                                                    data-key="{{ 1 }}"
                                                    data-org-id="{{ $organization->id }}"
                                                    data-phone="{{ str_replace('+', '', $organization->phone) }}"
                                                    data-default-number="{{ $organization->phone }}"
                                                    data-calls="{{ $organization->haveCalls() }}">
                                                    уточняйте
                                                </a>
                                            @else
                                                <a href='tel:{{ $organization->phone }}' class="price-link"> 
                                                    уточняйте
                                                </a>
                                            @endif
                                        @else
                                            {{ $sorted_organization->priceHtml() }}
                                        @endif
                                        р
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endif
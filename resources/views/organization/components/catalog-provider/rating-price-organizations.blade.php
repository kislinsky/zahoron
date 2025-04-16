
@if($organizations_rating->count()>0)

    <section class="raiting raiting_2">
        <div class="container">
            <div class="title_our_works">Рейтинг поставщиков по минимальной оптовой цене на {{$category->title}} в г. {{$city->title}}.</div>
            <div class="text_block">* Цены являются приблизительными. Уточняйте стоимость, позвонив в агентство.</div>
            <table class="raiting_table">
                <thead>
                    <tr>
                        <th>Агентство</th>
                        <th>{{$category->title}}</th>
                    </tr>
                </thead>
                <tbody>
                        @foreach($organizations_rating as $organization_rating)
                        <?php $organization=$organization_rating->organization;?>
                            <tr>
                                <td class='name_organization'>
                                    <img src="{{$organization->urlImg()}}" alt="">
                                    <a href='{{$organization->route()}}'class="title_organization">Ритуальное агентство 
                                        "{{$organization->title}}"
                                    </a>
                                </td>
                                <td><div class="text_black">от {{$organization_rating->price}} р</div></td>
                            </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif

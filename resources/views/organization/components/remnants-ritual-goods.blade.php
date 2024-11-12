@if($remnants_ritual_goods!=null && count($remnants_ritual_goods)>0)
    <div class="block_content_organization_single table_block_price_list">
        <div class="title_li">Остатки ритуальных товаров</div>
        <table class="table_price_list_organization">
            <thead>
                <tr>
                    <th class='title_rewies text_start'>Название прайслиста</th>
                    <th class='title_rewies'>Актуальность</th>
                    <th class='title_rewies'>Файл</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($remnants_ritual_goods as $price_list_item)
                    <tr>
                        <td class='text_black text_start'>{{$price_list_item->title}}</td>
                        <td class='text_black'>{{$price_list_item->created_at}}</td>
                        <td><a class='title_rewies' href="{{asset('storage/uploads_organization/'.$price_list_item->file_name)}}" download="">Скачать</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
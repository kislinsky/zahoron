<div class="block_search_organization_add">
    <div class="title_blue">Проверка компании в нашем каталоге</div>
    <div class="text_black">Введите название своей организации</div>
    <form action="{{route('account.agency.add.organization')}}" method='get'>
        <input type="text" class="search_organization_input" placeholder='Ритуал' name='s' value='<?php if($s!=null){echo $s;}?>'>
        <div class="select_city_form_add_organization">
            <img src="{{asset('storage/uploads/Vector (15).svg')}}" alt="">

            <select name="city_id" id="">
                @foreach($cities as $option_city)
                    <option @if($city->id==$option_city->id) {{'selected'}} @endif value="{{$option_city->id}}">{{$option_city->title}}</option>
                @endforeach
            </select>
        </div>
        <button class="blue_btn">Найти</button>
    </form>
</div>
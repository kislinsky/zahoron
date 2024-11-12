<div class="block_inpit_form_search">
    <div class="title_middle settings_margin_form">Юридический адресс</div>
    <div class="flex_search_form">
        <div class="block_inpit_form_search">
            <label class='label_input'>Регион</label>
            <div class="select">
                <select name="edge_id" id="">
                    @foreach ($edges as $edge)
                        <option <?php if($edge->id == $user->edge_id){echo 'selected';}?> value="{{$edge->id}}">{{$edge->title}}</option>   
                    @endforeach
                </select>
            </div>
            @error('edge_id')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_inpit_form_search">
            <label class='label_input'>Город</label>
            <div class="select">
                <select name="city_id" id="">
                    @foreach ($cities as $city)
                        <option <?php if($city->id == $user->city_id){echo 'selected';}?> value="{{$city->id}}">{{$city->title}}</option>   
                    @endforeach
                </select>
            </div>
            @error('city_id')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_inpit_form_search">
            <label class='label_input'>Адрес</label>
            <input type="text" name='adres' value='{{ $user->adres }}'placeholder='Адрес'>
            @error('adres')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
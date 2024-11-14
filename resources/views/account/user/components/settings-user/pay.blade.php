<div class="title_middle settings_margin_form">Данные оплаты</div>
    <div class="flex_form_settings">
        <div class="block_inpit_form_search">
            <label class='label_input'>Номер карты</label>
            <input type="text" autocomplete="off" name='number_cart' value='{{ $user->number_cart }}'placeholder='Номер карты'>

            @error('number_cart')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_inpit_form_search">
            <label class='label_input'>Банк</label>
            <select name='bank'>
                <option  value="Сбербанк">Сбербанк</option>
            </select>
            @error('bank')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
    </div>
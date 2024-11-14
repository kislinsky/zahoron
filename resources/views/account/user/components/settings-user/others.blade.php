<div class="block_inpit_form_search">
    <div class="title_middle settings_margin_form">Прочие настройки</div>
    <div class="flex_form_settings">
        <div class="block_inpit_form_search">
            <label class='label_input'>Язык интерфейса</label>
            <select name='language'>
                <option <?php if($user['language']==1){ echo'selected';}?> value="1">Русский</option>
            </select>
            @error('language')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_inpit_form_search">
            <label class='label_input'>Тема</label>
            <select name='theme'>
                <option  <?php if($user['theme']=='light'){ echo'selected';}?>value="light">Светлая</option>
                <option <?php if($user['theme']=='dark'){ echo'selected';}?> value="dark">Темная</option>
            </select>
            @error('theme')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
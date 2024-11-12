<div class="flex_search_form">
    <div class="block_inpit_form_search">
        <label class='label_input'>ИНН</label>
        <input class='inn_input'type="text" name='inn' value='{{ $user->inn }}'placeholder='ИНН'>
        @error('inn')
            <div class='error-text'>{{ $message }}</div>
        @enderror
    </div>
    <div class="block_inpit_form_search">
        <label class='label_input'>ОГРНИП/ОГРН</label>
        <input type="text" disabled  name='ogrn' value='{{ $user->ogrn }}'placeholder='ОГРНИП/ОГРН'>
        @error('ogrn')
            <div class='error-text'>{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="flex_search_form">
    <div class="block_inpit_form_search">
        <label class='label_input'>Имя</label>
        <input type="text" disabled name='name' value='{{ $user->name }}'placeholder='Имя'>
        @error('name')
            <div class='error-text'>{{ $message }}</div>
        @enderror
    </div>
    <div class="block_inpit_form_search">
        <label class='label_input'>Фамилия</label>
        <input type="text" disabled name='surname' value='{{ $user->surname }}'placeholder='Фамилия'>
        @error('surname')
            <div class='error-text'>{{ $message }}</div>
        @enderror
    </div>
    <div class="block_inpit_form_search">
        <label class='label_input'>Отчество</label>
        <input type="text" disabled name='patronymic' value='{{ $user->patronymic }}'placeholder='Отчество'>
        @error('patronymic')
            <div class='error-text'>{{ $message }}</div>
        @enderror
    </div>
</div>
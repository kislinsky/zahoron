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
        <input type="text"   disabled name='ogrn' value='{{ $user->ogrn }}'placeholder='ОГРНИП/ОГРН'>
        @error('ogrn')
            <div class='error-text'>{{ $message }}</div>
        @enderror
    </div>
    <div class="block_inpit_form_search">
        <label class='label_input'>Организация</label>
        <input type="text"  name='name_organization' value='{{ $user->name_organization }}'placeholder='Организация'>
        @error('organization')
            <div class='error-text'>{{ $message }}</div>
        @enderror
    </div>
</div>
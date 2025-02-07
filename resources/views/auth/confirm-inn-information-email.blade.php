@include('header.header')

<div class="container">

    <form class='form_login form_with_email'method="POST" action="{{ route('create.user.organization.email') }}">
        @csrf
        <div class="title_li">Регистрация</div>
        
        <input type="hidden" name="role" value='{{ $role }}'>
        <input type="hidden" name="password" value='{{ $password }}'>
        <input type="hidden" name="organization_form" value='{{ $organization_form }}'>

        <div class="block_info">
            <div class="text_black_bold text_start">Почта:</div>
            <div class="text_black"> {{ $email }}</div>
            <input type="hidden" name="email" value='{{ $email }}'>
        </div>
            
        <div class="block_input">
            <div class="text_black_bold text_start">ИНН:</div>
            <div class="text_black"> {{ $inn }} </div>
            <input type="hidden" name="inn" value='{{ $inn }}'>
        </div>

        <div class="block_input">
            <div class="text_black_bold text_start">Контрагент:</div>
            <div class="text_gray"> {{ $contragent }} </div>
            <input type="hidden" name="contragent" value='{{  $contragent }}'>
        </div>

        <div class="block_input">
            <div class="text_black_bold text_start">Статус:</div>
            <div class="text_gray"> {{ $status }} </div>
            <input type="hidden" name="status" value='{{ $status }}'>
        </div>

        <div class="block_input">
            <div class="text_black_bold text_start">ОКВЕД:</div>
            <div class="text_gray"> {{ $okved }} </div>
            <input type="hidden" name="okved" value='{{ $okved }}'>
        </div>

        <div class="grid_btn">
            <button type="submit" class="blue_btn">Подвердить</button>
            <a href='{{ route('register') }}' type="submit" class="btn_border_blue">Изменить</a>

        </div>
    </form>
</div>

@include('footer.footer') 
@include('header.header-decoder')


<div class="account_container">
    <div class="flex_account">
        @include('account.decoder.components.sidebar')
        <div class="container_content_account">
            <div class="container">
                <div class="title_middle">Здравствуйте, {{ $user->name }}! <br>Добро пожаловать в ваш личный кабинет.</div>                    
            </div>
        </div>
    </div>
</div>

@include('footer.footer')

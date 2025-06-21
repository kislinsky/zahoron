@include('header.header-404')

<section class="page_404">
    <div class="container">
        <div class='flex_404_page'>
            <h1 class="title_404">404</h1>
            <div class="title_404_middle">Страница не найдена</div>
            <div><a href="{{ route('index') }}" class="blue_btn">Вернуться на главную</a></div>
        </div>
        <img src="{{ asset('storage/uploads/lily-of-the-valley 2.svg') }}" alt="">
    </div>
</section>
    

@include('footer.footer')

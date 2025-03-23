
@include('header.header')
<section class="order_page bac_gray">
    <div class="container order_page_search">
        <div class="content_order_page">
            <div class="index_title">Поиск могил по фамилии в г. {{ selectCity()->title }}</div>    
            <form method='get' action="{{route('search.burial.result')}}" class="search_application">
                @csrf
                <div class="grid_3">
                    <div class="block_inpit_form_search">
                        <input type="text" name='surname' placeholder='Фамилия'>
                        @error('surname')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="block_inpit_form_search">
                        <input type="text" name='name' placeholder='Имя'>
                        @error('name')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="block_inpit_form_search">
                        <input type="text" name='patronymic' placeholder='Отчество'>
                        @error('patronymic')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="grid_3">
                    <div class="block_inpit_form_search">
                        <input type="date" name='date_birth' placeholder='Дата рождения'>
                        @error('date_birth')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                        <div class="text_input">дд.мм.гггг</div>
                    </div>
                    <div class="block_inpit_form_search">
                        <input type="date" name='date_death' placeholder='Дата смерти'>
                        @error('date_death')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                        <div class="text_input">дд.мм.гггг</div>
                    </div>
                    <button class='blue_btn' type='submit'>Заказать поиск</button>
                </div>
               
                
            </form>
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>



<section class='block_search_ul_advantages'>
    <div class="container">
        <div class="grid_4">
            <a href='{{ route('cemeteries') }}' class="block_border_gray">
                <img src="{{ asset('storage/uploads/Icon_search (2).svg') }}" alt="">
                <div class="title_checkout_form">Кладбища</div>
            </a>
            <a  href='{{ route('organizations') }}'c class="block_border_gray">
                <img src="{{ asset('storage/uploads/Icon_search (1).svg') }}" alt="">
                <div class="title_checkout_form">Ритуальные бюро </div>
            </a>
            <a href='{{ route('marketplace') }}' class="block_border_gray">
                <img src="{{ asset('storage/uploads/Icon_search (3).svg') }}" alt="">
                <div class="title_checkout_form">Маркетплейст</div>
            </a>
            <a href='{{ route('page.search.burial.request') }}'class="block_border_gray">
                <img src="{{ asset('storage/uploads/Icon_search (4).svg') }}" alt="">
                <div class="title_checkout_form">Запрос на поиск</div>
            </a>
        </div>
    </div>
</section>


<section class="price_service">
    <div class="container grid_two_page">
        <div class="">
            {{ view('burial.components.calculator-service',compact('services')) }}
        </div>
        <div class="sidebar">
            <div class="btn_border_blue"  data-bs-toggle="modal" data-bs-target="#beautification_form"><img src="{{asset('storage/uploads/Frame (20).svg')}}" alt="">Облагородить могилу</div>
            <div class="ul_news_sidebar">
                @if ($news->count()>0)                
                        @foreach ($news as $news_one )
                            <div class="li_news">
                                <img src="{{asset('storage/'. $news_one->img )}}" alt="">
                                <a href='{{ route('news.single',$news_one->id) }}' class="title_news">{{ $news_one->title }}</a>
                                <div class="text_li">{{ $news_one->created_at->format('d.m.Y') }}</div>
                            </div>
                        @endforeach                    
                @endif
            </div>
        </div>
    </div>
</section>

@include('components.rating-uneral-bureaus-raves-prices')

@include('components.monuments-grave')
@include('components.fence')
@include('components.tile')

<section class="block">
    <div class="container">
        <div class="grid_two index_block_grid">
            <img src="{{asset('storage/uploads/002-spisok-uslug-2 2.png')}}" alt="" class="img_text_block">
            <div class="text_block_index">
                <div class="title_text_block">Получите прямой расчёт
                    от 10 проверенных ритуальных агентств по низким ценам
                </div>
                <div class="blue_btn" data-bs-toggle="modal" data-bs-target="#beautification_form">Получить расчет</div>
            </div>
        </div>
    </div>
</section>

@include('components.rewies')

{{ view('burial.components.dead-in-day',compact('burials')) }}

@include('components.faq')

@include('components.news-video')

@if(selectCity()->text_about_project!=null)
    <section class="about_company bac_gray">
        <div class="container">
            <div class="title">О проекте "Цены на ритуальные услуги в г. {{selectCity()->title}}</div>
            <div class="content_block">{!! get_acf(15,'content_1') !!}</div>

        </div>
    </section>
@endif

@include('footer.footer') 



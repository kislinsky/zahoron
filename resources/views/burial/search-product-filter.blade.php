@include('header.header')

<section class="order_page bac_gray">
    <div class="container order_page_search">
        <div class="content_order_page content_order_page_2">
            <h1 class="index_title">Установить судьбу</h1>    
            <form method='get' action="{{route('search.burial.filter')}}" class="search">
                @csrf
                <div class="block_input">
                    <label for="">Фамилия</label>
                    <input type="text" name='surname' placeholder='Фамилия'>
                </div>
                <div class="block_input">
                    <label for="">Имя</label>
                    <input type="text" name='name' placeholder='Имя'>
                </div>
                <div class="block_input">
                    <label for="">Отчество</label>
                    <input type="text" name='patronymic' placeholder='Отчество'>
                </div>
                <div class="block_input">
                    <label for="">Ветеран</label>
                    <div class="select"><select name="who" id="">
                        <option value="Участник ВОВ">Участник ВОВ</option>
                        <option value="Участник СВО">Участник СВО</option>
                        <option value="Неопознанный">Неопознанный</option>
                    </select></div>
                </div>
                <div class="block_input block_input_2"><button class='blue_btn' type='submit'>Найти</button></div>
            </form>
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>



<section class="price_service">
    <div class="container grid_two_page">
        <div class="text_block">{!! get_acf(18,'content') !!}</div>
        
        <div class="sidebar">
            <div class="btn_border_blue"  data-bs-toggle="modal" data-bs-target="#beautification_form"><img src="{{asset('storage/uploads/Frame (20).svg')}}" alt="">Облагородить могилу</div>
        </div>
    </div>
</section>
@include('footer.footer') 



@include('header.header')

<section class="order_page bac_gray">
    <div class="container order_page_search">
        <div class="content_order_page content_order_page_2">
            <div class="index_title">Установить судьбу</div>    
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
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>



<section class="price_service">
    <div class="container grid_two_page">
        <div class="text_block">Победа в Великой Отечественной войне досталась нам очень высокой ценой. Судьбы тысяч людей так и остались невыясненными. До сих пор продолжаются поиски мест захоронений погибших воинов. С целью организации работы по увековечению памяти павших защитников Отечества и реализации на практике лозунга «Никто не забыт, ничто не забыто» Президент Российской Федерации издал ряд поручений и Указов.<br><br>
            В соответствии с Перечнем поручений Президента Российской Федерации от 23 апреля 2003 г. №пр-698 по вопросам организации военно-мемориальной работы в Российской Федерации и Указом от 22 января 2006 года № 37 «Вопросы увековечения памяти погибших при защите Отечества», Министерством обороны Российской Федерации создан Обобщенный компьютерный банк данных, содержащий информацию о защитниках Отечества, погибших и пропавших без вести в годы Великой Отечественной войны, а также в послевоенный период (ОБД Мемориал).<br><br>
            Главная цель проекта - дать возможность миллионам граждан установить судьбу или найти информацию о своих погибших или пропавших без вести родных и близких, определить место их захоронения.<br><br>
            Тылом Вооруженных Сил Российской Федерации (Военно-мемориальным центром ВС РФ) проведена уникальная по масштабам, технологии и срокам исполнения работа, в результате которой создана информационно-справочная система глобального значения, не имеющая аналогов в мировой практике.
        </div>
        
        <div class="sidebar">
            <div class="btn_border_blue"  data-bs-toggle="modal" data-bs-target="#beautification_form"><img src="{{asset('storage/uploads/Frame (20).svg')}}" alt="">Облагородить могилу</div>
        </div>
    </div>
</section>
@include('footer.footer') 



@include('header.header')
@include('forms.location-2')

<?php 

use Illuminate\Support\Facades\Auth;

?>
<section class="order_page bac_gray">
    <div class="container order_page_search">
        <div class="content_order_page search_request">
            <div class="index_title">Поиск захоронения</div>    
            <div class="text_block">Сотрудники проектавнесут в базу и предоставят Вам фотографии с места<br>захоронения. Чтобы места захоронения, где покоятся Ваши родные, не были признаны<br>заброшенными и не исчезли, позаботьтесь о них сейчас.</div>
            <form method='get' action="{{route('search.burial.request')}}" class="search_application">
                @csrf
                <div class="flex_search_form">
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
                <div class="flex_search_form">
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
                    <div class="block_inpit_form_search">
                        <div class="input_location">
                            <img  data-bs-toggle="modal" data-bs-target="#location_form_2" class='open_location' src="{{ asset('storage/uploads/Add (1).svg') }}" alt="">
                            <input type="text" name='location' placeholder='Расположение'>
                        </div>
                        @error('location')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                        <div class="text_input">Впишите название кладбища (или района/области) либо нажмите "+" и выберите из списка</div>
                    </div>
                </div>
                @if(Auth::check())
                    <div class='flex_customer'>
                        <label>Данные заказчика</label>
                        <div class="flex_search_form">
                            <div class="block_inpit_form_search">
                                <input type="text" name='name_customer' value='{{ Auth::user()->name }}'placeholder='Имя'>
                                @error('name_customer')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                        </div>
                            <div class="block_inpit_form_search">
                                <input type="email" name='email_customer' value='{{ Auth::user()->email }}'placeholder='Эл. почта'>
                                @error('email_customer')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                        </div>
                            <div class="block_inpit_form_search">
                                <input type="text" name='phone_customer' value='{{ Auth::user()->phone }}'placeholder='Номер телефона'>
                                @error('phone_customer')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                        </div>
                        </div>
                    </div>
                @else
                    <div class='flex_customer'>
                        <label>Данные заказчика</label>
                        <div class="flex_search_form">
                            <div class="block_inpit_form_search">
                                <input type="text" name='name_customer' placeholder='Имя'>
                                @error('name_customer')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                        </div>
                            <div class="block_inpit_form_search">
                                <input type="email" name='email_customer' placeholder='Эл. почта'>
                                @error('email_customer')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                        </div>
                            <div class="block_inpit_form_search">
                                <input type="text" name='phone_customer' placeholder='Номер телефона'>
                                @error('phone_customer')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                        </div>
                        </div>
                    </div>
                @endif

                <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
                @error('g-recaptcha-response')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <button class='blue_btn' type='submit'>Заказать поиск</button>
            </form>
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">
        
    </div>
</section>

<section class='content_search_application'>
    <div class="container grid_two_page">
        <div class='block_search_aplication'>
            <div class="index_title">О проекте</div>
            <div class="text_search_application">
                <ul>
                    <li>Сообщите точное, насколько это возможно, местоположение захоронения Вашего родственника (название и расположения кладбища, номер места). Если Вы не помните точно все эти данные, сообщите нам приблизительное местоположение, наши сотрудники найдут это место захоронения даже по очень приблизительной информации от Вас;</li>
                    <li>Данное место захоронения будет внесено в электронную базу проекта «Помним» с указанием его точного местоположения, захоронению будет присвоен уникальный ID номер;</li>
                    <li>Проведена экспертиза и рекомендации по реконструкции и благоустройству места захоронения. Будут сделаны фото места захоронения, чтобы Вы могли дистанционно оценить состояние памятника или креста, ограды, участка и заказать необходимые работы по благоустройству места захоронения и уходу на ним, например:</li>
                    <li>Нанесение портрета и выбивка текста на установленном памятнике</li>
                    <li>Изготовление и установка нового медальона, таблички из нержавеющей стали либо гранита</li>
                    <li>Обновление гравированного текста на памятнике</li>
                    <li>Покраска металической ограды или чистка гранитной ограды, уборка территории, установка свечи, возложение цветов</li>
                    <li>Ремонт памятника и ограды, укладка плитки и установка бордюров</li>
                    <li>Уборка могилы, надмогильного памятника (склепа)</li>
                    <li>Всего мы предлагаем более 50 видов работ по благоустройству и уходу за местами захоронения</li>
                    <li>По окончании работ Вам будет прислан полный фотоотчет о проделанной работе</li>
                </ul>
            </div>
        </div>
        <div class="sidebar">
            <div class="btn_border_blue"  data-bs-toggle="modal" data-bs-target="#beautification_form"><img src="{{asset('storage/uploads/Frame (20).svg')}}" alt="">Облагородить могилу</div>
        </div>
    </div>
</section>

<section class='how_work_search'>
    <div class="container">
        <div class="index_title">Как это работает?</div>
            <div class="ul_how_work_search">
                <div class="li_care">
                    <div class="number_li_care">1</div>
                    <div class="title_li">Оформление заявки</div>
                    <div class="text_li">Вы заполняете форму Заявки, в которой указываете контактные данные и данные для поиска</div>
                </div>
                <div class="li_care">
                    <div class="number_li_care">2</div>
                    <div class="title_li">Обработка заявки</div>
                    <div class="text_li">Мы получаем Заявку и начинаем поиск, Вам приходит сообщение о том, что работа по заявке началась</div>
                </div>
                <div class="li_care">
                    <div class="number_li_care">3</div>
                    <div class="title_li">Внесение в базу</div>
                    <div class="text_li">По результатам поиска захоронения мы вносим полученные данные в нашу базу</div>
                </div>
                <div class="li_care">
                    <div class="number_li_care">4</div>
                    <div class="title_li">Список рекомендаций</div>
                    <div class="text_li">Мы присылаем вам список рекомендаций по уходу за захоронением</div>
                </div>
            </div>
        </div>
    </div>
    
</section>


@include('footer.footer')
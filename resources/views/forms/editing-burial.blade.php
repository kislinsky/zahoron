<?php 

use Illuminate\Support\Facades\Auth;

$user=null;
if(Auth::check()){
    $user=Auth::user();
}
?>
@if ($user!=null)
    <div class="modal fade" id="editing_burial_form"  tabindex="-1" aria-labelledby="editing_burial_form" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body message">
                    <div class="flex_title_message">
                        <div class="title_middle">Редактировать захоронение</div>
                        <div data-bs-dismiss="modal" class="close_message">
                            <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                        </div>
                    </div>
                    <form action="{{ route('info-burial.edit',$product->id) }}" method="get" class='base_form'>
                        @csrf
                        <div class="block_input" >
                            <label for="">Фамилия</label>
                            <input type="text" name='surname_editing_burial' placeholder="Фамилия">
                            @error('name_editing_burial')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="block_input" >
                            <label for="">Имя</label>
                            <input type="text" name='name_editing_burial' placeholder="Имя">
                            @error('name_editing_burial')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="block_input" >
                            <label for="">Отчество</label>
                            <input type="text" name='patronymic_editing_burial' placeholder="Отчество">
                            @error('name_editing_burial')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex_input_form_contacts">
                            <div class="block_input" >
                                <label for="">Дата рождения</label>
                                <input type="date" name='date_birth_editing_burial' >
                                @error('date_birth_editing_burial')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="block_input" >
                                <label for="">Дата смерти</label>
                                <input type="date" name='date_death_editing_burial' >
                                @error('date_death_editing_burial')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="block_input beautification_form_checkbox">
                            <label class='flex_input_checkbox'> <input type="radio" value='Ветеран ВОВ'name="who_editing_burial" >Ветеран ВОВ</label>
                            <label class='flex_input_checkbox'> <input type="radio" value='СВО'name="who_editing_burial" >СВО</label>
                            <label class='flex_input_checkbox'> <input type="radio" value='Неопознанный'name="who_editing_burial" >Неопознанный</label>                                
                            @error('who_editing_burial')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                        <button type='submit'class="blue_btn btn_life_story">Сохранить</button> 
                    </form>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="modal fade" id="editing_burial_form"  tabindex="-1" aria-labelledby="editing_burial_form" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style='max-width:610px;'>
            <div class="modal-content">
                <div class="modal-body message">
                    <div class="flex_title_message">
                        <div class="title_middle">Редактирование захоронений доступно только зарегистрированным пользователям</div>
                        <div data-bs-dismiss="modal" class="close_message">
                            <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                        </div>
                    </div>
                    <div class="flex_btn_error">
                        <a href='#'class="border_blue_btn">Зарегистрироваться</a>
                        <a href="#" class="blue_btn">Войти</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
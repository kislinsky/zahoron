<?php 

use Illuminate\Support\Facades\Auth;

$user=null;
if(Auth::check()){
    $user=Auth::user();
}
?>
@if ($user!=null)
    <div class="modal fade" id="life_story_form"  tabindex="-1" aria-labelledby="life_story_form" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body message">
                    <div class="flex_title_message">
                        <div class="title_middle">Добавить некролог или историю жизни</div>
                        <div data-bs-dismiss="modal" class="close_message">
                            <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                        </div>
                    </div>
                    <form action="{{ route('life-story.add',$product->id) }}" method="get" class='base_form'>
                        @csrf
                        <div class="block_input" >
                            <label for="">Текст</label>
                            <textarea name="content_life_story" placeholder="Ваш текст" cols="30" rows="10"></textarea>
                            @error('content_life_story')
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
    <div class="modal fade" id="life_story_form"  tabindex="-1" aria-labelledby="life_story_form" aria-hidden="true">
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
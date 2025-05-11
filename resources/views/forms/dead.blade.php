
<?php 

use Illuminate\Support\Facades\Auth;
use App\Models\City;

$mortuaries=selectCity()->mortuaries;
$user=null;
if(Auth::check()){
    $user=Auth::user();
}

?>

<div class="modal fade" id="dead_form"  tabindex="-1" aria-labelledby="dead_form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="text_center">
                        <div class="title_middle">Узнать информацию по умерешему</div>
                    </div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <form action="{{ route('dead.send') }}" method="get" class='form_popup'>
                    @csrf
                    <input type="hidden" name="time_now" class='input_time_now'>

                    <div class="flex_input_form_contacts flex_beautification_form">
                        <div class="block_input" >
                            <label for="">Выберите город</label>
                            <div class="block_ajax_input_search_cities">
                                <input class='input_search_cities' type="text" name="city_search" id="" value='{{ selectCity()->title }}'>
                                <input type="hidden" name="city_dead" class='city_id_input' value='{{ selectCity()->id }}'>
                            </div>
                            @error('city_dead')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                        <div class="block_input" >
                            <div class="flex_input"><label for="">Выберите морг</label> <label class='flex_input_checkbox checkbox'><input type="checkbox" name='none_mortuary'>Неизвестно</label></div>
                            <div class="select"><select name="mortuary_dead" >
                                {{ view('components.components_form.mortuaries',compact('mortuaries')) }}
                            </select></div>
                            @error('mortuary_dead')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                    </div>
                   
                    <div class="block_input" >
                        <label for="">Ф.И.О. Умершего</label>
                        <input  type="text" name="fio_dead" placeholder="Иванов Иван Иванович">
                        @error('fio_dead')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>  
                    <div class="block_info_user_form">
                        <div class="flex_input_form_contacts flex_beautification_form">
                            <div class="block_input">
                                <label for="">Имя</label>
                                <input type="text" name='name_dead' placeholder="Имя" <?php if($user!=null){echo 'value='.$user->name;}?>>
                                @error('name_dead')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                            <div class="block_input">
                                <label for="">Номер телефона</label>
                                <input type="text" class='phone' name="phone_dead" id="" placeholder="Номер телефона" <?php if(isset($user)){if($user!=null){echo 'value="'.$user->phone.'"';}}?> >
                                @error('phone_dead')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                        </div>
                    </div>
                    <label class="aplication checkbox active_checkbox">
                        <input required type="checkbox" name="aplication"  checked >
                        <p>Я согласен на обработку персональных данных в соответствии с Политикой конфиденциальности</p>
                    </label>
                    <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    <div class="flex_btn block_call_time">
                        <div class="btn_bac_gray open_call_time">
                            Позвонить по времени<img src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                        </div>
                        <div class="call_time">
                            <input class="btn_bac_gray" type="time" name="call_time" id="">
                            <label class="aplication checkbox">
                                <input  type="checkbox" name="call_tomorrow" >
                                <p>Позвонить завтра</p>
                            </label>
                        </div>
                            
                        <button type='submit'class="blue_btn">Запросить информацию</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



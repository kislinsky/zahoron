
<?php 

use Illuminate\Support\Facades\Auth;
$mortuaries=selectCity()->mortuaries;
$cemeteries_beatification=selectCity()->cemeteries;;

$user=null;
if(Auth::check()){
    $user=Auth::user();
}

?>

<div class="modal fade" id="funeral_services_form"  tabindex="-1" aria-labelledby="funeral_services_form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="text_center">
                        <div class="title_middle">Быстрый запрос стоимости</div>
                        <div class="text_block">от 10 ритуальных агенств</div>
                    </div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <form action="{{ route('funeral-service.send') }}" method="get" class='form_popup'>
                    @csrf
                    <input type="hidden" name="time_now" class='input_time_now'>

                    <div class="flex_input_form_contacts flex_beautification_form">
                        <div class="block_input" >
                            <label for="">Выберите услугу</label>
                            <div class="select"><select name="funeral_service" >
                                <option value="1">Отправка груз 200</option>
                                <option value="2">Организация кремации</option>
                                <option value="3">Организация похорон</option>
                            </select></div>
                            @error('funeral_service')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  

                        <div class="block_input" >
                            <label class='label_city'for="">Город отправки</label>
                            <div class="block_ajax_input_search_cities">
                                <input class='input_search_cities' type="text" name="city_search" id="" value='{{ selectCity()->title }}'>
                                <input type="hidden" name="city_funeral_service" class='city_id_input' value='{{ selectCity()->id }}'>
                            </div>
                            @error('city_funeral_service')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  

                    </div>
                    <div class="flex_input_form_contacts flex_beautification_form">

                        <div class="block_input service_funeral_arrangements" >
                            <label for="">Выберите кладбище</label>
                            <div class="select"><select name="cemetery_funeral_service" >
                                {{view('components.components_form.cemetery',compact('cemeteries_beatification'));}}
                            </select></div>
                            @error('cemetery_funeral_service')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                        <div class="block_input service_cargo_200" >
                            <div class="flex_input"><label for="">Город получения</label> <label class='flex_input_checkbox checkbox'><input type="checkbox" name='none_mortuary'>Заграница</label></div>
                            <div class="block_ajax_input_search_cities">
                                <input search='false' class='input_search_cities' type="text" name="city_search" id="" value='{{ selectCity()->title }}'>
                                <input  type="hidden" name="city_funeral_service_to" class='city_funeral_service_to city_id_input' value='{{ selectCity()->id }}'>
                            </div>
                            @error('city_funeral_service_to')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  

                        <div class="block_input" >
                            <div class="flex_input"><label for="">Выберите морг</label> <label class='flex_input_checkbox checkbox' ><input type="checkbox" name='none_mortuary'>Неизвестно</label></div>
                            <div class="select"><select name="mortuary_funeral_service" >
                                {{ view('components.components_form.mortuaries',compact('mortuaries')) }}
                            </select></div>
                            @error('mortuary_funeral_service')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  

                    </div> 

                    <div class="flex_input_form_contacts flex_beautification_form">

                        <div class="block_input" >
                           <label for="">Статус умершего</label> 
                           <div class="select"> <select name="status_death_people_funeral_service" >
                               <option value="Неработающий пенсионер">Неработающий пенсионер</option>
                            </select></div>
                            @error('status_death_people_funeral_service')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  

                        <div class="block_input" >
                            <label for="">Гражданский статус</label> 
                             <div class="select"><select name="civilian_status_people_funeral_service" >
                                <option value="Гражданский">Гражданский</option>
                             </select></div>
                             @error('city_memorial')
                                 <div class='error-text'>{{ $message }}</div>
                             @enderror
                         </div>  

                    </div> 

                    <div class="flex_input_form_contacts flex_beautification_form">

                        <div class="block_input" >
                            <label class="aplication checkbox">
                                <input  type="checkbox" name="funeral_service_church"  >
                                <p>Отпевание в церкви</p>
                            </label>
                        </div>  

                        <div class="block_input" >
                            <label class="aplication checkbox">
                                <input  type="checkbox" name="farewell_hall"  >
                                <p>Прощальный зал</p>
                            </label>
                         </div>  

                    </div> 

                    <div class="block_info_user_form">
                        <div class="flex_input_form_contacts ">
                            <div class="block_input">
                                <label for="">Имя</label>
                                <input type="text" name='name_funeral_service' placeholder="Имя" <?php if($user!=null){echo 'value='.$user->name;}?>>
                                @error('name_memorial')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                            <div class="block_input">
                                <label for="">Номер телефона</label>
                                <input type="text" class='phone' name="phone_funeral_service" id="" placeholder="Номер телефона" <?php if(isset($user)){if($user!=null){echo 'value="'.$user->phone.'"';}}?> >
                                @error('phone_memorial')
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
                        
                        <button type='submit'class="blue_btn">Получить ценовое предложение</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


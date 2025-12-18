<!-- Модальное окно для обратной связи -->
<div class="modal fade" id="feedback_modal" tabindex="-1" aria-labelledby="feedback_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="text_center">
                        <div class="title_middle">Обратная связь</div>
                        <div class="text_block">Задайте вопрос, мы свяжемся с вами</div>
                    </div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <form action="{{ route('feedback.store') }}" method='post' class='form_popup'>
                    @csrf
                    <div class="block_input">
                        <label for="">Выберите тему вопроса</label>
                        <div class="select">
                            <select name="theme_feedback" id="">
                                <option value="Поиск могил">Поиск могил</option>
                                <option value="Ритуальные услуги">Ритуальные услуги</option>
                                <option value="Облагораживание могил">Облагораживание могил</option>
                                <option value="Не верная информация">Не верная информация</option>
                                <option value="Размещение фирмы">Размещение фирмы</option>
                                <option value="Вакансии">Вакансии</option>
                            </select>
                        </div>
                        @error('theme_feedback')
                        <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>  
                    
                    <div class="block_input">
                        <label for="">Задайте свой вопрос</label>
                        <textarea name="faq_feedback" id="" cols="30" rows="5" placeholder="Ваш вопрос"></textarea>
                        @error('faq_feedback')
                        <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>               
                    
                    <div class="flex_input_form_contacts">
                        <div class="block_input">
                            <label for="">Имя</label>
                            <input type="text" name='name_feedback' placeholder="Имя">
                            @error('name_feedback')
                            <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                        <div class="block_input">
                            <label for="">Номер телефона</label>
                            <input type="text" name='phone_feedback'  class='phone' placeholder="Номер телефона">
                            @error('phone_feedback')
                            <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                    </div>
                    
                    <label class="aplication checkbox active_checkbox">
                        <input required type="checkbox" name="aplication" checked>
                        <p>Я согласен на обработку персональных данных в соответствии с Политикой конфиденциальности</p>
                    </label>
                    
                    <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
                    @error('g-recaptcha-response')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <button type='submit' class="blue_btn">Отправить запрос</button>
                </form>
            </div>
        </div>
    </div>
</div>

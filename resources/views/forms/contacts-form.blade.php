<section class="form_contacts">
    <div class="container">
        <form action="" method='post'>
                @csrf
                <div class="block_input">
                    <label for="">Выберите тему вопроса</label>
                    <select name="theme" id="">
                        <option value="Поиск могил">Поиск могил</option>
                    </select>    
                    @error('theme')
                    <div class='error-text'>{{ $message }}</div>
                    @enderror
                </div>  
                <div class="block_input">
                    <label for="">Задайте свой вопрос</label>

                    <textarea name="faq" id="" cols="30" rows="10" placeholder="Ваш вопрос"></textarea>
                    @error('faq')
                    <div class='error-text'>{{ $message }}</div>
                    @enderror
                </div>               
                <div class="flex_input_form_contacts">
                    <div class="block_input">
                        <label for="">Имя</label>
                        <input type="text" name='name' placeholder="Имя">
                        @error('name')
                        <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>  
                    <div class="block_input">
                        <label for="">Номер телефона</label>
                        <input type="text" name='phone' placeholder="Номер телефона">
                        @error('phone')
                        <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>  
                </div>
                <div class="block_input"><button class="blue_btn">Отправить запрос</button></div>
        </form>
    </div>
</section>
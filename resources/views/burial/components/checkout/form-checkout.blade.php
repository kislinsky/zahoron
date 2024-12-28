<form method='post' action="{{ route('order.burial.add') }}" class="checkout_form">
    @csrf
    <div class="title_checkout_form">После оплаты, в личном кабинете сайта будет размещены услуги захоронения. <br>Если вы не были зарегистрированы, вы получите пароль для входа в личный кабинет</div>
    <div class="flex_checkout_form">
        <div class="block_checkout_form">
            <label for="">Имя</label>
            <input type="text" name="name" id="" placeholder="Имя"<?php if(isset($user)){if($user!=null){echo 'value='.$user->name;}}?>>
            @error('name')
            <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_checkout_form">
            <label for="">Фамилия</label>
            <input type="text" name="surname" id="" placeholder="Фамилия" <?php if(isset($user)){if($user!=null){echo 'value='.$user->surname;}}?>>
            @error('surname')
            <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="flex_checkout_form">
        <div class="block_checkout_form">
            <label for="">Эл. почта</label>
            <input type="eamil" name="email" id="" placeholder="Эл. почта" <?php if(isset($user)){if($user!=null){echo 'value='.$user->email;}}?>>
            @error('email')
            <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_checkout_form">
            <label for="">Номер телефона</label>
            <input type="text" name="phone" class='phone' id="" placeholder="Номер телефона" <?php if(isset($user)){if($user!=null){echo 'value="'.$user->phone.'"';}}?> >
            @error('phone')
            <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="block_checkout_form">
        <label for="">Комментарий к заказу</label>
        <textarea name="message" id="" cols="30" rows="10" placeholder="Ваш текст"></textarea>
    </div>
    <div class="block_checkout_form">
        <div class="title_middle">Способ оплаты</div>
        <label>
            <input class='input_choose' value='1' type="radio" name="choose_pay" selected id="" checked>
            Оплата картой онлайн
        </label>
        @error('choose_pay')
        <div class='error-text'>{{ $message }}</div>
        @enderror
    </div>
    <div class="block_checkout_form">
        <label class='checkbox active_checkbox'><input checked value='1'class='input_choose'type="checkbox" name="aplication" id="">Я согласен на обработку персональных данных в соответствии с <br><a href=''>Политикой конфиденциальности</a></label>
        @error('aplication')
        <div class='error-text'>{{ $message }}</div>
        @enderror
    </div>
    <button type='submit' class="blue_btn input_choose">Оформить заказ</button>
</form>
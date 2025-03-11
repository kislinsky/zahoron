<?php $user=user();?>
<div class="block_input" >
    <label for="">Имя</label>
    <input type="text" name="name" id="" <?php if(isset($user)){if($user!=null){echo 'value='.$user->name;}}?> placeholder="Имя">
    @error('name')
        <div class='error-text'>{{ $message }}</div>
    @enderror  
</div>  
<div class="block_input" >
    <label for="">Телефон</label>
    <input type="text" name="phone" class='phone' id="" <?php if(isset($user)){if($user!=null){echo 'value='.$user->phone;}}?> placeholder="+79594953453">
    @error('phone')
        <div class='error-text'>{{ $message }}</div>
    @enderror  
</div>  
 
<div class="block_input" >
    <label for="">Комментарий к заказу</label>
    <textarea name="message" id="" cols="30" rows="10" placeholder="Комментарий к заказу"></textarea>
    @error('email')
        <div class='error-text'>{{ $message }}</div>
    @enderror  
</div>  
<div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
    @error('g-recaptcha-response')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
<button class="blue_btn">
    Оформить заявку
</button>
<?php $user=$agent;?>
<div class="block_input" >
    <label for="">Имя</label>
    <input type="text" name="name" id="" <?php if(isset($user)){if($user!=null){echo 'value='.$user->name;}}?> placeholder="Имя">
    @error('name')
        <div class='error-text'>{{ $message }}</div>
    @enderror  
</div>  
<div class="block_input" >
    <label for="">Телефон</label>
    <input type="text" name="phone" id="" <?php if(isset($user)){if($user!=null){echo 'value='.$user->phone;}}?> placeholder="+79594953453">
    @error('phone')
        <div class='error-text'>{{ $message }}</div>
    @enderror  
</div>  
<div class="block_input" >
    <label for="">Почта</label>
    <input type="email" name="email" id="" <?php if(isset($user)){if($user!=null){echo 'value='.$user->email;}}?> placeholder="Почта">
    @error('email')
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
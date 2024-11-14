<div class="block_inpit_form_search">
    <div class="title_middle settings_margin_form">Настройки уведомлений</div>
   <label class='flex_input_checkbox'>
       Email уведомления 
       <label class="switch">
           <input type="checkbox" name='email_notifications'  value='1' <?php if($user['email_notifications']!=null){ echo'checked';}?>>
           <span class="slider"></span>
       </label>
   </label>
   <label class='flex_input_checkbox '>
       SMS уведомления
       <label class="switch">
           <input type="checkbox" name='sms_notifications' value='1' <?php if($user['sms_notifications']!=null){ echo'checked';}?>>
           <span class="slider"></span>
       </label>
</div>
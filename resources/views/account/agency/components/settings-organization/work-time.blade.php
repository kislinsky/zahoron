<div class="block_inpit_form_search">
    <div class="title_middle">Режим работы</div>
    <div class="flex_btn">
        <div class="blue_btn working_not_all_time">По расписанию</div>
        <div class="gray_btn working_all_time">Круглосуточно</div>
    </div>
    <div class="flex_input_form_contacts">
        <div class="block_input" >
            <div class="flex_input"><label for="">Пн</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Monday' <?php if(isset($days[0])){if($days[0]->holiday=='1'){ echo'checked';}}?>>
                        <span class="slider"></span>
                    </label>
                </label>
            </div> 
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  value="@if(isset($days[0])){{$days[0]->time_start_work}} - {{$days[0]->time_end_work}}@endif">

        </div>  
        <div class="block_input" >
            <div class="flex_input"><label for="">Вт</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Tuesday' <?php if(isset($days[1])){if($days[1]->holiday=='1'){ echo'checked';}}?>>
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  value="@if(isset($days[1])){{$days[1]->time_start_work}} - {{$days[1]->time_end_work}}@endif">

        </div>     
    </div>
    <div class="flex_input_form_contacts">
        <div class="block_input" >
            <div class="flex_input"><label for="">Ср</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Wednesday' <?php if(isset($days[2])){if($days[2]->holiday=='1'){ echo'checked';}}?>>
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  value="@if(isset($days[2])){{$days[2]->time_start_work}} - {{$days[2]->time_end_work}}@endif">

        </div>  
        <div class="block_input" >
            <div class="flex_input"><label for="">Чт</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Thursday' <?php if(isset($days[3])){if($days[3]->holiday=='1'){ echo'checked';}}?>>
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  value="@if(isset($days[3])){{$days[3]->time_start_work}} - {{$days[3]->time_end_work}}@endif">

        </div>     
    </div>
    <div class="flex_input_form_contacts">
        <div class="block_input" >
            <div class="flex_input"><label for="">Пт</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Friday' <?php if(isset($days[4])){if($days[4]->holiday=='1'){ echo'checked';}}?>>
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  value="@if(isset($days[4])){{$days[4]->time_start_work}} - {{$days[4]->time_end_work}}@endif">

        </div>  
        <div class="block_input" >
            <div class="flex_input"><label for="">Сб</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Saturday' <?php if(isset($days[5])){if($days[5]->holiday=='1'){ echo'checked';}}?>>
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  value="@if(isset($days[5])){{$days[5]->time_start_work}} - {{$days[5]->time_end_work}}@endif">

        </div>     
    </div>
    <div class="flex_input_form_contacts">
        <div class="block_input" >
            <div class="flex_input"><label for="">Вс</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Sunday' <?php if(isset($days[6])){if($days[6]->holiday=='1'){ echo'checked';}}?>>
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  value="@if(isset($days[6])){{$days[6]->time_start_work}} - {{$days[6]->time_end_work}}@endif">

        </div>  
          
    </div>
    
</div>

<script>
    $( ".working_all_time" ).on( "click", function() {
        $(this).removeClass('gray_btn')
        $(this).addClass('blue_btn')
        $('.working_not_all_time').addClass('gray_btn')
        $('.working_not_all_time').removeClass('blue_btn')
        $('input[name="working_day[]"]').val('00:00 - 24:00')
        $('input[name="holiday_day[]"]').prop('checked', false);
    })
    $( ".working_not_all_time" ).on( "click", function() {
        $(this).removeClass('gray_btn')
        $(this).addClass('blue_btn')
        $('.working_all_time').addClass('gray_btn')
        $('.working_all_time').removeClass('blue_btn')
    })

    


</script>
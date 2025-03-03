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
                        <input type="checkbox" name='holiday_day[]' value='Monday' >
                        <span class="slider"></span>
                    </label>
                </label>
            </div> 
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  >

        </div>  
        <div class="block_input" >
            <div class="flex_input"><label for="">Вт</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Tuesday' >
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00" >

        </div>     
    </div>
    <div class="flex_input_form_contacts">
        <div class="block_input" >
            <div class="flex_input"><label for="">Ср</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Wednesday'>
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  >

        </div>  
        <div class="block_input" >
            <div class="flex_input"><label for="">Чт</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Thursday'>
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00" >

        </div>     
    </div>
    <div class="flex_input_form_contacts">
        <div class="block_input" >
            <div class="flex_input"><label for="">Пт</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Friday' >
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  >

        </div>  
        <div class="block_input" >
            <div class="flex_input"><label for="">Сб</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Saturday' >
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  >

        </div>     
    </div>
    <div class="flex_input_form_contacts">
        <div class="block_input" >
            <div class="flex_input"><label for="">Вс</label> <label class='flex_input_checkbox time_flex'>
                Выходной
                    <label class="switch">
                        <input type="checkbox" name='holiday_day[]' value='Sunday'>
                        <span class="slider"></span>
                    </label>
                </label>
            </div>   
            <input type="text" name='working_day[]'placeholder="09:00 - 17:00"  >

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
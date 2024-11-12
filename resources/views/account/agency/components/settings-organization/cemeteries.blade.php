<div class="block_inpit_form_search">
        
    <div class="title_middle">Список кладбищ:</div>

    <div class="block_input input_location_flex">
        <div class="input_location_settings">
            <div class="input_location">
                <input type="hidden" name="id_cemetery" >
                <img  data-bs-toggle="modal" data-bs-target="#location_form_2" class='open_location' src="{{ asset('storage/uploads/Закрыть.svg') }}" alt="">
                <input type="text" name='location_cemetery' placeholder='Расположение'>
            </div>
            <div class='text_location_input'>Впишите название кладбища (или района/
                области) либо нажмите "+" и выберите из списка</div>
        </div>
        <div class="blue_btn add_cemetery">Добавить кладбище</div>

    </div> 
    <div class="ul_cemtery">
        @foreach ($cemeteries as $cemetery)
        <div class="li_cemetery_agent">
            <div class="mini_flex_li_product">
                <input type="hidden" value='{{ $cemetery->id }}'name="cemetery_ids[]">
                <div class="title_label">{{ $cemetery->title }}</div>
                <div class="text_li">Адрес: {{ $cemetery->adres }}</div>
            </div>
            <div  class="delete_cart delete_cemetery"><img src="{{asset('storage/uploads/Закрыть (1).svg')}}" alt=""></div>
        </div>
            
        @endforeach
           
    </div>
</div>


<script>
    $('.li_cemetery_3').on('click',function() {
        let name=$(this).html()
        let id=$(this).attr('id_cemetery')
        $('.input_location_settings').children('.input_location').children('input[name="id_cemetery"]').val(id);
        $('.input_location_settings').children('.input_location').children('input[name="location_cemetery"]').val(name);
         $('#location_form_2').modal('hide')
        // $('.edge_li').show()
        // $('.li_cemetery_3').hide()
        // $('.city_li').hide()
        // $('.cities_ul').hide()
        // $('.ul_location_main').css('display','grid')
        
    })
    $('input[name="location_cemetery"]').on( "change", function() {
        $('input[name="id_cemetery"]').val(' ');
    })
    $( ".add_cemetery" ).on( "click", function() {
    
        let id_location= $(this).siblings('.input_location_settings').children('.input_location').children('input[name="id_cemetery"]').val();
        let name_location = $(this).siblings('.input_location_settings').children('.input_location').children('input[name="location_cemetery"]').val();
        $.ajax({
            type: 'POST',
            url: '{{ route("add.cemetery.settings") }}',
            data: {
                "_token": "{{ csrf_token() }}",
                'id_location': id_location,
                'name_location': name_location,
            }, success: function (result) {
                
                if(result['error']){
                    alert(result['error'])
                }else{
                    $('.ul_cemtery').append('<div class="li_cemetery_agent"><div class="mini_flex_li_product"><input type="hidden" value="'+result['id_cemetery']+'"name="cemetery_ids[]"><div class="title_label">'+name_location+'</div><div class="text_li">Адрес: "'+result['adres']+'"</div></div><div  class="delete_cart delete_cemetery"><img src="{{asset('storage/uploads/Закрыть (1).svg')}}" alt=""></div></div>' );
                }
            },
            error: function () {
                alert('Ошибка');
            }
        });

    });
</script>
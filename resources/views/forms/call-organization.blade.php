<div class="modal fade" id="call_organization" tabindex="-1" aria-labelledby="call_organization" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="text_center">
                        <div class="title_middle">Отправить код на номер компании</div>
                    </div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                @if(user()!=null && (user()->role=='organization' || user()->role=='organization-provider'))
                    <form class='send_code_call_organization'>
                        <input type="hidden" name="organization_id_call" value=''>
                        <div class="blue_btn">Отправить</div>
                    </form>
                    
                    <form class='accept_code_call_organization' >
                        <div class="flex_btn_error">
                            <div class="block_input">
                                <input type="integer" placeholder='0000'>
                            </div>
                            <div class="blue_btn" >Подвердить</div>
                        </div>
                    </form>
                @else
                    <div class="flex_btn_error">
                        <a href='{{ route('register') }}'class="border_blue_btn">Зарегистрироваться</a>
                        <a href="{{ route('login') }}" class="blue_btn">Войти</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<script>
    
    $( ".send_code_call_organization .blue_btn" ).on( "click", function() {
        let filters  = {
            '_token':"{{ csrf_token() }}",
            'organization_id':$(this).siblings('input').val(),
        };
        $.ajax({
            type: 'POST',
            url: '{{route('organization.send-code')}}',
            data: filters,
            success: function (result) {
                $('.send_code_call_organization').hide()
                $('.accept_code_call_organization').show()
            },
            error: function () {
                alert('Ошибка')
            }
        });
    });


    $( ".accept_code_call_organization .blue_btn" ).on( "click", function() {
        let filters  = {
            '_token':"{{ csrf_token() }}",
            'code':$(this).siblings('.block_input').children('input').val(),
            'organization_id':$( ".send_code_call_organization .blue_btn" ).siblings('input').val(),
        };
        $.ajax({
            type: 'POST',
            url: '{{route('organization.accept-code')}}',
            data: filters,
            success: function (result) {
                if(result!='error'){
                    $('.accept_code_call_organization').hide()
                    $('#call_organization .title_middle').html('Организация добавлена в ваш лк.')
                }else{
                    alert('Ошибка')
                }
                
            },
            error: function () {
                alert('Ошибка')
            }
        });
    });
</script>


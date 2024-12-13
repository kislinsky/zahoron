@extends('account.agency.components.page')
@section('title', "Заявки в работе  по ритуальным услугам")

@section('content')

    <div class="block_input margin_top_20 service_filter">
        <label for="">Вид ритуальных услуг</label>
        <div class="select">
            <select name="service" id="">
                <option value="1">Отправка груз 200</option>
                <option value="2">Организация кремации</option>
                <option value="3">Организация похорон</option>
            </select>
        </div>
    </div>

    <div class="html_result">
        {{ view('account.agency.components.aplication.funeral-service.show-aplications',compact('aplications')) }}
    </div>


<script>


$( ".service_filter .select select" ).on( "change", function() {
    $('.bac_loader').show()
    $('.load_block').show()
    filters={
        'service':$(this).children('option:checked').val(),
        'status':1
    }
    $.ajax({
            type: 'GET',
            url: '{{route('account.agency.organization.aplication.funeral-service.filter')}}',
            data: filters,
            success: function (result) {
                $('.bac_loader').fadeOut()
                $('.load_block').fadeOut()
                $('.html_result').html(result)
                let strings = [];
                for (const [key, value] of Object.entries(filters)) {
                    strings.push(key+"="+value)
                }
                let st = strings.join("&")
                window.history.pushState('funeral-service', 'Title', '/{{selectCity()->slug}}/account/agency/aplication/funeral-service/in-work?'+st);
            },
            error: function () {
                alert('Ошибка');
                $('.bac_loader').fadeOut()
                $('.load_block').fadeOut()
            }
        });

})
    
</script>

@endsection
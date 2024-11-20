<div class="block_input">
    <div class="text_middle_index">Категория</div>
    <div class="select">
        <select name="cat" >
            @foreach ($categories as $categories_one)
                <option type='{{$categories_one->type}}' value="{{$categories_one->id}}">{{$categories_one->title}}</option>
            @endforeach
        </select>
    </div>
</div>


<div class="block_input">
    <div class="text_middle_index">Подкатегория</div>
    <div class="select">
        <select name="cat_children" >
            @foreach ($categories_children as $categories_one)
                <option value="{{$categories_one->id}}">{{$categories_one->title}}</option>
            @endforeach
        </select>
    </div>
</div>


<script>

$('select[name="cat"]').on( "change", function() {
    let type=$(this).children('option:checked').attr('type')
    let cat_id=$(this).children('option:checked').val()
    $.ajax({
        type: 'GET',
        url: '{{ route("category.product.children.ul") }}',
        data: {
            "_token": "{{ csrf_token() }}",
            'cat_id': cat_id,
        }, success: function (result) {
            if(type=='beatification'){
                $('.block_additionals').hide()
                $('.block_beatification').show()
            }
            if(type=='funeral-service'){
                $('.block_additionals').hide()
                $('.block_funeral_service').show()
            }
            if(type=='organization-commemorations'){
                $('.block_additionals').hide()
                $('.block_organization_commemorations').show()
            }
            $('select[name="cat_children"]').html(result)
        },
        error: function () {
            alert('Ошибка');
        }
    });
})
</script>
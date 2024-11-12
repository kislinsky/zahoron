<?php 
use App\Models\ImageProduct;

?>
<div class="block_content_organization_single">
    <div class="title_li">Ритуальные услуги и товары ритуального агенства “{{$organization->title}}”</div>
    <div class="flex_select_products_organization">
        <div class="block_input" >
            <label for="">Выберите категорию</label>
            <div class="select_gray">
                <select name="main_category" >
                @if($main_categories!=null && count($main_categories)>0)
                    @foreach ($main_categories as $main_category)
                        <option value="{{$main_category->id}}">{{$main_category->title}}</option>
                    @endforeach
                @endif
                </select>
            </div>
        </div>

        <div class="block_input" >
            <label for="">Выберите подкатегорию</label>
            <div class="select_gray">
                <select name="children_category" >
                @if($children_categories!=null && count($children_categories)>0)
                    @foreach ($children_categories as $children_category)
                        <option value="{{$children_category->id}}">{{$children_category->title}}</option>
                    @endforeach
                @endif
                </select>
            </div>
        </div>
    
    </div>
    <div class="ul_ritual_products">
        {{ view('organization.components.ajax.products-services-organization',compact('ritual_products'));}}
    </div>
    
</div>

<script>
$( "select[name='children_category']" ).on( "change", function() {
    let organization_id={{$organization->id}};
    let category_id= $(this).children('option:checked').val();
    let filters={
        "_token": "{{ csrf_token() }}",
        'category_id': category_id,
        'organization_id':organization_id,
    }
    $.ajax({
        type: 'GET',
        url: '{{route('organization.category.ajax.children')}}',
        data: filters,
        success: function (result) {
            $('.ul_ritual_products').html(result)
            
        },
        error: function () {
            alert('Ошибка');
        }
    });
});


$( "select[name='main_category']" ).on( "change", function() {
    let organization_id={{$organization->id}};
    let category_id= $(this).children('option:checked').val();
    console.log(category_id,organization_id)
    let filters={
        "_token": "{{ csrf_token() }}",
        'category_id': category_id,
        'organization_id':organization_id,
    }
    $.ajax({
        type: 'GET',
        url: '{{route('organization.category.ajax.main')}}',
        data: filters,
        success: function (result) {
            $('.ul_ritual_products').html(result)
            
        },
        error: function () {
            alert('Ошибка');
        }
    });
    $.ajax({
        type: 'GET',
        url: '{{route('organization.categories.ajax.children')}}',
        data: filters,
        success: function (result) {
            $( "select[name='children_category']" ).html(result)
            
        },
        error: function () {
            alert('Ошибка');
        }
    });
});
$( "select[name='children_category']" )


</script>
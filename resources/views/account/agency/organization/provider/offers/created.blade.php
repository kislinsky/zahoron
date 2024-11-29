@extends('account.agency.components.page')
@section('title', "Созданные запросы")

@section('content')

    <div class="flex_btn margin_top_down_20">
        <a  href="{{route('account.agency.provider.offer.add')}}">
            <img class='img_width_50' src="{{ asset('storage/uploads/Закрыть.svg')}}" alt="">
        </a>
        <a href='{{route('account.agency.provider.offer.created')}}' class="blue_btn">Созданные</a>
        <a href='{{route('account.agency.provider.offer.answers')}}' class="gray_btn">Ответы</a>

    </div>

    <div class="block_input margin_top_down_20">
        <div class="select">
            <select name="category" id="">
                <option  @if(0==$category_choose)  selected @endif  value="0">Категория</option>
                @foreach($categories_products_provider as $category)
                    <option  @if($category->id==$category_choose) selected @endif value="{{$category->id}}">{{$category->title}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="block_result">
        {{view('account.agency.components.provider.requests-created-show',compact('requests'))}}
    </div>

<script>

    $("select[name='category']").on( "change", function() {
    
    let id_cat=$(this).children('option:checked').val()
    let filters={
            'category': id_cat,
        }
    console.log(id_cat)
    $.ajax({
        type: 'GET',
        url: '{{ route("account.agency.provider.offer.created.category") }}',
        data: {
            "_token": "{{ csrf_token() }}",
            'category': id_cat,
        }, success: function (result) {
            $('.block_result').html(result)
            let strings = []; 
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }
            let st = strings.join("&")
            window.history.pushState('created', 'Title', '/{{$city->slug}}/account/agency/provider/offers/created?'+st);
        },
        error: function () {
            alert('Ошибка');
        }
    });
    })  
    
    </script> 
@endsection



@include('forms.location')

<?php 

use Illuminate\Support\Facades\Auth;
use App\Models\City;
use App\Models\Cemetery;
use App\Models\ProductPriceList;
use App\Models\CategoryProductPriceList;

$cities_memorial=City::orderBy('title','asc')->get();
$cemeteries_beatification=Cemetery::orderBy('title','asc')->where('city_id',selectCity()->id)->get();
$categories_product_price_list=CategoryProductPriceList::where('parent_id',null)->get();
$user=null;
if(Auth::check()){
    $user=Auth::user();
}

?>

<div class="modal fade" id="beautification_form"  tabindex="-1" aria-labelledby="beautification_form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="text_center">
                        <div class="title_middle">Быстрый запрос стоимости</div>
                        <div class="text_block">от 10 ритуальных агенств</div>
                    </div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <form action="{{ route('beautification.send') }}" method="get" class='form_popup'>
                    @csrf
                    @if(isset($product))
                        <input type="hidden" name="burial_id_beautification" value={{$product->id}}>
                    @endif
                    <div class="flex_input_form_contacts flex_beautification_form">
                        <div class="block_input" >
                            <label for="">Выберите город</label>
                            <div class="select">
                                <select name="city_beautification" >
                                @if(count($cities_memorial)>0)
                                    @foreach ($cities_memorial as $city_memorial)
                                        <option <?php if(selectCity()->id==$city_memorial->id){echo 'selected';}?> value="{{$city_memorial->id}}">{{$city_memorial->title}}</option>
                                    @endforeach
                                @endif
                                </select>
                            </div>
                            @error('city_beautification')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                        <div class="block_input" >
                            <label for="">Выберите кладбище</label>
                            <div class="select"><select name="cemetery_beautification" >
                                {{view('components.components_form.cemetery',compact('cemeteries_beatification'));}}
                            </select></div>
                            @error('cemetery_beautification')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                    </div>


                    <div class="block_input beautification_form_checkbox">
                        <div class="title_news">Выберите товар</div>
                        @if(count($categories_product_price_list)>0)
                            <div  class="ul_products_radio">
                                @foreach($categories_product_price_list as $category_product_price_list)
                                    <label class="checkbox">
                                        <input type="checkbox" value={{$category_product_price_list->id}} name='products_beautification[]'>{{$category_product_price_list->title}}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('product_beautification')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>  

                   
                    <div class="block_info_user_form border_top_beautification_form">
                        <div class="flex_input_form_contacts flex_beautification_form">
                            <div class="block_input">
                                <label for="">Имя</label>
                                <input type="text" name='name_beautification' placeholder="Имя" <?php if($user!=null){echo 'value='.$user->name;}?>>
                                @error('name_beautification')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                            <div class="block_input">
                                <label for="">Номер телефона</label>
                                <input type="text" name="phone_beautification" id="" placeholder="Номер телефона" <?php if(isset($user)){if($user!=null){echo 'value="'.$user->phone.'"';}}?> >
                                @error('phone_beautification')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                        </div>
                    </div>
                    <label class="aplication checkbox active_checkbox">
                        <input required type="checkbox" name="aplication"  checked >
                        <p>Я согласен на обработку персональных данных в соответствии с Политикой конфиденциальности</p>
                    </label>
                    <div class="flex_btn block_call_time">
                        <div class="btn_bac_gray open_call_time">
                            Позвонить по времени<img src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                        </div>
                        <div class="call_time">
                            <input class="btn_bac_gray" type="time" name="call_time" id="">
                            <label class="aplication checkbox">
                                <input  type="checkbox" name="call_tomorrow" >
                                <p>Позвонить завтра</p>
                            </label>
                        </div>
                            
                        <button type='submit'class="blue_btn">Получить ценовое предложение</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $( "#beautification_form select[name='city_beautification']" ).on( "change", function() {
        let data  = {
            "_token": "{{ csrf_token() }}",
            'city_id':$(this).children('option:checked').val(),
        };

        $.ajax({
            type: 'GET',
            url: '{{route('beautification.ajax.cemetery')}}',
            data:  data,
            success: function (result) {
                $( "#beautification_form select[name='cemetery_beautification']" ).html(result)
            },
            error: function () {
                alert('Ошибка');
            }
        });
       
    });
</script>







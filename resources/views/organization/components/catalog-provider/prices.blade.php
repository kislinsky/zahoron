<table>
    <tbody>
        <tr>
            <td class='title_black  main_table_price' rowspan='2'>Цена на {{$category->title}} в  г. {{$city->title}}</td>
            <td class='title_black'  >Минимальная</td>
            <td  class='title_black' >Средняя</td>
            <td  class='title_black' >Максимальная</td>
        </tr>
        <tr>
            <td class='title_blue'>
                @if($price_min==0 || $price_min==null)
                    Уточняйте
                @else
                    {{$price_min}} ₽
                @endif
            </td>
            <td class='title_blue'>
                @if($price_middle==0 || $price_middle==null)
                    Уточняйте
                @else
                    {{$price_middle}} ₽
                @endif
            </td>
            <td class='title_blue'>
                @if($price_max==0 || $price_max==null)
                    Уточняйте
                @else
                    {{$price_max}} ₽
                @endif
            </td>
        </tr>
    </tbody>
</table>
<div class="mini_text_gray">*стоимость услуг приблизительная, уточняйте точную стоимость у агенств по телефону</div>
<table>
    <tbody>
        <tr>
            <td class='title_black main_table_price'  rowspan='2'>Оптовая цена на {{$category->title}} в  г. {{$city->title}}</td>
            <td class='title_black'  >Минимальная</td>
            <td  class='title_black' >Средняя</td>
            <td  class='title_black' >Максимальная</td>
        </tr>
        <tr>
            <td class='title_blue'>{{$price_min}} ₽</td>
            <td class='title_blue'>{{$price_middle}} ₽</td>
            <td class='title_blue'>{{$price_max}} ₽</td>
        </tr>
    </tbody>
</table>
<div class="mini_text_gray">*стоимость услуг приблизительная, уточняйте точную стоимость у агенств по телефону</div>
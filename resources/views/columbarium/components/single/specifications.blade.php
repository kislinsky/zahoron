<?php 
$interval=timeDifference($columbarium->time_start_work,$columbarium->time_end_work);

?>
<div class="block_content_organization_single">
    <div class="ul_memorial_menu">
        <div class="li_memorial_menu">
            <div class="item_memorial_menu item_memorial_menu_1 text_gray">Тип заведения</div>
            <div class="line_gray_menu"></div>
            <div class="item_memorial_menu text_black"> Колумабрий</div>
        </div>
       
        <div class="li_memorial_menu">
            <div class="item_memorial_menu item_memorial_menu_1 text_gray">Круглосуточно</div>
            <div class="line_gray_menu"></div>
            <div class="item_memorial_menu text_black">
                @if($interval!=null && $interval->d==1)
                    Да
                @else
                    Нет
                @endif
            </div>
        </div>
        @if($columbarium->next_to!=null)
        <div class="li_memorial_menu">
            <div class="item_memorial_menu item_memorial_menu_1 text_gray">Рядом с</div>
            <div class="line_gray_menu"></div>
            <div class="item_memorial_menu text_black"> {{$columbarium->next_to}}</div>
        </div>
     @endif
        @if($columbarium->district_id!=null)
            <div class="li_memorial_menu">
                <div class="item_memorial_menu item_memorial_menu_1 text_gray">Район</div>
                <div class="line_gray_menu"></div>
                <div class="item_memorial_menu text_black"> {{$columbarium->district()->title}}</div>
            </div>
        @endif
        @if($columbarium->underground!=null)
            <div class="li_memorial_menu">
                <div class="item_memorial_menu item_memorial_menu_1 text_gray">Метро</div>
                <div class="line_gray_menu"></div>
                <div class="item_memorial_menu text_black"> {{$columbarium->underground}}</div>
            </div>
        @endif
        <div class="li_memorial_menu">
            <div class="item_memorial_menu item_memorial_menu_1 text_gray">Ритуальные обьекты</div>
            <div class="line_gray_menu"></div>
            <div class="item_memorial_menu text_black"> Колумабрий</div>
        </div>
        
    </div>
</div>

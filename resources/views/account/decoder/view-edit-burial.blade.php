@include('header.header-decoder')

@if($burial!=null)
    <div class="account_container">
            <div class="block_edit_burial_decoder">
                <div class="item_1_decoder">
                    <a href='{{route('home')}}' class="blue_btn">Назад</a>
                    {{-- <div class="box_blue">
                    <img src="{{asset('storage/uploads/Plus.svg')}}" alt="">  
                    </div>     --}}
                </div>    
                <div class="item_2_decoder">
                    <div class="img_burial_edit_decoder">
                        <div class="blue_btn">
                            Пожаловаться
                            <div class="comments_burial">
                                <div  class="comment_burial">Размытое изображение</div>
                                <div  class="comment_burial">Памятник мокрый данные нечитаемые</div>
                                <div class="comment_burial">Трава или цветы мешают распознать данные</div>
                                <div class="comment_burial">На фотографии нет памятника или нет данных на памятнике</div>
                                <form action="{{route('account.decoder.burial.add.comment')}}" style='display:none;'>
                                    @csrf
                                    <input type="hidden" name="burial_id" value='{{$burial->id}}'>
                                    <input id='comment_burial' type="hidden" name="comment" value=''>
                                </form>
                            </div>
                        </div>
                        <div id="zoomC" style=' background: url("{{$burial->urlImg()}}");background-position: center;background-size: cover;    background-repeat: no-repeat !important;'class=""><img src="" alt=""></div>

                    </div>

                    <div class="ul_info_edit_burial">
                        <div class="li_info_edit_burial">
                            <div class="text_black_big_bold">Фотограф: </div>
                            <div class="text_black">{{$burial->photographer}}</div>
                        </div>
                        <div class="li_info_edit_burial">
                            <div class="text_black_big_bold">Кладбище: </div>
                            <div class="text_black">{{$burial->cemetery->title}}</div>
                        </div>
                        <div class="li_info_edit_burial">
                            <div class="text_black_big_bold">Расположение: </div>
                            <div class="text_black">{{$burial->location_death}}</div>
                        </div>
                    </div>
                </div>    
                <div class="item_3_decoder">
                    <div>
                        <div class="text_black_big_bold">Проверка</div>
                        <div class="text_black">* если в дате не видно месяц или день введите 01 </div>
                    </div>

                    <form action="{{route('account.decoder.burial.update')}}" class='update_burial_decoder'>
                        @csrf
                        <input type="hidden" name="burial_id" value='{{$burial->id}}'>
                        <div class="flex_input_decoder">
                            <div class="block_input">
                                <label for="">Имя</label>
                                <input type="text" name='name' value={{$burial->name}}>
                                @error('name')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="flex_input_decoder">
                            <div class="block_input">
                                <label for="">Фамилия</label>
                                <input type="text" name='surname' value={{$burial->surname}}>
                                @error('surname')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="flex_input_decoder">
                            <div class="block_input">
                                <label for="">Отчество</label>
                                <input type="text" name='patronymic' value={{$burial->patronymic}}>
                                @error('patronymic')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="flex_input_decoder">
                            <div class="block_input">
                                <label for="">Дата рождения</label>
                                <input type="date" name='date_birth' value={{dateBurial($burial->date_birth)}}>
                                @error('date_birth')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="flex_input_decoder">
                            <div class="block_input">
                                <label for="">Дата смерти</label>
                                <input type="date" name='date_death' value={{dateBurial($burial->date_death)}}>
                                @error('date_death')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button class="blue_btn">Подвердить</button>
                    </form>
                   
                </div>    
            </div>                   
    </div>
@else
    <div class="container no_have_burial">
        <div class="title_middle">Нет захоронений для распознавания</div>
        <a href='{{route('home')}}' class="blue_btn">Вернуться назад</a>
    </div>    
@endif




@include('footer.footer')


<script>
 addZoom("zoomC");
</script>
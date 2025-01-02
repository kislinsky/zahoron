
<div class="choose_organization_block">
    <div class="flex_logo_choose_organization">
        <img src='{{asset('storage/uploads/ImgAbout.png')}}'>
        <img class='open_ul_agency_organizations' src="{{ asset('storage/uploads/Закрыть.svg')}}" alt="">
    </div>
    @if(user()->organization()!=null)
        <div class="info_selected_organization">
            <div class="text_block open_ul_agency_organizations">{{user()->organization()->title}} <img src="{{asset('storage/uploads/Vector_arrow (2).svg')}}" alt=""></div>
            <a href='{{user()->organization()->route()}}'class="text_middle_blue">Страница на {{user()->organization()->title}} <img src="{{asset('storage/uploads/Vector_arrow (1).svg')}}" alt=""></a>
        </div>
    @else
        <div class="info_selected_organization">
            <div class="text_block open_ul_agency_organizations">Выберите организацию <img src="{{asset('storage/uploads/Vector_arrow (2).svg')}}" alt=""></div>
            <a href=''class="text_middle_blue">Выберите организацию<img src="{{asset('storage/uploads/Vector_arrow (1).svg')}}" alt=""></a>
        </div>
    @endif

        <div class="ul_agency_organizations">
        @if(user()->organizations->count()>0)
            @foreach (user()->organizations as $organization)
                <a href='{{route('account.agency.choose.organization',$organization->id)}}' class="li_agency_organization text_block">{{$organization->title}} <div class="blue_btn">выбрать</div></a>   
            @endforeach
        @else
            <div class="li_agency_organization text_block">Добавьте организацию  </div>   
        @endif
        </div>
</div>
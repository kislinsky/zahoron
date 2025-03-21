
@if($organizations_category!=null && $organizations_category->total()>0)

    <?php $category=$organizations_category->first()->categoryProduct;?>

    @foreach ($organizations_category as $organization_category)

        <?php $organization=$organization_category->organization;?>
    
        

        <div class="li_organization">

            <?php $category_organiaztion=$organization_category->categoryProduct;?>


            <div class="info_li_org_mobile li_org_mobile">
                <img class='img_logo_organization'src="{{$organization->urlImg()}}" alt="">
                <div class="info_li_organization">
                    <a href='{{$organization->route()}}'class="title_li_organiaztion">{{$organization->title}}</a>
                    <div class="text_gray">{{$organization->name_type}}</div>
                </div>
            </div>

            <div class="info_li_org_mobile li_org_mobile"><div class="text_black"><img src="{{ asset('storage/uploads/Frame 334.svg') }}" alt=""> {{ $organization->rating }} - {{countReviewsOrganization($organization)}} оценок</div>   <div class="text_black">{{$organization->timeEndWorkingNow()}}</div></div>


            <div class="li_logo_organization li_org_dekstop">
                <img class='img_logo_organization'src="{{$organization->urlImg()}}" alt="">
                <div class="flex_stars">
                    <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt=""> <div class="text_black">{{$organization->rating}}</div>
                </div>
            </div>

            <div class="info_li_organization li_org_dekstop">
                <a href='{{$organization->route()}}'class="title_li_organiaztion"> {{$organization->title}}</a>
                <div class="text_gray">{{$organization->name_type}}</div>
                <div class="text_black"><img src="{{ asset('storage/uploads/Frame 334.svg') }}" alt=""> {{countReviewsOrganization($organization)}} оценки - {{$organization->timeEndWorkingNow()}}</div>
            </div>

            <div class="info_li_org_mobile li_org_mobile">
                <div class='info_li_organization'>
                    <div class="text_black_bold">{{$category_organiaztion->title}}</div>
                    <div class="text_gray">Сумма</div>
                    <div class="text_black_bold">от <span class='title_blue'>{{$organization_category->price}} ₽</span></div>
                </div>
                <div class="li_flex_icon_organization">
                    <a href="{{route('organization.like.add',$organization->id)}}"><img src="{{asset('storage/uploads/Vector (9).svg')}}" alt=""></a>
                    <a href=""><img src="{{asset('storage/uploads/Vector (8).svg')}}" alt=""></a>
                </div>
            </div>

            <div class="li_price_category_organization li_org_dekstop">
                <div class="text_gray">{{$category_organiaztion->title}}</div>
                <div class="title_blue">от {{$organization_category->price}} ₽</div>
            </div>

            <div class="li_flex_btn_organization">
                <a href='tel:{{$organization->phone}}'class="blue_btn">Позвонить</a>
                <a href='{{$organization->route()}}' class="btn_border_blue">Подробнее</a>
            </div>

            <div class="li_flex_icon_organization li_org_dekstop">
                <a href="{{route('organization.like.add',$organization->id)}}"><img src="{{asset('storage/uploads/Vector (9).svg')}}" alt=""></a>
                <a href=""><img src="{{asset('storage/uploads/Vector (8).svg')}}" alt=""></a>

            </div>
        </div>



    @endforeach

    {{ $organizations_category->withPath(route('organizations.category',$category->slug))->appends($_GET)->links() }}

@endif





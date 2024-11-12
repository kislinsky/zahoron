@include('header.header-decoder')


<div class="account_container">
    <div class="flex_account">
        @include('account.decoder.components.sidebar')
        <div class="container_content_account">
            <div class="container">
                <div class="title_middle">Документации</div>  
                <div class="ul_training_materials">
                @if($trainings!=null && $trainings->count()>0)
                    @foreach ($trainings as $training)
                        
                        <div class="li_training_materials">
                            <div class="title_training_materials">{{$training->title}}</div>
                            <div class="grid_training_materials">
                                <div class="content_training_materials">{!!$training->content!!} </div>
                                <a href="{{ asset('storage/uploads_decoder/'.$training->file) }}" download class="gray_btn">Скачать файл</a>
                            </div>
                        </div>  
                    @endforeach
                @endif
                </div>                  
            </div>
        </div>
    </div>
</div>

@include('footer.footer')
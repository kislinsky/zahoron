@include('header.header-decoder')


<div class="account_container">
    <div class="flex_account">
        @include('account.decoder.components.sidebar')
        <div class="container_content_account">
            <div class="container">
                <div class="title_middle">Обучающие видео</div>  
                <div class="ul_training_materials">
                @if($trainings!=null && $trainings->count()>0)
                    @foreach ($trainings as $training)
                        
                        <div class="li_training_materials">
                            <div class="title_training_materials">{{$training->title}}</div>
                            <div class="grid_training_materials">
                                <div class="content_training_materials">{!!$training->content!!} </div>
                                <div class="video_training_materials">
                                    <video controls src="{{ asset('storage/uploads_decoder/'.$training->video) }}"></video>
                                </div>
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
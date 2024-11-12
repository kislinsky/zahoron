<div class="block_inpit_form_search">
    <div class="title_middle settings_margin_form">Загрузка печати/подписи</div>
    <div class="flex_input_print">
        <div class="gray_btn open_form_print">Выберите файл <img src='{{ asset('/storage/uploads/Add.svg') }}'></div>
        @if (isset($imgs_agent))
            @if ($imgs_agent!=null)
            <div class="ul_img_agent">
                @foreach ($imgs_agent as $img_agent)
                    <div class="img_agent">
                        <a href='{{ route('account.organization.upload-seal.delete',$img_agent->id) }}'class="bac_img_agent">
                            <img src="{{ asset('storage/uploads/Group 36.svg') }}" alt="">
                        </a>
                        <img src="{{ asset('storage/uploads_organization/'.$img_agent->title) }}" alt="">
                    </div>
                @endforeach
            </div>
            @endif
        @endif
    </div>
</div>
@extends('account.agent.components.page')

@section('title','Услуги')

@section('content')


<div class="bac_black input_print_form">
    <div class='message'>
        <div class="flex_title_message">
            <div class="title_middle">Добавить фото</div>
            <div class="close_message">
                <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
            </div>
        </div>
        <form action='{{ route('account.agent.services.rent') }}' method='post' enctype='multipart/form-data' class="form_settings">
            @csrf
            <div class="block_inpit_form_search input_print">
                <div class="input__wrapper">
                    <input type="hidden" name="order_id" id='order_id_input'>
                    <input style='display:none;' name="file_services[]" type="file" id="input__file" multiple class="input input__file_2">
                    <label for="input__file" class="input__file-button">
                    <span class="input__file-button-text_2"><img src='{{ asset('/storage/uploads/add-icon.svg') }}'>Допускается загрузка фотографии в формате JPG и PNG размером не более 8 МБ.<br>Перетаскивайте фотографии прямо в эту область</span>
                    </label>
                </div>
                @error('file_services')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div>
            <button class="blue_btn btn_100">Загрузить</button>
        </form>
    </div>
</div>

<section class="orders">
    <div class="flex_titles_account">

        <div class="flex_titles_account">
            <a href='{{ route('account.agent.services.index') }}?status=1'class="btn_bac_gray <?php if($status!=null && $status==1){echo ' active_label_product';}?>">Ожидают оплаты </a>
            <a href='{{ route('account.agent.services.index') }}?status=2'class="btn_bac_gray <?php if($status!=null && $status==2){echo ' active_label_product';}?>">Оплаченные </a>
            <a href='{{ route('account.agent.services.index') }}?status=3'class="btn_bac_gray <?php if($status!=null && $status==3){echo ' active_label_product';}?>">В работе </a>
            <a href='{{ route('account.agent.services.index') }}?status=4'class="btn_bac_gray <?php if($status!=null && $status==4){echo ' active_label_product';}?>">На проверке</a>
            <a href='{{ route('account.agent.services.index') }}?status=5'class="btn_bac_gray <?php if($status!=null && $status==5){echo ' active_label_product';}?>">Исполненные </a>
        </div>

    </div>
    <div class="grid_two grid_mobile_1 margin_top_20">
        {{view('account.agent.components.services.show',compact('orders_services'))}}
    </div>
    {{ $orders_services->withPath(route('account.agent.services.index'))->appends($_GET)->links() }}

</section>

@endsection
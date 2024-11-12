@if ($category!=null && $category->manual_video!=null)
    <section class="manual">
        <div class="container">
            <div class="title">Инструкция как оформить заказ</div>
            <div class="text_page_marketplace">{{$category->manual}}</div>
            <video src="{{ asset('storage/uploads_cats_product/'.$category->manual_video) }}" controls></video>
        </div>
    </section>
@endif
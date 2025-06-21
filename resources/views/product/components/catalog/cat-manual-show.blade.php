@if ($category!=null && $category->manual_video!=null)
    <section class="manual">
        <div class="container">
            <h2 class="title">Инструкция как оформить заказ</h2>
            <div class="text_page_marketplace">{{$category->manual}}</div>
            <video src="{{ asset('storage/uploads_cats_product/'.$category->manual_video) }}" controls></video>
        </div>
    </section>
@endif
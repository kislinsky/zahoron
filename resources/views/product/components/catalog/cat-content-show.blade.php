@if ($category!=null && $category->content!=null)
    <section class='bac_gray about_service'>
        <div class="container">
            <div class="title">О сервисе</div>
            <div class="text_about_service">{{ $category->content }}</div>
        </div>
    </section>
@endif
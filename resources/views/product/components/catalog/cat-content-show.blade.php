@if ($category!=null && $category->content!=null)
    <section class='bac_gray about_service'>
        <div class="container">
            <h2 class="title">О сервисе</h2>
            <div class="text_about_service">{{ $category->content }}</div>
        </div>
    </section>
@endif
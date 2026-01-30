@if (isset($images) && count($images) > 0)
<div class="simple-gallery">
    <!-- Левая часть: основное фото и булеты -->
    <div class="main-area">
        <div class="big-image">
            <img src="{{ $images[0]->url() }}" alt="" id="mainImg">
        </div>
    </div>
    
    <!-- Правая часть: вертикальная миниатюры -->
    <div class="thumbs">
        @foreach ($images as $key => $image)
        <button 
            class="thumb {{ $key == 0 ? 'active' : '' }}" 
            onclick="showImage({{ $key }})"
            type="button"
            aria-label="Выбрать изображение {{ $key + 1 }}"
        >
            <img src="{{ $image->url() }}" alt="">
        </button>
        @endforeach
    </div>
</div>

<style>
.simple-gallery {
    display: flex;
    padding: 15px;
    border: 2px solid #3681C5;
    border-radius: 20px;
    gap: 15px;
    max-width: 800px;
}

/* Левая часть */
.main-area {
    flex: 1;
}

.big-image {
    height: 400px;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 10px;
}

.big-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.bullets {
    display: flex;
    justify-content: center;
    gap: 8px;
}


/* Правая часть - вертикальные миниатюры */
.thumbs {
    width: 80px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 400px;
    overflow-y: scroll;
}

.thumb {
    width: 100%;
    min-height: 80px;
    border-radius: 15px;
    padding: 0;
    background: none;
    cursor: pointer;
    overflow: hidden;
}



.thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Простая полоса прокрутки */
.thumbs::-webkit-scrollbar {
    width: 4px;
}

.thumbs::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.thumbs::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}


</style>

<script>
function showImage(index) {
    // Меняем большое изображение
    const mainImg = document.getElementById('mainImg');
    const images = @json($images->map(fn ($img) => $img->url()));
    
    if (index >= 0 && index < images.length) {
        mainImg.src = images[index];
        
        // Обновляем активный булет
        document.querySelectorAll('.bullet').forEach((bullet, i) => {
            bullet.classList.toggle('active', i === index);
        });
        
        // Обновляем активную миниатюру
        document.querySelectorAll('.thumb').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
            
            // Прокручиваем к активной миниатюре
            if (i === index) {
                thumb.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        });
    }
}
</script>
@endif
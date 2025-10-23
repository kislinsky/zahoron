@php
    $record = $getRecord();
@endphp

@if(isset($record->href_img) && $record->href_img == 1 && !empty($record->img_url))
    <div class="custom-image-container">
        <img 
            src="{{ $record->img_url }}" 
            alt="Изображение" 
            style="max-height: 150px; display: block; cursor: pointer;"
            class="zoomable-image"
            onclick="openImageModal('{{ $record->img_url }}')"
            onerror="this.style.display='none'; document.getElementById('download-fallback-{{ $record->id }}').style.display='block';"
        >
        <div id="download-fallback-{{ $record->id }}" style="display: none; border: 1px dashed #ccc; padding: 20px; text-align: center;">
            <p>Изображение не может быть отображено</p>
            <a href="{{ $record->img_url }}" download style="display: inline-block; padding: 8px 16px; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px;">
                Скачать файл
            </a>
        </div>
        <p style="margin-top: 8px; font-size: 12px; color: #666;">Нажмите на изображение для увеличения</p>
    </div>

    <!-- Модальное окно для изображения -->
    <div id="imageModal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9);">
        <span style="position: absolute; top: 20px; right: 35px; color: white; font-size: 40px; font-weight: bold; cursor: pointer; z-index: 10001;" onclick="closeImageModal()">&times;</span>
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 90%; max-height: 90%;">
            <img id="modalImage" src="" alt="Увеличенное изображение" style="max-width: 100%; max-height: 100%; display: block;">
        </div>
        <div style="position: absolute; bottom: 20px; left: 0; width: 100%; text-align: center;">
            <a id="downloadLink" href="" download style="display: inline-block; padding: 10px 20px; background: white; color: #333; text-decoration: none; border-radius: 4px; margin: 0 10px;">
                Скачать оригинал
            </a>
            <button onclick="closeImageModal()" style="padding: 10px 20px; background: white; color: #333; border: none; border-radius: 4px; cursor: pointer; margin: 0 10px;">
                Закрыть
            </button>
        </div>
    </div>

    <script>
        function openImageModal(imageUrl) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const downloadLink = document.getElementById('downloadLink');
            
            modalImage.src = imageUrl;
            downloadLink.href = imageUrl;
            modal.style.display = 'block';
            
            // Блокируем прокрутку body
            document.body.style.overflow = 'hidden';
        }
        
        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Закрытие по клику вне изображения
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
        
        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
@else
    <div style="padding: 20px; text-align: center; color: #999;">
        Изображение отсутствует
    </div>
@endif

<style>
    .zoomable-image {
        transition: transform 0.2s;
        cursor: pointer;
    }
    
    .zoomable-image:hover {
        transform: scale(1.02);
    }
    
    .custom-image-container {
        text-align: center;
        padding: 10px;
    }
    
    #imageModal {
        animation: fadeIn 0.3s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
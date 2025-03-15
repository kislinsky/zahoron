<div class="block_input">
    <label for="">Галерея</label>
    <div class="text_black">Загрузка файла</div>
    <div class="select">
        <select name="choose_file" id="">
            <option value="href">Ссылка</option>
            <option value="file">Файл</option>
        </select>

        <input class='input_file_setting_organization active_input_file_setting_organization' type="text" placeholder="http://" name='img_url'>
        <input class="form-control input_file_setting_organization" type="file" name='img_file'>
    </div>

    <!-- Кнопка добавления фото -->
    <div class="blue_btn add_img">Добавить фото</div>

    <!-- Галерея изображений -->
    <div class="ul_add_img_product">
        @foreach ($organization->images as $image)
            @if ($image->href_img == 1)
                <!-- Если изображение является ссылкой -->
                <div class='li_img_product_organization'>
                    <div class='delete_img_product_organization' onclick='$(this).parent().remove()'></div>
                    <img class='image_product_organization' src='{{ $image->urlImg() }}'>
                    <input type='hidden' name='images[]' value='{{ $image->urlImg() }}'>
                </div>
            @else
                <!-- Если изображение загружено как файл, проверяем его размер и преобразуем в Base64 -->
                @php
                    $absolutePath = $image->getAbsolutePath(); // Получаем абсолютный путь
                @endphp

                @if (file_exists($absolutePath) && filesize($absolutePath) <= 10485760) <!-- 10 MB -->
                    <div class='li_img_product_organization'>
                        <div class='delete_img_product_organization' onclick='$(this).parent().remove()'></div>
                        <img class='image_product_organization' src="{{ $image->urlImg() }}">
                        <input type='hidden' name='images[]' value="data:image/jpeg;base64,{{ base64_encode(file_get_contents($absolutePath)) }}">
                    </div>
                @else
                    <div class='li_img_product_organization'>
                        <div class='delete_img_product_organization' onclick='$(this).parent().remove()'></div>
                        <p>Файл слишком большой или отсутствует.</p>
                    </div>
                @endif
            @endif
        @endforeach
    </div>
</div>

<script>
    $(document).ready(function() {
    // Переключение между ссылкой и файлом
    $("select[name='choose_file']").on("change", function() {
        $('.input_file_setting_organization').removeClass('active_input_file_setting_organization');
        if ($(this).val() === 'href') {
            $('input[name="img_url"]').addClass('active_input_file_setting_organization');
        } else {
            $('input[name="img_file"]').addClass('active_input_file_setting_organization');
        }
    });

    // Добавление нового фото
    $('.add_img').click(function(event) {
        event.preventDefault(); // Предотвращаем стандартное поведение кнопки

        let activeInput = $('.active_input_file_setting_organization');
        let newFileInput = $("<input type='file' name='images[]' class='newInputFile' />");

        if (activeInput.attr('name') === 'img_file') {
            let file = activeInput.prop('files')[0]; // Используем prop('files')

            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Добавляем изображение и скрытое поле с Base64
                    $('.ul_add_img_product').append(`
                        <div class='li_img_product_organization'>
                            <div class='delete_img_product_organization' onclick='$(this).parent().remove()'></div>
                            <img class='image_product_organization' src='${e.target.result}'>
                            <input type='hidden' name='images[]' value='${e.target.result}'>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            }
        } else {
            let val = activeInput.val();
            if (val) {
                $('.ul_add_img_product').append(`
                    <div class='li_img_product_organization'>
                        <div class='delete_img_product_organization' onclick='$(this).parent().remove()'></div>
                        <img class='image_product_organization' src='${val}'>
                        <input type='hidden' name='images[]' value='${val}'>
                    </div>
                `);
            }
        }
    });
});
    </script>
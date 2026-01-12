<div class="block_input">
    <div class="text_middle_index">Фотографии</div>
    <div class='text_gray'>Добавьте изображения для галереи, не более 5 фото</div>
    <div class="block_add_image_product_organization">
        <div class="input__wrapper">
            <input style='display:none;' name="file" type="file" id="input__file_3" class="input input_file_add_image" multiple>
            <label for="input__file_3" class="input__file-button">
               <img src="{{ asset('storage/uploads/Plus (1).svg') }}" alt="">
            </label>
        </div>
        
        <div class="ul_add_img_product">
            {{-- Выводим существующие фото, если они переданы --}}
            @if(isset($images) && $images->count() > 0)
                @foreach($images as $image)
                    <div class='li_img_product_organization'>
                        <div class='delete_img_product_organization' 
                             onclick="deleteExistingImage({{ $image->id }}, this)"></div>
                        <img class='image_product_organization' 
                             src="{{ asset('storage/' . $image->title) }}" 
                             alt="Изображение товара">
                        <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    @error('images')
        <div class='error-text'>{{ $message }}</div>
    @enderror
    @error('images.*')
        <div class='error-text'>{{ $message }}</div>
    @enderror
</div>

<script>

$(document).ready(function() {
    $('#input__file_3').change(function(event) {
        let file = event.target.files[0];

        if (file) {
            var newFileInput = $("<input type='file' name='images[]' class='newInputFile' />");
            // Создаем DataTransfer для передачи файла
            var dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            // Устанавливаем файлы в новый input
            newFileInput[0].files = dataTransfer.files;
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.ul_add_img_product').append("<div class='li_img_product_organization' ><div class='delete_img_product_organization' onclick='$(this).parent().remove()'></div> <img class='image_product_organization' src='"+e.target.result+"' ></div>")
                $('.ul_add_img_product .li_img_product_organization:last-child').append(newFileInput)

            };
            reader.readAsDataURL(file);
        }
    });


    $('.delete_img_product_organization').on( "click", function() {
    $(this).parent().remove()
    });
});


</script>
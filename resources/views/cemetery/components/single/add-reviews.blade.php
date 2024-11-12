<form method='get' action='{{route('cemetery.review.add')}}'class="block_content_organization_single our_products_single_organization">
    @csrf
    <div class="flex_single_organization">
        <div class="title_li">Поделитесь мнением</div>
        <input type="hidden" value='{{$cemetery->id}}'name="cemetery_id">
        <div class="star-rating">
            <div class="star-rating__wrap">
            <input class="star-rating__input" id="star-rating-5-{{@$add_review_second}}" type="radio" name="rating" value="5">
            <label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-5-{{@$add_review_second}}" title="5 out of 5 stars"></label>
            <input class="star-rating__input" id="star-rating-4-{{@$add_review_second}}" type="radio" name="rating" value="4">
            <label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-4-{{@$add_review_second}}" title="4 out of 5 stars"></label>
            <input class="star-rating__input" id="star-rating-3-{{@$add_review_second}}" type="radio" name="rating" value="3">
            <label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-3-{{@$add_review_second}}" title="3 out of 5 stars"></label>
            <input class="star-rating__input" id="star-rating-2-{{@$add_review_second}}" type="radio" name="rating" value="2">
            <label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-2-{{@$add_review_second}}" title="2 out of 5 stars"></label>
            <input class="star-rating__input" id="star-rating-1-{{@$add_review_second}}" type="radio" name="rating" value="1">
            <label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-1-{{@$add_review_second}}" title="1 out of 5 stars"></label>
            </div>
        </div>
    </div>
    <div  class="add_review_organization">
        <div class="block_input">
            <textarea  id="" cols="30" rows="10" name='content_review'  placeholder="Расскажите о качестве услуги и обслуживания"></textarea>
            @error('content_review')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_input">
            <label for="">Ваше имя</label>
            <input type="text" name='name' placeholder="Имя" >
            @error('name')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div> 
        <label class="aplication checkbox active_checkbox">
            <input required type="checkbox" name="aplication"  checked >
            <p>Я согласен на обработку персональных данных в соответствии с Политикой конфиденциальности</p>
        </label>
        <button class="blue_btn" >Оставить отзыв</button>
    </div>
</form>

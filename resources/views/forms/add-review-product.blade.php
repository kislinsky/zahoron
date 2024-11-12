
<?php 

use Illuminate\Support\Facades\Auth;
$user=null;
if(Auth::check()){
    $user=Auth::user();
}

?>

<div class="modal fade" id="add_review_form"  tabindex="-1" aria-labelledby="add_review_form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                        <div class="title_middle">Отсавить отзыв</div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <form action="{{ route('product.add.review') }}" method="get" class='form_popup'>
                    @csrf
                    <input type="hidden" name="product_id" value='{{$product->id}}'>
                    <div class="block_info_user_form">
                        <div class="flex_input_form_contacts flex_beautification_form">
                            <div class="block_input">
                                <label for="">Имя</label>
                                <input required type="text" name='name' placeholder="Имя" <?php if($user!=null){echo 'value='.$user->name;}?>>
                                @error('name')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                            <div class="block_input">
                                <label for="">Фамилия</label>
                                <input required type="text" name='surname' placeholder="Фамилия" <?php if($user!=null){echo 'value='.$user->surname;}?>>
                                @error('surname')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                        </div>
                    </div>
                    <div class="block_input">
                        <label for="">Сообщение</label>
                       <textarea required name="message" id="" cols="30" rows="10"></textarea>
                        @error('message')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div> 
                    <label class="aplication checkbox active_checkbox">
                        <input required type="checkbox" name="aplication"  checked >
                        <p>Я согласен на обработку персональных данных в соответствии с Политикой конфиденциальности</p>
                    </label>

                    <button type='submit'class="blue_btn">Оставиьт отзыв</button>
                </form>
            </div>
        </div>
    </div>
</div>

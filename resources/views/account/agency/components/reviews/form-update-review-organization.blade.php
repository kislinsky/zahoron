
<div class="modal fade" id="review_update_content_form"  tabindex="-1" aria-labelledby="review_update_content__form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="text_center">
                        <div class="title_middle">Изменить текст отзыва</div>
                    </div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <div class='form_popup form_update_review'>
                    @csrf
                    <input type="hidden" name="id_review" id='id_review'>
                    <div class="block_input" >
                        <textarea name="content_review" id="content_review" cols="30" rows="10" placeholder="Текст отзыва"></textarea>
                        @error('content_review')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>  
                    <div class="blue_btn update_content_review">Сохранить изменения</div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>

    $( ".open_review_update_content_form" ).on( "click", function() {   
        let content_review=$(this).attr('content')
        let id_review=$(this).attr('id_review')
        $('#content_review').val(content_review) 
        $('#id_review').val(id_review) 
        $('#review_update_content_form').modal('show');
    });


     $('.update_content_review').on( "click", function() {

        let id_review=$('#id_review').val() 
        let content_review=$('#content_review').val() 

        $.ajax({
            type: 'GET',
            url: '{{ route("account.agency.review.organization.update") }}',
            data: {
                "_token": "{{ csrf_token() }}",
                'id_review': id_review,
                'content_review': content_review,
            }, success: function (result) {
                $('#review_update_content_form').modal('hide');

                $( ".open_review_update_content_form" ).each(function( index ) {
                    if($(this).attr('id_review')==id_review){
                        $(this).attr('content_review',content_review)   
                        $(this).parent().parent().siblings('.text_black').children('.content_all').html(content_review)
                        $(this).parent().parent().siblings('.text_black').children('.content_not_all').html(content_review)
                    }
                });
            },
            error: function () {
                alert('Ошибка');
            }
        });
    })
</script>
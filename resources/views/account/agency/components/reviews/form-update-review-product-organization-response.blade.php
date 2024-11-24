
<div class="modal fade" id="review_update_organization_response_form"  tabindex="-1" aria-labelledby="review_update_organization_response_form" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="text_center">
                        <div class="title_middle">Изменить ответ на отзыв</div>
                    </div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                <div  class='form_popup form_update_review'>
                    @csrf
                    <input type="hidden" name="organization_response_review_id" id='organization_response_review_id'>
                    <div class="block_input" >
                        <textarea name="organization_response_review" id="organization_response_review" cols="30" rows="10" placeholder="Текст отзыва"></textarea>
                        @error('content_review')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>  
                    <div class="blue_btn update_organization_response_review">Сохранить изменения</div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>

    $( ".open_review_update_organization_response_form" ).on( "click", function() {   

        let organization_response_review=$(this).attr('content_resonse')
        let id_review=$(this).attr('id_review')
        $('#organization_response_review').val(organization_response_review) 
        $('#organization_response_review_id').val(id_review) 
        $('#review_update_organization_response_form').modal('show');
    });


     $('.update_organization_response_review').on( "click", function() {

        let id_review=$('#organization_response_review_id').val() 
        let content_resonse=$('#organization_response_review').val() 

        $.ajax({
            type: 'GET',
            url: '{{ route("account.agency.review.product.update.organization-response") }}',
            data: {
                "_token": "{{ csrf_token() }}",
                'id_review': id_review,
                'organization_response_review': content_resonse,
            }, success: function (result) {
                $('#review_update_organization_response_form').modal('hide');

                $( ".open_review_update_organization_response_form" ).each(function( index ) {
                    if($(this).attr('id_review')==id_review){
                        $(this).attr('content_resonse',content_resonse)   
                    }
                });
            },
            error: function () {
                alert('Ошибка');
            }
        });
    })
</script>

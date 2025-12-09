<div class="block_inpit_form_search">
    <div class="title_middle">Список кладбищ:</div>

    <div class="block_input input_location_flex">
        <div class="input_location_settings">
            <div class="input_location">
                <div class='text_location_input'>Выберите кладбища, на которых работает организация</div>
                <div class="blue_btn" data-bs-toggle="modal" data-bs-target="#new_location_selector">Открыть список
                </div>
                <input type="hidden" name="cemetery_ids_list" class="cemetery_ids_list_input"
                       @if(!empty($cemeteries)) value="{{ implode(',', $cemeteries->pluck('id')->toArray()) }}" @endif>
                <input type="hidden" name="cemeteries_edge"
                       @if(!empty($cemeteries)) value="{{ $cemeteries->first()->city->edge->id }}" @endif>
            </div>
        </div>
    </div>

    <div class="ul_cemtery">
        @if(!empty($cemeteries))
            @foreach ($cemeteries as $cemetery)
                <div class="li_cemetery_agent" data-cemetery-id="{{ $cemetery->id }}">
                    <div class="mini_flex_li_product">
                        <input type="hidden" value='{{ $cemetery->id }}' name="cemetery_ids[]">
                        <div class="title_label">{{ $cemetery->title }}</div>
                        <div class="text_li">Адрес: {{ $cemetery->adres }}</div>
                    </div>
                    <div class="delete_cart delete_cemetery" data-cemetery-id="{{ $cemetery->id }}"><img
                            src="{{asset('storage/uploads/Закрыть (1).svg')}}" alt=""></div>
                </div>
            @endforeach
        @endif
    </div>
</div>


<script>
    function updateCemeteryIdsList() {
        let ids = [];

        $('.ul_cemtery').find('input[name="cemetery_ids[]"]').each(function() {
            ids.push($(this).val());
        });

        let cleanedIds = ids.filter(id => !isNaN(id));
        $('.cemetery_ids_list_input').val(cleanedIds.join(','));
        return cleanedIds;
    }

    $(document).on("click", ".delete_cemetery", function () {
        $(this).closest('.li_cemetery_agent').remove();
        updateCemeteryIdsList();
        $(document).trigger('cemeteryListUpdated');
    });

    function setEdgeId(edgeId) {
        $('input[name="cemeteries_edge"]').val(edgeId);
    }

    function clearAllSelectedCemeteries() {
        $('.ul_cemtery').empty();
        updateCemeteryIdsList();
        $(document).trigger('cemeteryListUpdated');
    }
</script>

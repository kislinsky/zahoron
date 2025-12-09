<div class="ul_area_cemetery_selector">
    <div class="title_news">Округ - Кладбище</div>

    <div class="list_objects">
        @foreach ($areas as $area)
            <div class="area_block my-2" data-area-id="{{ $area->id }}">
                <label class="flex_input_checkbox checkbox">
                    <input type="checkbox" class="area_checkbox" value="{{ $area->id }}"
                           data-area-id="{{ $area->id }}"
                           data-area-name="{{ $area->title }}"
                        {{ in_array($area->id, $selectedAreaIds) ? 'checked' : '' }}>
                    <span class="area_title">{{ $area->title }}</span>
                </label>

                <div class="cemetery_list mt-2" style="margin-left: 20px; display: none;"></div>
            </div>
        @endforeach
    </div>

    <div data-id="{{ $edge_id }}" class="btn_bac_gray back_to_edge mt-3 mb-2">Выбрать другой край</div>
</div>

<script>
    $(document).off('change' , '.area_checkbox').on('change', '.area_checkbox', function() {
        let currentEdgeIdInTemplate = String({{ $edge_id }});
        let $checkbox = $(this);
        let areaId = $checkbox.val();
        let $cemeteryList = $checkbox.closest('.area_block').find('.cemetery_list');

        let $edgeInput = $('input[name="cemeteries_edge"]');
        let previouslySelectedEdgeId = $edgeInput.val();
        let selectedCemeteryIds = $('.cemetery_ids_list_input').val();

        if ($checkbox.is(':checked')) {
            $checkbox.closest('label').addClass('active_checkbox');

            if (!isNaN(previouslySelectedEdgeId) &&
                previouslySelectedEdgeId !== currentEdgeIdInTemplate &&
                selectedCemeteryIds)
            {
                clearAllSelectedCemeteries();
            }

            setEdgeId(currentEdgeIdInTemplate);

            loadCemeteriesAndSelectAll(areaId, $cemeteryList, false).done(function() {
                updateCheckboxStates();
                if ($cemeteryList.css('display') === 'none') {
                    $cemeteryList.slideDown();
                }
            });

        } else {
            $checkbox.closest('label').removeClass('active_checkbox')

            $cemeteryList.slideUp(function() {
                $cemeteryList.find('.cemetery_checkbox').each(function() {
                    let cemeteryId = $(this).val();
                    $(this).prop('checked', false);
                    updateCemeteriesDisplay(cemeteryId, '', '', 'remove');
                });
                updateCheckboxStates();
            });
        }
    });

    initialSync();
</script>

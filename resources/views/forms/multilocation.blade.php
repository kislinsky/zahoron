<?php
use App\Models\Edge;

$edges = Edge::whereHas('area', function($query) {
    $query->whereHas('cities', function($query) {
        $query->whereHas('cemeteries');
    });
})
    ->orderBy('title', 'asc')
    ->get();

?>

<div class="modal fade" id="new_location_selector" tabindex="-1" aria-labelledby="new_location_selector" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="title_middle">Удобный выбор кладбищ</div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>

                <div class="load_block_2">
                    <div class="load-9">
                        <div class="spinner">
                            <div class="bubble-1"></div>
                            <div class="bubble-2"></div>
                        </div>
                    </div>
                </div>

                <div class="text_block">Область-округ-кладбище</div>

                <div class="html_geo">
                    {{ view('components.components_form.ul-edge-selector', compact('edges')) }}
                </div>

                <div class="blue_btn" data-bs-dismiss="modal">Закрыть</div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on("click", ".li_edge", function() {
        $('.load_block_2').show();

        let edgeId = $(this).data('id');
        let selectedCemeteryIds = $('.cemetery_ids_list_input').val();

        $.ajax({
            type: 'GET',
            url: '{{ route('geo.ajax.areas') }}',
            data: { 'edge_id': edgeId, 'selected_cemetery_ids': selectedCemeteryIds },
            success: function(result) {
                $('.load_block_2').hide();
                $(".html_geo").html(result);
            },
            error: function() {
                $('.load_block_2').hide();
                alert('Ошибка загрузки областей');
            }
        });
    });


    $(document).off('click', ".back_to_edge").on("click", ".back_to_edge", function() {
        $('.load_block_2').show();

        $.ajax({
            type: 'GET',
            url: '{{ route('geo.ajax.edges') }}',
            success: function(result) {
                $('.load_block_2').hide();
                $(".html_geo").html(result);
            },
            error: function() {
                $('.load_block_2').hide();
                alert('Ошибка загрузки краев');
            }
        });
    });

    function updateCemeteriesDisplay(cemeteryId, cemeteryName, address, action) {
        let $ulCemtery = $('.ul_cemtery');
        let cemeteryIdSelector = `.li_cemetery_agent[data-cemetery-id="${cemeteryId}"]`;

        if (action === 'add' && $ulCemtery.find(cemeteryIdSelector).length === 0) {
            let newAgentHtml = `
                <div class="li_cemetery_agent" data-cemetery-id="${cemeteryId}">
                    <input type="hidden" value='${cemeteryId}' name="cemetery_ids[]">
                    <div class="mini_flex_li_product">
                        <div class="title_label">${cemeteryName}</div>
                        <div class="text_li">Адрес: ${address}</div>
                    </div>
                    <div class="delete_cart delete_cemetery" data-cemetery-id="${cemeteryId}"><img src="{{asset('storage/uploads/Закрыть (1).svg')}}" alt=""></div>
                </div>`;
            $ulCemtery.append(newAgentHtml);
        } else if (action === 'remove') {
            $ulCemtery.find(cemeteryIdSelector).remove();
        }

        updateCemeteryIdsList();
    }

    function updateCheckboxStates() {
        const selectedCemeteryIds = updateCemeteryIdsList();
        let selectedAreasCount = 0;

        $('.area_block').each(function() {
            let $areaBlock = $(this);
            let $areaCheckbox = $areaBlock.find('.area_checkbox');
            let $cemeteryList = $areaBlock.find('.cemetery_list');

            let areaHasSelectedCemetery = false;

            $cemeteryList.find('.cemetery_checkbox').each(function() {
                let cemeteryId = $(this).val();
                let isSelected = selectedCemeteryIds.includes(cemeteryId);
                $(this).prop('checked', isSelected);

                if (isSelected) {
                    $(this).closest('label').addClass('active_checkbox');
                    areaHasSelectedCemetery = true;
                } else {
                    $(this).closest('label').removeClass('active_checkbox');
                }
            });

            $areaCheckbox.prop('checked', areaHasSelectedCemetery || $areaCheckbox.prop('checked'));

            if ($areaCheckbox.is(':checked')) {
                $areaCheckbox.closest('label').addClass('active_checkbox');

                selectedAreasCount++;
                if (!$cemeteryList.is(':empty')) {
                    $cemeteryList.show();
                }
            } else {
                $areaCheckbox.closest('label').removeClass('active_checkbox');
                $cemeteryList.slideUp();
            }
        });

        const maxAreas = 3;
        const isDisabled = selectedAreasCount >= maxAreas;

        $('.area_block').each(function() {
            let $areaCheckbox = $(this).find('.area_checkbox');

            if (!$areaCheckbox.is(':checked')) {
                $areaCheckbox.prop('disabled', isDisabled);
                $areaCheckbox.closest('label').toggleClass('disabled_limit', isDisabled);
            } else {
                $areaCheckbox.prop('disabled', false);
                $areaCheckbox.closest('label').removeClass('disabled_limit');
            }
        });
    }

    function loadCemeteriesAndSelectAll(areaId, $cemeteryList, isInitialLoad) {
        const deferred = $.Deferred();
        const selectedCemeteryIds = updateCemeteryIdsList();
        const $areaCheckbox = $cemeteryList.closest('.area_block').find('.area_checkbox');

        if (!$cemeteryList.is(':empty')) {

            $cemeteryList.find('.cemetery_checkbox').each(function() {
                let cemeteryId = $(this).val();
                let cemeteryName = $(this).data('cemetery-name');
                let address = $(this).data('cemetery-address');

                if (isInitialLoad) {
                    $(this).prop('checked', selectedCemeteryIds.includes(cemeteryId));
                } else {
                    $(this).prop('checked', true);
                    updateCemeteriesDisplay(cemeteryId, cemeteryName, address, 'add');
                }
            });
            return deferred.resolve().promise();
        }

        $('.load_block_2').show();

        $.ajax({
            type: 'GET',
            url: '{{ route('geo.ajax.cemeteries') }}',
            data: { 'area_id': areaId },
            success: function(result) {
                $('.load_block_2').hide();

                $cemeteryList.html(result);

                if (isInitialLoad && $areaCheckbox.is(':checked')) {
                    $cemeteryList.show();
                }

                $cemeteryList.find('.cemetery_checkbox').each(function() {
                    let cemeteryId = $(this).val();
                    let cemeteryName = $(this).data('cemetery-name');
                    let address = $(this).data('cemetery-address');

                    if (isInitialLoad) {
                        if (selectedCemeteryIds.includes(cemeteryId)) {
                            $(this).prop('checked', true);
                            updateCemeteriesDisplay(cemeteryId, cemeteryName, address, 'add');
                        }
                    } else {
                        $(this).prop('checked', true);
                        updateCemeteriesDisplay(cemeteryId, cemeteryName, address, 'add');
                    }
                });

                deferred.resolve();
            },
            error: function() {
                $('.load_block_2').hide();
                console.error('Ошибка загрузки кладбищ для области:', areaId);
                $areaCheckbox.prop('checked', false);
                updateCheckboxStates();
                deferred.resolve();
            }
        });
        return deferred.promise();
    }

    function initialSync() {
        updateCheckboxStates();

        let loadPromises = [];

        $('.area_block').each(function() {
            let $areaCheckbox = $(this).find('.area_checkbox');
            let areaId = $areaCheckbox.val();
            let $cemeteryList = $(this).find('.cemetery_list');

            if ($areaCheckbox.is(':checked')) {
                let promise = loadCemeteriesAndSelectAll(areaId, $cemeteryList, true);
                loadPromises.push(promise);
            }
        });

        if (loadPromises.length > 0) {
            $.when.apply($, loadPromises).done(function() {
                updateCheckboxStates();
            });
        } else {
            updateCheckboxStates();
        }
    }

    $(document).off('change', '.cemetery_checkbox').on('change', '.cemetery_checkbox', function() {
        let cemeteryId = $(this).val();
        let cemeteryName = $(this).data('cemetery-name');
        let address = $(this).data('cemetery-address');
        let action = $(this).is(':checked') ? 'add' : 'remove';

        updateCemeteriesDisplay(cemeteryId, cemeteryName, address, action);
        updateCheckboxStates();
    });

    $(document).off('cemeteryListUpdated').on('cemeteryListUpdated', function() {
        if ($('#new_location_selector').is(':visible')) {
            updateCheckboxStates();
        }
    });

    $('#new_location_selector').off('shown.bs.modal').on('shown.bs.modal', function () {
        updateCheckboxStates();
    });
</script>


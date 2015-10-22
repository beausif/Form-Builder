/********************************************
 *
 * Sets All Event Handlers
 *
 * @returns {boolean}
 *
 *******************************************/
function bindEventHandlers(){
    $('#addRowButton').off('click').on('click', addRow);
    $('.addElement, .addNewElementButton').off('click').on('click', insertEmptyElement);
    $('.elementContainer').off('mouseenter').off('mouseleave').on('mouseenter mouseleave', toggleOptionRow);
    $('.set_columns').off('click').on('click', setRowColumnSize);
    $('.column_clone').off('click').on('click', cloneRow);
    $('.column_delete').off('click').on('click', deleteRow);
    $('.clone_element').off('click').on('click', cloneElement);
    $('.delete_element').off('click').on('click', deleteElement);
    $('#addOption').off('click').on('click', addOption);
    $('#addCheckbox').off('click').on('click', addCheckbox);
    $('#addRadio').off('click').on('click', addRadio);
    $('#createForm').off('click').on('click', createForm);
    $('#checkboxModalButton').off('click').on('click', showCheckModal);
    $('#radioModalButton').off('click').on('click', showRadioModal);
    $('#selectModalButton').off('click').on('click', showSelectModal);
    $('#textInputModalButton').off('click').on('click', showTextInputModal);
    $('#textareaModalButton').off('click').on('click', showTextareaModal);
    $('#textModalButton').off('click').on('click', showTextModal);
    $('#submitModalButton').off('click').on('click', showSubmitModal);

    $('#insertCheckboxElement').off('click').on('click', getCheckboxElement);
    $('#insertRadioElement').off('click').on('click', getRadioElement);
    $('#insertSelectElement').off('click').on('click', getSelectElement);
    $('#insertTextInputElement').off('click').on('click', getTextInputElement);
    $('#insertTextareaElement').off('click').on('click', getTextareaElement);
    $('#insertTextElement').off('click').on('click', insertTextElement);
    $('#insertSubmitElement').off('click').on('click', getSubmitElement);
    $('.vc_row_layouts').off('mouseenter').off('mouseleave').on('mouseenter', showLayouts).on('mouseleave', hideLayouts);

    $("#buildDiv").sortable({handle: $('.column_move'), appendTo: 'body', helper: "clone", zIndex: 999999999, stop : cleanUpElements});
    $(".elementRow").sortable({ appendTo: 'body', helper: "clone", zIndex: 999999999, stop : cleanUpElements});
    $('.elementColumn').sortable({items: '.elementContainer', connectWith: $('.elementColumn'), appendTo: 'body', helper: "clone", zIndex: 999999999, stop : cleanUpElements});
    $("#buildDiv, .elementRow, .elementColumn").disableSelection();

    createPreview();

}
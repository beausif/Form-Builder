/********************************************
 *
 * Sets All Event Handlers
 *
 * @returns {boolean}
 *
 *******************************************/
function bindAddElementHandlers(){
    $('#addRowButton').off('click').on('click', addRow);
    $('.addElement, .addNewElementButton').off('click').on('click', insertEmptyElement);

    $('.elementContainer').off('mouseenter').off('mouseleave').on({
        mouseenter: function(){
            $(this).children('.hiddenOptionRow').css('display', 'inline-block');
        },
        mouseleave: function(){
            $(this).children('.hiddenOptionRow').css('display', 'none');
        }
    });

    $('.set_columns').off('click').on('click', function(){
        if(!$(this).hasClass('vc_active')){
            $(this).parent().children('.set_columns').removeClass('vc_active');
            $(this).addClass('vc_active');
            setRow(this);
        }
    });


    $('.column_clone').off('click').on('click', cloneRow);
    $('.column_delete').off('click').on('click', deleteRow);


    $('.clone_element').off('click').on('click', cloneElement);
    $('.delete_element').off('click').on('click', deleteElement);

    $('#addOption').off('click').on('click', addOption);
    $('#addCheckbox').off('click').on('click', addCheckbox);
    $('#addRadio').off('click').on('click', addRadio);
    $('#createForm').off('click').on('click', createForm);

    $('#checkboxModalButton').off('click').on('click', function(){
        $('.modal').modal('hide');
        $('#add-checkbox-modal').modal('show');
    });

    $('#radioModalButton').off('click').on('click', function(){
        $('.modal').modal('hide');
        $('#add-radio-modal').modal('show');
    });

    $('#selectModalButton').off('click').on('click', function(){
        $('.modal').modal('hide');
        $('#add-select-modal').modal('show');
    });

    $('#textInputModalButton').off('click').on('click', function(){
        $('.modal').modal('hide');
        $('#add-text-input-modal').modal('show');
    });

    $('#textareaModalButton').off('click').on('click', function(){
        $('.modal').modal('hide');
        $('#add-textarea-modal').modal('show');
    });

    $('#textModalButton').off('click').on('click', function(){
        $('.modal').modal('hide');
        $('#add-text-modal').modal('show');
    });

    $('#submitModalButton').off('click').on('click', function(){
        $('.modal').modal('hide');
        $('#add-submit-modal').modal('show');
    });

    $('#insertCheckboxElement').off('click').on('click', function(){
        element = getCheckboxElement();
    });

    $('#insertRadioElement').off('click').on('click', function(){
        element = getRadioElement();
    });

    $('#insertSelectElement').off('click').on('click', function(){
        element = getSelectElement();
    });

    $('#insertTextInputElement').off('click').on('click', function(){
        element = getTextInputElement();
    });

    $('#insertTextareaElement').off('click').on('click', function(){
        element = getTextareaElement();
    });

    $('#insertTextElement').off('click').on('click', function(){
        element = getTextElement();
    });

    $('#insertSubmitElement').off('click').on('click', function(){
        element = getSubmitElement();
    });

    $('.vc_row_layouts').off('mouseenter').off('mouseleave').on({
        mouseenter: function(){
            $(this).children().css('display', 'inline-block');
            $(this).parent().css('z-index', '9999');
        },
        mouseleave: function(){
            $(this).children().not('vc_active').css('display', 'none');
            $(this).parent().css('z-index', '10');
        }
    });

    var elementColumn = $('.elementColumn');
    $("#buildDiv").sortable({handle: $('.column_move'), appendTo: 'body', helper: "clone", zIndex: 999999999, stop : cleanUpElements});
    $(".elementRow").sortable({ appendTo: 'body', helper: "clone", zIndex: 999999999, stop : cleanUpElements});
    elementColumn.sortable({items: '.elementContainer', connectWith: elementColumn, appendTo: 'body', helper: "clone", zIndex: 999999999, stop : cleanUpElements});
    $("#buildDiv, .elementRow, .elementColumn").disableSelection();

    createPreview();

}
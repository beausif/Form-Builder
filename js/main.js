var row = null;
var position = null;

/********************************************
 *
 * Sets Event Handlers On Load
 *
 *******************************************/
$(function() {
    bindEventHandlers();
});

/********************************************
 *
 * Adds Table Row To Element Modals
 *
 *******************************************/
function addOption(){
    $('#optionBody').append('<tr><td><button type="button" class="close form-control" aria-hidden="true">X</button></td><td><input type="text" class="form-control optionName"></td><td><input type="text" class="form-control optionValue"></td><td><div class="row"><input type="checkbox" class="optionSelected"><label class="smallText">Selected</label></div><div class="row"><input type="checkbox" class="optionDisabled"><label class="smallText">Disabled</label></div></td></tr>');
    $('.close').off('click').on('click', deleteOptionRow);
}

function addCheckbox(){
    $('#checkboxBody').append('<tr><td><button type="button" class="close form-control" aria-hidden="true">X</button></td><td><input type="text" class="form-control checkID"></td><td><input type="text" class="form-control checkClass"></td><td><input type="text" class="form-control checkLabel"></td><td><input type="text" class="form-control checkValue"></td></tr>');
    $('.close').off('click').on('click', deleteOptionRow);
}

function addRadio(){
    $('#radioBody').append('<tr><td><button type="button" class="close form-control" aria-hidden="true">X</button></td><td><input type="text" class="form-control radioID"></td><td><input type="text" class="form-control radioClass"></td><td><input type="text" class="form-control radioLabel"></td><td><input type="text" class="form-control radioValue"></td></tr>');
    $('.close').off('click').on('click', deleteOptionRow);
}

/********************************************
 *
 * Set Up New Form Data
 *
 *******************************************/
function showNewFormModal(){
	$('#create-new-form-modal').modal('show');
	bindEventHandlers();
}

function setManageSubmissions(){
	if($(this).val() == 'db'){
		$('#emailRow').addClass('hide');
		$('#databaseRow').removeClass('hide');	
	} else if($(this).val() == 'email'){
		$('#databaseRow').addClass('hide');
		$('#emailRow').removeClass('hide');
	} else if($(this).val() == 'both'){
		$('#databaseRow, #emailRow').removeClass('hide');	
	} else {
		$('#databaseRow, #emailRow').addClass('hide');
	}
}

function setNewForm(){
	var noError = true;
	resetErrors();
	
	if(checkInput('#formNameModal', 'Form Name Required')){
		noError = false;
	}
	
	if($('#submissionType').val() == 'both'){
		if(checkInput('#databaseName', 'Database Name Required')){
			noError = false;
		}
		if(checkInput('#notificationEmail', 'Notification Email Required')){
			noError = false;
		}
	} else if($('#submissionType').val() == 'db'){
		if(checkInput('#databaseName', 'Database Name Required')){
			noError = false;
		}
	} else if($('#submissionType').val() == 'email'){
		if(checkInput('#notificationEmail', 'Notification Email Required')){
			noError = false;
		}	
	} else {
		if(checkInput('#submissionType', 'Must Select One')){
			noError = false;
		}
	}
	
	if(noError){
		$('#fb-formName').val($('#formNameModal').val());
		$('#formNameDiv').removeClass('hide');
		switch($('#submissionType').val()) {
			case 'both':
				$('#fb-dbName').val($('#databaseName').val());
				$('#fb-notificationEmail').val($('#notificationEmail').val());
				$('#fb-confirmationEmail').val($('#confirmationEmail').val());
				$('#formDatabaseDiv').removeClass('hide');
				$('#formEmailDiv').removeClass('hide');
				break;
			case 'db':
				$('#fb-dbName').val($('#databaseName').val());
				$('#formDatabaseDiv').removeClass('hide');
				break;
			case 'email':
				$('#fb-notificationEmail').val($('#notificationEmail').val());
				$('#fb-confirmationEmail').val($('#confirmationEmail').val());
				$('#formEmailDiv').removeClass('hide');
				break;
		}
		$('#create-new-form-modal').modal('hide');
		$('#newFormDiv').addClass('hide');
		$('.fb').removeClass('hide');
	} else {
			
	}
	
}

/********************************************
 *
 * Delete Full Row From Body
 *
 *******************************************/
function deleteRow(){
    $(this).parents('.fullRow').remove();
}

/********************************************
 *
 * Resets All Form Errors
 *
 *******************************************/
function resetErrors(){
    $('.errorBorder').removeClass('errorBorder');
    $('.errorLabel').text('');
}

/********************************************
 *
 * Inserts Element At Correct Place
 *
 * @returns {boolean}
 *
 *******************************************/
function insertElement(element){
    if(element != false){
        switch(position){
            case 'top':
                $(row).addClass('full').prepend(element);
                break;
            case 'bottom':
                $(row).addClass('full').append(element);
                break;
            default :
                $(row).after(element);
                $(row).parent().addClass('full');
                $(row).remove();
                break;
        }
        bindEventHandlers();
        $('.modal').modal('hide');
    }
}


/********************************************
 *
 * Verifies Modal Data For Selected Element
 * Creates and Appends Element
 *
 * @returns {boolean}
 *
 *******************************************/
function getTextInputElement(){
    var error = false;
    var textElementClasses = $('#textElementClasses');
    var textElementLabelText = $('#textElementLabelText');
    var textElementID = $('#textElementID');

    if(textElementLabelText.val() == ''){
        $(textElementLabelText).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Label Text');
        error = true;
    }
    if(textElementID.val() == '' || $('#' + textElementID.val()).length){
        $(textElementID).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Element ID');
        error = true;
    }

    if(!error){
        var textValue = $('#textValue').val();
        var textPlaceholder = $('#textPlaceholder').val();

        element = createTextInput(textElementID.val(), textElementLabelText.val(), textElementClasses.val(), textValue, textPlaceholder);
        insertElement(element);
    } else {
        return false;
    }
}

function getSelectElement(){
    var error = false;
    var selectElementClasses = $('#selectElementClasses');
    var selectElementLabelText = $('#selectElementLabelText');
    var selectElementID = $('#selectElementID');

    if(selectElementLabelText.val() == ''){
        $(selectElementLabelText).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Label Text');
        error = true;
    }
    if(selectElementID.val() == '' || $('#' + selectElementID.val()).length){
        $(selectElementID).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Element ID');
        error = true;
    }
    if(!error){

        removeEmptyTableRows();
        if(checkTableRows()){
            return false;
        }

        var options = getTableOptions('#optionBody');

        element = createSelectInput(selectElementID.val(), selectElementLabelText.val(), selectElementClasses.val(), options);
        insertElement(element);
    } else {
        return false;
    }
}

function getButtonElement(){
    var error = false;
    var buttonElementValue = $('#buttonValue');
    var buttonElementClasses = $('#selectElementClasses');
    var buttonElementID = $('#selectElementID');


    if(buttonElementValue.val() == ''){
        $(buttonElementValue).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Button Value');
        error = true;
    }
    if(buttonElementID.val() == '' || $('#' + buttonElementID.val()).length){
        $(buttonElementID).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Element ID');
        error = true;
    }
    if(!error){
        element = createButtonInput(buttonElementID.val(), buttonElementClasses.val(), buttonElementValue.val());
        insertElement(element);
    } else {
        return false;
    }
}

function getCheckboxElement(){
    var error = false;
    var checkboxGroupName = $('#checkboxName');
    var checkboxOverLabel = $('#checkboxOverLabel');

    if(checkboxGroupName.val() == ''){
        $(checkboxGroupName).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Checkbox Group Name');
        error = true;
    }
    if(checkboxOverLabel.val() == ''){
        $(checkboxOverLabel).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Checkbox Overhead Label');
        error = true;
    }

    removeEmptyCheckboxRows();
    if(checkboxRows()){
        return false;
    }

    var checkboxes = getCheckboxData('#checkboxBody');

    if(!error){
        element = createCheckboxInput(checkboxOverLabel.val(), checkboxes);
        insertElement(element);
    } else {
        return false;
    }
}

function getRadioElement(){
    var error = false;
    var radioGroupName = $('#radioName');
    var radioOverLabel = $('#radioOverLabel');

    if(radioGroupName.val() == ''){
        $(radioGroupName).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Radio Group Name');
        error = true;
    }
    if(radioOverLabel.val() == ''){
        $(radioOverLabel).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Radio Overhead Label');
        error = true;
    }

    removeEmptyRadioRows();
    if(radioRows()){
        return false;
    }

    var radios = getRadioData('#radioBody');

    if(!error){
        element = createRadioInput(radioOverLabel.val(), radios);
        insertElement(element);
    } else {
        return false;
    }
}

function getTextareaElement(){
    var error = false;
    var textareaValue = $('#textareaValue').val();
    var textareaPlaceholder = $('#textareaPlaceholder').val();
    var textareaElementLabelText = $('#textareaElementLabelText');
    var textareaElementClasses = $('#textareaElementClasses');
    var textareaElementID = $('#textareaElementID');

    if(textareaElementLabelText.val() == ''){
        $(textareaElementLabelText).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Label Text');
        error = true;
    }
    if(textareaElementID.val() == '' || $('#' + textareaElementID.val()).length){
        $(textareaElementID).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Element ID');
        error = true;
    }

    if(!error){
        element = createTextareaInput(textareaElementID.val(), textareaElementLabelText.val(), textareaElementClasses.val(), textareaValue, textareaPlaceholder);
        insertElement(element);
    } else {
        return false;
    }
}

function getSubmitElement(){
    var error = false;
    var submitElementValue = $('#submitValue');
    var submitElementID = $('#submitElementID');
    var submitElementClasses = $('#submitElementClasses');

    if(submitElementValue.val() == ''){
        $(submitElementValue).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Submit Value');
        error = true;
    }
    if(submitElementID.val() == '' || $('#' + submitElementID.val()).length){
        $(submitElementID).addClass('errorBorder').parent().prevAll('.errorLabel').text('Please Enter Element ID');
        error = true;
    }

    if(!error){
        element = createSubmitInput(submitElementID.val(), submitElementValue.val(), submitElementClasses.val());
        insertElement(element);
    } else {
        return false;
    }
}

/********************************************
 *
 * Generates Element HTML String
 *
 * @returns {string}
 *
 *******************************************/
function getElementStart(type){
    return '<div class="col-sm-12 elementContainer ' + type + ' form-horizontal" data-type="' + type + '"><div class="hiddenOptionRow"><input type="button" class="hiddenOptionButton edit_element"><input type="button" class="hiddenOptionButton clone_element"><input type="button" class="hiddenOptionButton delete_element"></div>';
}

function getElementEnd(){
    return '</div><label class="col-sm-12 errorLabel"></label>';
}

function createTextInput (elementID, labelText, elementClasses, textValue, textPlaceholder){
    return getElementStart('text') + '<label for="' + elementID + '" class="col-sm-12 control-label">' + labelText + '</label><div class="elementDiv col-sm-12"><input type="text" id="' + elementID + '" name="' + elementID + '" class="form-control ' + elementClasses + '" value="' + textValue + '" placeholder="' + textPlaceholder + '">' + getElementEnd();
}

function createSelectInput(id, label, classes, options) {
    return getElementStart('select') + '<label for="' + id + '" class="col-sm-12 control-label">' + label + '</label><div class="elementDiv col-sm-12"><select id="' + id + '" name="' + id + '" class="form-control ' + classes + '">' + options + '</select>' + getElementEnd();
}

function createButtonInput(id, classes, value){
    return getElementStart('button') + '<input type="button" id="' + id + '" name="' + id + '" class="btn form-control ' + classes + '" value="' + value + '">' + getElementEnd();
}

function createCheckboxInput(label, checkboxes){
    return getElementStart('checkbox') + '<label class="col-sm-12 control-label">' + label + '</label><div class="elementDiv col-sm-12">' + checkboxes + getElementEnd();
}

function createRadioInput(label, radios){
    return getElementStart('radio') + '<label class="col-sm-12 control-label">' + label + '</label><div class="elementDiv col-sm-12">' + radios + getElementEnd();
}

function createTextareaInput(id, label, classes, value, placeholder){
    return getElementStart('textarea') + '<label for="' + id + '" class="col-sm-12 control-label">' + label + '</label><div class="elementDiv col-sm-12"><textarea cols="4" id="' + id + '" name="' + id + '" class="form-control ' + classes + '" placeholder="' + placeholder + '">' + value + '</textarea>' + getElementEnd();
}

function createSubmitInput(id, value, classes){
    return getElementStart('submit') + '<input type="submit" id="' + id + '" name="' + id + '" class="btn btn-warning form-control ' + classes + '" value="' + value + '">' + getElementEnd();
}


/********************************************
 *
 * Deletes empty table rows in select modal
 *
 *******************************************/
function removeEmptyTableRows(){
    var tRow = $("#optionBody").find("tr");
    tRow.each(function(){
        if($(this).find('.optionName').val() == '' && $(this).find('.optionValue').val() == ''){
            $(this).remove();
        }
    });
    if(tRow.length == 0){
        addOption($("#optionBody"));
    }
}

/********************************************
 *
 * Deletes empty table rows in checkbox modal
 *
 *******************************************/
function removeEmptyCheckboxRows(){
    var tRow = $("#checkboxBody").find("tr");
    tRow.each(function(){
        if($(this).find('.checkID').val() == '' && $(this).find('.checkLabel').val() == '' && $(this).find('.checkValue').val() == ''){
            $(this).remove();
        }
    });
    if(tRow.length == 0){
        addCheckbox($("#checkboxBody"));
    }
}

/********************************************
 *
 * Deletes empty table rows in radio modal
 *
 *******************************************/
function removeEmptyRadioRows(){
    var tRow = $("#radioBody").find("tr");
    tRow.each(function(){
        if($(this).find('.radioID').val() == '' && $(this).find('.radioLabel').val() == '' && $(this).find('.radioValue').val() == ''){
            $(this).remove();
        }
    });
    if(tRow.length == 0){
        addRadio($("#radioBody"));
    }
}

/********************************************
 *
 * Checks that each table row in select modal is fully filled out
 *
 *******************************************/
function checkTableRows(){
    var error = false;
    var tRow = $("#optionBody").find("tr");
    tRow.each(function(){
        var optionName = $(this).find('.optionName');
        var optionValue = $(this).find('.optionValue');
        if(optionName.val() == ''){
            optionName.addClass('errorBorder');
            error = true;
        }
        if(optionValue.val() == ''){
            optionValue.addClass('errorBorder');
            error = true;
        }
    });
    return error;
}

/********************************************
 *
 * Checks that each table row in checkbox modal is fully filled out
 *
 *******************************************/
function checkboxRows(){
    var error = false;
    var tRow = $("#checkboxBody").find("tr");
    tRow.each(function(){
        var checkboxID = $(this).find('.checkID');
        var checkboxLabel = $(this).find('.checkLabel');
        var checkboxValue = $(this).find('.checkValue');
        if(checkboxID.val() == ''){
            checkboxID.addClass('errorBorder');
            error = true;
        }
        if(checkboxLabel.val() == ''){
            checkboxLabel.addClass('errorBorder');
            error = true;
        }
        if(checkboxValue.val() == ''){
            checkboxValue.addClass('errorBorder');
            error = true;
        }
    });
    return error;
}

/********************************************
 *
 * Checks that each table row in radio modal is fully filled out
 *
 *******************************************/
function radioRows(){
    var error = false;
    var tRow = $("#radioBody").find("tr");
    tRow.each(function(){
        var radioID = $(this).find('.radioID');
        var radioLabel = $(this).find('.radioLabel');
        var radioValue = $(this).find('.radioValue');
        if(radioID.val() == ''){
            radioID.addClass('errorBorder');
            error = true;
        }
        if(radioLabel.val() == ''){
            radioLabel.addClass('errorBorder');
            error = true;
        }
        if(radioValue.val() == ''){
            radioValue.addClass('errorBorder');
            error = true;
        }
    });
    return error;
}

/********************************************
 *
 * Gets data from option table in select element modal
 *
 *******************************************/
function getTableOptions(tbody){
    var optionData = '';
    var tRow = $(tbody).find("tr");
    tRow.each(function(){
        var optionName = $(this).find('.optionName').val();
        var optionValue = $(this).find('.optionValue').val();
        var optionSelected = $(this).find('.optionSelected').is(":checked");
        var optionDisabled = $(this).find('.optionDisabled').is(":checked");
        var selected = '';
        var disabled = '';

        if(optionSelected){
            selected = ' selected="selected"';
        }
        if(optionDisabled){
            disabled = ' disabled="disabled"';
        }

        optionData += '<option name="' + optionName + '"' + selected + disabled +  ' value="' + optionValue + '">' + optionValue + '</option>';
    });

    return optionData;
}

/********************************************
 *
 * Gets data from checkbox table in checkbox element modal
 *
 *******************************************/
function getCheckboxData(tbody){
    var optionData = '';
    var tRow = $(tbody).find("tr");
    var checkName = $('#checkboxName').val();
    tRow.each(function(){
        var checkID = $(this).find('.checkID').val();
        var checkClass = $(this).find('.checkClass').val();
        var checkLabel = $(this).find('.checkLabel').val();
        var checkValue = $(this).find('.checkValue').val();

        optionData += '<label class="btn btn-default radioButton"><input type="checkbox" id="' + checkID + '" name="' + checkName + '[]" value="' + checkValue + '"> ' + checkLabel + '</label>';
    });

    return optionData;
}

/********************************************
 *
 * Gets data from radio table in radio element modal
 *
 *******************************************/
function getRadioData(tbody){
    var optionData = '';
    var tRow = $(tbody).find("tr");
    var radioName = $('#radioName').val();
    tRow.each(function(){
        var radioID = $(this).find('.radioID').val();
        var radioClass = $(this).find('.radioClass').val();
        var radioLabel = $(this).find('.radioLabel').val();
        var radioValue = $(this).find('.radioValue').val();

        optionData += '<label class="btn btn-default radioButton"><input type="radio" id="' + radioID + '" name="' + radioName + '[]" value="' + radioValue + '"> ' + radioLabel + '</label>';
    });

    return optionData;
}

/********************************************
 *
 * Creates a empty row and appends it below
 *
 *******************************************/
function addRow(){
    $('#buildDiv').append('<div class="fullRow"><div class="controlRow col-sm-12"><a class="column_move" href="#" title="Drag row to reorder"></a><span class="vc_row_layouts"><a class="set_columns one vc_active" data-cells="12" data-cells-mask="12" title="1/1"></a><a class="set_columns two" data-cells="6_6" data-cells-mask="26" title="1/2 + 1/2"></a><a class="set_columns three" data-cells="8_4" data-cells-mask="29" title="2/3 + 1/3"></a><a class="set_columns four" data-cells="4_4_4" data-cells-mask="312" title="1/3 + 1/3 + 1/3"></a><a class="set_columns five" data-cells="3_3_3_3" data-cells-mask="420" title="1/4 + 1/4 + 1/4 + 1/4"></a><a class="set_columns six" data-cells="3_9" data-cells-mask="212" title="1/4 + 3/4"></a><a class="set_columns seven" data-cells="3_6_3" data-cells-mask="313" title="1/4 + 1/2 + 1/4"></a><a class="set_columns eight" data-cells="10_2" data-cells-mask="218" title="5/6 + 1/6"></a><a class="set_columns nine" data-cells="2_2_2_2_2_2" data-cells-mask="642" title="1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6"></a><a class="set_columns ten" data-cells="2_8_2" data-cells-mask="319" title="1/6 + 4/6 + 1/6"></a><a class="set_columns eleven" data-cells="2_2_2_6" data-cells-mask="424" title="1/6 + 1/6 + 1/6 + 1/2"></a></span><span class="vc_row_edit_clone_delete"><a class="column_delete" href="#" title="Delete this row"></a><a class="column_clone" href="#" title="Clone this row"></a></span></div><div class="col-sm-12 elementRow"><div class="elementColumn col-sm-12" data-length="12"><div class="col-sm-12 topBar addElementRowDiv"><input class="addNewElementButton addElementTop" type="button"></div><div class="col-sm-12 bottomBar addElementRowDiv"><input class="addNewElementButton addElementBottom" type="button"></div><div class="col-sm-12 elementContainer"><input type="button" class="btn addElement form-control" value="+ Element"></div></div></div></div>');
    bindEventHandlers();
}

/********************************************
 *
 * Creates a empty row without a blank element and appends it below
 *
 *******************************************/
function addEmptyRow(row){
    var $fullRow = $('<div class="fullRow"><div class="controlRow col-sm-12"><a class="column_move" href="#" title="Drag row to reorder"></a><span class="vc_row_layouts"><a class="set_columns one vc_active" data-cells="12" data-cells-mask="12" title="1/1"></a><a class="set_columns two" data-cells="6_6" data-cells-mask="26" title="1/2 + 1/2"></a><a class="set_columns three" data-cells="8_4" data-cells-mask="29" title="2/3 + 1/3"></a><a class="set_columns four" data-cells="4_4_4" data-cells-mask="312" title="1/3 + 1/3 + 1/3"></a><a class="set_columns five" data-cells="3_3_3_3" data-cells-mask="420" title="1/4 + 1/4 + 1/4 + 1/4"></a><a class="set_columns six" data-cells="3_9" data-cells-mask="212" title="1/4 + 3/4"></a><a class="set_columns seven" data-cells="3_6_3" data-cells-mask="313" title="1/4 + 1/2 + 1/4"></a><a class="set_columns eight" data-cells="10_2" data-cells-mask="218" title="5/6 + 1/6"></a><a class="set_columns nine" data-cells="2_2_2_2_2_2" data-cells-mask="642" title="1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6"></a><a class="set_columns ten" data-cells="2_8_2" data-cells-mask="319" title="1/6 + 4/6 + 1/6"></a><a class="set_columns eleven" data-cells="2_2_2_6" data-cells-mask="424" title="1/6 + 1/6 + 1/6 + 1/2"></a></span><span class="vc_row_edit_clone_delete"><a class="column_delete" href="#" title="Delete this row"></a><a class="column_clone" href="#" title="Clone this row"></a></span></div><div class="col-sm-12 elementRow"><div class="elementColumn col-sm-12" data-length="12"><div class="col-sm-12 topBar addElementRowDiv"><input class="addNewElementButton addElementTop" type="button"></div><div class="col-sm-12 bottomBar addElementRowDiv"><input class="addNewElementButton addElementBottom" type="button"></div></div></div></div>');
    $(row).after($fullRow);
    return $fullRow;
}

/********************************************
 *
 * Clones the row and appends it below
 *
 *******************************************/
function cloneRow(){
    var cloneRow = $(this).parents('.fullRow').clone();
    $(this).parents('.fullRow').after(cloneRow);
    bindEventHandlers();
}

/********************************************
 *
 * Sets the size of row columns based on passed layout.
 * Creates new rows if new column size cannot accommodate
 * the amount of full columns.
 *
 *******************************************/
function setRow(layout){
    var elements = $(layout).attr('data-cells').split('_');
    var totalElements = elements.length;
    var row = $(layout).parents('.fullRow');

    var elementArray = [];

    $(row).find('.full').each(function(){
        elementArray.push($(this).html());
    });
    $(row).find('.elementRow').empty();

    $.each(elements, function(index, element){
        $(row).children('.elementRow').append('<div class="elementColumn col-sm-' + element + '" data-length="' + element + '"><div class="col-sm-12 topBar addElementRowDiv"><input class="addNewElementButton addElementTop" type="button"></div><div class="col-sm-12 bottomBar addElementRowDiv"><input class="addNewElementButton addElementBottom" type="button"></div><div class="col-sm-12 removePadding elementContainer"><input type="button" class="btn addElement form-control" value="+ Element"></div></div>');
    });

    var difference = elementArray.length - totalElements;
    var botRow = row;

    $.each(elementArray, function(index, element) {
        if (index < totalElements || difference <= 0) {
            $(row).find('.elementColumn:nth(' + (index) + ')').empty().append(element);
            $(row).find('.elementColumn:nth(' + (index) + ')').addClass('full');
        } else {
            botRow = addEmptyRow(botRow);
            $(botRow).find('.elementColumn').empty().append(element);
            $(botRow).find('.elementColumn').addClass('full');
        }
    });
    bindEventHandlers();
}

/********************************************
 *
 * Creates a clone of the element and appends it below
 *
 *******************************************/
function cloneElement(){
    var element = $(this).parent().parent();
    var cElement = $(element).clone();

    $(element).after(cElement);
    bindEventHandlers();
}

/********************************************
 *
 * Deletes element from column
 *
 *******************************************/
function deleteElement(){
    $(this).parent().parent().remove();
    cleanUpElements();
}

/********************************************
 *
 * If container is empty adds blank element.
 * Adds/removes class based on if container has a non-blank element
 *
 *******************************************/
function cleanUpElements(){
    $('.elementColumn').each(function(){
        if($(this).children('.elementContainer').length == 0){
            $(this).append('<div class="col-sm-12 removePadding elementContainer"><input type="button" class="btn addElement form-control" value="+ Element"></div>');
        }
        if($(this).find('.form-horizontal').length > 0){
            $(this).addClass('full');
            row = $(this);
        } else {
            $(this).removeClass('full');
        }
    });
    bindEventHandlers();
}

/********************************************
 *
 * Sets new element placement. Shows select element modal 
 *
 *******************************************/
function insertEmptyElement(){
    if($(this).hasClass('addElementTop')){
        position = 'top';
        row = $(this).parent().parent();
    } else if ($(this).hasClass('addElementBottom')){
        position = 'bottom';
        row = $(this).parent().parent();
    } else {
        position = 'element';
        row = $(this).parent();
    }
    $('#select-element-modal').modal('show');
}

/********************************************
 *
 * Deletes row from option table. Adds row if no rows left from select modal
 *
 *******************************************/
function deleteOptionRow(){
    var optionRow = $(this).parents('tr');
    if($(optionRow).parent().children('tr').length == 1){
        addOption($('#optionBody'));
    }
    $(optionRow).remove();
}

/********************************************
 *
 * *****Will be replaced with a server call*****
 *
 * Creates the preview of the form and displays it
 *
 *******************************************/
function createPreview(){
    var pElement = $('#buildDiv').clone();
    $(pElement).find('.controlRow').remove();
    $(pElement).find('.topBar').remove();
    $(pElement).find('.bottomBar').remove();
    $(pElement).find('.hiddenOptionRow').remove();
    $(pElement).find('.addElement').remove();
    $(pElement).find('div').removeClass('elementContainer');
    $(pElement).find('div').removeClass('elementColumn');
    $(pElement).find('div').removeClass('elementRow');
    $(pElement).find('div').removeClass('elementDiv');

    $('#fullPreview').empty().append(pElement)
}

/********************************************
 *
 * Hides/shows element overlay based on mouseenter/mouseleave
 *
 *******************************************/
function toggleOptionRow(){
    $(this).children('.hiddenOptionRow').toggleClass('inline')
}

/********************************************
 *
 * Hides all modals then shows correct modal
 *
 *******************************************/
function showCheckModal(){
    $('.modal').modal('hide');
    $('#add-checkbox-modal').modal('show');
}

function showRadioModal(){
    $('.modal').modal('hide');
    $('#add-radio-modal').modal('show');
}

function showSelectModal(){
    $('.modal').modal('hide');
    $('#add-select-modal').modal('show');
}

function showTextInputModal(){
    $('.modal').modal('hide');
    $('#add-text-input-modal').modal('show');
}

function showTextareaModal(){
    $('.modal').modal('hide');
    $('#add-textarea-modal').modal('show');
}

function showTextModal(){
    $('.modal').modal('hide');
    $('#add-text-modal').modal('show');
}

function showSubmitModal(){
    $('.modal').modal('hide');
    $('#add-submit-modal').modal('show');
}

/********************************************
 *
 * Shows/Hides column layouts on mouseover/mouseout
 *
 *******************************************/
function showLayouts(){
    $(this).children().css('display', 'inline-block');
    $(this).parent().css('z-index', '9999');
}

function hideLayouts(){
    $(this).children().not('vc_active').css('display', 'none');
    $(this).parent().css('z-index', '10');
}

/********************************************
 *
 * Sets column layout image and sets row column widths
 *
 *******************************************/
function setRowColumnSize(){
    if(!$(this).hasClass('vc_active')){
        $(this).parent().children('.set_columns').removeClass('vc_active');
        $(this).addClass('vc_active');
        setRow(this);
    }
}

/********************************************
 *
 * Creates AJAX call to process form data on server
 *
 *******************************************/
function createForm(){
    $('.subBtn').prop('disabled', true);
	
	/* TODO
	CHECK FOR EMAIL FIELD IF CONFIRMATION IS YES
	CHECK THAT ALL INFORMATION IS RECEIVED
	*/

    $('#buildDiv').ajaxSubmit({
        success: showResponse,
        data: { form_data: createSubmitData(), form_name: $('#fb-formName').val(), db_name: $('#fb-dbName').val(), notification_email: $('#fb-notificationEmail').val(), confirmation_email: $('#fb-confirmationEmail').val() },
        type: "POST",
        url : "php/createForm.php",
        dataType : "JSON"
    });
}

/********************************************
 *
 * Parses AJAX response from server
 *
 *******************************************/
function showResponse(responseText)  {
    responseText = JSON.parse(responseText);
    if(responseText['success']){
        $('#form-messages').text(responseText['html']);
        console.log(responseText);
    } else {
        $('#form-messages').text(responseText['error']);
    }
    $('.subBtn').prop('disabled', false);
}

/********************************************
 *
 * Loops through each row to create JSON string
 * for serverside processing
 *
 * @returns {string}
 *
 *******************************************/
function createSubmitData() {
    var data = {};
    data.rows = [];
    $('#buildDiv').find('.fullRow').each(function(row) {
        $row = $(this);
        var row = {};
        row.row = [];
        $(this).find('.elementColumn').each(function() {
            $column = $(this);
            col = {};
            col.len = $column.attr('data-length');
            col.containers = [];
            if($column.hasClass('full')){
                col.empty = false;
                $(this).find('.elementContainer').each(function() {
                    $con = $(this);
                    if($con.attr('data-type') == 'blank'){
                        return;
                    }
                    e = {};
                    e.type = $con.attr('data-type');
                    e.data = getElementData(e.type, $con);
                    col.containers.push(e);
                });
            } else {
                col.empty = true;
            }
            row.row.push(col);
        });
        data.rows.push(row);
    });
    return JSON.stringify(data);
}

/********************************************
 *
 * Calls Function to Parse Element Based on Type
 *
 * @returns {object}
 *
 *******************************************/
function getElementData(type, element){
    var data = null;
    switch(type) {
        case 'text':
            data = getTextInfo(element);
            break;
        case 'select':
            data = getSelectInfo(element);
            break;
        case 'checkbox':
            data = getCheckboxInfo(element);
            break;
        case 'radio':
            data = getRadioInfo(element);
            break;
        case 'textarea':
            data = getTextareaInfo(element);
            break;
        case 'submit':
            data = getSubmitInfo(element);
            break;
        case 'textElement':
            data = getTextElementInfo(element);
            break;
        default:
            data = null;
    }
    return data;
}

/********************************************
 *
 * Generates Objects Based On Element Type
 *
 * @returns {object}
 *
 *******************************************/
function getTextInfo(element){
    var dataObj = {};
    var txtEle  = $(element).find('input[type=text]');

    dataObj.id          = $(txtEle).attr('id');
    dataObj.classes     = $(txtEle).attr('class');
    dataObj.value       = $(txtEle).val();
    dataObj.placeholder = $(txtEle).attr('placeholder'); 
    dataObj.label       = $(element).find('label:first').text();

    return dataObj;
}

function getSelectInfo(element){
    var dataObj     = {};
    var selectEle   = $(element).find('select');

    dataObj.id          = $(selectEle).attr('id');
    dataObj.classes     = $(selectEle).attr('class');
    dataObj.value       = $(selectEle).val();
    dataObj.label       = $(element).find('label:first').text();
    dataObj.options     = [];

    $(selectEle).children('option').each(function(){
        var optionObj   = {};

        optionObj.text      = $(this).text();
        optionObj.value     = $(this).val();
        optionObj.selected  = $(this).is(':selected') ? true : false;
        optionObj.disabled  = $(this).is(':disabled') ? true : false;

        dataObj.options.push(optionObj);
    });

    return dataObj;
}

function getCheckboxInfo(element){
    var dataObj     = {};
    var checkboxes  = $(element).find('input[type=checkbox]');

    dataObj.label       = $(element).find('label:first').text();
    dataObj.group_name  = $(checkboxes).first().attr('name');
    dataObj.checkboxes  = [];

    $(checkboxes).each(function(){
        var optionObj   = {};

        optionObj.id        = $(this).attr('id');
        optionObj.name      = $(this).parent().text();
        optionObj.value     = $(this).val();
        optionObj.classes   = $(this).parent().attr('class');

        dataObj.checkboxes.push(optionObj);
    });

    return dataObj;
}

function getRadioInfo(element){
    var dataObj     = {};
    var radio       = $(element).find('input[type=radio]');

    dataObj.label       = $(element).find('label:first').text();
    dataObj.group_name  = $(radio).first().attr('name');
    dataObj.radio       = [];

    $(radio).each(function(){
        var optionObj   = {};

        optionObj.id        = $(this).attr('id');
        optionObj.name      = $(this).parent().text();;
        optionObj.value     = $(this).val();
        optionObj.classes   = $(this).parent().attr('class');

        dataObj.radio.push(optionObj);
    });

    return dataObj;
}

function getTextareaInfo(element){
    var dataObj = {};
    var txtEle  = $(element).find('textarea');

    dataObj.id          = $(txtEle).attr('id');
    dataObj.classes     = $(txtEle).attr('class');
    dataObj.text        = $(txtEle).text();
    dataObj.placeholder = $(txtEle).attr('placeholder'); 
    dataObj.label       = $(element).find('label:first').text();

    return dataObj;
}

function getTextElementInfo(element){
    var dataObj = {};
    var txtEle  = $(element).find('.elementDiv').children(':first');

    dataObj.id          = $(txtEle).attr('id');
    dataObj.classes     = $(txtEle).attr('class');
    dataObj.text        = $(txtEle).text();
    dataObj.label       = $(element).find('label:first').text();
    dataObj.type        = $(txtEle).attr('data-type');

    return dataObj;
}

function getSubmitInfo(element){
    var dataObj = {};
    var submit  = $(element).find('input[type=submit]');

    dataObj.id          = $(submit).attr('id');
    dataObj.classes     = $(submit).attr('class');
    dataObj.value       = $(submit).val();

    return dataObj;
}

function checkInput(input, error){
	var errors = false;
	$(input).each(function(index, element){
		if($.trim($(element).val()) == ''){
			errors = true;
			$(element).addClass('errorBorder');
			$(element).parent().next('.errorLabel').text(error);
		}
	});
	return errors;
}
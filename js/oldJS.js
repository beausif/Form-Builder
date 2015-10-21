function createHTML(){
    var mainForm = $('#fullPreview');
    var formName = $('#formName').val();
    var html = '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><title>' + formName + '</title><link href="../formAssets/css/bootstrap.min.css" rel="stylesheet"><link href="../formAssets/' + formName + '/css/main.css" rel="stylesheet"></head><body><div id ="form-messages" class="continer-fluid"></div><div class="container-fluid">';

    html += $(mainForm).get(0).outerHTML;

    html += "</div>";

    html += '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script><script src="../formAssets/js/jquery.maskedinput.min.js"></script><script src="../formAssets/js/jquery.numeric.min.js"></script><script src="../formAssets/js/jquery.currency.js"></script><script src="../formAssets/js/jquery.form.min.js"></script><script src="../formAssets/js/json3.min.js"></script><script src="../formAssets/js/bootstrap.min.js"></script><script src="../formAssets/' + formName + '/js/main.js"></script></body></html>';

    return html;
}

function createCSS(){

    return "body{background-color:#D0D0CE!important}html{overflow-x:hidden}.defloat{float:none}.addBold{font-weight:bold}.noDisc{list-style-type:none!important;padding-left:0!important;margin-left:0!important}.bigTopMargin{margin-top:200px;margin-bottom:200px}textarea{vertical-align:-webkit-baseline-middle;vertical-align:middle}.possibleClasses{border-radius:25px;border:2px solid#8AC007;display:inline-block;padding:5px;font-size:10px}ol li{padding-bottom:.5%}.addBorder{border:1px black solid}.topMargin{margin-top:5%}.alignRight{text-align:right}.alignLeft{text-align:left}label{text-align:right}.floatRight{float:right}.leftMargin{margin-left:2%}.stepClass{background-color:black;color:white}.removeMargin{margin:0}.inputColor{background-color:#efefef}.container-wrap{padding-bottom:0!important}.close{color:red!important;opacity:1!important;-webkit-box-shadow:none!important;box-shadow:none!important}.table{margin-bottom:10px!important}.subBtn{width:100%!important;background-color:#FFC72C!important;padding:10px 16px!important;font-size:18px!important;line-height:1.33!important;border-radius:6px!important}.removePadding{padding:0!important}.form-messages{font-size:2em;color:blue;border:5px red solid;text-align:center;display:none}.smallText{font-size:10px!important}.alignCenter{text-align:center!important}.errorBorder{border:1px solid red!important}.errorLabel{color:red!important;min-height:20px!important}.btn-file{position:relative;overflow:hidden}.btn-file input[type=file]{position:absolute;top:0;right:0;min-width:100%;min-height:100%;font-size:100px;text-align:right;filter:alpha(opacity=0);opacity:0;background:red;cursor:inherit;display:block}input[readonly]{background-color:white!important;cursor:text!important}.column_delete,.column_clone,.column_edit{float:right}.halfWidth{width:40%!important;float:left;text-align:center;margin-right:2.5%}.btnClass{width:15%!important;float:left}table{border-collapse:collapse}table,th,td{border:1px solid black;text-align:center!important}.dateDiv{margin-top:5%;margin-bottom:15%!important}th{font-weight:bold}th,td{padding:10px}";
}

function createPHP(){
    var mainForm = $('#previewDiv');
    var formName = $('#formName').val();
    var php = "<?php require '../../../PHPMailer/PHPMailerAutoload.php'; require '../../php/sendMail.php'; require '../../php/databaseQuery.php';";
    var elementNames = '';

    $(mainForm).find('.elementDiv').each(function(){
        var elementID = $(this).children().attr('id');
        var elementName = $(this).children().attr('name');
        elementNames += elementName + ', ';

        if($(this).children().hasClass('required')){
            php += "$" + elementID + " = is_empty($_POST['" + elementName + "']);";
        } else {
            php += "$" + elementID + " = empty($_POST['" + elementName + "']) ? NULL : $_POST['" + elementName + "'];";
        }
    });

    elementNames = elementNames.slice(0,-2);

    php += "$sql = 'INSERT INTO " + formName + " (date, " + elementNames + ") VALUES (CURDATE(), $" + elementNames.replace(/, /g, ', $')  + ")'; ";

    php += "$result = db_query($sql, 'bkforms'); if($result === false) { $response['success'] = false; $response['error'] = db_error(); die(json_encode($response)); }";

    php += "$addressArray = array('equilter@msgindy.com');";

    php += "$subject = '" + formName + "'; $body = '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta name=\"viewport\" content=\"width=device-width\" /><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><title>" + formName + "</title><!---<link rel=\"stylesheet\" type=\"text/css\" href=\"ZURBemails_files/email.css\">---></head><body bgcolor=\"#FFFFFF\" style=\"margin:0; padding:0;\"><table align=\"center\" width=\"900px\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse;\"><tr bgcolor=\"0053A0\"><td align=\"center\" align=\"absbottom\" style=\"padding: 25px 10px 0 10px;\"><img style=\"width:300px; height:111px; display:block;\" src=\"http://www.balkamp.com/formAssets/aimKits/images/bk.jpg\" /></td></tr><tr bgcolor=\"0053A0\"><td align=\"center\" style=\"padding: 0 10px 0 10px;\"><h2 style=\"color:#FDB82B; letter-spacing:2px; line-height:0; font-weight:600; -webkit-text-size-adjust: 100%; font-size: 34px; margin:15px;\">Below is your submitted information.</h2></td></tr><tr bgcolor=\"0053A0\"><td align=\"center\" style=\"padding-bottom: 10px;\"><h3 style=\"color:#FDB82B; letter-spacing:1px; line-height:0; font-weight:200; -webkit-text-size-adjust: 100%; font-size: 20px;\">If there are any discrepancies please contact Eugene Quilter at <a href=\"mailto:equilter@msgindy.com\" style=\"color: #f2f2f2; text-decoration: none;\">equilter@msgindy.com</a>.</h3></td></tr><tr><td bgcolor=\"#D4D4D4\" style=\"padding: 20px 20px 20px 20px; margin:inherit; line-height: 10px; letter-spacing: 1px; border-bottom: 1px #666666 dotted;\"><table style=\"font-size: 18px; font-weight: 300;\">";

    $(mainForm).find('.elementDiv').each(function(){
        var elementLabel = $(this).children().attr('id');
        var elementName = $(this).children().attr('name');

        if($(this).children().hasClass('required')){
            php += "<tr><td style=\"text-align: right;  padding: 10px;\">" + elementLabel  + "</td><td>' . $_POST['" + elementName  + "'] . '</td></tr>';";
        } else {
            php += "if(!empty($_POST[\"" + elementName  + "\"]) || $_POST[\"" + elementName  + "\"] !== \"\" || $_POST[\"" + elementName  + "\"] !== NULL || $_POST[\"" + elementName  + "\"] !== \"null\"){ $body .= \"<tr><td style=\'text-align: right;  padding: 10px;\'>" + elementLabel  + "</td><td>\" . $_POST['" + elementName  + "'] . \"</td></tr>\"; }";

        }
    });


    php += "$body .=\"</table></td></tr></table></body></html>\";";

    php += "$altBody = \"\";";

    php += "$success = sendEmail($addressArray, $subject, $body, $altBody);";

    php += "if($success === true){ $response[\"success\"] = true; $response[\"html\"] = \"Successfully Submitted\"; echo json_encode($response); } else { $response[\"success\"] = false; $response[\"error\"] = \"Successfully Submitted. Failed to Send Confirmation Email.\"; echo json_encode($response); }";

    php += "function is_empty($value){ if(empty($value) || $value == \"\" || $value == NULL || $value == \"null\"){ $response[\"success\"] = false; $response[\"error\"] = \"Failed to submit data. Please contact equilter@msgindy.com.\"; die(json_encode($response)); } else { return db_quote($value); }}";

    php += "?>";

    return php;
}



function createJavascript(){
    var mainForm = $('#fullPreview');
    var formName = $('#formName').val();

    var jScript = "$(function() {setFormSubmission(); setEvents();}); function setFunctions(){ ";

    if($(mainForm).find('.phoneMask').length > 0){
        jScript += "$('.phoneMask').mask('999-999-9999'); ";
    }
    if($(mainForm).find('.dateMask').length > 0){
        jScript += "$('.dateMask').mask('99/99/9999'); ";
    }
    if($(mainForm).find('.threeDigitMask').length > 0){
        jScript += "$('.threeDigitMask').mask('999'); ";
    }
    if($(mainForm).find('.fourDigitMask').length > 0){
        jScript += "$('.fourDigitMask').mask('9999'); ";
    }
    if($(mainForm).find('.positive-integer').length > 0){
        jScript += "$('.positive-integer').numeric({decimal: false, negative: false}, function() {this.value = ''; this.focus(); }); ";
    }

    jScript += "$('input, select, textarea').on('keydown', function(e) { if (e.keyCode == 13) { return false; } }); ";


    jScript += "} ";

    jScript += "function setFormSubmission(){ $('#ajax-content').submit(function() { $(this).ajaxSubmit({ beforeSubmit:  showRequest, success: showResponse, error: showError, type: 'POST', url: '../formAssets/" +  formName + "/php/main.php' }); return false; }); } ";

    jScript += "function showRequest(formData, jqForm, options) { var error = false; $('.subBtn').prop('disabled', true); $('.errorBorder').removeClass('errorBorder'); $('.errorLabel').each( function( index, element ){ $(element).text('');});";

    $(mainForm).find('.required').each(function(){
        var id = $(this).attr('id');
        if($(this).is('input:text')){
           jScript += "if($.trim($('#" + id + "').val()) == ''){ $('#" + id + "').addClass('errorBorder'); $('#" + id + "').parent().prevAll('.errorLabel:first').text('This Field is Required');  error = true; }";
        }

        if($(this).is('select')){
            jScript += "if($.trim($('#" + id + "').val()) == '' || $.trim($('#" + id + "').val()) == null){ $('#" + id + "').addClass('errorBorder'); $('#" + id + "').parent().prevAll('.errorLabel:first').text('This Field is Required');  error = true; }";
        }

        if($(this).is('input:checkbox')){
            jScript += "if(!$('#" + id + "').is(':checked')){ $('#" + id + "').addClass('errorBorder'); $('#" + id + "').parent().prevAll('.errorLabel:first').text('This Field is Required');  error = true; }";
        }

        if($(this).is('input:radio')){
            jScript += "if(!$('#" + id + "').is(':checked')){ $('#" + id + "').addClass('errorBorder'); $('#" + id + "').parent().prevAll('.errorLabel:first').text('This Field is Required');  error = true; }";
        }

        if($(this).is('textarea')){
            jScript += "if($.trim($('#" + id + "').val()) == ''){ $('#" + id + "').addClass('errorBorder'); $('#" + id + "').parent().prevAll('.errorLabel:first').text('This Field is Required');  error = true; }";
        }
    });

    jScript += "if(error){ $('.subBtn').prop('disabled', false); return false; } else { submitted(); return true; }";

    jScript += "} ";

    jScript += "function showResponse(responseText, statusText, xhr, $form)  { responseText = JSON.parse(responseText); if(responseText['success']){ $('#fullPreview')[0].reset(); $('#form-messages').text(responseText['html']); } else { $('#form-messages').text(responseText['error']); } $('.subBtn').prop('disabled', false); } ";

    jScript += "function showError(jqXHR, textStatus, errorThrown) { $('.subBtn').prop('disabled', false); $('#form-messages').css('display', 'block'); $('#form-messages').text('<p>There was a problem submitting the form.</p><p>Please try again or contact equilter@msgindy.com.</p><p>' + jqXHR + ' | ' + textStatus + ' | ' + errorThrown + '</p>'); window.scrollTo(0,0); } ";

    jScript += "function submitted(){ $('#form-messages').css('display', 'block'); $('#form-messages').text('Processing...Please Wait.'); window.scrollTo(0,0); } ";

    return jScript;
}



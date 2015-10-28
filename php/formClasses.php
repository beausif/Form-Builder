<?php

class Form {

	private $form_name;
	private $db_name;
	private $note_email;
	private $conf_email;
	private $form_email;
	private $data;
	private $form_html;
	private $form_js;
	private $form_js_required;
	private $form_css;
	private $form_php;
	private $form_php_required;
	private $element_list = [];


	public function __construct($data, $form_name, $db_name, $note_email, $conf_email){
		$this->data = $data;
		$this->form_name = $this->scrub_name($form_name);
		$this->db_name = $this->scrub_name($db_name);
		$this->note_email = $note_email;
		$this->conf_email = $conf_email;
		$this->create_form();
		echo $this->form_email;
		if($this->conf_email == 'yes' && empty($this->form_email)){
			$response["success"] = false;
			$response["text"] = "Confirmation email set to yes. This requires a Text Input Element with the name email";
			die(json_encode($response));
		}
		$this->form_js = $this->create_js();
		$this->form_php = $this->create_php();
	}

	private function create_form(){
		$this->form_html .= $this->start_html();
		foreach($this->data as $row){
			$this->form_html .= $this->start_row_html();
			foreach($row->row as $containers){
				$this->form_html .= $this->start_container_html($containers->len);
				foreach($containers->containers as $element){
					if($element->data !== null){
						$element_class = $this->select_type($element);
						$this->element_list[] = $element_class;
						$this->form_html .= $element_class->html;
						$this->form_js_required .= $element_class->is_required();
						if($element_class->get_id() == 'email'){
							$this->form_email = $element_class->get_value();
						}
					}
				}
				$this->form_html .= $this->end_div_html();
			}
			$this->form_html .= $this->end_div_html();
		}
		$this->form_html .= $this->end_html();
	}

	private function create_js(){
		return 

"(function($) {
	$(function() {
		setEvents();
	});
	
	function setEvents(){
		$('#fb-form').on('submit', function(e) {
			var event = e.originalEvent;
			event.preventDefault ? e.preventDefault() : event.returnValue = false;
			$(this).ajaxSubmit({
				url:		   'php/main.php',
				type:		   'POST',
				dataType:	   'JSON',
				beforeSubmit:  showRequest,
				success:       showResponse,
				error:		   showError
			});
			return false;
		});

		$('input, select').off('keydown');
		
		$('input, select').on('keydown', function(e) {
			if (e.keyCode == 13) {
				return false;
			}
		});
	}

	function showRequest(formData, jqForm, options){
		resetErrors();
		var noError = true;
		$('input[type=submit]').prop('disabled', true);

		" .

		$this->form_js_required
		
		. "

		if(noError){
			createMessage('success', 'Please wait form is being submitted');
		} else {
			$('input[type=submit]').prop('disabled', false);
		}
		return noError;
	}

	function showResponse(response, status, xhr, \$form){
		if(response.success){
			$('#fb-form')[0].reset();
			createMessage('success', response.text);
		} else {
			createMessage('error', response.text);
		}
		$('input[type=submit]').prop('disabled', false);
	}

	function showError(jqXHR, textStatus, errorThrown) {
		$('input[type=submit]').prop('disabled', false);
		createMessage('error', 'Problem submitting the form : ' +  jqXHR + ' | ' + textStatus + ' | ' + errorThrown);
	}

	function resetErrors(){
		$('.errorLabel').text('');
		$('.errorBorder').removeClass('errorBorder');
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

	function setError(response){
		if(typeof response == 'string'){
			createMessage('error', response);
		} else {
			createMessage('error', 'Unknown Error Occured')
		}
	}

	function createMessage(type, message){
		var alert_type = null;
		var alert_msg = null;
		var html = null;
		if(type == 'success'){
			alert_type = 'success';
			alert_msg = 'Success!';
		} else {
			alert_type = 'danger';
			alert_msg = 'Error!';
		}

		html = ' \
		<div class=\"col-sm-6 col-sm-offset-3\"> \
			<div class=\"alert alert-' + alert_type + ' fade in\"> \
				<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a> \
				<strong>' + alert_msg + ' </strong><p class=\"inline\">' + message + '<p> \
			</div> \
		</div>';


		$('#message-div').html(html);
		window.scrollTo(0,0);
	}

})( jQuery );";
	}
	
	private function create_php(){
									$php =
'<?php
';
									if($this->note_email !== NULL){ $php .= '
	require "PHPMailer/PHPMailerAutoload.php";
	require "sendMail.php";
	';
									}
									if($this->db_name !== NULL){ $php .= '
	require "databaseQuery.php";

	';
									$php .= 
	$this->get_post_data();
									}
									if($this->db_name !== NULL){ $php .= '
	//SQL Statements
	$sql = "CREATE DATABASE IF NOT EXISTS ' . $this->db_name . '";
	$result = db_query($sql);

	db_new_connection("' . $this->db_name . '");
	
	' . $this->get_sql_statements() .
	'								
	$result = db_query($sql);
	if($result === false) {
		$response["success"] = false;
		$response["text"] = db_error();
		die(json_encode($response));
	}
	';
									}
									
									if($this->note_email === NULL){ $php .= '
	$response["success"] = true;
	$response["text"] = "Successfully Submitted Form";
	die(json_encode($response));	
	';
									}
									
									if($this->note_email !== NULL){ $php .= '
	//Create and Send Email
	';
	
									if($this->conf_email !== NULL){ $php .= "
	\$addressArray = array(\$email, '{$this->note_email}'); ";
									} else { $php = "
	\$addressArray = array('{$this->note_email}'); ";
									}
									
									$php .= '
	$subject = "' . $this->form_name . ' Confirmation Email";
	$body = \'<!DOCTYPE html>
	<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>' . $this->form_name . ' Confirmation Email</title>
	<!---<link rel="stylesheet" type="text/css" href="ZURBemails_files/email.css">--->
	</head>
	<body bgcolor="#FFFFFF" style="margin:0; padding:0;">
		<table align="center" width="900px" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;""> 
			<tr bgcolor="0053A0">
				<td align="center" align="absbottom" style="padding: 25px 10px 0 10px;">
					<img style="width:300px; height:111px; display:block;" src="" />              
				</td>
			</tr>
			<tr bgcolor="0053A0">
				<td align="center" style="padding: 0px 10px 0px 10px;">
					<h2 style="color:#FDB82B; letter-spacing:2px; line-height:0px; font-weight:600; -webkit-text-size-adjust: 100%; font-size: 34px; margin:15px;">Submitted Information Below</h2>
				</td>
			</tr>
			<tr bgcolor="0053A0">
				<td align="center" style="padding-bottom: 10px;">
					<h3 style="color:#FDB82B; letter-spacing:1px; line-height:0px; font-weight:200; -webkit-text-size-adjust: 100%; font-size: 20px;">
				
				
					</h3>
				</td>
			</tr>
			<tr>
				<td bgcolor="#D4D4D4" style="padding: 20px 20px 20px 20px; margin:inherit; line-height: 10px; letter-spacing: 1px; border-bottom: 1px #666666 dotted;">
					<table style="font-size: 18px; font-weight: 300;">';
					
									foreach($this->element_list as $element){
										if($element->get_user_element()){
											$php .= '	
						<tr>
							<td style="text-align: right;  padding: 10px;">' . $element->get_label() . ': </td>
							<td>$' . $this->scrub_name($element->get_id()) . '</td>
						</tr>';
										}
										
									}
						
									$php .= '\';			
		 $body .= "</table>
				</td>
			</tr>
		</table>
	</body>
	</html>";
		
	$altBody = "";
	
	$success = sendEmail($addressArray, $subject, $body, $altBody);
	
	if($success === true){
		$response["success"] = true;
		$response["text"] = "Successfully Submitted Form";
		echo json_encode($response);
	} else {
		$response["success"] = false;
		$response["text"] = "Form Submitted. Error Sending Email.";
		echo json_encode($response);
	}';
									}
	
									$php .= '
	function missing_data() {
		$response["success"] = false;
		$response["text"] = "Missing Data";
		die(json_encode($response));
	}
	
?>	
	';
	
	return $php;
	}
	
	
	private function get_post_data(){
		$req_data = "";
		$non_req_data = "";
		$array_check = "";
		$quoted_data = "";
		foreach($this->element_list as $element){
			if($element->get_user_element()){
				$eID = $this->scrub_name($element->get_id());
				if($element->get_required() == false){
					$non_req_data .= 
		'$' . $eID . ' = empty($_POST["' . $element->get_id() . '"]) ? NULL : $_POST["' . $element->get_id() . '"];
	';
				} else {
					$req_data .=
		'empty($_POST["' . $element->get_id() . '"]) ? missing_data() : $' . $eID . ' = $_POST["' . $element->get_id() . '"];
	';	
				}
				if(is_a($element, 'Checkbox_Input') || is_a($element, 'Radio_Input')){
					$array_check .= 
		'if(is_array($' . $element->get_id() . ')){
		$' . $eID . ' = implode(",", $' . $eID . ');
	}';
				}
				
				$quoted_data .= 
		'$' . $eID . '_quoted = db_quote($' . $eID . ');
	';
			}
		}
		
		$data = 
	'//Non Required Form Data
	' . $non_req_data . '
	//Required Form Data
	' . $req_data . '
	//Array Concat
	' . $array_check . '
	//Quoted Data
	' . $quoted_data;
		return $data;
	}
	
	private function get_sql_statements(){
		$sql_statement = "\$sql = \"INSERT INTO {$this->form_name} ";
		$sql_columns = "(submission_date, ";
		$sql_values = "VALUES (CURDATE(), ";
		$create_values = "(id int NOT NULL AUTO_INCREMENT PRIMARY KEY, submission_date date, ";
		
		foreach($this->element_list as $element){
			if($element->get_user_element()){
				$eID = $this->scrub_name($element->get_id());
				$sql_columns .= $eID . ', ';
				$sql_values .= '{$' . $eID . '_quoted}, ';
				$create_values .= $eID . " {$element->db_type}, ";
			}
		}
		
		$sql_columns = substr($sql_columns, 0, -2);
		$sql_values = substr($sql_values, 0, -2);
		$create_values = substr($create_values, 0, -2);
		
		$sql_columns .= ") ";
		$sql_values .= ')";';
		$create_values .= ")";
		
		$create_statement =
		"\$sql = 'CREATE TABLE IF NOT EXISTS " . $this->form_name . " " . $create_values . "';
	\$result = db_query(\$sql);
	
	";
		
		
		$sql_statement .= $sql_columns . $sql_values;
		return $create_statement . $sql_statement;
	}
	
	private function scrub_name($name){
		$name = preg_replace('/[^.[:alnum:]_-]/','_',trim($name));
		$name = preg_replace('/\.*$/','',$name);
		
		return $name;
	}
	
	private function start_html(){
		return
"<!DOCTYPE html>
	<head>
		<link href='css/bootstrap.min.css' rel='stylesheet'>
		<link href='css/main.css' rel='stylesheet'>
	</head>
	<body>
		<div id='message-div' class='row'></div>
		<form id='fb-form'>
";
	}
	
	private function end_html(){
		return
"
		</form>
		<script src='js/jquery-1.11.3.min.js'></script>
		<script src='js/jquery.form.min.js'></script>
		<script src='js/main.js'></script>
		<script src='js/bootstrap.min.js'></script>
	</body>
</html>";	
	}

	private function start_row_html(){
		return "
				<div id='formDiv' class='row'>";
	}

	private function start_container_html($length){
		return "
					<div class='col-sm-{$length} column'>";
	}
	private function end_div_html(){
		return "
					</div>";
	}

	private function select_type($element){
	    switch($element->type) {
	        case 'text':
	            $e = new Text_Input($element->data);
	            break;
	        case 'select':
	            $e = new Select_Input($element->data);
	            break;
	        case 'checkbox':
	            $e = new Checkbox_Input($element->data);
	            break;
	        case 'radio':
	            $e = new Radio_Input($element->data);
	            break;
	        case 'textarea':
	            $e = new Textarea_Input($element->data);
	            break;
	        case 'submit':
	            $e = new Submit_Element($element->data);
	            break;
	        case 'textElement':
	            $e = new Text_Element($element->data);
	            break;
	        default:
	            return null;
	    }
	    return $e;
	}

	public function get_html(){
		return $this->form_html;
	}

	public function get_js(){
		return $this->form_js;
	}
	
	public function get_php(){
		return $this->form_php;	
	}
}

class Element {
	protected $id;
	protected $classes;
	protected $label;
	protected $required;
	protected $value;
	protected $user_element;
	public $html;
	
	public function is_required(){
		$class_list = explode(' ', $this->classes);

		foreach($class_list as $class){
			if($class == 'required'){
				$this->required = true;
				return 		
		"if(checkInput('#{$this->id}', 'Required Input')){
			noError = false;
		}
		";
			}
		}
		$this->required = false;
		return "";
	}
	
	public function get_id(){
		return $this->id;	
	}
	
	public function get_label(){
		return $this->label;	
	}
	
	public function get_value(){
		return $this->value;	
	}
	
	public function get_required(){
		return $this->required;	
	}
	
	public function get_classes(){
		return $this->classes;	
	}
	
	public function get_user_element(){
		return $this->user_element;	
	}
}

class Text_Input extends Element {
	private $placeholder;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->value = $data->value;
		$this->placeholder = $data->placeholder;
		$this->label = $data->label;
		$this->user_element = true;

		$this->create_element();
	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<label for='{$this->id}' class='col-sm-12 control-label'>{$this->label}</label>
							<div class='col-sm-12'>
								<input type='text' id='{$this->id}' name='{$this->id}' class='{$this->classes}' value='{$this->value}' placeholder='{$this->placeholder}'>
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";
	}
}

class Select_Input extends Element {
	private $options;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->value = $data->value;
		$this->label = $data->label;
		$this->options = $data->options;
		$this->user_element = true;

		$this->create_element();

	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<label for='{$this->id}' class='col-sm-12 control-label'>{$this->label}</label>
							<div class='col-sm-12'>
								<select id='{$this->id}' name='{$this->id}' class='{$this->classes}'>
									{$this->create_options($this->options)}
								</select>
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";

	}

	function create_options($options){
		$options_html = null;
		foreach($options as $option){
			$selected = ($option->selected === false) ? null : ' selected';
			$disabled = ($option->disabled === false) ? null : ' disabled'; 
			$options_html .= "<option name='{$option->text}' value='{$this->value}'{$selected}{$disabled}>{$option->text}</option>
									";
		}

		return $options_html;
	}
}

class Checkbox_Input extends Element {
	private $group_name;
	private $checkboxes;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->label 		= $data->label;
		$this->id 			= str_replace('[]', '', $data->group_name);
		$this->group_name 	= str_replace('[]', '', $data->group_name);
		$this->checkboxes 	= $data->checkboxes;
		$this->user_element = true;

		$this->create_element();
	}

	function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<label class='col-sm-12 control-label'>{$this->label}</label>
							<div class='col-sm-12'>
								{$this->create_checkboxes($this->checkboxes)}
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";
	}

	function create_checkboxes($checkboxes){
		$checkboxes_html = null;
		foreach($checkboxes as $checkbox){ 
			$checkboxes_html .= "<label class='{$checkbox->classes}'>
									<input type='checkbox' id='{$checkbox->id}' name='{$this->group_name}[]' value='{$checkbox->value}'> {$checkbox->name}
								</label>
								";
		}

		return $checkboxes_html;
	}
}

class Radio_Input extends Element {
	private $group_name;
	private $radios;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->label 		= $data->label;
		$this->id 			= str_replace('[]', '', $data->group_name);
		$this->group_name 	= str_replace('[]', '', $data->group_name);
		$this->radios 		= $data->radio;
		$this->user_element = true;

		$this->create_element();
	}

	function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<label class='col-sm-12 control-label'>{$this->label}</label>
							<div class='col-sm-12'>
								{$this->create_radios()}
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";
	}

	function create_radios(){
		$radios_html = null;
		foreach($this->radios as $radio){ 
			$radios_html .= "<label class='{$radio->classes}'>
									<input type='radio' id='{$radio->id}' name='{$this->group_name}[]' value='{$radio->value}'> {$radio->name}
								</label>
								";
		}

		return $radios_html;
	}
}

class Textarea_Input extends Element {
	private $text;
	private $placeholder;
	public $db_type = "text";

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->text = $data->text;
		$this->placeholder = $data->placeholder;
		$this->label = $data->label;
		$this->user_element = true;

		$this->create_element();
	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<label for='{$this->id}' class='col-sm-12 control-label'>{$this->label}</label>
							<div class='col-sm-12'>
								<textarea rows='4' id='{$this->id}' name='{$this->id}' class='{$this->classes}' placeholder='{$this->placeholder}'>{$this->text}</textarea>
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";
	}
}

class Text_Element extends Element {
	private $text;
	private $type;

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->text = $data->text;
		$this->type = $data->type;
		$this->user_element = false;

		$this->create_element();
	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<div class='col-sm-12'>
								<{$this->type} id='{$this->id}' name='{$this->id}' class='{$this->classes}'>{$this->text}</{$this->type}>
							</div>
							<label class='col-sm-12 errorLabel'></label>
						</div>";
	}
}

class Submit_Element extends Element {

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->value = $data->value;
		$this->user_element = false;
		
		$this->create_element();
	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<input type='submit' id='{$this->id}' name='{$this->id}' class='{$this->classes}' value='{$this->value}'>
						</div>";
	}
}

?>
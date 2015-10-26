<?php

class Form {

	private $form_name;
	private $db_name;
	private $data;
	private $form_html;
	private $form_js;
	private $form_js_required;
	private $form_css;
	private $form_php;
	private $form_php_required;
	private $element_list = [];


	public function __construct($data, $form_name, $db_name){
		$this->data = $data;
		$this->form_name = $this->scrub_name($form_name);
		$this->db_name = $this->scrub_name($db_name);
		$this->create_form();
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
		<div class=\'col-sm-6 col-sm-offset-3\'> \
			<div class=\'alert alert-\' + alert_type + \'fade in\'> \
				<a href=\'#\' class=\'close\' data-dismiss=\'alert\' aria-label=\'close\'>&times;</a> \
				<strong>\' + alert_msg + \' </strong><p class=\'inline-text\'>\' + message + \'<p> \
			</div> \
		</div>';


		$('message-div').html(html);
		window.scrollTo(0,0);
	}

})( jQuery );";
	}
	
	private function create_php(){
		return '
<?php
	require "PHPMailer/PHPMailerAutoload.php";
	require "sendMail.php";
	require "databaseQuery.php";
	
	' . 
	$this->get_post_data() .
	'
	//SQL Statements
	'.
	$this->get_sql_statements()
	
	. '
	
	$result = db_query($sql, "' . $this->db_name . '");
	if($result === false) {
		$response["success"] = false;
		$response["text"] = db_error();
		die(json_encode($response));
	} else {
		$response["success"] = true;
		$response["text"] = "Successfully Submitted Form";
		die(json_encode($response));	
	}
	
	function missing_data() {
		$response["success"] = false;
		$response["text"] = "Missing Data";
		die(json_encode($response));
	}
	
?>	
	';
	}
	
	private function get_post_data(){
		$req_data = "";
		$non_req_data = "";
		$quoted_data = "";
		foreach($this->element_list as $element){
			if(!is_a($element, 'Submit_Element')){
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
			if(!is_a($element, 'Submit_Element')){
				$eID = $this->scrub_name($element->get_id());
				$sql_columns .= $eID . ', ';
				$sql_values .= '{$' . $element->get_id() . '_quoted}, ';
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
	\$result = db_query(\$sql, '" . $this->db_name . "');
	
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


class Text_Input {
	private $id;
	private $classes;
	private $value;
	private $placeholder;
	private $label;
	private $required;
	public $html;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->value = $data->value;
		$this->placeholder = $data->placeholder;
		$this->label = $data->label;

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
	
	public function get_required(){
		return $this->required;	
	}

}

class Select_Input {
	private $id;
	private $classes;
	private $value;
	private $label;
	private $options;
	private $required;
	public $html;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->value = $data->value;
		$this->label = $data->label;
		$this->options = $data->options;

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
}

class Checkbox_Input {
	private $label;
	private $group_name;
	private $checkboxes;
	private $required;
	public $html;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->label = $data->label;
		$this->group_name = $data->group_name;
		$this->checkboxes = $data->checkboxes;

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
									<input type='checkbox' id='{$checkbox->id}' name='{$this->group_name}' value='{$checkbox->value}'> {$checkbox->name}
								</label>
								";
		}

		return $checkboxes_html;
	}

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
}

class Radio_Input {
	private $label;
	private $group_name;
	private $radios;
	private $required;
	public 	$html;
	public $db_type = "varchar(255)";

	public function __construct($data){
		$this->label 		= $data->label;
		$this->group_name 	= $data->group_name;
		$this->radios 		= $data->radio;

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
									<input type='radio' id='{$radio->id}' name='{$this->group_name}' value='{$radio->value}'> {$radio->name}
								</label>
								";
		}

		return $radios_html;
	}

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
}

class Textarea_Input {
	private $id;
	private $classes;
	private $text;
	private $placeholder;
	private $label;
	private $required;
	public $html;
	public $db_type = "text";

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->text = $data->text;
		$this->placeholder = $data->placeholder;
		$this->label = $data->label;

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

}

class Text_Element {
	private $id;
	private $classes;
	private $text;
	private $type;
	private $required;
	public  $html;

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->text = $data->text;
		$this->label = $data->label;
		$this->type = $data->type;

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
}

class Submit_Element {
	private $id;
	private $classes;
	private $value;
	private $required;
	public  $html;

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->value = $data->value;
		
		$this->create_element();
	}

	private function create_element(){
		$this->html = "
						<div class='col-sm-12 form-horizontal'>
							<input type='submit' id='{$this->id}' name='{$this->id}' class='{$this->classes}' value='{$this->value}'>
						</div>";
	}

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
}

?>
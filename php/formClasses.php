<?php


class Form {

	private $data;
	private $form_html;

	public function __construct($data){
		$this->data = $data;
		$this->create_form();
	}

	private function create_form(){
		foreach($this->data as $row){
			$this->form_html .= $this->start_row_html();
			foreach($row->row as $containers){
				$this->form_html .= $this->start_container_html($containers->len);
				foreach($containers->containers as $element){
					if($element->data !== null){
						$this->form_html .= $this->select_type($element);
					}
				}
				$this->form_html .= $this->end_div_html();
			}
			$this->form_html .= $this->end_div_html();
		}
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
	    return $e->html;
	}

	public function get_html(){
		return $this->form_html;
	}
}


class Text_Input {
	private $id;
	private $classes;
	private $value;
	private $placeholder;
	private $label;
	public $html;

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

}

class Select_Input {
	private $id;
	private $classes;
	private $value;
	private $label;
	private $options;
	public $html;

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
}

class Checkbox_Input {
	private $label;
	private $group_name;
	private $checkboxes;
	public $html;

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
}

class Radio_Input {
	private $label;
	private $group_name;
	private $radios;
	public 	$html;

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
}

class Textarea_Input {
	private $id;
	private $classes;
	private $text;
	private $placeholder;
	private $label;
	public $html;

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

}

class Text_Element {
	private $id;
	private $classes;
	private $text;
	private $type;
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
}

class Submit_Element {
	private $id;
	private $classes;
	private $val;
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
							<input type='submit' id='{$this->id}' name='{$this->id}' class='{$this->classes}' value='{$this->val}'>
						</div>";
	}
}


?>
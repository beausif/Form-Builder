<?php

require 'Element.php';

class HtmlFile {

	private $html;
	private $data;
	private $element_list = [];
	private $has_email;
	private $form_js_required;

	public function __construct($data){
		$this->data = $data;
		$this->create_html();
	}
	
	private function create_html(){
		$this->html .= $this->start_html();
		foreach($this->data as $row){
			$this->html .= $this->start_row_html();
			foreach($row->row as $containers){
				$this->html .= $this->start_container_html($containers->len);
				foreach($containers->containers as $element){
					if($element->data !== null){
						$element_class = $this->select_type($element);
						$this->element_list[] = $element_class;
						$this->form_js_required .= $element_class->is_required();
						$this->html .= $element_class->html;
						if($element_class->get_id() == 'email'){
							$this->has_email = true;
						}
					}
				}
				$this->html .= $this->end_div_html();
			}
			$this->html .= $this->end_div_html();
		}
		$this->html .= $this->end_html();
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
		return $this->html;	
	}
	
	public function get_has_email(){
		return $this->has_email;	
	}
	
	public function get_form_js_required(){
		return $this->form_js_required;	
	}
	
	public function get_element_list(){
		return $this->element_list;	
	}
}
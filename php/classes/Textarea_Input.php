<?php

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

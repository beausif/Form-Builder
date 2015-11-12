<?php

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
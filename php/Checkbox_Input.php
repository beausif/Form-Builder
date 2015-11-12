<?php

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
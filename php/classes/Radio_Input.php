<?php

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
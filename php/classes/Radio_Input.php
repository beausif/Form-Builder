<?php

class Radio_Input extends Element {
	private $group_name;
	private $radios;
	public $db_type = "varchar(255)";

	public function __construct($data, $count){
		$this->label 		= $data->label;
		$this->id 			= $data->label . '_' . str_pad($count, 5, '0', STR_PAD_LEFT);
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
									<input type='radio' id='{$radio->id}' name='{$this->id}[]' value='{$radio->value}'> {$radio->name}
								</label>
								";
		}

		return $radios_html;
	}
}
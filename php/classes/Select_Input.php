<?php

class Select_Input extends Element {
	private $options;
	public $db_type = "varchar(255)";

	public function __construct($data, $count){
		$this->id = $data->label . '_' . str_pad($count, 5, '0', STR_PAD_LEFT);
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

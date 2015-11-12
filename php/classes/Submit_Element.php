<?php

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
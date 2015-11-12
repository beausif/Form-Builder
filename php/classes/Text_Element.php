<?php

class Text_Element extends Element {
	private $text;
	private $type;

	public function __construct($data){
		$this->id = $data->id;
		$this->classes = $data->classes;
		$this->text = $data->text;
		$this->type = $data->type;
		$this->user_element = false;

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
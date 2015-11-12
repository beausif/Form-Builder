<?php

require 'Text_Input.php';
require 'Select_Input.php';
require 'Checkbox_Input.php';
require 'Radio_Input.php';
require 'Textarea_Input.php';
require 'Text_Element.php';
require 'Submit_Element.php';

class Element {
	protected $id;
	protected $classes;
	protected $label;
	protected $required;
	protected $value;
	protected $user_element;
	public $html;
	
	public function is_required(){
		$class_list = explode(' ', $this->classes);

		foreach($class_list as $class){
			if($class == 'required'){
				$this->required = true;
				return 		
		"if(checkInput('#{$this->id}', 'Required Input')){
			noError = false;
		}
		";
			}
		}
		$this->required = false;
		return "";
	}
	
	public function get_id(){
		return $this->id;	
	}
	
	public function get_label(){
		return $this->label;	
	}
	
	public function get_value(){
		return $this->value;	
	}
	
	public function get_required(){
		return $this->required;	
	}
	
	public function get_classes(){
		return $this->classes;	
	}
	
	public function get_user_element(){
		return $this->user_element;	
	}
}

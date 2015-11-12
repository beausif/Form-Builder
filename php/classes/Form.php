<?php

require 'Html.php';
require 'JsFile.php';
require 'PhpMainFile.php';
require 'PhpEmailFile.php';

class Form {

	private $data;
	private $form_name;
	private $db_name;
	private $note_email;
	private $conf_email;

	private $html;
	private $js;
	private $php_main;
	private $php_email;


	public function __construct($data, $form_name, $db_name, $note_email, $conf_email){
		$this->data 		= $data;
		$this->form_name 	= $this->scrub_name($form_name);
		$this->db_name 		= $this->scrub_name($db_name);
		$this->note_email 	= $note_email;
		$this->conf_email 	= $conf_email;

		$this->html 		= new HtmlFile($data);
		$this->js 			= new JsFile($this->html->get_form_js_required());
		$this->php_main 	= new PhpMainFile($this->db_name, $this->html->get_has_email(), $this->html->get_element_list(), $this->form_name);
		$this->php_email 	= new PhpEmailFile($this->note_email, $this->conf_email, $this->form_name . ' Confirmation Email');
	
		/*if($this->conf_email == 'yes' && empty($this->form_email)){
			$response["success"] = false;
			$response["text"] = "Confirmation email set to yes. This requires a Text Input Element with the name email";
			die(json_encode($response));
		}*/
	}
	
	
	
	private function scrub_name($name){
		$name = preg_replace('/[^.[:alnum:]_-]/','_',trim($name));
		$name = preg_replace('/\.*$/','',$name);
		
		return $name;
	}

	public function get_html(){
		return $this->html;
	}

	public function get_js(){
		return $this->js;
	}
	
	public function get_php_main(){
		return $this->php_main;
	}
	
	public function get_php_email(){
		return $this->php_email;
	}
}

?>
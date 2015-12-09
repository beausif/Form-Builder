<?php

class PhpMainFile {

	private $php;
	private $db_name;
	private $has_email;
	private $form_name;
	private $conf_email;
	private $subject;
	private $element_list = [];

	public function __construct($db_name, $has_email, $element_list, $form_name){
		$this->db_name = $db_name;
		$this->has_email = $has_email;
		$this->element_list = $element_list;
		$this->form_name = $form_name;
		$this->create_php();
	}

	private function create_php(){
		$this->php =
'<?php
';
		if($this->db_name !== NULL){ 
			$this->php .= $this->db_start();
		}
		
		$this->php .= $this->get_post_data();
		
		if($this->db_name !== NULL){ 
			$this->php .= $this->get_sql_data();
		}
														
		if($this->has_email){ 
			$this->php .= $this->get_email();
		}
	
		$this->php .= $this->get_php_main_ending();
		
		$this->php .= $this->get_extra_methods();
	}
	
	private function get_post_data(){
		$req_data = "";
		$non_req_data = "";
		$array_check = "";
		foreach($this->element_list as $element){
			if($element->get_user_element()){
				$eID = $this->scrub_name($element->get_id());
				if($element->get_required() == false){
					$non_req_data .= 
		'$' . $eID . ' = empty($_POST["' . $element->get_id() . '"]) ? NULL : $_POST["' . $element->get_id() . '"];
	';
				} else {
					$req_data .=
		'empty($_POST["' . $element->get_id() . '"]) ? missing_data() : $' . $eID . ' = $_POST["' . $element->get_id() . '"];
	';	
				}
				if(is_a($element, 'Checkbox_Input') || is_a($element, 'Radio_Input')){
					$array_check .= 
		'if(is_array($' . $element->get_id() . ')){
		$' . $eID . ' = implode(",", $' . $eID . ');
	}';
				}
			}
		}
		
		$data = 
	'//Non Required Form Data
	' . $non_req_data . '
	//Required Form Data
	' . $req_data . '
	//Array Concat
	' . $array_check;
	
		return $data;
	}
	
	private function get_sql_statements(){
		$sql_statement = 
	'try {
		$db->db_query("INSERT INTO ' . $this->form_name;
		

		$sql_columns = " (submission_date, ";
		$sql_values = "VALUES (CURDATE(), ";
		$create_values = "(id int NOT NULL AUTO_INCREMENT PRIMARY KEY, submission_date date, ";
		$params = 'array(';
		
		$count = 1;
		foreach($this->element_list as $key=>$element){
			if($element->get_user_element()){
				$eID = $this->scrub_name($element->get_id());
				$sql_columns .= $eID . ', ';
				$sql_values .= ':input' . $count . ', ';
				$create_values .= $eID . " {$element->db_type}, ";
				$params .= '":input' . $count . '" => $' . $eID . ', ';
				$count = $count + 1;
			}
		}
		
		$sql_columns = substr($sql_columns, 0, -2);
		$sql_values = substr($sql_values, 0, -2);
		$create_values = substr($create_values, 0, -2);
		$params = substr($params, 0, -2);
		
		$sql_columns .= ") ";
		$sql_values .= ')", ';
		$create_values .= ")";
		$params .= "));";
		
		$create_statement = 
	'try {
		$db->db_query("CREATE TABLE IF NOT EXISTS ' . $this->form_name . ' ' . $create_values . '");
	} catch (PDOException $ex) {
		$response["success"] = false;
		$response["text"] = "DB Error: " . $ex->getMessage();
		die(json_encode($response));
	}
	
	';
		
		
		$sql_statement .= $sql_columns . $sql_values;
		
		$sql_statement .= $params . '
	} catch (PDOException $ex) {
		$response["success"] = false;
		$response["text"] = "DB Error: " . $ex->getMessage();
		die(json_encode($response));
	}
	
	';
		
		return $create_statement . $sql_statement;
	}
	
	private function db_start(){
		return '
	require "databaseQuery.php";
	
	//Create MySQL Database Object
	$db = new PdoMysql();

	';	
	}
	
	private function get_sql_data(){
		return '
	//SQL Statements
	try {
		$result = $db->db_query("CREATE DATABASE IF NOT EXISTS ' . $this->db_name . '");
	} catch (PDOException $ex) {
		$response["success"] = false;
		$response["text"] = "DB Error: " . $ex->getMessage();
		die(json_encode($response));
	}

	try {
		$db->db_new_connection("' . $this->db_name . '");
	} catch (PDOException $ex) {
		$response["success"] = false;
		$response["text"] = "DB Error: " . $ex->getMessage();
		die(json_encode($response));
	}

	' . $this->get_sql_statements() . '
						
	';	
	}
	
	private function get_php_main_ending(){
		if($this->has_email === false){ 
			return '
	$response["success"] = true;
	$response["text"] = "Successfully Submitted Form";
	die(json_encode($response));	
	';
		} else {
			return '
	if($success === true){
		$response["success"] = true;
		$response["text"] = "Successfully Submitted Form";
		echo json_encode($response);
	} else {
		$response["success"] = false;
		$response["text"] = "Form Submitted. Error Sending Email.";
		echo json_encode($response);
	}';	
		}
	}
	
	private function get_email(){
		return "
	//Create and Send Email
	require_once 'email.php';
	";	
	}
	
	private function get_extra_methods(){
		return '
	function missing_data() {
		$response["success"] = false;
		$response["text"] = "Missing Data";
		die(json_encode($response));
	}
	
?>	
	';	
	}
	
	public function get_php_main(){
		return $this->php;	
	}
	
	private function scrub_name($name){
		$name = preg_replace('/[^.[:alnum:]_-]/','_',trim($name));
		$name = preg_replace('/\.*$/','',$name);
		
		return $name;
	}
}
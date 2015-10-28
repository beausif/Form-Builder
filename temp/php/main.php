<?php

	require "databaseQuery.php";

	//Non Required Form Data
	
	//Required Form Data
	empty($_POST["asdfaf"]) ? missing_data() : $asdfaf = $_POST["asdfaf"];
	
	//Array Concat
	
	//Quoted Data
	$asdfaf_quoted = db_quote($asdfaf);
	
	//SQL Statements
	$sql = "CREATE DATABASE IF NOT EXISTS bbb";
	$result = db_query($sql);

	db_new_connection("bbb");
	
	$sql = 'CREATE TABLE IF NOT EXISTS aaa (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, submission_date date, asdfaf varchar(255))';
	$result = db_query($sql);
	
	$sql = "INSERT INTO aaa (submission_date, asdfaf) VALUES (CURDATE(), {$asdfaf_quoted})";								
	$result = db_query($sql);
	if($result === false) {
		$response["success"] = false;
		$response["text"] = db_error();
		die(json_encode($response));
	}
	
	$response["success"] = true;
	$response["text"] = "Successfully Submitted Form";
	die(json_encode($response));	
	
	function missing_data() {
		$response["success"] = false;
		$response["text"] = "Missing Data";
		die(json_encode($response));
	}
	
?>	
	
<?php

	require "databaseQuery.php";

	//Non Required Form Data
	$sdfasdfa = empty($_POST["sdfasdfa"]) ? NULL : $_POST["sdfasdfa"];
	
	//Required Form Data
	
	//Array Concat
	if(is_array($sdfasdfa)){
			$sdfasdfa = implode(",", $sdfasdfa);
		}
	//Quoted Data
	$sdfasdfa_quoted = db_quote($sdfasdfa);
	
	//SQL Statements
	$sql = "CREATE DATABASE IF NOT EXISTS adsfdafa";
	$result = db_query($sql);

	db_new_connection("adsfdafa");
	
	$sql = 'CREATE TABLE IF NOT EXISTS asdfd (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, submission_date date, sdfasdfa varchar(255))';
	$result = db_query($sql);
	
	$sql = "INSERT INTO asdfd (submission_date, sdfasdfa) VALUES (CURDATE(), {$sdfasdfa_quoted})";								
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
	
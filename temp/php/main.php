<?php

	require "PHPMailer/PHPMailerAutoload.php";
	require "sendMail.php";
	
	require "databaseQuery.php";

	//Non Required Form Data
	$asdfadfa = empty($_POST["asdfadfa"]) ? NULL : $_POST["asdfadfa"];
	$email = empty($_POST["email"]) ? NULL : $_POST["email"];
	
	//Required Form Data
	
	//Array Concat
	
	//Quoted Data
	$asdfadfa_quoted = db_quote($asdfadfa);
	$email_quoted = db_quote($email);
	
	//SQL Statements
	$sql = "CREATE DATABASE IF NOT EXISTS ";
	$result = db_query($sql);

	db_new_connection("");
	
	$sql = 'CREATE TABLE IF NOT EXISTS adsfafa (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, submission_date date, asdfadfa varchar(255), email varchar(255))';
	$result = db_query($sql);
	
	$sql = "INSERT INTO adsfafa (submission_date, asdfadfa, email) VALUES (CURDATE(), {$asdfadfa_quoted}, {$email_quoted})";								
	$result = db_query($sql);
	if($result === false) {
		$response["success"] = false;
		$response["text"] = db_error();
		die(json_encode($response));
	}
	
	//Create and Send Email
	
	$addressArray = array($email, 'asdfad'); 
	$subject = "adsfafa Confirmation Email";
	$body = '<!DOCTYPE html>
	<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>adsfafa Confirmation Email</title>
	<!---<link rel="stylesheet" type="text/css" href="ZURBemails_files/email.css">--->
	</head>
	<body bgcolor="#FFFFFF" style="margin:0; padding:0;">
		<table align="center" width="900px" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;""> 
			<tr bgcolor="0053A0">
				<td align="center" align="absbottom" style="padding: 25px 10px 0 10px;">
					<img style="width:300px; height:111px; display:block;" src="" />              
				</td>
			</tr>
			<tr bgcolor="0053A0">
				<td align="center" style="padding: 0px 10px 0px 10px;">
					<h2 style="color:#FDB82B; letter-spacing:2px; line-height:0px; font-weight:600; -webkit-text-size-adjust: 100%; font-size: 34px; margin:15px;">Submitted Information Below</h2>
				</td>
			</tr>
			<tr bgcolor="0053A0">
				<td align="center" style="padding-bottom: 10px;">
					<h3 style="color:#FDB82B; letter-spacing:1px; line-height:0px; font-weight:200; -webkit-text-size-adjust: 100%; font-size: 20px;">
				
				
					</h3>
				</td>
			</tr>
			<tr>
				<td bgcolor="#D4D4D4" style="padding: 20px 20px 20px 20px; margin:inherit; line-height: 10px; letter-spacing: 1px; border-bottom: 1px #666666 dotted;">
					<table style="font-size: 18px; font-weight: 300;">	
						<tr>
							<td style="text-align: right;  padding: 10px;">asdfdaf: </td>
							<td>$asdfadfa</td>
						</tr>	
						<tr>
							<td style="text-align: right;  padding: 10px;">email: </td>
							<td>$email</td>
						</tr>';			
		 $body .= "</table>
				</td>
			</tr>
		</table>
	</body>
	</html>";
		
	$altBody = "";
	
	$success = sendEmail($addressArray, $subject, $body, $altBody);
	
	if($success === true){
		$response["success"] = true;
		$response["text"] = "Successfully Submitted Form";
		echo json_encode($response);
	} else {
		$response["success"] = false;
		$response["text"] = "Form Submitted. Error Sending Email.";
		echo json_encode($response);
	}
	function missing_data() {
		$response["success"] = false;
		$response["text"] = "Missing Data";
		die(json_encode($response));
	}
	
?>	
	
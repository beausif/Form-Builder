<?php

function sendEmail($addressArray, $subject, $body, $altBody, $theFile = Null, $fileName = Null){
	$mail = new PHPMailer;
	
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host =   // REQUIRED: YOU MUST ENTER HOST INFORMATION HERE
	
	$mail->From = 'bkInfo@balkamp.com';
	$mail->FromName = "BK Info";
	
	foreach($addressArray as $address){
		$mail->addAddress($address);     // Add a recipient
	}
	
	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	
	for($i=0;$i<count($theFile);$i++){
		$mail->addAttachment($theFile[$i], $fileName[$i]);
	}

	$mail->isHTML(true);                                  // Set email format to HTML
	
	$mail->Subject = $subject;
	
	$mail->Body    = $body;
		
	$mail->AltBody = $altBody;
	
	if(!$mail->send()) {
		$response['success'] = false;
		$response['error'] = $mail->ErrorInfo;
		return $response;
	} else {
		$response['success'] = true;
		return $response;
	}
}

function sendErrorMail($error){
	sendMail('equilter@msgindy.com', 'SQL Connection Error', $error, $error);
}

?>
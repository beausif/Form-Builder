<?php

function sendEmail($addressArray, $subject, $body, $altBody, $theFile = Null, $fileName = Null){
	$mail = new PHPMailer;
	
	$mail->isSMTP();                                      // Set mailer to use SMTP
	//$mail->Host = 'hqxchg1.balkamp.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true; // authentication enabled
	$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 465; // or 587
	$mail->IsHTML(true);                            
	$mail->Username = 'beausif3@gmail.com';                            
	$mail->Password = 'rowdy317';
	
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
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		return false;
	} else {
		return true;
	}
}

function sendErrorMail($error){
	sendMail('equilter@msgindy.com', 'SQL Connection Error', $error, $error);
}

?>
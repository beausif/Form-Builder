<?php

require 'formClasses.php';

$form_data = empty($_POST['form_data']) ? NULL : json_decode($_POST['form_data']);
$form_name = empty($_POST['form_name']) ? NULL : $_POST['form_name'];
$db_name = empty($_POST['db_name']) ? NULL : $_POST['db_name'];
$note_email = empty($_POST['notification_email']) ? NULL : $_POST['notification_email'];
$conf_email = empty($_POST['confirmation_email']) ? NULL : $_POST['confirmation_email'];

if($form_data === NULL){
	$json['success'] = false;
	$json['error'] = "No Form Data Submitted";
	die(json_encode($json));
}

if($form_name === NULL){
	$json['success'] = false;
	$json['error'] = "No Form Name Submitted";
	die(json_encode($json));
}

$form_data = $form_data->rows;

$form = new Form($form_data, $form_name, $db_name, $note_email, $conf_email);
//echo $form->get_html();
//echo $form->get_js();
//echo $form->get_php();

file_put_contents("../temp/index.html", $form->get_html());
file_put_contents("../temp/js/main.js", $form->get_js());
file_put_contents("../temp/php/main.php", $form->get_php());
file_put_contents("../temp/php/email.php", $form->get_php_email());

?>
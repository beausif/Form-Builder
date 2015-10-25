<?php

require 'formClasses.php';

$form_data = empty($_POST['form_data']) ? NULL : json_decode($_POST['form_data']);

if($form_data === NULL){
	$json['success'] = false;
	$json['error'] = "No Form Data Submitted";
	die(json_encode($json));
}

$form_data = $form_data->rows;

$form = new Form($form_data);
echo $form->get_html();
echo $form->get_js();

file_put_contents("../temp/index.html", $form->get_html());
file_put_contents("../temp/js/main.js", $form->get_js());

?>
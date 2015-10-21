<?php

date_default_timezone_set(America/Indiana/Indianapolis);

require '../../php/PHPMailer/PHPMailerAutoload.php';
require '../../php/sendMail.php';
require '../../php/databaseQuery.php';

$html = urldecode(is_empty($_POST['html']));
$css = urldecode(is_empty($_POST['css']));
$jscript = urldecode(is_empty($_POST['jscript']));
$php = urldecode(is_empty($_POST['php']));
$form_name = is_empty($_POST['formName']);

$base_dir = "../testDir/" . $form_name;
$html_dir = "../testDir/" . $form_name . "/html";
$css_dir = "../testDir/" . $form_name . "/formAssets/" . $form_name . "/css";
$js_dir = "../testDir/" . $form_name . "/formAssets/" . $form_name . "/js";
$php_dir = "../testDir/" . $form_name . "/formAssets/" . $form_name . "/php";


if(!file_exists($base_dir)){
    if (!mkdir($base_dir, 0777, true)) {
        $json['success'] = false;
        $json['error'] = "Failed to create base folder...";
        die(json_encode($json));
    }
}

if(!file_exists($html_dir)){
    if (!mkdir($html_dir, 0777, true)) {
        $json['success'] = false;
        $json['error'] = "Failed to create html folder...";
        die(json_encode($json));
    }
}

if(!file_exists($css_dir)){
    if (!mkdir($css_dir, 0777, true)) {
        $json['success'] = false;
        $json['error'] = "Failed to create css folder...";
        die(json_encode($json));
    }
}

if(!file_exists($js_dir)){
    if (!mkdir($js_dir, 0777, true)) {
        $json['success'] = false;
        $json['error'] = "Failed to create js folder...";
        die(json_encode($json));
    }
}

if(!file_exists($php_dir)){
    if (!mkdir($php_dir, 0777, true)) {
        $json['success'] = false;
        $json['error'] = "Failed to create php folder...";
        die(json_encode($json));
    }
}

$html_file = $html_dir . "/index.html";
$css_file = $css_dir . "/main.css";
$js_file = $js_dir . "/main.js";
$php_file = $php_dir . "/main.php";
$zip_file = "../testDir/" . $form_name . ".zip";

if(file_put_contents($html_file,$html)==false){
    $json['success'] = false;
    $json['error'] = "Cannot create file (".basename($html_file).")";
    die(json_encode($json));
}

if(file_put_contents($css_file,$css)==false){
    $json['success'] = false;
    $json['error'] = "Cannot create file (".basename($css_file).")";
    die(json_encode($json));
}

if(file_put_contents($js_file,$jscript)==false){
    $json['success'] = false;
    $json['error'] = "Cannot create file (".basename($js_file).")";
    die(json_encode($json));
}

if(file_put_contents($php_file,$php)==false){
    $json['success'] = false;
    $json['error'] = "Cannot create file (".basename($php_file).")";
    die(json_encode($json));
}

if(zip($base_dir, $zip_file)){
    $json['success'] = true;
    $json['html'] = "http://www.balkamp.com/formAssets/formBuilder/" . substr($zip_file, 3);
    die(json_encode($json));
} else {
    $json['success'] = false;
    $json['error'] = "Unable to Zip Files";
    die(json_encode($json));
}

/**
 * Zips a folder provided to the destination zip file
 * @param  string $source      path to folder you want to zip
 * @param  string $destination destination zip file (will be created if it doesn't already exist)
 * @return boolean              true if successful
 */
function zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if(!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', DIRECTORY_SEPARATOR, realpath($source));
    $source = str_replace('/', DIRECTORY_SEPARATOR, $source);

    if(is_dir($source) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
            $file = str_replace('/', DIRECTORY_SEPARATOR, $file);

            if ($file == '.' || $file == '..' || empty($file) || $file==DIRECTORY_SEPARATOR) continue;
            // Ignore "." and ".." folders
            if ( in_array(substr($file, strrpos($file, DIRECTORY_SEPARATOR)+1), array('.', '..')) )
                continue;

            $file = realpath($file);
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
            $file = str_replace('/', DIRECTORY_SEPARATOR, $file);

            if (is_dir($file) === true) {
                $d = str_replace($source . DIRECTORY_SEPARATOR, '', $file );
                if(empty($d)) continue;
                $zip->addEmptyDir($d);
            } elseif (is_file($file) === true) {
                $zip->addFromString(str_replace($source . DIRECTORY_SEPARATOR, '', $file), file_get_contents($file));
            } else {
                // do nothing
            }
        }
    } elseif (is_file($source) === true) {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}




function is_empty($value){
    if(empty($value) || $value == '' || $value == NULL || $value == 'null'){
        $response['success'] = false;
        $response['error'] = "Failed to submit data. Please contact equilter@msgindy.com.";
        die(json_encode($response));
    } else {
        return $value;
    }
}




<?php
function db_connect() {
	static $connection;
	
    $file = '../../../../datab.ini';

    if (!$settings = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');
	
	if(!isset($connection)) {
		$connection = mysqli_connect($settings['database']['host'],$settings['database']['username'],$settings['database']['password']); //REQUIRED YOU MUST INSERT USERNAME/PASSWORD HERE | USING A CONFIGURATION FILE IS RECOMMENDED
	}

    // If connection was not successful, handle the error
    if($connection === false) {
        // Handle error - notify administrator, log to a file, show an error screen, etc.
		//sendErrorMail($error);
        return mysqli_connect_error(); 
    }
	
    return $connection;
}

function db_new_connection($db){
	$connection = db_connect();
	mysqli_select_db($connection, $db);
}

function db_query($query, $returnAI = false) {
    // Connect to the database
	$connection = db_connect();	
	
    // Query the database
    $result = mysqli_query($connection,$query);
	$lastAI = $connection->insert_id;
	
	if($returnAI){
		return array($result, $lastAI);
	} else {
    	return $result;
	}
}

function db_select($query) {
    $rows = array();
    $result = db_query($query);
    // If query failed, return `false`
    if($result === false) {
        return false;
    }

    // If query was successful, retrieve all the rows into an array
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function db_error() {
	$connection = db_connect();	
	
    return mysqli_error($connection);
}

function db_quote($value) {
	$connection = db_connect();	
	
    return "'" . mysqli_real_escape_string($connection,$value) . "'";
}


?>
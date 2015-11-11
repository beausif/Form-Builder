<?php

class pdo_mysql {
	private $host;
	private $user;
	private $pass;
	private $dbname;
	private $db;
	
	private $ini = '../../../../datab.ini';
	
	
	public function __construct(){
		if (!$settings = parse_ini_file($this->ini, TRUE)) throw new exception('Unable to open ' . $this->ini . '.');	
		
		$this->host = $settings['database']['host'];
		$this->user = $settings['database']['username'];
		$this->pass = $settings['database']['password']; 
		
		$this->set_connection();
	}
	
	private function set_connection(){
		if(!isset($connection)) {
			try {
				$this->db = new PDO("mysql:host={$this->host};charset=utf8", $this->user, $this->pass, array(
					PDO::ATTR_PERSISTENT => true
				));
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			} catch(PDOException $ex) {
				echo $ex->getMessage();
			}
		}
	}

	public function db_new_connection($db){
		$this->dbname = $db;
		$this->db->exec("use {$this->dbname}");
	}

	public function db_query($query, $params = NULL, $returnAI = false) {
		$stmt = $this->db->prepare($query);
		$stmt->execute($params);
		$affected_rows = $stmt->rowCount();
		
		if($returnAI){
			return array($affected_rows, $lastAI);
		} else {
			return $affected_rows;
		}
	}

	public function db_select($query, $params = NULL) {
		$stmt = $this->db->prepare($query);
		$stmt = $this->db->execute($params);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return empty($rows) ? false : $rows;
	}
	
	public function db_close(){
		$this->db = null;
	}
}
?>
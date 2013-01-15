<?php
#Author: Sunnefa Lind
#Project: Emerald Samurai
#Date: 04.04.2011

/* This is classes/database.php */



class Database {
	protected $user = '****';	
	protected $pass = '****';
	protected $data = 'emeralds_emerald';
	protected $host = 'localhost';
	private $connection;
	
	function __construct() {
		$this->connection = mysql_connect($this->host, $this->user, $this->pass) or die(mysql_error());

		mysql_select_db($this->data) or die(mysql_error());
	}
	
	public function sanitize($string) {
		if(get_magic_quotes_gpc()) {
			$string = stripslashes($string);	
		}
		$string = mysql_real_escape_string($string);
		return $string;
	}
	
	public function get_data($sql, $single = false, $para = false, $class = 'post') {
		$query = mysql_query($sql);
		if(!$query) die(mysql_error());
		
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			if($para == true && isset($row['content'])) {
				$row['content'] = html_entity_decode($row['content']);
				$row['content'] = str_replace('<p>', '<p class="post">', $row['content']);
			}
			$data[] = $row;	
		}
		if($single == true) $data = flatten_array($data);
		return $data;
	}
	
	public function add_data($sql) {
		$query = mysql_query($sql);
		
		if(!$query) return false;
		
		return true;
			
	}
	
	public function error() {
		return mysql_error($this->connection);	
	}
	
	public function insert_id() {
		return mysql_insert_id($this->connection);	
	}
	
	public function update_data($sql) {
		$query = mysql_query($sql);
		if(!$query) return false;
		
		else {
			return true;	
		}
	}
	
}
?>
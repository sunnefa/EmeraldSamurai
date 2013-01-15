<?php
#Author: Sunnefa Lind
#Project: Emerald Samurai
#Date: 04.04.2011

/* This is functions.php */

function url_parser() {
	//if there is no trailing slash in the url we add it
	if (substr($_SERVER["REQUEST_URI"], -1, 1) != "/") $_SERVER["REQUEST_URI"] .= "/";
	
	//separate the uri into an array by slashes
	$queries = explode("/", $_SERVER["REQUEST_URI"]);
	//remove the first element because it's empty
	array_shift($queries);
	//remove the last element because it's empty
	array_pop($queries);
	
	//to make sure that the indexes are always right, we recalculate them
	$queries = array_values($queries);
	
	return $queries;
}

function nl2p($str, $class) {
	$str = "\t<p class=\"$class\">" . preg_replace("(\n|\r)", "</p>$0\t<p class=\"$class\">", $str) . "</p>";
	$str = str_replace("\t<p class=\"$class\"></p>", "", $str);
	return $str;	
}

function flatten_array($array) {
	$single = array();
	foreach($array as $one) {
		foreach($one as $key => $value) {
			$single[$key] = $value;	
		}
	}
	return $single;
}


function emerald_super_salty($string) {
	return md5(sha1($string));	
}

function check_login() {
	if(isset($_SESSION['logged_in'])) return true;
	else return false;	
}

function login($input_pass, $expected_pass) {
	if(emerald_super_salty($input_pass) == emerald_super_salty($expected_pass)) {
		$_SESSION['logged_in'] = true;
		return true;	
	}
	else return false;
}
?>
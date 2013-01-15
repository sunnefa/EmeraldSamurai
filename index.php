<?php
#Author: Sunnefa Lind
#Project: Emerald Samurai
#Date: 04.04.2011

/* This is index.php. All requests are funneled through this page */

include('initialize.php');

session_start();

$queries = url_parser();

ob_start();

$database = new Database();
$theme = new Template();

if(isset($queries[0])) {
	switch($queries[0]) {
		default:
			$show = $queries[0];
			include 'main.php';
			break;
		case 'cms':
			include 'cms.php';	
	}
}
else {
	$show = 'posts';
	include 'main.php';
}
$theme->html_master();
$theme->replace_tokens(array('CONTENT' => ob_get_clean(), 'URL' => URL, 'MENU' => $theme->load_menu()), true);
echo $theme->display(true);
?>
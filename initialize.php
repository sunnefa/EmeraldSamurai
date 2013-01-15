<?php
#Author: Sunnefa Lind
#Project: Emerald Samurai
#Date: 04.04.2011

/* This is initialize.php */

$password = '***';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');

define('URL', 'http://www.emeraldsamurai.com/');

include('classes/database.php');

include('functions.php');

include('classes/template.php');

include('theme/theme.php');

?>
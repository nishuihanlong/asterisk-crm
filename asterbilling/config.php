<?php
error_reporting(0);
require_once ('include/common.class.php');
Common::read_ini_file("asterbilling.conf.php",$config);
define("LOG_ENABLED", $config['system']['log_enabled']); // Enable debuggin
define("FILE_LOG", $config['system']['log_file_path']);  // File to debug.
?>
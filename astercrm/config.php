<?php
//error_reporting(0);
require_once ('include/common.class.php');
Common::read_ini_file("astercrm.conf.php",$config);
define("LOG_ENABLED", $config['system']['log_enabled']); // Enable debug
define("FILE_LOG", $config['system']['log_file_path']);  // File to output debug message.
?>
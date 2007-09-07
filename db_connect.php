<?php

// If you have the PEAR PHP package, you can comment the next line.
ini_set('include_path',dirname($_SERVER["SCRIPT_FILENAME"])."/include");

require_once 'DB.php';
require_once 'PEAR.php';
require_once 'config.php';

// Change for your DB parameters
define('SQLC', $config['database']['dbtype']."://".$config['database']['username'].":".$config['database']['password']."@".$config['database']['dbhost']."/".$config['database']['dbname']."");

$GLOBALS['db'] = DB::connect(SQLC);

// need to check if db connected
if (DB::iserror($GLOBALS['db'])){
	die($GLOBALS['db']->getmessage());
}

$GLOBALS['db']->setFetchMode(DB_FETCHMODE_ASSOC);

?>
<?php

// If you have the PEAR PHP package, you can comment the next line.
ini_set('include_path',dirname($_SERVER["SCRIPT_FILENAME"])."/include");

require_once 'DB.php';
require_once 'PEAR.php';
require_once 'config.php';

// Change for your DB parameters
define('SQLC', "$dbtype://$username:$password@$dbhost/$dbname");

$GLOBALS['db'] = DB::connect(SQLC);

$GLOBALS['db']->setFetchMode(DB_FETCHMODE_ASSOC);
?>

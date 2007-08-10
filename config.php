<?php

// database config
$dbtype = 'mysql';
$dbhost = 'localhost';
$dbname = 'asterisk';
$username = 'asteriskuser';
$password = '';

// asterisk config

$asmanager['server'] ='';
$asmanager['port'] = '';
$asmanager['username'] = '';
$asmanager['secret'] = '';

// asterisk context setting

$outcontext = 'from-sipuser';
$incontext = 'from-siptrunk';

#popup only when the length of callerid is longer than $length

$length = 6;
?>
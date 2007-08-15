<?php

// database config
$dbtype = 'mysql';
$dbhost = 'localhost';
$dbname = 'asterisk';
$username = 'asteriskuser';
$password = 'movingon';

// asterisk config

$asmanager['server'] ='210.83.203.100';
$asmanager['port'] = '7998';
$asmanager['username'] = 'solo';
$asmanager['secret'] = '123654';

// asterisk context setting

$config['OUTCONTEXT'] = 'from-sipuser';
$config['INCONTEXT'] = 'from-siptrunk';

// popup only when the length of callerid is longer than $PHONE_NUMBER_LENGTH

$config['PHONE_NUMBER_LENGTH'] = 6;

$config['POP_UP_WHEN_DIAL_OUT'] = true;

$config['POP_UP_WHEN_INCOMING'] = true;
//$config['POP_UP_WHEN_DIAL_OUT'] = true;
// if

//$config['POP_UP_WHEN_DIAL_OUT'] = true;
//define(POP_UP_WHEN_DIAL_OUT, true);  
?>
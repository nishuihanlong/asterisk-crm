<?php

// database config
$dbtype = 'mysql';
$dbhost = 'localhost';
$dbname = 'asterisk';
$username = '';
$password = '';

// asterisk config

$asmanager['server'] ='';
$asmanager['port'] = '';
$asmanager['username'] = '';
$asmanager['secret'] = '';

// asterisk context setting

$config['OUTCONTEXT'] = 'from-sipuser';
$config['INCONTEXT'] = 'from-siptrunk';

// popup only when the length of callerid is longer than $PHONE_NUMBER_LENGTH

$config['PHONE_NUMBER_LENGTH'] = 6;

$config['POP_UP_WHEN_DIAL_OUT'] = true;

$config['POP_UP_WHEN_INCOMING'] = true;

$config['ENABLE_EXTERNAL_CRM'] = true;

$config['EXTERNAL_URL_DEFAULT'] = 'http://www.magiclink.cn';

// method: incoming, dialout
$config['EXTERNAL_URL'] = "http://www.magiclink.cn/index.html?callerid=%callerid&calleeid=%calleeid&method=%method";
?>
<?php
/*******************************************************************************
* config.php
* system configuration file
* Function Desc

* Revision 0.045  2007/10/11 17:55:00  modified by solo
* Desc: add $config['asterisk']['monitorpath']

* Revision 0.045  2007/10/8 17:55:00  modified by solo
* Desc: delete all Chinese comments


* Revision 0.043  2007/10/8 17:55:00  modified by solo
* Desc: add $config['system']['preDialer_context'],$config['system']['preDialer_extension']

* Revision 0.0442  2007/09/7 17:55:00  modified by solo
* Desc: add $config['system']['open_new_window']

* Revision 0.044  2007/09/7 17:55:00  modified by solo
* Desc: change all system configuration parameter to be saved in variable: $config

********************************************************************************/

/** 
* Database connection parameter 
*/ 

/** 
* Database type 
* Only support mysql for now
*/ 
$config['database']['dbtype'] = 'mysql';

$config['database']['dbhost'] = 'localhost';
$config['database']['dbname'] = '';
$config['database']['username'] = '';
$config['database']['password'] = '';

/** 
* Asterisk connection parameter 
*/ 
$config['asterisk']['server'] = '';
$config['asterisk']['port'] = '';			//should be matched in manager.conf
$config['asterisk']['username'] = '';		//should be matched in manager.conf
$config['asterisk']['secret'] = '';			//should be matched in manager.conf

/**
* Recorded file path
*
*/
$config['asterisk']['monitorpath'] = '/var/spool/asterisk/monitor/';
$config['asterisk']['monitorformat'] = 'gsm';	//gsm|wav|wav49 

/** 
* Asterisk context parameter, use which context when dial in or dial out
*/ 

$config['system']['outcontext'] = 'from-sipuser';	//context when dial out, in trixbox this could be from-internal
$config['system']['incontext'] = 'from-siptrunk';	//context when dial in, in trixbox this could be from-trunk

/** 
* Asterisk context parameter, use which context and extenstion 
* when predictive dialer connect the call
*/ 

$config['system']['preDialer_context'] = 'from-siptrunk';
$config['system']['preDialer_extension'] = '1';


/**
* astercrm wouldnot pop-up unless the length of callerid is greater than or equal to
* this number
*/
$config['system']['phone_number_length'] = 6; // number only

/**
* astercrm wouldnot pop-up when dial out unless this parameter is true
*/
$config['system']['pop_up_when_dial_out'] = true;	// true | false

/**
* astercrm wouldnot pop-up when dial in unless this parameter is true
*/
$config['system']['pop_up_when_dial_in'] = true;	// true | false

/**
* which phone ring first when using click to dial
*/
$config['system']['firstring'] = 'caller'; //	callee | caller

/**
* astercrm will use external crm software if this parameter is true
*/
$config['system']['enable_external_crm'] = false;	// true | false

/**
* asterCRM will open a new browser window when need popup
*/
$config['system']['open_new_window'] = true;	// true | false

/**
* when using external crm, put default page here
*/
$config['system']['external_crm_default_url'] = 'http://www.magiclink.cn';

/**
* when using external crm, put pop up page here
* %callerid		callerid				
* %calleeid		calleeid				
* %method		dialout or dialin	
*/
$config['system']['external_crm_url'] = "http://www.magiclink.cn/index.html?callerid=%callerid&calleeid=%calleeid&method=%method";
?>
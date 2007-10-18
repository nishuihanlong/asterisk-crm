<?php
/*******************************************************************************
* config.php
* system configuration file
* Function Desc


* Revision 0.045  2007/10/18 17:55:00  modified by solo
* Desc: add $config['system']['log_enabled']

* Revision 0.045  2007/10/18 17:55:00  modified by solo
* Desc: add $config['system']['log_file_path']

* Revision 0.045  2007/10/17 17:55:00  modified by solo
* Desc: add $config['system']['allow_dropcall']

* Revision 0.045  2007/10/17 17:55:00  modified by solo
* Desc: add $config['system']['allow_same_data']

* Revision 0.045  2007/10/17 17:55:00  modified by solo
* Desc: add $config['system']['trim_zreo']

* Revision 0.045  2007/10/15 17:55:00  modified by solo
* Desc: add $config['system']['maximize_when_pop_up']

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
define("UPLOAD_IMAGE_PATH", "./upload/"); 

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
* log file path
*
*/
$config['system']['log_file_path'] = '/tmp/astercrmDebug.log';

/**
* log enabled
*
*/
$config['system']['log_enabled'] = true;

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
* astercrm wouldnot pop-up unless the length of callerid is greater than 
* this number
*/
$config['system']['phone_number_length'] = 6; // number only

/**
* if astercrm trim the 0 in the phonenumber before search for customer/contact in database
*/
$config['system']['trim_zreo'] = true; // true | false

/**
* if your astercrm work on the same server with asterisk, set to true
* when astercrm start a call, it would drop a .call file to asterisk spool 
*/
$config['system']['allow_dropcall'] = true; // true | false

/**
* if astercrm allow same customer name
*/
$config['system']['allow_same_data'] = true; // true | false


/**
* astercrm wouldnot pop-up when dial out unless this parameter is true
*/
$config['system']['pop_up_when_dial_out'] = true;	// true | false

/**
* astercrm wouldnot pop-up when dial in unless this parameter is true
*/
$config['system']['pop_up_when_dial_in'] = true;	// true | false

/**
* browser will maximize when pop up
*/
$config['system']['maximize_when_pop_up'] = true;	// true | false

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
//**********************

?>
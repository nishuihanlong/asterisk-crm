<?php
/*******************************************************************************
* config.php
* 系统配置信息文件
* system configuration file
* 功能描述
* Function Desc

* Revision 0.044  2007/09/7 17:55:00  modified by solo
* Desc: change all system configuration parameter to be saved in variable: $config
* 描述: 统一了系统参数的格式, 配置信息都保存在变量 $config 中

********************************************************************************/

/** 
* Database connection parameter 
* 数据库连接参数 
*/ 

/** 
* Database type 
* 数据库类型 
* Only support mysql for now
* 目前只支持mysql数据库
*/ 
$config['database']['dbtype'] = 'mysql';

$config['database']['dbhost'] = 'localhost';
$config['database']['dbname'] = 'asterisk';
$config['database']['username'] = '';
$config['database']['password'] = '';

/** 
* Asterisk connection parameter 
* Asterisk连接参数 
*/ 
$config['asterisk']['server'] = '';
$config['asterisk']['port'] = '';			//should be matched in manager.conf
$config['asterisk']['username'] = '';		//should be matched in manager.conf
$config['asterisk']['secret'] = '';		//should be matched in manager.conf

/** 
* Asterisk context parameter, set whick context to use when dial in or dial out
* 设定拨入拨出时使用哪个context 
*/ 

$config['system']['outcontext'] = 'from-sipuser';	//context when dial out, in trixbox this could be from-internal
$config['system']['incontext'] = 'from-siptrunk';	//context when dial in, in trixbox this could be from-trunk

/**
* astercrm wouldnot pop-up unless the callerid is longer than this number
* 只有当callerid大于该参数时, astercrm才会弹屏
*/
$config['system']['phone_number_length'] = 6; // number only

/**
* astercrm wouldnot pop-up when dial out unless this parameter is true
* 当该参数为true时, 拨出电话时系统将弹屏
*/
$config['system']['pop_up_when_dial_out'] = true;	// true | false

/**
* astercrm wouldnot pop-up when dial in unless this parameter is true
* 当该参数为true时, 拨入电话时系统将弹屏
*/
$config['system']['pop_up_when_dial_in'] = true;	// true | false

/**
* which phone ring first when using click to dial
* 当使用页面点击呼叫功能时,是桌面分机先振铃还是被叫号码先振铃
* callee 主叫分机先振铃
* caller 被叫号码先振铃
*/
$config['system']['firstring'] = 'callee'; //	callee | caller

/**
* astercrm will use external crm software if this parameter is true
* 当该参数为true时, 系统将使用外挂CRM系统
*/
$config['system']['enable_external_crm'] = false;	// true | false

/**
* when using external crm, put default page here
* 当使用外部crm系统时, 默认的显示页面
*/
$config['system']['external_crm_default_url'] = 'http://www.magiclink.cn';

/**
* when using external crm, put pop up page here
* 当使用外部crm系统时, 系统弹屏时显示的界面
* %callerid		callerid				主叫号码
* %calleeid		calleeid				被叫号码
* %method		dial out or dial in		拨入还是拨出		dial_in | dial_out
*/
$config['system']['external_crm_url'] = "http://www.magiclink.cn/index.html?callerid=%callerid&calleeid=%calleeid&method=%method";
?>
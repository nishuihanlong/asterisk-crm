<?php
/*******************************************************************************
* preferences.server.php

* 配置管理系统后台文件
* preferences background management script

* Function Desc
	provide preferences management script

* 功能描述
	提供配置管理脚本

* Function Desc
		init				初始化页面元素
		initIni				从配置文件中读取信息填充页面上的input对象
		initLocate			初始化页面上的说明信息
		savePreferences		保存配置文件
		checkDb				检查数据库是否能正确连接
		checkAMI			检查AMI是否能正确连接
		checkSys			检查系统参数是否正确
							目前仅检查了上传目录是否可写

* Revision 0.0456  2007/11/12 15:47:00  last modified by solo
* Desc: page created
********************************************************************************/

require_once ("db_connect.php");
require_once ("preferences.common.php");
require_once ("include/asterisk.class.php");

/**
*  initialize page elements
*
*/

function init(){
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin,$_SESSION['curuser']['country'],$_SESSION['curuser']['language']));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));
	$objResponse->loadXML(initLocate());
	$objResponse->loadXML(initIni());
	return $objResponse;
}

function initIni(){
	global $config;

	$objResponse = new xajaxResponse();

	//database section
	$objResponse->addAssign("iptDbDbtype","value",$config["database"]["dbtype"]);
	$objResponse->addAssign("iptDbDbhost","value",$config["database"]["dbhost"]);
	$objResponse->addAssign("iptDbDbname","value",$config["database"]["dbname"]);
	$objResponse->addAssign("iptDbUsername","value",$config["database"]["username"]);
	$objResponse->addAssign("iptDbPassword","value",$config["database"]["password"]);
	
	//asterisk section
	$objResponse->addAssign("iptAsServer","value",$config["asterisk"]["server"]);
	$objResponse->addAssign("iptAsPort","value",$config["asterisk"]["port"]);
	$objResponse->addAssign("iptAsUsername","value",$config["asterisk"]["username"]);
	$objResponse->addAssign("iptAsSecret","value",$config["asterisk"]["secret"]);
	$objResponse->addAssign("iptAsMonitorpath","value",$config["asterisk"]["monitorpath"]);
	$objResponse->addAssign("iptAsMonitorformat","value",$config["asterisk"]["monitorformat"]);

	//system section
	$objResponse->addAssign("iptSyseventtype","value",$config["system"]["eventtype"]);
	$objResponse->addAssign("iptSysLogEnabled","value",$config["system"]["log_enabled"]);

	//print $config["system"]["log_enabled"];
	//exit;
	$objResponse->addAssign("iptSysLogFilePath","value",$config["system"]["log_file_path"]);
	$objResponse->addAssign("iptSysAsterccPath","value",$config["system"]["astercc_path"]);
	$objResponse->addAssign("iptSysOutcontext","value",$config["system"]["outcontext"]);
	$objResponse->addAssign("iptSysIncontext","value",$config["system"]['incontext']);

	$objResponse->addAssign(
			"iptSysStop_work_verify",
			"value",
			$config["system"]["stop_work_verify"]);

	$objResponse->addAssign(
			"iptSysPredialerExtension",
			"value",
			$config["system"]["predialer_extension"]);

	$objResponse->addAssign(
			"iptSysPhoneNumberLength",
			"value",
			$config["system"]["phone_number_length"]);
	$objResponse->addAssign(
			"iptSysSmartMatchRemove",
			"value",
			$config["system"]["smart_match_remove"]);
	$objResponse->addAssign(
			"iptSysStatusCheckInterval",
			"value",
			$config["system"]["status_check_interval"]);

	$objResponse->addAssign(
			"iptSysTrimPrefix",
			"value",
			$config["system"]["trim_prefix"]);
	$objResponse->addAssign("iptSysAllowDropcall","value",$config["system"]["allow_dropcall"]);
	$objResponse->addAssign("iptSysAllowSameData","value",$config["system"]["allow_same_data"]);

	$objResponse->addAssign("iptSysPortalDisplayType","value",$config["system"]["portal_display_type"]);

	$objResponse->addAssign("iptSysPopUpWhenDialOut","value",$config["system"]["pop_up_when_dial_out"]);

	$objResponse->addAssign("iptSysPopUpWhenDialIn","value",$config["system"]["pop_up_when_dial_in"]);

	$objResponse->addAssign("iptSysBrowserMaximizeWhenPopUp","value",$config["system"]["browser_maximize_when_pop_up"]);

	$objResponse->addAssign("iptSysFirstring","value",$config["system"]["firstring"]);
	$objResponse->addAssign("iptSysEnableExternalCrm","value",$config["system"]["enable_external_crm"]);

	$objResponse->addAssign("iptSysEnableContact","value",$config["system"]["enable_contact"]);
	$objResponse->addAssign("iptSysDetailLevel","value",$config["system"]["detail_level"]);

	$objResponse->addAssign("iptSysOpenNewWindow","value",$config["system"]["open_new_window"]);

	$objResponse->addAssign("iptSysExternalCrmDefaultUrl","value",$config["system"]["external_crm_default_url"]);

	$objResponse->addAssign("iptSysExternalCrmUrl","value",$config["system"]["external_crm_url"]);

	$objResponse->addAssign("iptSysUploadFilePath","value",$config["system"]["upload_file_path"]);

	$objResponse->addAssign("iptGooglemapkey","value",$config["google-map"]["key"]);

	Common::read_ini_file($config['system']['astercc_path'].'/astercc.conf',$asterccConfig);
	$objResponse->addAssign("asterccLicenceto","value",$asterccConfig["licence"]["licenceto"]);
	$objResponse->addAssign("asterccChannels","value",$asterccConfig["licence"]["channel"]);
	$objResponse->addAssign("asterccKey","value",$asterccConfig["licence"]["key"]);

	return $objResponse;
}

function initLocate(){
	global $locate;

	$objResponse = new xajaxResponse();

	//database section
	$objResponse->addAssign("divDbDbtype","innerHTML",$locate->Translate('db_dbtype'));
	$objResponse->addAssign("divDbDbhost","innerHTML",$locate->Translate('db_dbhost'));
	$objResponse->addAssign("divDbDbname","innerHTML",$locate->Translate('db_dbname'));
	$objResponse->addAssign("divDbUsername","innerHTML",$locate->Translate('db_username'));
	$objResponse->addAssign("divDbPassword","innerHTML",$locate->Translate('db_password'));

	//asterisk section
	$objResponse->addAssign("divAsServer","innerHTML",$locate->Translate('as_server'));
	$objResponse->addAssign("divAsPort","innerHTML",$locate->Translate('as_port'));
	$objResponse->addAssign("divAsUsername","innerHTML",$locate->Translate('as_username'));
	$objResponse->addAssign("divAsSecret","innerHTML",$locate->Translate('as_secret'));
	$objResponse->addAssign(
				"divAsMonitorpath",
				"innerHTML",
				$locate->Translate('as_monitorpath'));
	$objResponse->addAssign(
				"divAsMonitorformat",
				"innerHTML",
				$locate->Translate('as_monitorformat'));


	//system section
	$objResponse->addAssign("divSyseventtype","innerHTML",$locate->Translate('Sys_eventtype'));
	$objResponse->addAssign("divSysLogEnabled","innerHTML",$locate->Translate('sys_log_enabled'));
	$objResponse->addAssign("divSysLogFilePath","innerHTML",$locate->Translate('sys_log_file_path'));
	$objResponse->addAssign("divSysAsterccPath","innerHTML",$locate->Translate('astercc_path'));
	$objResponse->addAssign("divSysOutcontext","innerHTML",$locate->Translate('sys_outcontext'));
	$objResponse->addAssign("divSysIncontext","innerHTML",$locate->Translate('sys_incontext'));

	$objResponse->addAssign(
			"divSysStop_work_verify",
			"innerHTML",
			$locate->Translate('Sys_Stop_work_verify'));

	$objResponse->addAssign(
			"divSysPredialerExtension",
			"innerHTML",
			$locate->Translate('sys_predialer_extension'));

	$objResponse->addAssign(
			"divSysPhoneNumberLength",
			"innerHTML",
			$locate->Translate('sys_phone_number_length'));

	$objResponse->addAssign(
			"divSysSmartMatchRemove",
			"innerHTML",
			$locate->Translate('smart_match_remove'));

	$objResponse->addAssign(
			"divSysStatusCheckInterval",
			"innerHTML",
			$locate->Translate('status_check_interval'));

	$objResponse->addAssign(
			"divSysTrimPrefix",
			"innerHTML",
			$locate->Translate('sys_trim_prefix'));
	$objResponse->addAssign("divSysAllowDropcall","innerHTML",$locate->Translate('sys_allow_dropcall'));
	$objResponse->addAssign("divSysAllowSameData","innerHTML",$locate->Translate('sys_allow_same_data'));

	$objResponse->addAssign("divSysPortalDisplayType","innerHTML",$locate->Translate('sys_portal_display_type'));

	$objResponse->addAssign("divSysPopUpWhenDialOut","innerHTML",$locate->Translate('sys_pop_up_when_dial_out'));

	$objResponse->addAssign("divSysPopUpWhenDialIn","innerHTML",$locate->Translate('sys_pop_up_when_dial_in'));

	$objResponse->addAssign("divSysBrowserMaximizeWhenPopUp","innerHTML",$locate->Translate('sys_browser_maximize_when_pop_up'));

	$objResponse->addAssign("divSysFirstring","innerHTML",$locate->Translate('sys_firstring'));
	$objResponse->addAssign("divSysEnableExternalCrm","innerHTML",$locate->Translate('sys_enable_external_crm'));

	$objResponse->addAssign("divSysEnableContact","innerHTML",$locate->Translate('sys_enable_contact'));

	$objResponse->addAssign("divSysDetailLevel","innerHTML",$locate->Translate('read group database or system database'));

	$objResponse->addAssign("divSysOpenNewWindow","innerHTML",$locate->Translate('sys_open_new_window'));

	$objResponse->addAssign("divSysExternalCrmDefaultUrl","innerHTML",$locate->Translate('sys_external_crm_default_url'));

	$objResponse->addAssign("divSysExternalCrmUrl","innerHTML",$locate->Translate('sys_external_crm_url'));

	$objResponse->addAssign("divSysUploadFilePath","innerHTML",$locate->Translate('sys_upload_file_path'));
	
	return $objResponse;
}

function savePreferences($aFormValues){
	global $config,$locate;
	//print_r($aFormValues);
	//exit;
	$objResponse = new xajaxResponse();
	//Common::read_ini_file("astercrm.conf.php",$myPreferences);
	$myPreferences = $config;
	//database section
	$myPreferences['database']['dbtype'] = $aFormValues['iptDbDbtype'];
	$myPreferences['database']['dbhost'] = $aFormValues['iptDbDbhost'];
	//print $aFormValues['iptDbDbhost'];
	$myPreferences['database']['dbname'] = $aFormValues['iptDbDbname'];
	$myPreferences['database']['username'] = $aFormValues['iptDbUsername'];
	$myPreferences['database']['password'] = $aFormValues['iptDbPassword'];

	//asterisk section
	$myPreferences['asterisk']['server'] = $aFormValues['iptAsServer'];
	$myPreferences['asterisk']['port'] = $aFormValues['iptAsPort'];
	$myPreferences['asterisk']['username'] = $aFormValues['iptAsUsername'];
	$myPreferences['asterisk']['secret'] = $aFormValues['iptAsSecret'];
	$myPreferences['asterisk']['monitorpath'] = $aFormValues['iptAsMonitorpath'];
	$myPreferences['asterisk']['monitorformat'] = $aFormValues['iptAsMonitorformat'];
	//system section
	$myPreferences['system']['log_enabled'] = $aFormValues['iptSysLogEnabled'];
	$myPreferences['system']['log_file_path'] = $aFormValues['iptSysLogFilePath'];
	$myPreferences['system']['astercc_conf_path'] = $aFormValues['iptSysAsterccConfPath'];
	$myPreferences['system']['outcontext'] = $aFormValues['iptSysOutcontext'];
	$myPreferences['system']['incontext'] = $aFormValues['iptSysIncontext'];
	$myPreferences['system']['eventtype'] = $aFormValues['iptSyseventtype'];
	$myPreferences['system']['stop_work_verify'] = $aFormValues['iptSysStop_work_verify'];



	$myPreferences['system']['phone_number_length'] = $aFormValues['iptSysPhoneNumberLength'];
	$myPreferences['system']['smart_match_remove'] = $aFormValues['iptSysSmartMatchRemove'];
	$myPreferences['system']['status_check_interval'] = $aFormValues['iptSysStatusCheckInterval'];
	$myPreferences['system']['trim_prefix'] = $aFormValues['iptSysTrimPrefix'];
	$myPreferences['system']['allow_dropcall'] = $aFormValues['iptSysAllowDropcall'];
	$myPreferences['system']['allow_same_data'] = $aFormValues['iptSysAllowSameData'];
	$myPreferences['system']['portal_display_type'] = $aFormValues['iptSysPortalDisplayType'];
	$myPreferences['system']['pop_up_when_dial_out'] = $aFormValues['iptSysPopUpWhenDialOut'];
	$myPreferences['system']['pop_up_when_dial_in'] = $aFormValues['iptSysPopUpWhenDialIn'];
	$myPreferences['system']['browser_maximize_when_pop_up'] = $aFormValues['iptSysBrowserMaximizeWhenPopUp'];
	$myPreferences['system']['firstring'] = $aFormValues['iptSysFirstring'];
	$myPreferences['system']['enable_external_crm'] = $aFormValues['iptSysEnableExternalCrm'];
	$myPreferences['system']['enable_contact'] = $aFormValues['iptSysEnableContact'];
	$myPreferences['system']['detail_level'] = $aFormValues['iptSysDetailLevel'];
	$myPreferences['system']['open_new_window'] = $aFormValues['iptSysOpenNewWindow'];
	$myPreferences['system']['external_crm_default_url'] = $aFormValues['iptSysExternalCrmDefaultUrl'];
	$myPreferences['system']['external_crm_url'] = $aFormValues['iptSysExternalCrmUrl'];
	$myPreferences['system']['upload_file_path'] = $aFormValues['iptSysUploadFilePath'];
	$myPreferences['google-map']['key'] = $aFormValues['iptGooglemapkey'];
	if (Common::write_ini_file("astercrm.conf.php",$myPreferences) >0)
		$objResponse->addAlert($locate->Translate('save_success'));
	else
		$objResponse->addAlert($locate->Translate('save_failed'));
	return $objResponse;
}

//检查数据库连接
function checkDb($aFormValues){
	global $locate;
	$objResponse = new xajaxResponse();
	$sqlc = $aFormValues['iptDbDbtype']."://".$aFormValues['iptDbUsername'].":".$aFormValues['iptDbPassword']."@".$aFormValues['iptDbDbhost']."/".$aFormValues['iptDbDbname']."";

	// set a global variable to save database connection
	$dbtest = DB::connect($sqlc);

	// need to check if db connected
	if (DB::iserror($dbtest)){
		$objResponse->addAssign("divDbMsg","innerHTML","<span class='failed'>".$locate->Translate('db_connect_failed')."</span>");
	}else{
		$objResponse->addAssign("divDbMsg","innerHTML","<span class='passed'>".$locate->Translate('db_connect_success')."</span>");
	}
	return $objResponse;

}

//检查AMI连接
function checkAMI($aFormValues){
	global $locate;
	$objResponse = new xajaxResponse();
	$myAsterisk = new Asterisk();
	
	$myConfig['server'] = $aFormValues["iptAsServer"];
	$myConfig['port'] = $aFormValues["iptAsPort"];
	$myConfig['username'] = $aFormValues["iptAsUsername"];
	$myConfig['secret'] =  $aFormValues["iptAsSecret"];

	$myAsterisk->config['asmanager'] = $myConfig;

	$res = $myAsterisk->connect();
	if ($res){
		$objResponse->addAssign("divAsMsg","innerHTML","<span class='passed'>".$locate->Translate('AMI_connect_success')."</span");
	}else{
		$objResponse->addAssign("divAsMsg","innerHTML","<span class='failed'>".$locate->Translate('AMI_connect_failed')."</span>");
	}

	return $objResponse;
}


function checkSys($aFormValues){
	global $locate;
	$objResponse = new xajaxResponse();

	//check directory permittion
	if (is_writable($aFormValues['iptSysUploadFilePath'])){
		$objResponse->addAssign("divSysMsg","innerHTML","<span class='passed'>".$locate->Translate('Upload Folder Writable')."</span");
	}else{
		$objResponse->addAssign("divSysMsg","innerHTML","<span class='failed'>".$locate->Translate('permission_error')."</span");
	}
		
	return $objResponse;
}

function saveLicence($aFormValues){
	global $config,$locate;
	$objResponse = new xajaxResponse();

	if(!file_exists($config['system']['astercc_path'].'/astercc.conf')){
		$objResponse->addAlert($locate->Translate('astercc_conf_non').$config['system']['astercc_path']);
		return $objResponse;
	}
	
	if(!file_exists($config['system']['astercc_path'].'/astercc')){
		$objResponse->addAlert($locate->Translate('astercc_non').$config['system']['astercc_path']);
		return $objResponse;
	}

	Common::read_ini_file($config['system']['astercc_path'].'/astercc.conf',$asterccConfig);

	$asterccConfig['licence']['licenceto'] = $aFormValues['asterccLicenceto'];
	$asterccConfig['licence']['channel'] = $aFormValues['asterccChannels'];
	$asterccConfig['licence']['key'] = $aFormValues['asterccKey'];

	if (Common::write_ini_file($config['system']['astercc_path'].'/astercc.conf',$asterccConfig) > 0){
		$rval = exec($config['system']['astercc_path'].'/astercc -t',$asterccMsg);
		$asterccMsg = implode("\n",$asterccMsg);

		if ( stristr($asterccMsg,'Success') === FALSE ) { //check key if vaild
			$objResponse->addAlert($asterccMsg);
		}else{
			$objResponse->addAlert($locate->Translate('update_licence_success'));
		}
	}else{
		$objResponse->addAlert($locate->Translate('update_licence_failed'));
	}
	return $objResponse;
}

$xajax->processRequests();
?>

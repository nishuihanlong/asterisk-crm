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

* Revision 0.0456  2007/11/12 15:47:00  last modified by solo
* Desc: page created
********************************************************************************/

require_once ("db_connect.php");
//require_once ('include/xajaxGrid.inc.php');
require_once ('include/common.class.php');
require_once ("preferences.common.php");

/**
*  initialize page elements
*
*/

function init(){
	$objResponse = new xajaxResponse();
	return $objResponse;
}

function initIni(){
	global $config;
	$objResponse = new xajaxResponse();

	return $objResponse;
}

function initLocate(){
	global $locate;

	$objResponse = new xajaxResponse();

	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

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
	$objResponse->addAssign("divAsSecret","innerHTML",$locate->Translate('us_secret'));
	$objResponse->addAssign("divAsMonitorpath","innerHTML",$locate->Translate('db_account'));
	$objResponse->addAssign("divAsMonitorformat","innerHTML",$locate->Translate('db_account'));

	//system section
	$objResponse->addAssign("divSysLogEnabled","innerHTML",$locate->Translate('sys_log_enabled'));
	$objResponse->addAssign("divSysLogFilePath","innerHTML",$locate->Translate('sys_log_file_path'));
	$objResponse->addAssign("divSysOutcontext","innerHTML",$locate->Translate('sys_outcontext'));
	$objResponse->addAssign("divSysIncontext","innerHTML",$locate->Translate('sys_incontext'));

	$objResponse->addAssign(
			"divSysPredialerContext",
			"innerHTML",
			$locate->Translate('sys_predialer_context'));

	$objResponse->addAssign(
			"divSysPredialerExtension",
			"innerHTML",
			$locate->Translate('sys_predialer_extension'));

	$objResponse->addAssign(
			"divSysPhoneNumberLength",
			"innerHTML",
			$locate->Translate('sys_phone_number_length'));

	$objResponse->addAssign(
			"divSysTrimPrefix",
			"innerHTML",
			$locate->Translate('sys_trim_prefix'));
	$objResponse->addAssign("divSysAllowDropcall","innerHTML",$locate->Translate('sys_allow_dropcall'));
	$objResponse->addAssign("divSysAllowDropcall","innerHTML",$locate->Translate('sys_allow_same_data'));

	$objResponse->addAssign("divSysPortalDisplayType","innerHTML",$locate->Translate('sys_portal_display_type'));

	$objResponse->addAssign("divSysPopUpWhenDialOut","innerHTML",$locate->Translate('sys_pop_up_when_dial_out'));

	$objResponse->addAssign("divSysPopUpWhenDialIn","innerHTML",$locate->Translate('sys_pop_up_when_dial_in'));

	$objResponse->addAssign("divSysMaximizeWhenPopUp","innerHTML",$locate->Translate('sys_maximize_when_pop_up'));

	$objResponse->addAssign("divSysFirstring","innerHTML",$locate->Translate('sys_firstring'));
	$objResponse->addAssign("divSysEnableExternalCrm","innerHTML",$locate->Translate('sys_enable_external_crm'));

	$objResponse->addAssign("divSysEnableContact","innerHTML",$locate->Translate('sys_enable_contact'));

	$objResponse->addAssign("divSysOpenNewWindow","innerHTML",$locate->Translate('sys_open_new_window'));

	$objResponse->addAssign("divSysExternalCrmDefaultUrl","innerHTML",$locate->Translate('sys_external_crm_default_url'));

	$objResponse->addAssign("divSysExternalCrmUrl","innerHTML",$locate->Translate('sys_external_crm_url'));

	$objResponse->addAssign("divSysUploadFilePath","innerHTML",$locate->Translate('sys_upload_file_path'));
	
	return $objResponse;
}

function saveIniFile($aFormValues){

	//database section
	$myIni['database']['dbtype'] = $aFormValues['iptDbDbtype'];
	$myIni['database']['dbhost'] = $aFormValues['iptDbDbhost'];
	$myIni['database']['dbname'] = $aFormValues['iptDbDbname'];
	$myIni['database']['username'] = $aFormValues['iptDbUsername'];
	$myIni['database']['password'] = $aFormValues['iptDbPassword'];

	//asterisk section
	$myIni['asterisk']['server'] = $aFormValues['iptAsServer'];
	$myIni['asterisk']['port'] = $aFormValues['iptAsPort'];
	$myIni['asterisk']['username'] = $aFormValues['iptAsUsername'];
	$myIni['asterisk']['secret'] = $aFormValues['iptAsSecret'];
	$myIni['asterisk']['monitorpath'] = $aFormValues['iptAsMonitorpath'];
	$myIni['asterisk']['monitorformat'] = $aFormValues['iptAsMornitformat'];

	//system section
	$myIni['system']['log_enabled'] = $aFormValues['iptSysLogEnabled'];
	$myIni['system']['log_file_path'] = $aFormValues['iptSysLogFilePath'];
	$myIni['system']['outcontext'] = $aFormValues['iptSysOutcontext'];
	$myIni['system']['incontext'] = $aFormValues['iptSysIncontext'];
	$myIni['system']['predialer_context'] = $aFormValues['iptSysPredialerContext'];
	$myIni['system']['predialer_extension'] = $aFormValues['iptSysPredialerExtension'];
	$myIni['system']['phone_number_length'] = $aFormValues['iptSysPhoneNumberLength'];
	$myIni['system']['trim_prefix'] = $aFormValues['iptSysTrimPrefix'];
	$myIni['system']['allow_dropcall'] = $aFormValues['iptSysAllowDropcall'];
	$myIni['system']['allow_same_data'] = $aFormValues['iptAllowSameData'];
	$myIni['system']['portal_display_type'] = $aFormValues['iptPortalDisplayType'];
	$myIni['system']['pop_up_when_dial_out'] = $aFormValues['iptPopUpWhenDialOut'];
	$myIni['system']['pop_up_when_dial_in'] = $aFormValues['iptPopUpWhenDialIn'];
	$myIni['system']['browser_maximize_when_pop_up'] = $aFormValues['iptBrowserMaximizeWhenPopUp'];
	$myIni['system']['firstring'] = $aFormValues['iptFirstring'];
	$myIni['system']['enable_external_crm'] = $aFormValues['iptEnableExternalCrm'];
	$myIni['system']['enable_contact'] = $aFormValues['iptEnableContact'];
	$myIni['system']['open_new_window'] = $aFormValues['iptOpenNewWindow'];
	$myIni['system']['external_crm_default_url'] = $aFormValues['iptExternalCrmDefaultUrl'];
	$myIni['system']['external_crm_url'] = $aFormValues['iptExternalCrmUrl'];
	$myIni['system']['upload_excel_path'] = $aFormValues['iptUploadExcelPath'];
}

$xajax->processRequests();
?>

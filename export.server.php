<?php
/*******************************************************************************
* export.server.php

* 数据导出
* export datas

* Function Desc


* 功能描述


* Function Desc

	export				提交表单, 导出数据
	init				初始化页面元素

* Revision 0.045  2007/10/22 16:33:00  last modified by solo
* Desc: page created

********************************************************************************/
require_once ("db_connect.php");
require_once ("customer.common.php");
require_once ('include/xajaxGrid.inc.php');
require_once ('include/common.class.php');

/**
*  submit frmDownload
*
*/

function export(){
	$objResponse = new xajaxResponse();

	$objResponse->addScript("xajax.$('frmDownload').submit();");
	$objResponse->addAlert("downloading, please wait");
	return $objResponse;
}

/**
*  initialize page elements
*
*/

function init(){
	global $locate;

	$objResponse = new xajaxResponse();

	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	$objResponse->addAssign("btnCustomer","value",$locate->Translate("customer"));
	$objResponse->addAssign("btnContact","value",$locate->Translate("contact"));
	$objResponse->addAssign("btnNote","value",$locate->Translate("note"));


	return $objResponse;
}

$xajax->processRequests();

?>
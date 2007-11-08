<?php
/*******************************************************************************
* queue.server.php

* 队列管理系统后台文件
* queue management script

* Function Desc

* 功能描述

* Function Desc
		init				初始化页面元素

* Revision 0.0456  2007/11/07 16:10:00  last modified by solo
* Desc: page created

*/
require_once ("queue.common.php");
require_once ("db_connect.php");
require_once ('include/xajaxGrid.inc.php');
require_once ('include/asterevent.class.php');
require_once ('include/asterisk.class.php');
require_once ('include/common.class.php');

function init(){
	global $locate,$config;
	$objResponse = new xajaxResponse();

	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		$objResponse->addAssign("divAMIStatus", "innerHTML", $locate->Translate("AMI_connection_failed"));
	}
	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));
	$objResponse->addAssign("msgChannelsInfo", "value", $locate->Translate("msgChannelsInfo"));

	return $objResponse;
}

$xajax->processRequests();
?>

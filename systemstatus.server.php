<?php
/*******************************************************************************
* systemstatus.server.php

* Function Desc
	show sip status and active channels

* 功能描述
	提供SIP分机状态信息和正在进行的通道

* Function Desc

	showGrid
	init				初始化页面元素
	showStatus			显示sip分机状态信息
	showChannelsInfo	显示激活的通道信息

* Revision 0.045  2007/10/18 15:38:00  last modified by solo
* Desc: comment added

********************************************************************************/
require_once ("systemstatus.common.php");
require_once ("db_connect.php");
require_once ('include/xajaxGrid.inc.php');
require_once ('include/asterevent.class.php');
require_once ('include/asterisk.class.php');
require_once ('include/common.class.php');

/**
*  initialize page elements
*
*/

function init(){
	global $locate,$config;
	$objResponse = new xajaxResponse();

	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		$objResponse->addAssign("AMIStatudDiv", "innerHTML", $locate->Translate("AMI_connection_failed"));
	}
	$objResponse->addAssign("msgChannelsInfo", "value", $locate->Translate("msgChannelsInfo"));

	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin,$_SESSION['curuser']['country'],$_SESSION['curuser']['language']));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	return $objResponse;
}


function listCommands(){
	global $config;

	$objResponse = new xajaxResponse();
	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		$objResponse->addAssign("AMIStatudDiv", "innerHTML", $locate->Translate("AMI_connection_failed"));
	}else{
		print_r($myAsterisk->ListCommands());
	}

	return $objResponse;
}

/**
*  show extension status
*  @return	objResponse		object		xajax response object
*/

function showStatus(){
	$objResponse = new xajaxResponse();
	$html .= "<br><br><br><br>";
	$html .= asterEvent::checkExtensionStatus(0,'table');
	$objResponse->addAssign("divStatus", "innerHTML", $html);
	return $objResponse;
}


/**
*  initialize page elements
*  @return	objResponse		object		xajax response object
*/

function showChannelsInfo(){
	global $locate;
	$channels = split(chr(13),asterisk::getCommandData('show channels verbose'));
/*
	if ($channels == null){
			$objResponse->addAssign("channels", "innerHTML", "can not connect to AMI, please check config.php");
			return $objResponse;
	}
*/	$channels = split(chr(10),$channels[1]);
	//trim the first two records and the last three records

	//	array_pop($channels); 
	array_pop($channels); 
	$activeCalls = array_pop($channels); 
	$activeChannels = array_pop($channels); 

	array_shift($channels); 
	$title = array_shift($channels); 
	$title = split("_",implode("_",array_filter(split(" ",$title))));
	$myInfo[] = $title;

	foreach ($channels as $channel ){
		if (strstr($channel," Dial")) {
			$myItem = split("_",implode("_",array_filter(split(" ",$channel))));
			$myInfo[] = $myItem;
		}
	}
	
	$myChannels = common::generateTabelHtml($myInfo);

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("divActiveCalls", "innerHTML", $activeCalls);

	$objResponse->addAssign("channels", "innerHTML", nl2br(trim($myChannels)));
	return $objResponse;
}

function chanspy($exten,$spyexten){
	//print $spyexten;
	//exit;
	global $config,$locate;
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		return;
	}
	$myAsterisk->chanSpy($exten,"SIP/".$spyexten);
	//$objResponse->addAlert($spyexten);
	return $objResponse;

}

$xajax->processRequests();
?>

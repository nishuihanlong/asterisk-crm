<?php
/*******************************************************************************
* queuestatus.server.php

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
require_once ("queuestatus.common.php");
require_once ("db_connect.php");
require_once ('include/xajaxGrid.inc.php');
require_once ('include/asterevent.class.php');
require_once ('include/astercrm.class.php');
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
	
	////set time intervals of check system status
	$check_interval = 2000;
	if ( is_numeric($config['system']['status_check_interval']) ) {
		$check_interval = $config['system']['status_check_interval'] * 1000;
		$objResponse->addAssign("check_interval","value",$check_interval);
	}
	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin,$_SESSION['curuser']['country'],$_SESSION['curuser']['language']));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	return $objResponse;
}


/**
*  show extension status
*  @return	objResponse		object		xajax response object
*/

function showStatus(){
	global $db;
	$objResponse = new xajaxResponse();
	if ($_SESSION['curuser']['usertype'] == 'admin') {
		// display all queue
		$query = "SELECT * FROM queue_name";
	}else{
		// display queue in campaign for group
		$query = "SELECT campaign.groupid, queue_name.* FROM queue_name LEFT JOIN campaign ON queue_name.queuename = campaign.queuename WHERE campaign.groupid = ".$_SESSION['curuser']['groupid'];
	}
	$res = $db->query($query);
	$html = '<table class="groups_channel" cellspacing="0" cellpadding="0" border="0" width="95%"><tbody>';
	while ($res->fetchInto($row)) {
		//"<li></li>"
		$html .= '<tr><th colspan="2">'.$row['data'].'</th></tr>';
		$html .= '<tr><td width="70%"><b>'.'Members'.'</b></td><td><b>Waiting callers</b></td></tr>';
		$query = "SELECT * FROM queue_agent WHERE queuename = '".$row['queuename']."' ORDER BY agent ASC";
		$res_agent = $db->query($query);
		$html .='<tr><td valign="top">';
		$html .='<table class="groups_channel" cellspacing="0" cellpadding="0" border="0" width="95%"><tbody>';
		while ($res_agent->fetchInto($row_agent)) {
			$logoffBtn = '';
			$able = 'disabled';	
			$html .='<tr><td>';
			if(strstr(strtoupper($row_agent['agent']),'AGENT')){
				$agent = substr($row_agent['agent'],6);
				$logoffBtn .= '&nbsp;&nbsp;<input type="button" value="Logoff" onclick="xajax_agentLogoff('.$agent.')"';
				if($row_agent['status'] == 'Unavailable' || $row_agent['status'] == 'Invalid'){
					$logoffBtn .= 'disabled';
				}
				$logoffBtn .= '>';
				if($row_agent['status'] == 'Busy'){
					$query = "SELECT * FROM curcdr WHERE dstchan = '".strtoupper($row_agent['agent'])."'";
					if($agent_cdr = $db->getRow($query)){
						$srcchan = $agent_cdr['srcchan'];
					}
					$able = '';
					$query = "SELECT * FROM astercrm_account WHERE agent = '$agent'";
					if($agent_exten = $db->getRow($query)){
						$exten = $agent_exten['extension'];
					}
				}
			}else{
				if($row_agent['status'] == 'In use'){
					$dstchan = explode('@',$row_agent['agent']);
					$dstchan = $dstchan['0'];
					$exten = explode('/',$dstchan);
					$exten = $exten['1'];
					$query = "SELECT * FROM curcdr WHERE dstchan LIKE  '".$dstchan."@%'";
					if($agent_cdr = $db->getRow($query)){
						$srcchan = $agent_cdr['srcchan'];
					}
					$able = '';
				}
			}
			
			$html .= '<input type="button" value="Spy" onclick="xajax_chanspy(\''.$_SESSION['curuser']['extension'].'\',\''.$exten.'\')" '.$able.'>';
			$html .= '&nbsp;&nbsp;<input type="button" value="Whisper" onclick="xajax_chanspy(\''.$_SESSION['curuser']['extension'].'\',\''.$exten.'\',\'w\')" '.$able.'>';
			$html .= '&nbsp;&nbsp;<input type="button" value="Hangup" onclick="xajax_hangup(\''.$srcchan.'\')" '.$able.'>';
			$html .= $logoffBtn;
			if($row_agent['status'] == 'In use' || $row_agent['status'] == 'Not in use' || $row_agent['status'] == 'Busy'){
				$html .= '&nbsp;&nbsp;'.$row_agent['data'].'&nbsp;&nbsp;';
			}else{
				$html .= '&nbsp;&nbsp;<span style="color:#999999;">'.$row_agent['data'].'</span>&nbsp;&nbsp;';
			}
			$html .= '</td></tr>';
		}//<button>Spy</button><button>Whisper</button>
		$html .='</tbody></table></td><td valign="top">';
		$query = "SELECT * FROM queue_caller WHERE queuename = '".$row['queuename']."' ";
		$res_caller = $db->query($query);
		$html .='<table class="groups_channel" cellspacing="0" cellpadding="0" border="0" width="90%"><tbody>';
		while ($res_caller->fetchInto($row_caller)) {
			$html .= "<tr><td>".$row_caller['data']."</td></tr>";
		}
		$html .="</tbody></table></td></tr>";
		
	}
	$html .= '</tbody></table>';//echo $html;exit;
	$objResponse->addAssign("channels","innerHTML",$html);
	return $objResponse;
}


function chanspy($exten,$spyexten,$pam = ''){
	global $config,$locate;
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		return;
	}
	$myAsterisk->chanSpy($exten,"SIP/".$spyexten,$pam);
	return $objResponse;

}

function hangup($channel){
	global $config,$locate;
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();
	if (trim($channel) == '')
		return $objResponse;
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		$objResponse->addALert("action Huangup failed");
		return $objResponse;
	}
	$myAsterisk->Hangup($channel);
	return $objResponse;
}


function agentLogoff($agent){
	global $locate,$config;

	$myAsterisk = new Asterisk();	
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		return;
	}
	$res = $myAsterisk->agentLogoff($agent);
	$objResponse = new xajaxResponse();
	return $objResponse;
}


$xajax->processRequests();
?>

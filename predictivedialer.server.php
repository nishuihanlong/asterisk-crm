<?php
/*******************************************************************************
* predictivedialer.server.php

* 账户管理系统后台文件
* predictivedialer management script

* Function Desc
	predictivedialer management script

* 功能描述
	提供帐户管理脚本

* Function Desc
		init				初始化页面元素
		showChannelsInfo	显示asterisk channels
		showPredictiveDialer
		predictiveDialer

* Revision 0.0461  2008/2/1 20:37:00  last modified by solo
* Desc: fix predictive dialer bug

* Revision 0.0455  2007/10/24 20:37:00  last modified by solo
* Desc: add another dial method: sendCall()

* Revision 0.045  2007/10/18 20:10:00  last modified by solo
* Desc: comment added

*/
require_once ("predictivedialer.common.php");
require_once ("db_connect.php");
require_once ('include/xajaxGrid.inc.php');
require_once ('include/asterisk.class.php');
require_once ('include/astercrm.class.php');
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


function showChannelsInfo(){
	global $locate;
	$channels = split(chr(13),asterisk::getCommandData('show channels verbose'));
	$channels = split(chr(10),$channels[1]);
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
//	$objResponse->addAssign("divActiveCalls", "innerHTML", uniqid(""));
	$objResponse->addAssign("channels", "innerHTML", nl2br(trim($myChannels)));

	return $objResponse;
}

function showPredictiveDialer($preDictiveDialerStatus){
	global $db,$locate,$config;

	$objResponse = new xajaxResponse();
	/*
	if ($config['system']['allow_dropcall'] == false){
		$objResponse->addAssign("divPredictiveDialerMsg", "innerHTML", $locate->Translate("cannot_use_predictive_dialer"));
		return $objResponse;
	}
	*/

	//从数据库读取预拨号的总数
	if ($_SESSION['curuser']['usertype']  == "groupadmin")
		$query = "SELECT COUNT(*) FROM diallist WHERE groupid =".$_SESSION['curuser']['groupid'];
	else
		$query = "SELECT COUNT(*) FROM diallist ";

	$res =& $db->getOne($query);

	if ($res == 0 || $res == "0"){
		$objResponse->addAssign("divPredictiveDialerMsg", "innerHTML", $locate->Translate("no_phonenumber_in_database"));
	} else{
		$objResponse->addAssign("divPredictiveDialerMsg", "innerHTML", $locate->Translate("ready_to_dial"));
		$objResponse->addAssign("spanTotalRecords", "innerHTML", $res.' '.$locate->Translate("records_left"));

		// add dial button
		$objResponse->addCreateInput("divPredictiveDialer", "button", "btnDial", "btnDial");
		$objResponse->addAssign("btnDial", "value", $locate->Translate("dial"));
		$objResponse->addEvent("btnDial", "onclick", "btnDialOnClick();");

		// add max active calls field
		$objResponse->addCreateInput("divPredictiveDialer", "text", "fldMaxActiveCalls", "fldMaxActiveCalls");
		$objResponse->addAssign("fldMaxActiveCalls", "size", "3");
		$objResponse->addAssign("fldMaxActiveCalls", "value", "5");

		//add dial language
		$objResponse->addCreateInput("divPredictiveDialer", "hidden", "btnDialMsg", "btnDialMsg");
		$objResponse->addAssign("btnDialMsg", "value", $locate->Translate("dial"));

		//add stop language
		$objResponse->addCreateInput("divPredictiveDialer", "hidden", "btnStopMsg", "btnStopMsg");
		$objResponse->addAssign("btnStopMsg", "value", $locate->Translate("stop"));

		//add number only language
		$objResponse->addCreateInput("divPredictiveDialer", "hidden", "btnNumberOnlyMsg", "btnNumberOnlyMsg");
		$objResponse->addAssign("btnNumberOnlyMsg", "value", $locate->Translate("number_only"));

		//add dialer stopped language
		$objResponse->addCreateInput("divPredictiveDialer", "hidden", "btnDialerStoppedMsg", "btnDialerStoppedMsg");
		$objResponse->addAssign("btnDialerStoppedMsg", "value", $locate->Translate("dialer_stopped"));

	}
	return $objResponse;
}

function predictiveDialer($maxChannels,$totalRecords){
	global $config,$db,$locate;
	$objResponse = new xajaxResponse();
	
	$myAsterisk = new Asterisk();

	//获取一个号码
	if ($_SESSION['curuser']['usertype']  == "admin"){
		$row =& astercrm::getDialNumber();
		$grouprow = & astercrm::getRecordByField("groupid",$row['groupid'],"accountgroup");
		$pdextension = $grouprow['pdextension'];
		$pdcontext = $grouprow['pdcontext'];
	}else{
		$row =& astercrm::getDialNumber($_SESSION['curuser']['groupid']);
		$pdextension = $_SESSION['curuser']['group']['pdextension'];
		$pdcontext = $_SESSION['curuser']['group']['pdcontext'];
	}
	
	if ($row['id'] == ''){
		$objResponse->addAssign("divPredictiveDialerMsg", "innerHTML", $locate->Translate("no_phonenumber_in_database"));
		$objResponse->addScript("stopDial();");
		return $objResponse;
	} else {
		$id = $row['id'];
		$groupid = $row['groupid'];
		$campaignid = $row['campaignid'];
		$phoneNum = $row['dialnumber'];
		$assign = $row['assign'];
		// get active channel
		$channels = split(chr(13),asterisk::getCommandData('show channels verbose'));
		$channels = split(chr(10),$channels[1]);
		//trim the first two records and the last three records

		array_pop($channels); 
		$activeCalls = array_pop($channels); 
		$activeChannels = array_pop($channels); 
		
		$curCalls = split(" ",$activeCalls);
		$curCalls = $curCalls[0];
		if ($curCalls >= $maxChannels){
			$objResponse->addAssign("divPredictiveDialerMsg", "innerHTML", $locate->Translate("reach_maximum_concurrent_calls"));
			return $objResponse;
		}

		$res = astercrm::deleteRecord($id,"diallist");
		$f['dialnumber'] = $phoneNum;
		$f['dialedby'] = $_SESSION['curuser']['username'];
		$f['groupid'] = $groupid;
		$f['assign'] = $assign;
		$f['campaignid'] = $campaignid;
		$res = astercrm::insertNewDialedlist($f);

		$sid=md5(uniqid(""));
		// if we didnt set pdextension, we use send phone number to pdcontext
		if ($pdextension == '') $pdextension = $phoneNum;

		/*
		$query = '
			INSERT INTO dialresult SET
			phoneid = \''.$id.'\',
			phonenumber = \''.$phoneNum.'\',
			dialstatus = \'begin\',
			actionid = \''.$actionid.'\'
			';
		$res = $db->query($query);
		*/
		$strChannel = "Local/".$phoneNum."@".$config['system']['outcontext']."/n";
		if ($config['system']['allow_dropcall'] == true){

		$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
									'WaitTime'=>30,
									'Exten'=>$pdextension,
									'Context'=>$pdcontext,
									'Variable'=>"$strVariable",
									'Priority'=>1,
									'MaxRetries'=>0,
									'CallerID'=>$phoneNum));
		}else{
			$myAsterisk->config['asmanager'] = $config['asterisk'];
			$res = $myAsterisk->connect();

			$myAsterisk->sendCall($strChannel,$pdextension,$pdcontext,1,NULL,NULL,30,$phoneNum,NULL,NULL,NULL,$actionid);
		}
		$objResponse->addAssign("divPredictiveDialerMsg", "innerHTML", $locate->Translate("dialing")." $phoneNum");
		$totalRecords = $totalRecords-1;
		if ($totalRecords < 0 )
			$totalRecords = 0;
		$objResponse->addAssign("spanTotalRecords", "innerHTML", $totalRecords." ".$locate->Translate("records_left"));

//		$myAsterisk->Originate($strChannel,$config['system']['preDialer_extension'],$config['system']['incontext'],1,NULL,NULL,30,$phoneNum,NULL,NULL,NULL,$actionid);

	}
	
	return $objResponse;


}

$xajax->processRequests();
?>

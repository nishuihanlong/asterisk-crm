<?php
/*******************************************************************************
* poral.server.php
* agent portal interface

* Function Desc
	agent portal background script

* 功能描述
	座席管理脚本

* Function Desc

	showDetail
	getPrivateDialListNumber
	init
	listenCalls
	incomingCalls
	waitingCalls
	createGrid
	getContact
	monitor
	dial
	transfer
	addWithPhoneNumber
	invite
	chanspy
	searchFormSubmit   多条件搜索，重构显示页面
	knowledgechange
	setKnowledge
	getPreDiallist
	agentWorkstat

* Revision 0.047  2008/2/24 14:45:00  last modified by solo
* Desc: add a new parameter callerid in function monitor
* when monitor, record the callerid and the filename to database

* Revision 0.0456  2007/11/7 14:45:00  last modified by solo
* Desc: add function chanspy

* Revision 0.0456  2007/11/7 11:01:00  last modified by solo
* Desc: fix table width

* Revision 0.0456  2007/11/1 9:48:00  last modified by solo
* Desc: fix bug: when use sendCall method, cant hangup until one party is connected

* Revision 0.0456  2007/10/30 12:47:00  last modified by solo
* Desc: add link for customer and contact

* Revision 0.0456  2007/10/30 8:47:00  last modified by solo
* Desc: add function invite

* Revision 0.0451  2007/10/25 15:21:00  last modified by solo
* Desc: remove confirmCustomer,confirmContact to common file

* Revision 0.0451  2007/10/24 20:37:00  last modified by solo
* Desc: use another dial method: sendCall() to replace Originate

* Revision 0.045  2007/10/18 14:19:00  modified by solo
* Desc: comment added

* Revision 0.045  2007/10/17 20:55:00  modified by solo
* Desc: change callerid match method to like '%callerid'
* 描述: 将电话号码匹配方式修改为前端模糊式检索

* Revision 0.045  2007/10/17 12:55:00  modified by solo
* Desc: fix bugs in search, ordering

********************************************************************************/

require_once ("db_connect.php");
require_once ("portal.common.php");
require_once ('include/asterevent.class.php');
require_once ('include/asterisk.class.php');
require_once ('astercrm.server.common.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('portal.grid.inc.php');
require_once ('include/phoogle.php');

/**
*  show customer contact detail
*  @param			noteid		int			noteid
*  @return			object		xajax response object
*/

function showDetail($noteid){
	global $config;
	$objResponse = new xajaxResponse();

	if ($config['system']['portal_display_type'] == "note"){
		$objResponse->addScript("xajax_showContact('$noteid','note');");
		$objResponse->addScript("xajax_showCustomer('$noteid','note');");
	}elseif ($config['system']['portal_display_type'] == "customer"){
		//$objResponse->addScript("xajax_showContact('$noteid','customer');");
		$objResponse->addScript("xajax_showCustomer('$noteid','customer');");
	}

	return $objResponse;
}

/**
*  show phone numbers and dial button if there are phone numbers assigned to this agent
*  in diallist table
*  @param	extension		string			extension
*  @return	object				xajax response object
*/

function getPrivateDialListNumber($extension = null){
	global $locate,$db;
	$objResponse = new xajaxResponse();

	$count = astercrm::getDialNumCountByAgent($extension);
	if ($count == 0){
		$objResponse->addAssign("divDialList", "innerHTML", $locate->Translate("no_dial_list"));
		$objResponse->addAssign("divWork", "innerHTML", '');
		$objResponse->addAssign("btnWorkStatus","value", "" );
		$objResponse->addAssign("btnWork","value", $locate->Translate("Start work") );
		$objResponse->addAssign("btnWork","disabled", true );
		$_SESSION['curuser']['WorkStatus'] = '';
	} else{
		// add div
		$objResponse->addRemove("spanDialListRecords");
		$objResponse->addRemove("btnGetAPhoneNumber");

		$objResponse->addCreate("divDialList", "div", "spanDialListRecords");
		$objResponse->addAssign("spanDialListRecords", "innerHTML", $locate->Translate("records_in_dial_list_table").$count);

		// add start campaign button
		$objResponse->addCreateInput("divDialList", "button", "btnGetAPhoneNumber", "btnGetAPhoneNumber");
		$objResponse->addAssign("btnGetAPhoneNumber", "value", $locate->Translate("get_a_phone_number"));
		$objResponse->addEvent("btnGetAPhoneNumber", "onclick", "btnGetAPhoneNumberOnClick();");
	}

	return $objResponse;
}

/**
*  init page
*  @return	object				xajax response object
*/

function init(){
	global $locate,$config,$db;

	$objResponse = new xajaxResponse();

	$check_interval = 2000;
	if ( is_numeric($config['system']['status_check_interval']) ) $check_interval = $config['system']['status_check_interval'] * 1000;

	$objResponse->addAssign("checkInterval","value", $check_interval );
	
	$html = $locate->Translate("welcome").':'.$_SESSION['curuser']['username'].',';
	$html .= $locate->Translate("extension").$_SESSION['curuser']['extension'];
	$objResponse->addAssign("divUserMsg","innerHTML", $html );

	$objResponse->addAssign("username","value", $_SESSION['curuser']['username'] );
	$objResponse->addAssign("extension","value", $_SESSION['curuser']['extension'] );
	$objResponse->addAssign("myevents","innerHTML", $locate->Translate("waiting") );
//	$objResponse->addAssign("status","innerHTML", $locate->Translate("listening") );
	$objResponse->addAssign("extensionStatus","value", 'idle');
	$objResponse->addAssign("processingMessage","innerHTML", $locate->Translate("processing_please_wait") );
	
//	$objResponse->addAssign("btnPause","value", $locate->Translate("Continue") );
//	$objResponse->addAssign("breakStatus","value", 1);
//	$memberstatus = Customer::getMyMemberStatus();
//
//	while ($memberstatus->fetchInto($row)) {
//		if($row['status'] != 'paused'){
//			$objResponse->addAssign("btnPause","value", $locate->Translate("Break") );
//			$objResponse->addAssign("breakStatus","value", 0);
//			break;
//		}
//	}


	$objResponse->addAssign("spanMonitorStatus","innerHTML", $locate->Translate("idle") );
	$objResponse->addAssign("btnMonitorStatus","value", "idle" );
	$objResponse->addAssign("btnMonitor","value", $locate->Translate("start_record") );
	if($_SESSION['curuser']['WorkStatus'] == ''){
		$objResponse->addAssign("btnWork","value", $locate->Translate("Start work") );
		$objResponse->addAssign("btnWorkStatus","value", "" );
		$objResponse->addEvent("btnWork", "onclick", "workctrl('start');");
	}else{
		$objResponse->addAssign("btnWork","value", $locate->Translate("Stop work") );
		$objResponse->addAssign("btnWorkStatus","value", "working" );
		$objResponse->addEvent("btnWork", "onclick", "workctrl('stop');");
		$interval = $_SESSION['curuser']['dialinterval'];
		$objResponse->addScript("autoDial('$interval');");
	}
	$objResponse->addAssign("btnMonitor","disabled", true );
	$objResponse->addAssign("divCopyright","innerHTML",Common::generateCopyright($skin));

	$objResponse->loadXML(getPrivateDialListNumber($_SESSION['curuser']['extension']));

	//$objResponse->addAssign("divCopyright","innerHTML",Common::generateCopyright($skin));
	if(strtoupper($config['system']['transfer_pannel']) == 'OFF'){		
		$objResponse->addAssign("spanTransfer", "style.display", "none");		
	}else{
		$objResponse->addAssign("btnTransfer","disabled",true);
	}

	if(strtoupper($config['system']['dial_pannel']) == 'OFF'){		
		$objResponse->addAssign("divInvite", "style.display", "none");
	}

	if(strtoupper($config['system']['monitor_pannel']) == 'OFF'){		
		$objResponse->addAssign("divMonitor", "style.display", "none");		
		$objResponse->addAssign("monitorTitle", "style.display", "none");
	}
	if($_SESSION['curuser']['agent'] != ''){

	}
	if(strtoupper($config['system']['mission_pannel']) == 'OFF' || $_SESSION['curuser']['agent'] != ''){
		$objResponse->addAssign("divDialList", "style.display", "none");
		$objResponse->addAssign("misson", "style.display", "none");
			
	}

	if(strtoupper($config['system']['diallist_pannel']) != 'OFF'){
		$objResponse->addAssign("sptAddDiallist", "style.display", "");	
		$objResponse->addAssign("dpnShow", "value", "1");
		$objResponse->addScript("xajax_showDiallist('".$_SESSION['curuser']['extension']."',0,0,5,'','','','formDiallistPannel','','');");

		//$objResponse->addAssign("formDiallistPannel", "style.visibility", "visible");
	}


	foreach ($_SESSION['curuser']['extensions'] as $extension){
		$extension = trim($extension);
		$row = astercrm::getRecordByField('username',$extension,'astercrm_account');		
		$objResponse->addScript("addOption('sltExten','".$row['extension']."','$extension');");
	}
	$speeddial = & Customer::getAllSpeedDialRecords();
	$speednumber['0']['number'] = $_SESSION['curuser']['extension'];
	$speednumber['0']['description'] = $_SESSION['curuser']['username'];
	$n = 1;
	while ($speeddial->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$speednumber[$n]['description'] = $row['description'];
		$speednumber[$n]['number'] = $row['number'];
		$n++;
	}
	$n = count($speednumber);
	for ($i=0;$i<$n;++$i){
		$objResponse->addScript("addOption('iptDestNumber','".$speednumber[$i]['number']."','".$speednumber[$i]['description']."-".$speednumber[$i]['number']."');");
	}

	if ($config['system']['display_recent_cdr'] == true && $_SESSION['curuser']['usertype'] == "agent"){	

	}else{
		$panelHTML = '<a href=? onclick="xajax_showRecentCdr(\'\',\'recent\');return false;">'.$locate->Translate("recentCDR").'</a>&nbsp;&nbsp;';
	}


	if ($_SESSION['curuser']['usertype'] != "agent"  ){
		$panelHTML .= '<a href=# onclick="this.href=\'managerportal.php\'">'.$locate->Translate("manager").'</a>&nbsp;&nbsp;';
	}

	$panelHTML .="<a href='login.php'>".$locate->Translate("logout")."</a>";
	$objResponse->addAssign("divPanel","innerHTML", $panelHTML);

	if ($config['system']['enable_external_crm'] == false){	//use internal crm
		$objResponse->addIncludeScript("js/astercrm.js");
		$objResponse->addIncludeScript("js/ajax.js");
		$objResponse->addIncludeScript("js/ajax-dynamic-list.js");
		$objResponse->addScript("xajax_showGrid(0,".ROWSXPAGE.",'','','')");
		$objResponse->addAssign("divSearchContact", "style.visibility", "visible");
	} else {
		$objResponse->addIncludeScript("js/extercrm.js");
		if ($config['system']['open_new_window'] == false){
			$mycrm = '<iframe id="mycrm" name="mycrm" src="'.$config['system']['external_crm_default_url'].'" width="100%"  frameBorder=0 scrolling=auto height="100%"></iframe>';
			$objResponse->addAssign("divCrm","innerHTML", $mycrm );
		}else{
			$javascript = "openwindow('".$config['system']['external_crm_default_url']."')";
			$objResponse->addScript($javascript);
		}
	}
	$monitorstatus = astercrm::getRecordByID($_SESSION['curuser']['groupid'],'astercrm_accountgroup');
	
	if ($monitorstatus['monitorforce']) {
		$objResponse->addAssign("chkMonitor","checked", 'true');
		$objResponse->addAssign("chkMonitor","style.visibility", 'hidden');
		$objResponse->addAssign("btnMonitor","disabled", 'true');
	}
	//if enabled monitor by astercctools
	Common::read_ini_file($config['system']['astercc_path'].'/astercc.conf',$asterccConfig);

	if ($asterccConfig['system']['force_record'] == 1 ) {
//		echo $asterccConfig['system']['force_record'];exit;
		$objResponse->addAssign("chkMonitor","checked", false);
		$objResponse->addAssign("chkMonitor","style.visibility", 'hidden');
		$objResponse->addAssign("btnMonitor","disabled", 'true');
	}
	return $objResponse;
}

/**
*	 check if there's new event happen
*
*/
function listenCalls($aFormValues){
	global $config,$locate;
	//print_r($aFormValues);exit;
	$objResponse = new xajaxResponse();
	if($agentData = Customer::getAgentData()){
		
		if($aFormValues['breakStatus'] == -1){
			$span = '<input type="button" value="" name="btnPause" id="btnPause" onclick="queuePaused();" >';
			$objResponse->addAssign("spnPause","innerHTML", $span );
		}
		if($agentData['cretime'] > $aFormValues['clkPauseTime']){
			$objResponse->addAssign("agentData","innerHTML", $agentData['data'] );
			if($agentData['agent_status'] != 'paused'){
				$objResponse->addAssign("btnPause","value", $locate->Translate("Break") );
				$objResponse->addAssign("breakStatus","value", 0);
			}else{
				$objResponse->addAssign("btnPause","value", $locate->Translate("Continue") );
				$objResponse->addAssign("breakStatus","value", 1);
			}
		}
	}else{
		if($_SESSION['curuser']['agent'] == '' ){
			$objResponse->addAssign("agentData","innerHTML", '');
			$objResponse->addAssign("spnPause","innerHTML", '' );
			$objResponse->addAssign("breakStatus","value", -1);
		}
	}

	if($aFormValues['dpnShow'] > 0){ //for refresh diallist pannel
		$lastDiallistId = Customer::getLastOwnDiallistId();
		if($lastDiallistId == '') $lastDiallistId = 1;
		if( $aFormValues['dpnShow'] != $lastDiallistId ){
			$objResponse->addAssign("dpnShow","value", $lastDiallistId );
			$objResponse->addScript("xajax_showDiallist('".$_SESSION['curuser']['extension']."',0,0,5,'','','','formDiallistPannel','','');");
		}
	}

	if ($aFormValues['uniqueid'] == ''){
		$objResponse->loadXML(waitingCalls($aFormValues));
	} else{
		$objResponse->loadXML(incomingCalls($aFormValues));
	}

	//set time intervals of update events
	//$check_interval = 2000;
	//if ( is_numeric($config['system']['status_check_interval']) ) $check_interval = $config['system']['status_check_interval'] * 1000;

	//$objResponse->addScript('setTimeout("updateEvents()", '.$check_interval.');');
	return $objResponse;
}

/**
*	 check if there's new event happen
*
*/
function transfer($aFormValues){
	global $config;
	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	$objResponse = new xajaxResponse();
	
	if ($aFormValues['iptTtansfer'] != ''){
		$action = $aFormValues['iptTtansfer'];
	}elseif ($aFormValues['sltExten'] != ''){
		$action = $aFormValues['sltExten'];
	}else{
		return $objResponse;
	}

	if ($aFormValues['direction'] == 'in')		
		$myAsterisk->Redirect($aFormValues['callerChannel'],'',$action,$config['system']['outcontext'],1);
	else
		$myAsterisk->Redirect($aFormValues['calleeChannel'],'',$action,$config['system']['outcontext'],1);
	$objResponse->addAssign("divMsg", "style.visibility", "hidden");
	return $objResponse;
}

//check if call (uniqueid) hangup
function incomingCalls($myValue){
	global $db,$locate,$config;
	$objResponse = new xajaxResponse();

	if ($myValue['direction'] != ''){
		$call = asterEvent::checkCallStatus($myValue['curid'],$myValue['uniqueid']);

		if ($call['status'] ==''){
			return $objResponse;
		} elseif ($call['status'] =='link'){
			if($myValue['callResultStatus'] != '2'){
				if($dialedlistid = asterCrm::checkDialedlistCall($myValue['callerid'])){
					$divCallresult = Customer::getCampaignResultHtml($dialedlistid,'ANSWERED');
					//echo $divCallresult;exit;
					$objResponse->addAssign("divCallresult", "style.display", "");
					$objResponse->addAssign("divCallresult", "innerHTML", $divCallresult);
					$objResponse->addAssign("dialedlistid","value", $dialedlistid );
				}else{
					$objResponse->addAssign("dialedlistid","value", 0 );
				}
				$objResponse->addAssign("callResultStatus","value", '2' );
			}

			if ($myValue['extensionStatus'] == 'link')	 //already get link event
				return $objResponse;
//			if ($call['callerChannel'] == '' or $call['calleeChannel'] == '')
//				return $objResponse;
			$status	= "link";
			$info	= $locate->Translate("talking_to").$myValue['callerid'];
			$objResponse->addAssign("callerChannel","value", $call['callerChannel'] );
			$objResponse->addAssign("calleeChannel","value", $call['calleeChannel'] );
			//if chkMonitor be checked or monitor by astercctools btnMonitor must be disabled
			Common::read_ini_file($config['system']['astercc_path'].'/astercc.conf',$asterccConfig);
			if ($myValue['chkMonitor'] != 'on' && $asterccConfig['system']['force_record'] != 1) {
				$objResponse->addAssign("btnMonitor","disabled", false );
			}
			//$objResponse->addAssign("btnMonitor","value", $locate->Translate("start_record") );
			astercrm::events($myValue['chkMonitor'].'-chkMonitor');
			astercrm::events($myValue['btnMonitorStatus'].'-btnMonitorStatus');
			//echo $myValue['chkMonitor'];exit;
			if ($myValue['chkMonitor'] == 'on' && $myValue['btnMonitorStatus'] == 'idle') 
				$objResponse->addScript("monitor();");			
			$objResponse->addAssign("btnHangup","disabled", false );
			if(strtoupper($config['system']['transfer_pannel']) == 'ON'){
				$objResponse->addAssign("btnTransfer","disabled", false );
			}
		} elseif ($call['status'] =='hangup'){
			//$objResponse->addAssign("divCallresult", "style.display", "none");
			$objResponse->addAssign("callResultStatus", "value", "");
			
			//$objResponse->addAssign("divCallresult", "innerHTML", '<input type="radio" value="normal" id="callresult" name="callresult" onclick="updateCallresult(this.value);" checked>'.$locate->Translate("normal").' <input type="radio" value="fax" id="callresult" name="callresult" onclick="updateCallresult(this.value);">'. $locate->Translate("fax").' <input type="radio" value="voicemail" id="callresult" name="callresult" onclick="updateCallresult(this.value);">'. $locate->Translate("voicemail").'<input type="hidden" id="dialedlistid" name="dialedlistid" value="0">');
			if ($myValue['chkMonitor'] == 'on' && $myValue['btnMonitorStatus'] == 'recording') 
				$objResponse->addScript("monitor();");
			$status	= 'hang up';
			$info	= "Hang up call from " . $myValue['callerid'];
//			$objResponse->addScript('document.title=\'asterCrm\';');
			$objResponse->addAssign("uniqueid","value", "" );
			$objResponse->addAssign("callerid","value", "" );
			$objResponse->addAssign("callerChannel","value", '');
			$objResponse->addAssign("calleeChannel","value", '');
			if(strtoupper($config['system']['transfer_pannel']) == 'ON'){
				$objResponse->addAssign("btnTransfer","disabled", true );
			}

			//disable monitor
			$objResponse->addAssign("btnMonitor","disabled", true );
			$objResponse->addAssign("spanMonitorStatus","innerHTML", $locate->Translate("idle") );
			$objResponse->addAssign("btnMonitor","value", $locate->Translate("start_record") );

			//disable hangup button
			$objResponse->addAssign("btnHangup","disabled", true );
			$objResponse->addAssign('divTrunkinfo',"innerHTML",'');
			$objResponse->addAssign('divDIDinfo','innerHTML','');
			if($myValue['btnWorkStatus'] == 'working') {				
				$interval = $_SESSION['curuser']['dialinterval'];
				$objResponse->addScript("autoDial('$interval');");
			}
		}
		$objResponse->addAssign("status","innerHTML", $status );
//		$objResponse->addAssign("extensionStatus","value", $status );
		$objResponse->addAssign("myevents","innerHTML", $info );
	}

	return $objResponse;
}

/*
	add a new parameter callerid		by solo2008/2/24
	when monitor, record the callerid and the filename to database
*/
function monitor($channel,$callerid,$action = 'start',$uniqueid = ''){
	global $config,$locate;
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		$objResponse->addAlert($locate->Translate("failed when connect to AMI"));
		return;
	}

	if ($action == 'start'){
		$filename = str_replace("/","-",$channel);
		$filename = $config['asterisk']['monitorpath'].date('Y/m/d/H/').$filename;
		$filename .= '.'.time();
		$format = $config['asterisk']['monitorformat'];
		$mix = true;
		$res = $myAsterisk->Monitor($channel,$filename,$format,$mix);
		if ($res['Response'] == 'Error'){
			return $objResponse;
		}
		// 录音信息保存到数据库
		astercrm::insertNewMonitor($callerid,$filename,$uniqueid,$format);
		$objResponse->addAssign("spanMonitorStatus","innerHTML", $locate->Translate("recording") );
		$objResponse->addAssign("btnMonitorStatus","value", "recording" );

		$objResponse->addAssign("btnMonitor","value", $locate->Translate("stop_record") );
	}else{
		$myAsterisk->StopMontor($channel);

		$objResponse->addAssign("spanMonitorStatus","innerHTML", $locate->Translate("idle") );
		$objResponse->addAssign("btnMonitorStatus","value", "idle" );

		$objResponse->addAssign("btnMonitor","value", $locate->Translate("start_record") );
	}

	//$objResponse->addAssign("btnMonitor","disabled", false );
	return $objResponse;
}


function waitingCalls($myValue){
	global $db,$config,$locate;
	$objResponse = new xajaxResponse();
	$curid = trim($myValue['curid']);

// to improve system efficiency
/**************************
**************************/
	if(strtoupper($config['system']['extension_pannel']) == 'ON'){
		$phone_html = asterEvent::checkExtensionStatus($curid);
		$objResponse->addAssign("divExtension","innerHTML", $phone_html );
		$objResponse->addScript("menuFix();");
	}else{
		$objResponse->addAssign("divExtension","style.visibility", 'hidden');
	}

	//	modified 2007/10/30 by solo
	//  start
	//print_r($_SESSION);exit;
	//if ($_SESSION['curuser']['channel'] == '')
		$call = asterEvent::checkNewCall($curid,$_SESSION['curuser']['extension'],$_SESSION['curuser']['channel'],$_SESSION['curuser']['agent']);
	//else
	//	$call = asterEvent::checkNewCall($curid,$_SESSION['curuser']['channel']);
	//  end
	//print_r($call['callerid']);exit;
	if ($call['status'] == ''){
		$title	= $locate->Translate("waiting");
		$status	= 'idle';
		//$call['curid'] = $curid;
		$direction	= '';
		$info	= $locate->Translate("stand_by");
	} elseif ($call['status'] == 'incoming'){	//incoming calls here
		$title	= $call['callerid'];
		$stauts	= 'ringing';
		$direction	= 'in';
		$info	= $locate->Translate("incoming"). ' ' . $call['callerid'];
		$dialedlistid = asterCrm::checkDialedlistCall($call['callerid']);
		if($myValue['callResultStatus'] == '' && $call['callerid'] != ''){
				if($dialedlistid){
					$divCallresult = Customer::getCampaignResultHtml($dialedlistid,'NOANSWER');
					//echo $divCallresult;exit;
					$objResponse->addAssign("divCallresult", "style.display", "");
					$objResponse->addAssign("divCallresult", "innerHTML", $divCallresult);
					$objResponse->addAssign("dialedlistid","value", $dialedlistid );
				}else{
					$objResponse->addAssign("dialedlistid","value", 0 );
					$objResponse->addAssign("divCallresult", "style.display", "none");
				}
				$objResponse->addAssign("callResultStatus","value", '1' );
		}
		if($dialedlistid){
			if($config['diallist']['popup_diallist'] == 1){
				$dialistHtml = Customer::formDiallist($dialedlistid);
				$objResponse->addAssign('formDiallistPopup','innerHTML',$dialistHtml);
				$objResponse->addAssign('formDiallistPopup',"style.visibility", "visible");
			}
		}
		if($call['didnumber'] != ''){
			$didinfo = $locate->Translate("Callee id")."&nbsp;:&nbsp;<b>".$call['didnumber']."</b>";
			$objResponse->addAssign('divDIDinfo','innerHTML',$didinfo);
		}
		
		$trunk = split("-",$call['callerChannel']);
		//print_r($trunk);exit;
		
		$info	= $info. ' channel: ' . $trunk[0];
		// get trunk info
		$mytrunk = astercrm::getTrunkinfo($trunk[0],$call['didnumber']);
		if ($mytrunk){
			$infomsg = "<strong>".$mytrunk['trunkname']."</strong><br>";
			$infomsg .= astercrm::db2html($mytrunk['trunknote']);
			$objResponse->addAssign('divTrunkinfo',"innerHTML",$infomsg);
		}else{
			$infomsg = $locate->Translate("no information get for trunk").": ".$trunk[0];
			$objResponse->addAssign('divTrunkinfo',"innerHTML",$infomsg);
		}
		
		$objResponse->addAssign("iptCallerid","value", $call['callerid'] );
		$objResponse->addAssign("btnHangup","disabled", false );

		if ($config['system']['pop_up_when_dial_in']){
			if (strlen($call['callerid']) > $config['system']['phone_number_length'] && $call['callerid'] != '<unknown>'){
				if ($myValue['popup'] == 'yes'){
					if ($config['system']['enable_external_crm'] == false){
							$objResponse->loadXML(getContact($call['callerid']));
							if ( $config['system']['browser_maximize_when_pop_up'] == true ){
								$objResponse->addScript('maximizeWin();');
							}
					}else{
						//print_r($call);exit;
						//use external link
						$myurl = $config['system']['external_crm_url'];
						$myurl = preg_replace("/\%method/","dial_in",$myurl);
						$myurl = preg_replace("/\%callerid/",$call['callerid'],$myurl);
						$myurl = preg_replace("/\%calleeid/",$_SESSION['curuser']['extension'],$myurl);
						$myurl = preg_replace("/\%uniqueid/",$call['uniqueid'],$myurl);
						$myurl = preg_replace("/\%calldate/",$call['calldate'],$myurl);

						if($config['system']['external_url_parm'] != ''){
							if ($config['system']['detail_level'] == 'all')
								$customerid = astercrm::getCustomerByCallerid($call['callerid']);
							else
								$customerid =	astercrm::getCustomerByCallerid($call['callerid'],$_SESSION['curuser']['groupid']);
							
							if($customerid != ''){
								$customer = astercrm::getCustomerByID($customerid,"customer");
								$url_parm = split(',',$config['system']['external_url_parm']);

								foreach($url_parm as $parm){
									if($parm != '' ){
										$more_parm .= '&'.$parm.'='.urlencode($customer[$parm]);
									}
								}
								$myurl .= $more_parm;
							}

						}

						if ($config['system']['open_new_window'] == false){
								$mycrm = '<iframe id="mycrm" name="mycrm" src="'.$myurl.'" width="100%"  frameBorder=0 scrolling=auto height="100%"></iframe>';
								$objResponse->addAssign("divCrm","innerHTML", $mycrm );
						}else{
							$javascript = "openwindow('".$myurl."')";
							$objResponse->addScript($javascript);
						}
					}
				}
			}else{

			}
		}
	} elseif ($call['status'] == 'dialout'){	//dailing out here
		$title	= $call['callerid'];
		$status	= 'dialing';
		$direction	= 'out';
		$info	= $locate->Translate("dial_out"). ' '. $call['callerid'];
		if($myValue['callResultStatus'] == '' && $call['callerid'] != ''){
				if($dialedlistid = asterCrm::checkDialedlistCall($call['callerid'])){
					$divCallresult = Customer::getCampaignResultHtml($dialedlistid,'NOANSWER');
					//echo $divCallresult;exit;
					$objResponse->addAssign("divCallresult", "style.display", "");
					$objResponse->addAssign("divCallresult", "innerHTML", $divCallresult);
					$objResponse->addAssign("dialedlistid","value", $dialedlistid );
				}else{
					$objResponse->addAssign("dialedlistid","value", 0 );
					$objResponse->addAssign("divCallresult", "style.display", "none");
				}
				$objResponse->addAssign("callResultStatus","value", '1' );
		}
		$objResponse->addAssign("iptCallerid","value", $call['callerid'] );
		$objResponse->addAssign("btnHangup","disabled", false );

		if($call['didnumber'] != ''){
			$didinfo = $locate->Translate("Callee id")."&nbsp;:&nbsp;".$call['didnumber'];
			$objResponse->addAssign('divDIDinfo','innerHTML',$didinfo);
		}

		if ($config['system']['pop_up_when_dial_out']){
			if (strlen($call['callerid']) > $config['system']['phone_number_length']){
				if ($myValue['popup'] == 'yes'){
					if ($config['system']['enable_external_crm'] == false ){
							$objResponse->loadXML(getContact($call['callerid']));
							if ( $config['system']['browser_maximize_when_pop_up'] == true ){
								$objResponse->addScript('maximizeWin();');
							}
					}else{
						//use external link
						$myurl = $config['system']['external_crm_url'];
						$myurl = preg_replace("/\%method/","dial_out",$myurl);
						$myurl = preg_replace("/\%callerid/",$_SESSION['curuser']['extension'],$myurl);
						$myurl = preg_replace("/\%calleeid/",$call['callerid'],$myurl);
						$myurl = preg_replace("/\%uniqueid/",$call['uniqueid'],$myurl);
						$myurl = preg_replace("/\%calldate/",$call['calldate'],$myurl);
						if ($config['system']['open_new_window'] == false){
							$mycrm = '<iframe id="mycrm" name="mycrm" src="'.$myurl.'" width="100%"  frameBorder=0 scrolling=auto height="100%"></iframe>';
							$objResponse->addAssign("divCrm","innerHTML", $mycrm );
						} else {
							$javascript = "openwindow('".$myurl."')";
							$objResponse->addScript($javascript);
						}
					}
				}
			}
		}
	}
//	$objResponse->addScript('document.title='.$title.';');
//	$objResponse->addAssign("status","innerHTML", $stauts );
	$objResponse->addAssign("extensionStatus","value", $stauts );
	//echo $call['uniqueid'];exit;
	$objResponse->addAssign("uniqueid","value", $call['uniqueid'] );
	$objResponse->addAssign("callerid","value", $call['callerid'] );	
	$objResponse->addAssign("callerChannel","value", $call['callerChannel'] );
	$objResponse->addAssign("calleeChannel","value", $call['calleeChannel'] );
	$objResponse->addAssign("curid","value", $call['curid'] );
	$objResponse->addAssign("direction","value", $direction );
	$objResponse->addAssign("myevents","innerHTML", $info);

	return $objResponse;
}

//	create grid
function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = "",$stype=null){
	global $locate,$config;

	$_SESSION['ordering'] = $ordering;

	if($filter == null or $content == null or $content == 'Array' or $filter == 'Array'){
		$numRows =& Customer::getNumRows();
		$arreglo =& Customer::getAllRecords($start,$limit,$order);
		$content = null;
		$filter = null;
	}else{
		foreach($content as $value){
			if(trim($value) != ""){  //搜索内容有值
				$flag = "1";
				break;
			}
		}
		foreach($filter as $value){
			if(trim($value) != ""){  //搜索条件有值
				$flag2 = "1";
				break;
			}
		}
		foreach($stype as $value){
			if(trim($value) != ""){  //搜索方式有值
				$flag3 = "1";
				break;
			}
		}
		if($flag != "1" || $flag2 != "1"){  //无值
			$order = null;
			$numRows =& Customer::getNumRows();
			$arreglo =& Customer::getAllRecords($start,$limit,$order);
		}elseif($flag3 != 1){ //无搜索方式
			$order = "id";
			$numRows =& Customer::getNumRows($filter, $content);
			$arreglo =& Customer::getRecordsFiltered($start, $limit, $filter, $content, $order);
		}else{
			$order = "id";
			$numRows =& Customer::getNumRowsMorewithstype($filter, $content,$stype,$table);
			$arreglo =& Customer::getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table);
		}
	}

	// Editable zone

	// Select Box: type table.
	$typeFromSearch = array();
	$typeFromSearch[] = 'like';
	$typeFromSearch[] = 'equal';
	$typeFromSearch[] = 'more';
	$typeFromSearch[] = 'less';

	// Selecct Box: Labels showed on searchtype select box.
	$typeFromSearchShowAs = array();
	$typeFromSearchShowAs[] = $locate->Translate('like');
	$typeFromSearchShowAs[] = '=';
	$typeFromSearchShowAs[] = '>';
	$typeFromSearchShowAs[] = '<';


	// Databse Table: fields
	$fields = array();
	$fields[] = 'customer';
	$fields[] = 'category';
	$fields[] = 'contact';
	$fields[] = 'note';
	$fields[] = 'attitude';   //face
	$fields[] = 'cretime';
	$fields[] = 'creby';
	$fields[] = 'priority';
	$fields[] = 'private';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("customer_name")."<BR>";//"Customer Name";
	$headers[] = $locate->Translate("category")."<BR>";//"Category";
	$headers[] = $locate->Translate("contact")."<BR>";//"Contact";
	$headers[] = $locate->Translate("note")."<BR>";//"Note";
	$headers[] = $locate->Translate("attitude")."<BR>";//"face";
	$headers[] = $locate->Translate("create_time")."<BR>";//"Create Time";
//	$headers[] = $locate->Translate("create_by")."<BR>";//"Create By";
	$headers[] = "P<BR>";
	if ($config['system']['portal_display_type'] == "note")
		$headers[] = $locate->Translate("private")."<BR>";
//	$headers[] = "D";

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="20%" nowrap';
	$attribsHeader[] = 'width="10%" nowrap';
	$attribsHeader[] = 'width="8%" nowrap';
	$attribsHeader[] = 'width="36%" nowrap';//note
	$attribsHeader[] = 'width="8%" nowrap'; //face
	$attribsHeader[] = 'width="10% nowrap"';
//	$attribsHeader[] = 'width="10%"';
//	$attribsHeader[] = 'width="7%"';
	$attribsHeader[] = 'width="8%" nowrap';
	if ($config['system']['portal_display_type'] == "note")
		$attribsHeader[] = 'width="8%" nowrap';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left; textarea-layout:fixed; word-break:break-all;"';
	$attribsCols[] = 'style="text-align: left"';
//	$attribsCols[] = 'nowrap style="text-align: left"';
	$attribsCols[] = 'style="text-align: left;"';
	if ($config['system']['portal_display_type'] == "note")
		$attribsCols[] = 'style="text-align: left;"';


	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","customer","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","category","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","contact","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","note","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","attitude","'.$divName.'","ORDERING");return false;\'';  //face
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","cretime","'.$divName.'","ORDERING");return false;\'';
//	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","creby","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","priority","'.$divName.'","ORDERING");return false;\'';
	if ($config['system']['portal_display_type'] == "note")
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","private","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	if ($config['system']['portal_display_type'] == "note"){
		$fieldsFromSearch[] = 'customer';
		$fieldsFromSearch[] = 'category';
		$fieldsFromSearch[] = 'contact.contact';
		$fieldsFromSearch[] = 'note';
		$fieldsFromSearch[] = 'attitude';  //face
		$fieldsFromSearch[] = 'priority';
		$fieldsFromSearch[] = 'note.cretime';
	}elseif ($config['system']['portal_display_type'] == "customer"){
		$fieldsFromSearch[] = 'customer.customer';
		$fieldsFromSearch[] = 'customer.category';
		$fieldsFromSearch[] = 'customer.contact';
		$fieldsFromSearch[] = 'note.note';
		$fieldsFromSearch[] = 'note.attitude';  //face
		$fieldsFromSearch[] = 'note.priority';
		$fieldsFromSearch[] = 'customer.cretime';
	}

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("customer_name");
	$fieldsFromSearchShowAs[] = $locate->Translate("category");
	$fieldsFromSearchShowAs[] = $locate->Translate("contact");
	$fieldsFromSearchShowAs[] = $locate->Translate("note");
	$fieldsFromSearchShowAs[] = $locate->Translate("attitude"); //face
	$fieldsFromSearchShowAs[] = $locate->Translate("priority");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_time");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	if ($config['system']['portal_display_type'] == "note"){
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader);
	}else{
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader,$edit=true,$delete=false,$detail=true);
	}
	$table->setAttribsCols($attribsCols);
	//$table->addRowSearch("note",$fieldsFromSearch,$fieldsFromSearchShowAs);
	//$table->addRowSearchMore("note",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content);
	$table->addRowSearchMore($config['system']['portal_display_type'],$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,1,0,$typeFromSearch,$typeFromSearchShowAs,$stype);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];

	if ($config['system']['portal_display_type'] == "note"){
		$rowc[] = "<a href=? onclick=\"xajax_showCustomer('".$row['customerid']."');return false;\">".$row['customer']."</a>";
	}else{
		$rowc[] = $row['customer'];
	}


		$rowc[] = $row['category'];

	if ($config['system']['portal_display_type'] == "note"){
		$rowc[] = "<a href=? onclick=\"xajax_showContact('".$row['contactid']."');return false;\">".$row['contact']."</a>";
	}else{
		$rowc[] = $row['contact'];
	}


		//$rowc[] = '<textarea readonly="true" style="overflow:auto;width: 240px;height:50px;" wrap="soft">'.str_replace('<br>',chr(13),$row['note']).'</textarea>';
		if($row['private'] == 0 || $row['creby'] == $_SESSION['curuser']['username'])
			$rowc[] = ''.$row['note'].'';
		else
			$rowc[] = '';

		if ($row['attitude'] != '')
			$rowc[] = '<img src="skin/default/images/'.$row['attitude'].'.gif" width="25px" height="25px" border="0" />';
		else 
			$rowc[] = '';

		$rowc[] =  str_replace(" ","<br>",$row['cretime']);
//		$rowc[] = $row['creby'];
		$rowc[] = $row['priority'];
//		$rowc[] = 'Detail';
		if ($config['system']['portal_display_type'] == "note"){
			if($row['private'] == 1) 
				$rowc[] = '<img src="images/groups_icon01.gif"  border="0"';
			else $rowc[] = '';
			$table->addRow("note",$rowc,1,1,1,$divName,$fields);
		}else{
			$table->addRow("customer",$rowc,1,0,1,$divName,$fields);
		}
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}

function addWithPhoneNumber(){
	$objResponse = new xajaxResponse();
	global $db;
	
	$row = astercrm::getDialNumByAgent($_SESSION['curuser']['extension']);

	if ($row['id'] == ''){

	} else {
		$phoneNum = $row['dialnumber'];
		$objResponse->loadXML(getContact($phoneNum));
		astercrm::deleteRecord($row['id'],"diallist");
		$row['dialednumber'] = $phoneNum;
		$row['dialedby'] = $_SESSION['curuser']['extension'];
		$row['trytime'] = $row['trytime'] + 1;
		astercrm::insertNewDialedlist($row);
	}

	$objResponse->loadXML(getPrivateDialListNumber($_SESSION['curuser']['extension']));

	return $objResponse;
}

function checkworkexten() {
	global $db,$locate,$config;

	$objResponse = new xajaxResponse();
	if($config['system']['checkworkexten'] != 'yes'){
		$objResponse->addAssign("workingextenstatus","value", "ok" );
		return $objResponse;
	}
	
	if($_SESSION['curuser']['channel'] == ''){
		$row = astercrm::getRecordByField("peer","sip/".$_SESSION['curuser']['extension'],"peerstatus");
	}else{
		$row = astercrm::getRecordByField("peer",$_SESSION['curuser']['channel'],"peerstatus");
	}

	if($row['status'] != 'reachable') {
		$objResponse->addAssign("workingextenstatus","value", $locate->Translate("extension_unavailable") );
	}else{
		$objResponse->addAssign("workingextenstatus","value", "ok" );
	}

	return $objResponse;
}

function workstart() {
	global $db,$locate,$config;
	$objResponse = new xajaxResponse();

	$row = astercrm::getDialNumByAgent($_SESSION['curuser']['extension']);
	if ($row['id'] == ''){

	} else {
		$objResponse->addAssign("btnWork","value", $locate->Translate("Stop work") );
		if($config['system']['stop_work_verify'])
			$objResponse->addEvent("btnWork", "onclick", "workctrl('check');");
		else
			$objResponse->addEvent("btnWork", "onclick", "workctrl('stop');");
		$objResponse->addAssign("btnWorkStatus","value", "working" );
		$objResponse->addAssign("divWork","innerHTML", $locate->Translate("dialing to")." ".$row['dialnumber']);
		$_SESSION['curuser']['WorkStatus'] = 'working';
		$phoneNum = $row['dialnumber'];			
		astercrm::deleteRecord($row['id'],"diallist");

		$row['trytime'] = $row['trytime'] + 1;
		$row['dialednumber'] = $phoneNum;
		$row['dialedby'] = $_SESSION['curuser']['extension'];
		$dialedlistid = astercrm::insertNewDialedlist($row);
		$objResponse->loadXML(getContact($phoneNum));
		$objResponse->loadXML(getPrivateDialListNumber($_SESSION['curuser']['extension']));
		if($config['system']['firstring'] == 'callee'){
			invite($phoneNum,$_SESSION['curuser']['extension'],$row['campaignid'],$dialedlistid);
		}else{
			invite($_SESSION['curuser']['extension'],$phoneNum,$row['campaignid'],$dialedlistid);
		}
	}		
	return $objResponse;
}

function workoffcheck($f=''){
	global $locate,$config;
	$objResponse = new xajaxResponse();
	if($config['system']['stop_work_verify']){
		if($f['adminname'] == '') return $objResponse;
		$admininfo = astercrm::getRecordByField('username',$f['adminname'],'astercrm_account');
		if($admininfo['password'] == $f['Workoffpwd'] && (($admininfo['usertype'] == 'groupadmin' && $admininfo['groupid'] == $_SESSION['curuser']['groupid']) || $admininfo['usertype'] == 'admin')) {
			
		}else{
			return $objResponse;
		}
	}

	$objResponse->addAssign("btnWork","value", $locate->Translate("Start work") );
	$objResponse->addEvent("btnWork", "onclick", "workctrl('start');");
	$objResponse->addAssign("btnWorkStatus","value", "" );
	$objResponse->addAssign("divWork","innerHTML", "" );
	$_SESSION['curuser']['WorkStatus'] = '';
	$objResponse->addAssign("formWorkoff", "style.visibility", "hidden");
	$objResponse->addAssign("formWorkoff", "innerHTML", '');
	$objResponse->loadXML(getPrivateDialListNumber($_SESSION['curuser']['extension']));
	
	return $objResponse;
}
# click to dial
# $phoneNum	phone to call
# $first	which phone will ring first, caller or callee

function dial($phoneNum,$first = '',$myValue,$dtmf = ''){
	global $config,$locate;

	$objResponse = new xajaxResponse();
	if(trim($myValue['curid']) > 0) $curid = trim($myValue['curid']) - 1;
	else $curid = trim($myValue['curid']);

	$call = asterEvent::checkNewCall($curid,$curid,$_SESSION['curuser']['extension'],$_SESSION['curuser']['channel'],$_SESSION['curuser']['agent']);
	
	if($call['status'] != '') {
		$objResponse->addAssign("divMsg", "style.visibility", "hidden");
		$objResponse->addScript("alert('".$locate->Translate("Exten in use")."')");
		return $objResponse->getXML();
	}
	$group_info = astercrm::getRecordByID($_SESSION['curuser']['groupid'],"astercrm_accountgroup");

	if ($group_info['incontext'] != '' ) $incontext = $group_info['incontext'];
	else $incontext = $config['system']['incontext'];
	if ($group_info['outcontext'] != '' ) $outcontext = $group_info['outcontext'];
	else $outcontext = $config['system']['outcontext'];

	if ($dtmf != '') {
		$app = 'Dial';
		$data = 'local/'.$phoneNum.'@'.$config['system']['outcontext'].'|30'.'|D'.$dtmf;
		$first = 'caller';
	}

	$myAsterisk = new Asterisk();	
	if ($first == ''){
		$first = $config['system']['firstring'];
	}

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res)
		$objResponse->addAssign("mobileStatus", "innerText", "Failed");

	if ($first == 'caller'){	//caller will ring first
		$strChannel = "local/".$_SESSION['curuser']['extension']."@".$incontext."/n";

		if ($config['system']['allow_dropcall'] == true){
			$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
								'WaitTime'=>30,
								'Exten'=>$phoneNum,
								'Context'=>$outcontext,
								'Account'=>$_SESSION['curuser']['accountcode'],
								'Variable'=>"$strVariable",
								'Priority'=>1,
								'MaxRetries'=>0,
								'CallerID'=>$phoneNum));
		}else{
			$myAsterisk->sendCall($strChannel,$phoneNum,$outcontext,1,$app,$data,30,$phoneNum,NULL,$_SESSION['curuser']['accountcode']);
		}
	}else{
		$strChannel = "local/".$phoneNum."@".$outcontext."/n";

		if ($config['system']['allow_dropcall'] == true){

/*
	coz after we use new method to capture dial event
	there's no good method to make both leg display correct clid for now
	so we comment these lines
*/
			$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
								'WaitTime'=>30,
								'Exten'=>$_SESSION['curuser']['extension'],
								'Context'=>$incontext,
								'Account'=>$_SESSION['curuser']['accountcode'],
								'Variable'=>"$strVariable",
								'Priority'=>1,
								'MaxRetries'=>0,
								'CallerID'=>$_SESSION['curuser']['extension']));
		}else{
			$myAsterisk->sendCall($strChannel,$_SESSION['curuser']['extension'],$incontext,1,$app,$data,30,$_SESSION['curuser']['extension'],NULL,NULL);
		}
	}
	//$myAsterisk->disconnect();
	$objResponse->addAssign("divMsg", "style.visibility", "hidden");
	return $objResponse->getXML();
}

/**
*  Originate src and dest extension
*  @param	src			string			extension
*  @param	dest		string			extension
*  @return	object						xajax response object
*/

function invite($src,$dest,$campaignid='',$dialedlistid=0){
	global $config,$locate;
	$src = trim($src);
	$dest = trim($dest);
	$objResponse = new xajaxResponse();	
	//$objResponse->addAssign("dialmsg", "innerHTML", "<b>".$locate->Translate("dailing")." ".$src."</b>");
	if ($src == $_SESSION['curuser']['extension']){
		$callerid = $dest;
	}else{
		$callerid = $src;
	}
	$variable = null;
	$myAsterisk = new Asterisk();
	
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res)
		$objResponse->addAssign("mobileStatus", "innerText", "Failed");
	if($campaignid != ''){
		$row_campaign = astercrm::getRecordByID($campaignid,"campaign");
		//print_r($row_campaign);exit;
		if(trim($row_campaign['nextcontext']) != '' ){
			$incontext = $row_campaign['nextcontext'];
		}elseif(trim($row_campaign['incontext']) != ''){
			$incontext = $row_campaign['incontext'];
		}else{
			$incontext = $config['system']['incontext'];
		}

		if(trim($row_campaign['firstcontext']) != '' ){
			$outcontext = $row_campaign['firstcontext'];
		}elseif(trim($row_campaign['outcontext']) != ''){
			$outcontext = $row_campaign['outcontext'];
		}else{
			$outcontext = $config['system']['outcontext'];
		}

		if($row_campaign['callerid'] == ""){
			$variable = '__CUSCID=NONE';
		}else{
			//$callerid = $row_campaign['callerid'];
			$variable .= '__CUSCID='.$row_campaign['callerid'];
		}
		$variable .= '__CAMPAIGNID='.$row_campaign['id'].'|'; #传拨号计划id给asterisk
		$variable .= '__DIALEDLISTID='.$dialedlistid.'|'; #dialedlist id给asterisk
		//if($row_campaign['inexten'] != '') $src = $row_campaign['inexten'];
		//echo $variable;exit;
	}else{
		$group_info = astercrm::getRecordByID($_SESSION['curuser']['groupid'],"astercrm_accountgroup");

		if ($group_info['incontext'] != '' ) $incontext = $group_info['incontext'];
		else $incontext = $config['system']['incontext'];
		if ($group_info['outcontext'] != '' ) $outcontext = $group_info['outcontext'];
		else $outcontext = $config['system']['outcontext'];
	}
	$strChannel = "local/".$src."@".$incontext."/n";

	if ($config['system']['allow_dropcall'] == true){
		$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
							'WaitTime'=>30,
							'Exten'=>$dest,
							'Context'=>$outcontext,
							'Account'=>$_SESSION['curuser']['accountcode'],
							'Variable'=>"$strVariable",
							'Priority'=>1,
							'MaxRetries'=>0,
							'CallerID'=>$callerid));
	}else{
		$myAsterisk->sendCall($strChannel,$dest,$outcontext,1,NULL,NULL,30,$callerid,$variable,$_SESSION['curuser']['accountcode']);
	}
	
	$objResponse->addAssign("divMsg", "style.visibility", "hidden");
	return $objResponse->getXML();
}

/**
*  hangup a channel
*  @param	channel			string		channel name
*  @return	object						xajax response object
*/


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
	//$objResponse->addAssign("btnHangup", "disabled", true);
	$objResponse->addAssign("divMsg", "style.visibility", "hidden");
	return $objResponse;
}

function getContact($callerid){
	global $db,$locate,$config;	
	$mycallerid = $callerid;
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("iptCallerid", "value", $callerid);
	if ( $config['system']['trim_prefix'] != ''){
		$prefix = split(",",$config['system']['trim_prefix']);
		foreach ($prefix as $myprefix ) {
			if (substr($mycallerid,0,1) == $myprefix){
				$mycallerid = substr($mycallerid,1);
				break;
			}
		}
	}
			

	//check contact table first
	if ($config['system']['detail_level'] == 'all')
		$row = astercrm::getContactByCallerid($mycallerid);
	else
		$row = astercrm::getContactByCallerid($mycallerid,$_SESSION['curuser']['groupid']);

	if ($row['id'] == ''){	//no match
		//	print 'no match in contact list';

		//try get customer
		if ($config['system']['detail_level'] == 'all')
			$customerid = astercrm::getCustomerByCallerid($mycallerid);
		else
			$customerid = astercrm::getCustomerByCallerid($mycallerid,$_SESSION['curuser']['groupid']);

		if ($customerid == ''){
			$objResponse->addScript('xajax_add(\'' . $callerid . '\');');
			// callerid smart match
			if ($config['system']['smart_match_remove']) {
				if ($config['system']['detail_level'] == 'all') {
					$contact_res = astercrm::getContactSmartMatch($mycallerid);
					$customer_res = astercrm::getCustomerSmartMatch($mycallerid);
				}else {
					$contact_res = astercrm::getContactSmartMatch($mycallerid,$_SESSION['curuser']['groupid']);
					$customer_res = astercrm::getCustomerSmartMatch($mycallerid,$_SESSION['curuser']['groupid']);
				}
				$smartcount = 0;
				while ($customer_res->fetchInto($row)) {
					$smartcount++;
					$smartmatch_html .= '<a href="###" onclick="xajax_showCustomer(\''.$row['id'].'\',\'customer\','.$callerid.');showMsgBySmartMatch(\'customer\',\''.$row['customer'].'\');">'.$locate->Translate("customer").':&nbsp;'.$row['customer'].'<br>'.$locate->Translate("phone").':'.$row['phone'].'</a><hr>';
				}

				while ($contact_res->fetchInto($row)) {
					$smartcount++;
					$smartmatch_html .= '<a href="###" onclick="xajax_showContact(\''.$row['id'].'\');showMsgBySmartMatch(\'contact\',\''.$row['contact'].'\');">'.$locate->Translate("contact").':&nbsp;'.$row['contact'].'<br>'.$locate->Translate("phone").':'.$row['phone'].'&nbsp;&nbsp;'.$row['phone1'].'&nbsp;&nbsp;'.$row['phone2'].'</a><hr>';
				}

				if ($smartcount < 3 ) {
					$objResponse->addAssign("smartMsgDiv", "style.height", '');
					$objResponse->addAssign("SmartMatchDiv", "style.height", '');
				}else{
					$objResponse->addAssign("smartMsgDiv", "style.height", '160px');
					$objResponse->addAssign("SmartMatchDiv", "style.height", '240px');
				}

				if ($smartcount) {
					$objResponse->addAssign("smartMsgDiv", "innerHTML", $smartmatch_html);
					$objResponse->addScript('getSmartMatchMsg();');
				}
			}
		}else{
			
			$html = Table::Top($locate->Translate("add_record"),"formDiv");  // <-- Set the title for your form.
			$html .= Customer::formAdd($callerid,$customerid);  // <-- Change by your method
			$html .= Table::Footer();
			$objResponse->addAssign("formDiv", "style.visibility", "visible");
			$objResponse->addAssign("formDiv", "innerHTML", $html);
			$objResponse->addScript('xajax_showCustomer(\''.$customerid.'\',\'customer\','.$callerid.');');
		}
	} else{ // one match

		$customerid = $row['customerid'];
		$contactid = $row['id'];
		
		$html = Table::Top($locate->Translate("add_record"),"formDiv");  // <-- Set the title for your form.
		$html .= Customer::formAdd($callerid,$customerid,$contactid);  // <-- Change by your method
		$html .= Table::Footer();
		$objResponse->addAssign("formDiv", "style.visibility", "visible");
		$objResponse->addAssign("formDiv", "innerHTML", $html);

		$objResponse->addScript('xajax_showContact(\''.$contactid.'\');');
		if ($customerid != 0)
			$objResponse->addScript('xajax_showCustomer(\''.$customerid.'\',\'customer\','.$callerid.');');

	}

	return $objResponse;
}

function displayMap($address){
	global $config,$locate;
	$objResponse = new xajaxResponse();
	if($config['google-map']['key'] == ''){
		$objResponse->addAssign("divMap","style.visibility","hidden");
		$objResponse->addScript("alert('".$locate->Translate("google_map_no_key")."')");	
		return $objResponse;
	}
	if ($address == '')
		return $objResponse;
	$map = new PhoogleMap();
	$map->setAPIKey($config['google-map']['key']);
	$map->addAddress($address);
	//$map->showMap();
	$js = $map->generateJs();

	$objResponse->addAssign("divMap","style.visibility","visible");
	//$objResponse->addScript("alert('".$js."')");
	$objResponse->addScript($js);
	return $objResponse;
}

function chanspy($exten,$spyexten,$pam = ''){
	global $config,$locate;

	if($_SESSION['curuser']['groupid'] > 0){
		$group = astercrm::getRecordByID($_SESSION['curuser']['groupid'],"astercrm_accountgroup");
		if($group['outcontext'] != ''){
			$exten .= '@'.$group['outcontext'].'/n';
		}else{
			if($config['system']['outcontext'] != ''){
				$exten .= '@'.$config['system']['outcontext'].'/n';
			}
		}
	}else{
		if($config['system']['outcontext'] != ''){
			$exten .= '@'.$config['system']['outcontext'].'/n';
		}
	}

	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		return;
	}
	$myAsterisk->chanSpy($exten,"sip/".$spyexten,$pam);
	return $objResponse;
}

function bargeInvite($srcchan,$dstchan,$exten){
	//echo $srcchan,$dstchan,$exten;exit;
	global $config,$locate;
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		return;
	}

	$group_info = astercrm::getRecordByID($_SESSION['curuser']['groupid'],"astercrm_accountgroup");

	if ($group_info['incontext'] != '' ) $incontext = $group_info['incontext'];
	else $incontext = $config['system']['incontext'];
	//if ($group_info['outcontext'] != '' ) $outcontext = $group_info['outcontext'];
	//else $outcontext = $config['system']['outcontext'];

	$strChannel = "local/".$exten."@".$incontext."/n";
	$myAsterisk->Originate($strChannel,'','',1,'meetme',$exten."|pqdx",30,$exten,NULL,NULL);

	$myAsterisk->Redirect($srcchan,$dstchan,$exten,"astercc-barge","1");

	$objResponse->addAssign("divMsg", "style.visibility", "hidden");
	return $objResponse;
}

function searchFormSubmit($searchFormValue,$numRows = null,$limit = null,$id = null,$type = null){
	global $locate,$db,$config;
	$objResponse = new xajaxResponse();
	$searchField = array();
	$searchContent = array();
	$searchContent = $searchFormValue['searchContent'];  //搜索内容 数组
	$searchField = $searchFormValue['searchField'];      //搜索条件 数组
	$searchType =  $searchFormValue['searchType'];
	$divName = "grid";
	if($type == "delete"){
		if ($config['system']['portal_display_type'] == "note"){
			$res = Customer::deleteRecord($id,'note');
		}else{
			$res = Customer::deleteRecord($id,'customer');
		}
		if ($res){
			$html = createGrid($searchFormValue['numRows'], $searchFormValue['limit'],$searchField, $searchContent, $searchField, $divName, "");
			$objResponse = new xajaxResponse();
			$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("delete_rec")); 
		}else{
			$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("rec_cannot_delete")); 
		}
	}else{
		$html = createGrid($numRows, $limit,$searchField, $searchContent, $searchField, $divName, "",$searchType);
	}
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	return $objResponse->getXML();
}

function addSchedulerDial($display='',$number,$customerid = ''){
	global $locate,$db;

	$objResponse = new xajaxResponse();
	if($display == "none"){
		$campaignflag = false;
		$html = '<td nowrap align="left">'.$locate->Translate("Scheduler Dial").'</td>
					<td align="left">'.$locate->Translate("DialNumber").' : <input type="text" id="sDialNum" name="sDialNum" size="15" maxlength="35" value="'.$number.'">';
		if($number != ''){
			$curtime = date("Y-m-d H:i:s");
			$curtime = date("Y-m-d H:i:s",strtotime("$curtime - 30 seconds"));
			$sql = "SELECT campaignid FROM dialedlist WHERE dialednumber = '".$number."' AND dialedtime > '".$curtime."' ";
			$curcampaignid = $db->getOne($sql);
			if($curcampaignid != ''){
				$campaignflag = true;
				$curcampaign = astercrm::getRecordByID($curcampaignid,'campaign');
				$curcampaign_name = $curcampaign['campaignname'];
				$html .= '&nbsp;'.$locate->Translate("campaign").' : <input type="text" value="'.$curcampaign_name.'" id="campaignname" name="campaignname" size="15" readonly><input type="hidden" value="'.$curcampaignid.'" id="curCampaignid" name="curCampaignid" size="15" readonly>';
			}
		}
		if(!$campaignflag){
			$campaign_res = astercrm::getRecordsByField("groupid",$_SESSION['curuser']['groupid'],"campaign");
			while ($campaign_res->fetchInto($campaign)) {
				$campaignoption .= '<option value="'.$campaign['id'].'">'.$campaign['campaignname'].'</option>'; 
			}
			$html .= '&nbsp;'.$locate->Translate("campaign").' : <select id="curCampaignid" name="curCampaignid" >'.$campaignoption.'</select>';
		}
		//
		$html .= '<br>'.$locate->Translate("Dialtime").' : <input type="text" name="sDialtime" id="sDialtime" size="15" value="" onfocus="displayCalendar(this,\'yyyy-mm-dd hh:ii\',this,true)">&nbsp;&nbsp;';
		if ($customerid >0 ){
			$html .= '<input type="button" value="'.$locate->Translate("Add").'" onclick="saveSchedulerDial(\''.$customerid.'\');">';
		}
		$html .= '</td>';
		$objResponse->addAssign("trAddSchedulerDial", "innerHTML", $html);
		$objResponse->addAssign("trAddSchedulerDial", "style.display", "");
	}else{
		$objResponse->addAssign("trAddSchedulerDial", "style.display", "none");
	}
	return $objResponse->getXML();
}

function saveSchedulerDial($dialnumber='',$campaignid='',$dialtime='',$customerid){
	global $locate,$db;
	$objResponse = new xajaxResponse();
	if($dialnumber == ''){
		$objResponse->addAlert($locate->Translate("Number can not be blank"));
		return $objResponse->getXML();
	}
	if($campaignid == ''){
		$objResponse->addAlert($locate->Translate("Campaign can not be blank"));
		return $objResponse->getXML();
	}
	/*
	if($dialtime == ''){
		$objResponse->addAlert($locate->Translate("Dial time can not be blank"));
		return $objResponse->getXML();
	}	
	*/
	$f['customerid'] = $customerid;
	$f['curCampaignid'] = $campaignid;
	$f['sDialNum'] = $dialnumber;
	$f['sDialtime'] = $dialtime;

	$res = astercrm::insertNewSchedulerDial($f);
	if($res){
		$objResponse->addAlert($locate->Translate("Add scheduler dial success"));
		$objResponse->addAssign("trAddSchedulerDial", "style.display", "none");
	}else{
		$objResponse->addAlert($locate->Translate("Add scheduler dial failed"));
	}
	return $objResponse->getXML();
}

function queuePaused($paused){
	global $locate,$config;

	$myAsterisk = new Asterisk();	
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	$objResponse = new xajaxResponse();
	$memberstatus = Customer::getMyMemberStatus();
	if($paused){
		while ($memberstatus->fetchInto($row)) {
			if($row['agent_status'] != 'paused'){
				sleep(1);
				$myAsterisk->queuePause('',$row['agent'],$paused);				
			}
		}
		$objResponse->addAssign("btnPause","value", $locate->Translate("Continue") );
		$objResponse->addAssign("breakStatus","value", $paused);
	}else{
		while ($memberstatus->fetchInto($row)) {
			if($row['agent_status'] == 'paused'){
				sleep(1);
				$myAsterisk->queuePause('',$row['agent'],$paused);				
			}
		}
		$objResponse->addAssign("btnPause","value", $locate->Translate("Break") );
		$objResponse->addAssign("breakStatus","value", $paused);
	}
	$objResponse->addAssign("clkPauseTime","value", date("Y-m-d H:i:s"));
	return $objResponse;
}

function updateCallresult($id,$result){
	global $locate,$config,$db;
	$objResponse = new xajaxResponse();
	$sql = "UPDATE dialedlist SET campaignresult = '$result' , resultby = '".$_SESSION['curuser']['username']."' WHERE id = $id";

	$res =& $db->query($sql);
	if ($res){
//		$objResponse->addAlert("campaign result updated");
	}else{
		$objResponse->addAlert("fail to update campaign result");
	}
	return $objResponse;
}

function setSecondCampaignResult($parentid){
	$objResponse = new xajaxResponse();
	$res = Customer::getRecordsByField('parentid',$parentid,"campaignresult");
	
	//添加option
	$n = 0;
	while ($res->fetchInto($row)) {
		$objResponse->addScript("addOption('scallresult','".$row['id']."','".$row['resultname']."');");
		if($n == 0){
			$objResponse->addAssign("callresultname","value", $row['resultname']);
			$objResponse->addAssign("spnScallresult","style.display", "");
			$n++;
		}
	}
	if($n == 0) {
		$objResponse->addAssign("spnScallresult","style.display", "none");
	}

	return $objResponse;
}

function setCallresult($id){
	$objResponse = new xajaxResponse();
	$row = astercrm::getRecordByID($id,'campaignresult');
	$objResponse->addAssign("callresultname","value", $row['resultname']);
	return $objResponse;
}

function knowledgechange($knowledgeid){
	$objResponse = new xajaxResponse();
	$html = Customer::knowledge($knowledgeid);
	//$row = astercrm::getRecordByID($knowledgeid,'knowledge');
	$objResponse->addAssign("tdcontent","innerHTML",$html);
	return $objResponse;
}

function setKnowledge(){
	global $locate,$config,$db;

	$objResponse = new xajaxResponse();
	/*知识库*/
    $knowledge = Customer::getKnowledge();
	$knowledgehtml =Table::Top($locate->Translate("knowledge"),"formKnowlagePannel");
	$knowledgehtml .= '<table><tr><td>'.$locate->Translate("knowledgetitle").':</td><td><select id="knowledgetitle" onchange="knowledgechange(this.value);"><option value="0">'.$locate->Translate("please_select").'</option>';
	while ($knowledge->fetchInto($knowledgerow)) {
           $knowledgehtml .= '<option value="'.$knowledgerow['id'].'">'.$knowledgerow['knowledgetitle'].'</option>';
	}
    $knowledgehtml .= '</select></td></tr><tr><td>'.$locate->Translate("content").':</td><td id="tdcontent"><textarea rows="20" cols="70" id="content" wrap="soft" style="overflow:auto;" readonly></textarea></td></tr></table>';
	$objResponse->addAssign("formKnowlagePannel", "innerHTML", $knowledgehtml);
	$objResponse->addAssign("formKnowlagePannel", "style.visibility", "visible");
	return $objResponse;
	/*知识库*/
}

function getPreDiallist($dialid){
	$objResponse = new xajaxResponse();
	global $db;
	
	$row = astercrm::getRecordByID($dialid,'diallist');

	if ($row['id'] == ''){

	} else {
		$phoneNum = $row['dialnumber'];
		$objResponse->loadXML(getContact($phoneNum));
		astercrm::deleteRecord($row['id'],"diallist");
		$row['dialednumber'] = $phoneNum;
		$row['dialedby'] = $_SESSION['curuser']['extension'];
		$row['trytime'] = $row['trytime'] + 1;
		astercrm::insertNewDialedlist($row);
	}

	$objResponse->loadXML(getPrivateDialListNumber($_SESSION['curuser']['extension']));

	return $objResponse;
} 

function agentWorkstat(){
	global $locate;
	$objResponse = new xajaxResponse();
	$workstat = Customer::getAgentWorkStat();

	if($workstat['billsec'] == ''){
		$billsec = '00'.$locate->Translate("hour").'00'.$locate->Translate("min").'00'.$locate->Translate("sec");
	}else{
		$billsec = $workstat['billsec'];
		$hour = intval($billsec/3600);
		if($hour < 10 ) $hour = '0'.$hour;
		$min = intval($billsec%3600/60);
		if($min < 10) $min = '0'.$min;
		$sec = $billsec%60;
		if($sec < 10) $sec = '0'.$sec;
		$billsec = $hour.$locate->Translate("hour").$min.$locate->Translate("min").$sec.$locate->Translate("sec");
	}
	$html =Table::Top($locate->Translate("work stat").'-'.date("Y-m-d"),"formAgentWordStatDiv");
	$html .= '<table><tr><td>'.$locate->Translate("total calls").':</td><td>'.$workstat['count'].'</td><tr><tr><td>'.$locate->Translate("duration").':</td><td>'.$billsec.'</td><tr></table>';
	$objResponse->addAssign("formAgentWordStatDiv", "innerHTML", $html);
	$objResponse->addAssign("formAgentWordStatDiv", "style.visibility", "visible");
	return $objResponse;
}


function popupDiallist(){
}

$xajax->processRequests();

?>

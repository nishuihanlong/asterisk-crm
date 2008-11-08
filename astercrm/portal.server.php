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
require_once ('include/asterevent.class.php');
require_once ('include/asterisk.class.php');
require_once ('astercrm.server.common.php');
require_once ("portal.common.php");
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

	$count = astercrm::getCountByField("assign",$extension,"diallist");
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
	
	$html = $locate->Translate("welcome").':'.$_SESSION['curuser']['username'].',';
	$html .= $locate->Translate("extension").$_SESSION['curuser']['extension'];
	$objResponse->addAssign("divUserMsg","innerHTML", $html );

	$objResponse->addAssign("username","value", $_SESSION['curuser']['username'] );
	$objResponse->addAssign("extension","value", $_SESSION['curuser']['extension'] );
	$objResponse->addAssign("myevents","innerHTML", $locate->Translate("waiting") );
//	$objResponse->addAssign("status","innerHTML", $locate->Translate("listening") );
	$objResponse->addAssign("extensionStatus","value", 'idle');
	$objResponse->addAssign("processingMessage","innerHTML", $locate->Translate("processing_please_wait") );
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

	$objResponse->addAssign("divCopyright","innerHTML",Common::generateCopyright($skin));

	$objResponse->addAssign("btnTransfer","disabled",true);

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
	$panelHTML = '<a href=? onclick="xajax_showRecentCdr(\'\',\'recent\');return false;">'.$locate->Translate("recentCDR").'</a>&nbsp;&nbsp;';
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

	return $objResponse;
}

/**
*	 check if there's new event happen
*
*/
function listenCalls($aFormValues){
	$objResponse = new xajaxResponse();

	if ($aFormValues['uniqueid'] == ''){
		$objResponse->loadXML(waitingCalls($aFormValues));
	} else{
		$objResponse->loadXML(incomingCalls($aFormValues));
	}
	$objResponse->addScript('setTimeout("updateEvents()", 1000);');
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
	global $db,$locate;
	$objResponse = new xajaxResponse();

	if ($myValue['direction'] != ''){
		$call = asterEvent::checkCallStatus($myValue['curid'],$myValue['uniqueid']);

		if ($call['status'] ==''){
			return $objResponse;
		} elseif ($call['status'] =='link'){

			if ($myValue['extensionStatus'] == 'link')	 //already get link event
				return $objResponse;
//			if ($call['callerChannel'] == '' or $call['calleeChannel'] == '')
//				return $objResponse;
			$status	= "link";
			$info	= $locate->Translate("talking_to").$myValue['callerid'];
			$objResponse->addAssign("callerChannel","value", $call['callerChannel'] );
			$objResponse->addAssign("calleeChannel","value", $call['calleeChannel'] );
			//if chkMonitor be checked btnMonitor must be disabled
			if ($myValue['chkMonitor'] != 'on') {
				$objResponse->addAssign("btnMonitor","disabled", false );
			}
			//$objResponse->addAssign("btnMonitor","value", $locate->Translate("start_record") );
			astercrm::events($myValue['chkMonitor'].'-chkMonitor');
			astercrm::events($myValue['btnMonitorStatus'].'-btnMonitorStatus');
			if ($myValue['chkMonitor'] == 'on' && $myValue['btnMonitorStatus'] == 'idle') 
				$objResponse->addScript("monitor();");			
			$objResponse->addAssign("btnHangup","disabled", false );
			$objResponse->addAssign("btnTransfer","disabled", false );
		} elseif ($call['status'] =='hangup'){
			if ($myValue['chkMonitor'] == 'on' && $myValue['btnMonitorStatus'] == 'recording') 
				$objResponse->addScript("monitor();");
			$status	= 'hang up';
			$info	= "Hang up call from " . $myValue['callerid'];
//			$objResponse->addScript('document.title=\'asterCrm\';');
			$objResponse->addAssign("uniqueid","value", "" );
			$objResponse->addAssign("callerid","value", "" );
			$objResponse->addAssign("callerChannel","value", '');
			$objResponse->addAssign("calleeChannel","value", '');
			$objResponse->addAssign("btnTransfer","disabled", true );

			//disable monitor
			$objResponse->addAssign("btnMonitor","disabled", true );
			$objResponse->addAssign("spanMonitorStatus","innerHTML", $locate->Translate("idle") );

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
			$objResponse->addAlert($res['Message']);
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
	$phone_html = asterEvent::checkExtensionStatus($curid);
	$objResponse->addAssign("divExtension","innerHTML", $phone_html );
	$objResponse->addScript("menuFix();");

	//	modified 2007/10/30 by solo
	//  start
	//print_r($_SESSION);exit;
	//if ($_SESSION['curuser']['channel'] == '')
		$call = asterEvent::checkNewCall($curid,$_SESSION['curuser']['extension'],$_SESSION['curuser']['channel'],$_SESSION['curuser']['agent']);
	//else
	//	$call = asterEvent::checkNewCall($curid,$_SESSION['curuser']['channel']);
	//  end

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
						//use external link
						$myurl = $config['system']['external_crm_url'];
						$myurl = preg_replace("/\%method/","dial_in",$myurl);
						$myurl = preg_replace("/\%callerid/",$call['callerid'],$myurl);
						$myurl = preg_replace("/\%calleeid/",$_SESSION['curuser']['extension'],$myurl);

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
	$typeFromSearchShowAs[] = 'like';
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

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left; textarea-layout:fixed; word-break:break-all;"';
	$attribsCols[] = 'style="text-align: left"';
//	$attribsCols[] = 'nowrap style="text-align: left"';
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

		$rowc[] = ''.$row['note'].'';

		if ($row['attitude'] != '')
			$rowc[] = '<img src="skin/default/images/'.$row['attitude'].'.gif" width="25px" height="25px" border="0" />';
		else 
			$rowc[] = '';

		$rowc[] =  str_replace(" ","<br>",$row['cretime']);
//		$rowc[] = $row['creby'];
		$rowc[] = $row['priority'];
//		$rowc[] = 'Detail';
		if ($config['system']['portal_display_type'] == "note"){
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
	
	$row = astercrm::getRecordByField("assign",$_SESSION['curuser']['extension'],"diallist");

	if ($row['id'] == ''){

	} else {
		$phoneNum = $row['dialnumber'];
		$objResponse->loadXML(getContact($phoneNum));
		astercrm::deleteRecord($row['id'],"diallist");
		$row['dialednumber'] = $phoneNum;
		$row['dialedby'] = $_SESSION['curuser']['extension'];
		astercrm::insertNewDialedlist($row);
	}

	$objResponse->loadXML(getPrivateDialListNumber($_SESSION['curuser']['extension']));

	return $objResponse;
}

function checkworkexten() {
	global $db,$locate;
	$objResponse = new xajaxResponse();
	if($_SESSION['curuser']['channel'] == ''){
		$row = astercrm::getRecordByField("peer","SIP/".$_SESSION['curuser']['extension'],"peerstatus");
	}else{
		$row = astercrm::getRecordByField("peer",$_SESSION['curuser']['channel'],"peerstatus");
	}
	if($row['status'] != 'reachable') {
		$objResponse->addAssign("workingextenstatus","value", "it seems ur extension is not avaiable, are u sure to procress?" );
	}else{
		$objResponse->addAssign("workingextenstatus","value", "ok" );
	}

	return $objResponse;
}

function workstart() {
	global $db,$locate,$config;
	$objResponse = new xajaxResponse();

	$row = astercrm::getRecordByField("assign",$_SESSION['curuser']['extension'],"diallist");
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
		astercrm::insertNewDialedlist($row);

		$objResponse->loadXML(getPrivateDialListNumber($_SESSION['curuser']['extension']));
		invite($_SESSION['curuser']['extension'],$phoneNum,$row['campaignid']);
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
	if ($_SESSION['curuser']['channel'] == '')
		$call = asterEvent::checkNewCall($curid,$_SESSION['curuser']['extension']);
	else
		$call = asterEvent::checkNewCall($curid,$_SESSION['curuser']['channel']);

	if($call['status'] != '') {
		$objResponse->addAssign("divMsg", "style.visibility", "hidden");
		$objResponse->addScript("alert('".$locate->Translate("Exten in use")."')");
		return $objResponse->getXML();
	}

	if ($dtmf != '') {
		$app = 'Dial';
		$data = 'LOCAL/'.$phoneNum.'@'.$config['system']['outcontext'].'|30'.'|D'.$dtmf;
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
		$strChannel = "Local/".$_SESSION['curuser']['extension']."@".$config['system']['incontext']."/n";

		if ($config['system']['allow_dropcall'] == true){
			$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
								'WaitTime'=>30,
								'Exten'=>$phoneNum,
								'Context'=>$config['system']['outcontext'],
								'Account'=>$_SESSION['curuser']['accountcode'],
								'Variable'=>"$strVariable",
								'Priority'=>1,
								'MaxRetries'=>0,
								'CallerID'=>$phoneNum));
		}else{
			$myAsterisk->sendCall($strChannel,$phoneNum,$config['system']['outcontext'],1,$app,$data,30,$phoneNum,NULL,$_SESSION['curuser']['accountcode']);
		}
	}else{
		$strChannel = "Local/".$phoneNum."@".$config['system']['outcontext']."/n";

		if ($config['system']['allow_dropcall'] == true){

/*
	coz after we use new method to capture dial event
	there's no good method to make both leg display correct clid for now
	so we comment these lines
*/
			$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
								'WaitTime'=>30,
								'Exten'=>$_SESSION['curuser']['extension'],
								'Context'=>$config['system']['incontext'],
								'Account'=>$_SESSION['curuser']['accountcode'],
								'Variable'=>"$strVariable",
								'Priority'=>1,
								'MaxRetries'=>0,
								'CallerID'=>$_SESSION['curuser']['extension']));
		}else{
			$myAsterisk->sendCall($strChannel,$_SESSION['curuser']['extension'],$config['system']['incontext'],1,$app,$data,30,$_SESSION['curuser']['extension'],NULL,NULL);
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

function invite($src,$dest,$campaignid=''){
	global $config,$locate;
	$src = trim($src);
	$dest = trim($dest);
	$objResponse = new xajaxResponse();	
	//$objResponse->addAssign("dialmsg", "innerHTML", "<b>".$locate->Translate("dailing")." ".$src."</b>");
	if ($src == $_SESSION['curuser']['extension'])
		$callerid = $dest;
	else //if ($dest == $_SESSION['curuser']['extension'])
		$callerid = $src;
//	else
//		return $objResponse;
	
	$myAsterisk = new Asterisk();
	
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res)
		$objResponse->addAssign("mobileStatus", "innerText", "Failed");
	if($campaignid != ''){
		$row_campaign = astercrm::getRecordByID($campaignid,"campaign");
		if(trim($row_campaign['incontext']) != '' ) $incontext = $row_campaign['incontext'];
		else $incontext = $config['system']['incontext'];
		if(trim($row_campaign['outcontext']) != '' ) $outcontext = $row_campaign['outcontext'];
		else $outcontext = $config['system']['outcontext'];
		//if($row_campaign['inexten'] != '') $src = $row_campaign['inexten'];
	}else{
		$incontext = $config['system']['incontext'];
		$outcontext = $config['system']['outcontext'];
	}
	$strChannel = "Local/".$src."@".$incontext."/n";

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
		$myAsterisk->sendCall($strChannel,$dest,$outcontext,1,NULL,NULL,30,$callerid,NULL,$_SESSION['curuser']['accountcode']);
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
		}else{
			
			$html = Table::Top($locate->Translate("add_record"),"formDiv");  // <-- Set the title for your form.
			$html .= Customer::formAdd($callerid,$customerid);  // <-- Change by your method
			$html .= Table::Footer();
			$objResponse->addAssign("formDiv", "style.visibility", "visible");
			$objResponse->addAssign("formDiv", "innerHTML", $html);
			$objResponse->addScript('xajax_showCustomer(\''.$customerid.'\');');
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
			$objResponse->addScript('xajax_showCustomer(\''.$customerid.'\');');

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
	$js = $map->generateJs();

	$objResponse->addAssign("divMap","style.visibility","visible");
	$objResponse->addScript("alert('".$js."')");
	$objResponse->addScript($js);
	return $objResponse;
}

function chanspy($exten,$spyexten){
	global $config,$locate;
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		return;
	}
	$myAsterisk->chanSpy($exten,"SIP/".$spyexten);
	//$objResponse->addAlert($exten);
	//$objResponse->addAlert($spyexten);
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

$xajax->processRequests();

?>
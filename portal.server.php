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
	confirmCustomer
	confirmContact
	addWithPhoneNumber


* Revision 0.045  2007/10/18 14:19:00  modified by solo
* Desc: comment added

* Revision 0.045  2007/10/17 20:55:00  modified by solo
* Desc: change callerid match method to like '%callerid'
* 描述: 将电话号码匹配方式修改为前端模糊

* Revision 0.045  2007/10/17 12:55:00  modified by solo
* Desc: fix bugs in search, ordering

********************************************************************************/

require_once ("db_connect.php");
require_once ('include/asterevent.class.php');
require_once ('include/asterisk.class.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/common.class.php');
require_once ('portal.grid.inc.php');
require_once ('astercrm.server.common.php');
require_once ("portal.common.php");

function showDetail($recordId){
	$objResponse = new xajaxResponse();
	$objResponse->addScript('xajax_showContact(\''.$recordId.'\',\'note\');');
	$objResponse->addScript('xajax_showCustomer(\''.$recordId.'\',\'note\');');
	return $objResponse;
}

function getPrivateDialListNumber($extension = null){
	global $locate,$db;
	$objResponse = new xajaxResponse();
	// get dial list
	if ($extension == null)
		$query = 'SELECT COUNT(*) FROM diallist';
	else
		$query = 'SELECT COUNT(*) FROM diallist WHERE assign = "'.$extension.'"';

	$res =& $db->getOne($query);

	if ($res == 0 || $res == "0"){
		$objResponse->addAssign("divDialList", "innerHTML", $locate->Translate("no_dial_list"));
	} else{
		// add div
		$objResponse->addRemove("spanDialListRecords");
		$objResponse->addRemove("btnGetAPhoneNumber");

		$objResponse->addCreate("divDialList", "div", "spanDialListRecords");
		$objResponse->addAssign("spanDialListRecords", "innerHTML", $locate->Translate("records_in_dial_list_table").$res);

		// add start campaign button
		$objResponse->addCreateInput("divDialList", "button", "btnGetAPhoneNumber", "btnGetAPhoneNumber");
		$objResponse->addAssign("btnGetAPhoneNumber", "value", $locate->Translate("get_a_phone_number"));
		$objResponse->addEvent("btnGetAPhoneNumber", "onclick", "btnGetAPhoneNumberOnClick();");
	}

	return $objResponse;
}

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
	$objResponse->addAssign("btnDial","value", $locate->Translate("dial") );
	$objResponse->addAssign("processingMessage","innerHTML", $locate->Translate("processing_please_wait") );
	$objResponse->addAssign("spanMonitorSetting","innerText", $locate->Translate("always_record_when_connected") );
	$objResponse->addAssign("spanMonitor","innerText", $locate->Translate("monitor") );

	$objResponse->addAssign("spanMonitorStatus","innerText", $locate->Translate("idle") );
	$objResponse->addAssign("btnMonitorStatus","value", "idle" );
	$objResponse->addAssign("btnMonitor","value", $locate->Translate("start_record") );
	$objResponse->addAssign("btnMonitor","disabled", true );
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	$objResponse->loadXML(getPrivateDialListNumber($_SESSION['curuser']['extension']));

//	echo $_SESSION['curuser']['usertype'];
//	exit;
	if ($_SESSION['curuser']['usertype'] == "admin"){
		$panelHTML = '<a href=# onclick="this.href=\'managerportal.php\'">'.$locate->Translate("manager").'</a>&nbsp;';
	}
	$panelHTML .="<a href='login.php'>".$locate->Translate("logout")."</a>";
	$objResponse->addAssign("divPanel","innerHTML", $panelHTML);

	if ($config['system']['enable_external_crm'] == false){
		$objResponse->addClear("divCrm","innerHTML");
		$objResponse->addIncludeScript("js/astercrm.js");
		$objResponse->addIncludeScript("js/ajax.js");
		$objResponse->addIncludeScript("js/ajax-dynamic-list.js");


		$mycrm = '
					<br><br><br><br><br><br>
					<br><br><br><br><br><br>
					<br><br><br><br><br><br>
					<table width="100%" border="0" style="background: #F9F9F9; padding: 0px;">
					<tr>
						<td style="padding: 0px;">
							<fieldset>
							<div id="formDiv" class="formDiv"></div>
							<div id="formCustomerInfo" class="formCustomerInfo"></div>
							<div id="formContactInfo" class="formContactInfo"></div>
							<div id="formNoteInfo" class="formNoteInfo"></div>
							<div id="formEditInfo" class="formEditInfo"></div>
							<div id="grid" align="center"> </div>
						</td>
					</tr>
					</table>';
		$objResponse->addAppend("divCrm","innerHTML", $mycrm );
		$objResponse->addScript("xajax_showGrid(0,".ROWSXPAGE.",'','','')");
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

	return $objResponse;
}

function listenCalls($aFormValues){
	if ($aFormValues['uniqueid'] == ''){
		return waitingCalls($aFormValues);
	} else{
		return incomingCalls($aFormValues);
	}
}

//transfer
function transfer($aFormValues){
	global $config;
	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	$objResponse = new xajaxResponse();

	if ($aFormValues['direction'] == 'in')		
		$myAsterisk->Redirect($aFormValues['callerChannel'],'',$aFormValues['sltExten'],$config['system']['outcontext'],1);
	else
		$myAsterisk->Redirect($aFormValues['calleeChannel'],'',$aFormValues['sltExten'],$config['system']['outcontext'],1);
	$myAsterisk->disconnect();
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
			if ($myValue['extensionStatus'] == 'link')
				return $objResponse;
//			if ($call['callerChannel'] == '' or $call['calleeChannel'] == '')
//				return $objResponse;
			$status	= "link";
			$info	= $locate->Translate("talking_to").$myValue['callerid'];
			$objResponse->addAssign("callerChannel","value", $call['callerChannel'] );
			$objResponse->addAssign("calleeChannel","value", $call['calleeChannel'] );
//			if  ($call['callerChannel'] != '' and $call['calleeChannel']!=''){
				//enable monitor
				$objResponse->addAssign("btnMonitor","disabled", false );
				$objResponse->addAssign("btnMonitor","value", $locate->Translate("start_record") );
				if ($myValue['chkMonitor'] == 'on') //always recording
					$objResponse->addScript("monitor();");
//			}
			
			if ($myValue['sltExten'] == ''){
				$transfer = '
							<SELECT id="sltExten" name="sltExten">
							';
				foreach ($_SESSION['curuser']['extensions'] as $extension){
					$transfer .= '
									<option value="'.trim($extension).'">'.trim($extension).'</option>
								';
				}

				$transfer .= '
							</SELECT>
							<INPUT type="BUTTON" value="'.$locate->Translate(spanTransfer).'" onclick="xajax_transfer(xajax.getFormValues(\'myForm\'));return false;">
							';
				$objResponse->addAssign(spanTransfer,"innerHTML", $transfer );
			}

		} elseif ($call['status'] =='hangup'){
			$status	= 'hang up';
			$info	= "Hang up call from " . $myValue['callerid'];
//			$objResponse->addScript('document.title=\'asterCrm\';');
			$objResponse->addAssign("uniqueid","value", "" );
			$objResponse->addAssign("callerid","value", "" );
			$objResponse->addAssign("callerChannel","value", '');
			$objResponse->addAssign("calleeChannel","value", '');
			$objResponse->addAssign(spanTransfer,"innerHTML", '');

			//disable monitor
			$objResponse->addAssign("btnMonitor","disabled", true );
			$objResponse->addAssign("spanMonitorStatus","innerHTML", $locate->Translate("idle") );


		}
//		$objResponse->addAssign("status","innerHTML", $status );
		$objResponse->addAssign("extensionStatus","value", $status );
		$objResponse->addAssign("myevents","innerHTML", $info );
	}

	return $objResponse;
}



function waitingCalls($myValue){
	global $db,$config,$locate;
	$objResponse = new xajaxResponse();
	$curid = trim($myValue['curid']);

	$phone_html = asterEvent::checkExtensionStatus($curid);
	
	$objResponse->addAssign("divExtension","innerHTML", $phone_html );

	$call = asterEvent::checkNewCall($curid,$_SESSION['curuser']['extension']);


	if ($call['status'] == ''){
		$title	= $locate->Translate("waiting");
		$status	= 'idle';
		$call['curid'] = $curid;
		$direction	= '';
		$info	= $locate->Translate("stand_by");
	} elseif ($call['status'] == 'incoming'){	//incoming calls here
		$title	= $call['callerid'];
		$stauts	= 'ringing';
		$direction	= 'in';
		$info	= $locate->Translate("incoming"). ' ' . $call['callerid'];
		if ($config['system']['pop_up_when_dial_in']){
			if (strlen($call['callerid']) > $config['system']['phone_number_length'] && $call['callerid'] != '<unknown>'){
				if ($myValue['popup'] == 'yes'){
					if ($config['system']['enable_external_crm'] == false){
							$objResponse->loadXML(getContact($call['callerid']));
							if ( $config['system']['maximize_when_pop_up'] == true ){
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
		if ($config['system']['pop_up_when_dial_out']){
			if (strlen($call['callerid']) > $config['system']['phone_number_length']){
				if ($myValue['popup'] == 'yes'){
					if ($config['system']['enable_external_crm'] == false ){
							$objResponse->loadXML(getContact($call['callerid']));
							if ( $config['system']['maximize_when_pop_up'] == true ){
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
	$objResponse->addAssign("uniqueid","value", $call['uniqueid'] );
	$objResponse->addAssign("callerid","value", $call['callerid'] );
	$objResponse->addAssign("curid","value", $call['curid'] );
	$objResponse->addAssign("direction","value", $direction );
	$objResponse->addAssign("myevents","innerHTML", $info);

	return $objResponse;
}

//	create grid
function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	global $locate,$config;

	$_SESSION['ordering'] = $ordering;

	$numRows =& Customer::getNumRows($filter,$content);
	
	if(($filter == null) or ($content == null)){
		$arreglo =& Customer::getAllRecords($start,$limit,$order);
	}else{
		$arreglo =& Customer::getRecordsFiltered($start, $limit, $filter, $content, $order);	
	}

	// Editable zone

	// Databse Table: fields
	$fields = array();
	$fields[] = 'customer';
	$fields[] = 'category';
	$fields[] = 'contact';
	$fields[] = 'note';
	$fields[] = 'cretime';
	$fields[] = 'creby';
	$fields[] = 'priority';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("customer_name");//"Customer Name";
	$headers[] = $locate->Translate("category");//"Category";
	$headers[] = $locate->Translate("contact");//"Contact";
	$headers[] = $locate->Translate("note");//"Note";
	$headers[] = $locate->Translate("create_time");//"Create Time";
	$headers[] = $locate->Translate("create_by");//"Create By";
	$headers[] = "P";
//	$headers[] = "D";

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="7%"';
	$attribsHeader[] = 'width="39%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="8%"';
	$attribsHeader[] = 'width="4%"';
//	$attribsHeader[] = 'width="5%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'nowrap style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","customer","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","category","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","contact","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","note","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","cretime","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","creby","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","priority","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'customer';
	$fieldsFromSearch[] = 'category';
	$fieldsFromSearch[] = 'contact.contact';
	$fieldsFromSearch[] = 'note';
	$fieldsFromSearch[] = 'priority';
	$fieldsFromSearch[] = 'note.cretime';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("customer_name");
	$fieldsFromSearchShowAs[] = $locate->Translate("category");
	$fieldsFromSearchShowAs[] = $locate->Translate("contact");
	$fieldsFromSearchShowAs[] = $locate->Translate("note");
	$fieldsFromSearchShowAs[] = $locate->Translate("priority");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_time");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearch("note",$fieldsFromSearch,$fieldsFromSearchShowAs);


	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['customer'];
		$rowc[] = $row['category'];
		$rowc[] = $row['contact'];
		$rowc[] = $row['note'];
		$rowc[] = $row['cretime'];
		$rowc[] = $row['creby'];
		$rowc[] = $row['priority'];
//		$rowc[] = 'Detail';
		$table->addRow("note",$rowc,1,1,1,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}



// 判断是否存在$customerName, 如果存在就显示
function confirmCustomer($customerName,$callerID = null,$contactID){
	global $locate;
	$objResponse = new xajaxResponse();
	if (trim($customerName) == '')
		return $objResponse;

	$customerID = Customer::checkValues("customer","customer",$customerName); 
	if ($customerID && $customerID !=0){//存在
		$html = Table::Top($locate->Translate("add_record"),"formDiv");
		$html .= Customer::formAdd($callerID,$customerID,$contactID);
		$html .= Table::Footer();
		$objResponse->addAssign("formDiv", "style.visibility", "visible");
		$objResponse->addAssign("formDiv", "innerHTML", $html);
		$objResponse->addScript("xajax_showCustomer($customerID)");
	} //else
	//		$objResponse->addAlert("不存在" );

	return $objResponse;
}

//判断是否存在$contactName
function confirmContact($contactName,$customerID,$callerID){
	global $locate;

	$objResponse = new xajaxResponse();
	$contactID = Customer::checkValues("contact","contact",$contactName,"string","customerid",$customerID,"int"); 
	if ($contactID){//存在

		$html = Table::Top($locate->Translate("add_record"),"formDiv"); 
		$html .= Customer::formAdd($callerID,$customerID,$contactID);
		$html .= Table::Footer();
		$objResponse->addAssign("formDiv", "style.visibility", "visible");
		$objResponse->addAssign("formDiv", "innerHTML", $html);
		//显示customer信息
		if ($customerID !=0)
			$objResponse->addScript("xajax_showCustomer($customerID)");

		//显示contact信息
		$objResponse->addScript("xajax_showContact($contactID)");

	} 

	return $objResponse;
}


function addWithPhoneNumber(){
	$objResponse = new xajaxResponse();
	global $db;
	//get a phone number from database
	
	$query = '
		SELECT id,dialnumber 
		FROM diallist
		WHERE assign = '.$_SESSION['curuser']['extension'].'
		ORDER BY id DESC
		LIMIT 0,1 
		 ' ;
	
	$res = $db->query($query);
	if ($res->numRows() == 0){

	} else {
		$res->fetchInto($list);
		$phoneNum = $list['dialnumber'];
		$objResponse->loadXML(getContact($phoneNum));
		$id = $list['id'];
		$query = 'DELETE FROM diallist WHERE id ='.$id ;
		$res = $db->query($query);

		$query = 'INSERT INTO dialedlist (dialnumber,dialedby,dialedtime) VALUES ("'.$phoneNum.'","'.$_SESSION['curuser']['extension'].'",now())';
		$res = $db->query($query);
	}

	$objResponse->loadXML(getPrivateDialListNumber($_SESSION['curuser']['extension']));

	return $objResponse;
}

# click to dial
# $phoneNum	phone to call
# $first	which phone will ring first, caller or callee

function dial($phoneNum,$first = 'caller'){
	global $config;
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res)
		$objResponse->addAssign("mobileStatus", "innerText", "Failed");


	if ($config['system']['firstring'] == 'caller'){	//caller will ring first
		$strChannel = "Local/".$_SESSION['curuser']['extension']."@".$config['system']['incontext']."/n";

		if ($config['system']['allow_dropcall'] == true){
			$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
								'WaitTime'=>30,
								'Exten'=>$phoneNum,
								'Context'=>$config['system']['outcontext'],
								'Variable'=>"$strVariable",
								'Priority'=>1,
								'MaxRetries'=>0,
								'CallerID'=>$phoneNum));
		}else{
			$myAsterisk->Originate($strChannel,$phoneNum,$config['system']['outcontext'],1,NULL,NULL,30,$phoneNum,NULL,NULL);
		}
	}else{
		$strChannel = "Local/".$phoneNum."@".$config['system']['outcontext']."/n";

		if ($config['system']['allow_dropcall'] == true){

			$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
								'WaitTime'=>30,
								'Exten'=>$_SESSION['curuser']['extension'],
								'Context'=>$config['system']['incontext'],
								'Variable'=>"$strVariable",
								'Priority'=>1,
								'MaxRetries'=>0,
								'CallerID'=>$phoneNum));
		}else{
			$myAsterisk->Originate($strChannel,$_SESSION['curuser']['extension'],$config['system']['incotext'],1,NULL,NULL,30,$phoneNum,NULL,NULL);
		}
	}
	$myAsterisk->disconnect();
	return $objResponse->getXML();
}

function monitor($channel,$action = 'start'){
	global $config,$locate;
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		return;
	}

	if ($action == 'start'){
		$filename = str_replace("/","-",$channel);
		$filename = $config['asterisk']['monitorpath'].$channel;
		$filename .= '.'.time();

		$format = $config['asterisk']['monitorformat'];
		$mix = true;
		$res = $myAsterisk->Monitor($channel,$filename,$format,$mix);
		if ($res['Response'] == 'Error'){
			$objResponse->addAlert($res['Message']);
			return $objResponse;
		}
		$objResponse->addAssign("spanMonitorStatus","innerText", $locate->Translate("recording") );
		$objResponse->addAssign("btnMonitorStatus","value", "recording" );

		$objResponse->addAssign("btnMonitor","value", $locate->Translate("stop_record") );
	}else{
		$myAsterisk->StopMontor($channel);

		$objResponse->addAssign("spanMonitorStatus","innerText", $locate->Translate("idle") );
		$objResponse->addAssign("btnMonitorStatus","value", "idle" );

		$objResponse->addAssign("btnMonitor","value", $locate->Translate("start_record") );
	}

	$objResponse->addAssign("btnMonitor","disabled", false );
	return $objResponse;
}

function getContact($callerid){
	global $db,$locate,$config;	
	$mycallerid = $callerid;
	$objResponse = new xajaxResponse();

	if ( $config['system']['trim_zreo'] == true){
		while (substr($mycallerid,0,1) == '0'){
			$mycallerid = substr($mycallerid,1);
		}
	}
			

	//check contact table first
	$query = '
			SELECT id,customerid 
			FROM contact
			WHERE phone LIKE \'%'. $mycallerid . '\'
			OR phone1 LIKE \'%'. $mycallerid . '\'
			OR phone2 LIKE \'%'. $mycallerid . '\'
			OR mobile LIKE \'%'. $mycallerid . '\'
			 ' ;
//	print $query;
	$res = $db->query($query);

	if ($res->numRows() == 0){	//no match
		//try get customer
		$customerid = Customer::getCustomerByCallerid($mycallerid);
//		print 'no match in contact';
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
	} elseif ($res->numRows() == 1) { // one match

		$res->fetchInto($list);
		$customerid = $list['customerid'];
		$contactid = $list['id'];
		
		$html = Table::Top($locate->Translate("add_record"),"formDiv");  // <-- Set the title for your form.
		$html .= Customer::formAdd($callerid,$customerid,$contactid);  // <-- Change by your method
		$html .= Table::Footer();
		$objResponse->addAssign("formDiv", "style.visibility", "visible");
		$objResponse->addAssign("formDiv", "innerHTML", $html);

		$objResponse->addScript('xajax_showContact(\''.$contactid.'\');');
		if ($customerid != 0)
			$objResponse->addScript('xajax_showCustomer(\''.$customerid.'\');');

	}else {	//match a lot records... [only display the first one for now]
		$res->fetchInto($list);
		$customerid = $list['customerid'];
		$contactid = $list['id'];
		
		$html = Table::Top($locate->Translate("add_record"),"formDiv");  // <-- Set the title for your form.
		$html .= Customer::formAdd($callerid,$customerid,$contactid);  // <-- Change by your method
		$html .= Table::Footer();
		$objResponse->addAssign("formDiv", "style.visibility", "visible");
		$objResponse->addAssign("formDiv", "innerHTML", $html);

		$objResponse->addScript('xajax_showContact(\''.$contactid.'\');');
		$objResponse->addScript('xajax_showCustomer(\''.$customerid.'\');');
	}

	return $objResponse;
}

$xajax->processRequests();

?>
<?php
// Tanslate to chinese by Donnie
require_once ("manager.common.php");
require_once ("db_connect.php");
require_once ('grid.account.inc.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('asterevent.class.php');
require_once ('include/asterisk.php');

function init(){
	global $locate;
	$objResponse = new xajaxResponse();
	$html .= "<a href=# onclick='showAccounts();return false;'>".$locate->Translate("extension_manager")."</a><br>";

	$html .= "<a href=# onclick='showStatus();return false;'>".$locate->Translate("system_monitor")."</a><br>";

	$html .= "<a href=# onclick='showChannelsInfo();return false;'>".$locate->Translate("active_channels")."</a><br>";

	$html .= "<a href=# onclick='showPredictiveDialer();return false;'>".$locate->Translate("predictive_dialer")."</a><br>";
	  
	$html .= "<a href=# onclick=\"self.location.href='portal.php';return false;\">".$locate->Translate("back")."</a><br>";

	$objResponse->addAssign("panelDiv", "innerHTML", $html);
	$objResponse->addAssign("msgChannelsInfo", "value", $locate->Translate("msgChannelsInfo"));

	return $objResponse;
}

function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	$html .= "<br><br><br><br>";
	$html .= createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);

	return $objResponse;
}

function showStatus(){
	$objResponse = new xajaxResponse();
	$html .= "<br><br><br><br>";
	$html .= asterEvent::checkExtensionStatus(0,'table');
	$objResponse->addAssign("grid", "innerHTML", $html);
	return $objResponse;
}

function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	global $locate;
	$_SESSION['ordering'] = $ordering;
	
	if(($filter == null) or ($content == null)){
		
		$numRows =& Account::getNumRows();
		$arreglo =& Account::getAllRecords($start,$limit,$order);
	}else{
		
		$numRows =& Account::getNumRows($filter, $content);
		$arreglo =& Account::getRecordsFiltered($start, $limit, $filter, $content, $order);	
	}

	// Editable zone

	// Databse Table: fields
	$fields = array();
	$fields[] = 'username';
	$fields[] = 'password';
	$fields[] = 'extension';
	$fields[] = 'extensions';
	$fields[] = 'usertype';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("username");
	$headers[] = $locate->Translate("password");
	$headers[] = $locate->Translate("extension");
	$headers[] = $locate->Translate("extensions");
	$headers[] = $locate->Translate("usertype").'&nbsp;'.$locate->Translate("usertype_note");

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="35%"';
	$attribsHeader[] = 'width="20%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","username","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","password","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","extension","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","extensions","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","extensions","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'username';
	$fieldsFromSearch[] = 'passowrd';
	$fieldsFromSearch[] = 'extension';
	$fieldsFromSearch[] = 'extensions';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("username");
	$fieldsFromSearchShowAs[] = $locate->Translate("password");
	$fieldsFromSearchShowAs[] = $locate->Translate("extension");
	$fieldsFromSearchShowAs[] = $locate->Translate("extensions");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearch("account",$fieldsFromSearch,$fieldsFromSearchShowAs);


	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['username'];
		$rowc[] = $row['password'];
		$rowc[] = $row['extension'];
		$rowc[] = $row['extensions'];
		$rowc[] = $row['usertype'];
		$table->addRow("account",$rowc,1,1,1,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}

function add(){
   // Edit zone
	global $locate;
	$objResponse = new xajaxResponse();
	$html = Table::Top($locate->Translate("adding_account"),"formDiv");  // <-- Set the title for your form.
	$html .= Account::formAdd();  // <-- Change by your method
	// End edit zone
	$html .= Table::Footer();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	
	return $objResponse->getXML();
}

function save($f){
	global $locate;
	$objResponse = new xajaxResponse();

	$message = Account::checkAllData($f,1); // <-- Change by your method

	$respOk = Account::insertNewAccount($f); // add a new account
	if ($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("add_note"));
		$objResponse->addAssign("formDiv", "style.visibility", "hidden");
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("add_account"));
	}else{
		$objResponse->addAlert($message);
	}
	return $objResponse->getXML();
	
}

function update($f){
	global $locate;
	$objResponse = new xajaxResponse();

	$respOk = Account::updateRecord($f);

	if($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("update_rec"));
		$objResponse->addAssign("formDiv", "style.visibility", "hidden");
	}else{
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("rec_cannot_update"));
	}
	
	return $objResponse->getXML();
}

function delete($id = null){
	global $locate;
	$respOk = Account::deleteRecord($id); 				// <-- Change by your method
	$objResponse = new xajaxResponse();
	if($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("delete_rec")); // <-- Change by your leyend
	}else{
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("rec_cannot_delete"));
	}
	return $objResponse->getXML();
}

function edit($id = null){

	$lable = "Editing record";

	// Edit zone
	$html = Table::Top($lable,"formDiv"); 	// <-- Set the title for your form.
	$html .= Account::formEdit($id, $type); 			// <-- Change by your method
	$html .= Table::Footer();
	// End edit zone

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	return $objResponse->getXML();
}

<<<<<<< .mine
//闇�瑕佸拰asterisk鍦ㄤ竴鍙拌澶囦笂杩愯
function preDialer($phoneNum){
	global $config,$db;
=======
function showChannelsInfo(){
/*
	global $config;
>>>>>>> .r70
	$myAsterisk = new Asterisk();

//	$myAsterisk->config['asmanager'] = $config['asterisk'];
//	$res = $myAsterisk->connect();

	$channels = $myAsterisk->Command("show channels");	
	$sip_channels = $myAsterisk->Command("sip show channels");
	print $channels['data'];
	$objResponse = new xajaxResponse();
<<<<<<< .mine

//	if (!$res){
//		$objResponse->addAlert("connect failed");
//		return $objResponse;
//	}
//	$phoneNum = '84350822';


	$actionid = md5(uniqid(""));
	$query = '
		INSERT INTO dialresult SET
		phoneid = \''.$id.'\',
		phonenumber = \''.$phoneNum.'\',
		dialstatus = \'begin\',
		actionid = \''.$actionid.'\'
		';
	$res = $db->query($query);

=======
	return $objResponse;
*/
	$channels = split(chr(13),getChannels());
	$channels = split(chr(10),$channels[1]);
	//trim the first two records and the last three records

//	array_pop($channels); 
	array_pop($channels); 
	$activeCalls = array_pop($channels); 
	$activeChannels = array_pop($channels); 

	array_shift($channels); 
	array_shift($channels); 
//	print_r($channels);
	
//	$channels = implode("<BR \>",$myChannels);
	$sipChannels = split(chr(13),getSipChannels());
	$sipChannels = split(chr(10),$sipChannels[1]);
	//trim the first two records and the last three records
//	array_pop($sipChannels); 
	array_pop($sipChannels); 
	$activeSipCalls=array_pop($sipChannels); 
	array_shift($sipChannels); 
	array_shift($sipChannels); 
//	print_r($sipChannels);

	//get channels
	$myInfo[] = Array("<b>Account</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;","<b>Dialed Number</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;","<b>Call Type</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;","<b>Call Status</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;","<b>Trunk</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;","<b>Start Time</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
	foreach ($channels as $channel ){
		if (strstr($channel," Dial(")) {
			$myItem = split("_",implode("_",array_filter(split(" ",$channel))));
//			print_r($myItem);
//			exit;
//			$myChannels[] = $channel;
			//get sip account from myItem[0]
			//print $myItem[0];
			//exit;
			$mySipChannel = $myItem[0];						// 0
			$sipAccount = split("-",$mySipChannel);
			$sipAccount = split("\/",$sipAccount[0]);
			$mySipAccount = $sipAccount[1];					// 1
//			print_r($sipAccount);
//			exit;
			$dialedNumber = split("\@",$myItem[1]);
			$myDialedNumber = $dialedNumber[0];				// 2
			$myCallType = $dialedNumber[1];					// 3
			if (strstr($myCallType,"call")){
				$myCallType = "Call Shop";
			} elseif (strstr($myCallType,"a2b")){
				$myCallType = "Callback";
			}
			$myCallStatus = $myItem[2];						// 4
//			if ($myCallStatus == "Up")
//				print_r($myItem);
			$trunk = $myItem[3];
//			preg_match("/Dial\((.+)|(.+)", $trunk, $matches);
			$trunk = split("\(",$trunk);
			$trunk = split("\/",$trunk[1]);
			$myTrunk = $trunk[0]."/".$trunk[1];				// 5
//			print $myTrunk;
//			print_r($matches);
//			exit;
			
			//$myInfo[] = $mySipChannel."&nbsp;&nbsp;&nbsp;&nbsp;".$mySipAccount."&nbsp;&nbsp;&nbsp;&nbsp;".$myDialedNumber."&nbsp;&nbsp;&nbsp;&nbsp;".$myCallType."&nbsp;&nbsp;&nbsp;&nbsp;".$myCallStatus."&nbsp;&nbsp;&nbsp;&nbsp;".$myTrunk;
//			do {
//				$mySipChannel = array_shift($sipChannels);
//				$myItem = split("_",implode("_",array_filter(split(" ",$mySipChannel))));
//				if (is_numeric($myItem[1])){
//					if (trim($myItem[1]) == trim($mySipAccount) )
//						$myInfo[] = "&nbsp;&nbsp;&nbsp;&nbsp;".$mySipAccount."&nbsp;&nbsp;&nbsp;&nbsp;".$myDialedNumber."&nbsp;&nbsp;&nbsp;&nbsp;".$myCallType."&nbsp;&nbsp;&nbsp;&nbsp;".$myCallStatus."&nbsp;&nbsp;&nbsp;&nbsp;".$myTrunk."&nbsp;&nbsp;&nbsp;&nbsp;".$myItem[0]."&nbsp;&nbsp;&nbsp;&nbsp;".$myItem[1];
			$timestamp = getTimeStamp($mySipChannel);
			$myInfo[] = Array($mySipAccount,$myDialedNumber,$myCallType,$myCallStatus,$myTrunk,$timestamp);
//			$myInfo[] = "&nbsp;&nbsp;&nbsp;&nbsp;".$mySipAccount."&nbsp;&nbsp;&nbsp;&nbsp;".$myDialedNumber."&nbsp;&nbsp;&nbsp;&nbsp;".$myCallType."&nbsp;&nbsp;&nbsp;&nbsp;".$myCallStatus."&nbsp;&nbsp;&nbsp;&nbsp;".$myTrunk."&nbsp;&nbsp;&nbsp;&nbsp;".$timestamp;
//					else
//						$myInfo[] = "&nbsp;&nbsp;&nbsp;&nbsp;".$mySipAccount."&nbsp;&nbsp;&nbsp;&nbsp;".$myDialedNumber."&nbsp;&nbsp;&nbsp;&nbsp;".$myCallType."&nbsp;&nbsp;&nbsp;&nbsp;".$myCallStatus."&nbsp;&nbsp;&nbsp;&nbsp;".$myTrunk."&nbsp;&nbsp;&nbsp;&nbsp;<font color=red>".$myItem[0]."&nbsp;&nbsp;&nbsp;&nbsp;".$myItem[1]."</font>";

//					$flag = 0;
//				} 

//			} while ($flag == 1);
			//获得相符的sip channels
		}
		//print $channel;
		//exit;
	}
	
//	print_r($myInfo);
//	exit;
	$sipChannels = implode("<BR \>",$sipChannels);
//	$myChannels = implode("<BR \>",$myInfo);
//	print_r($myInfo);
	$myChannels = generateTabelHtml($myInfo);
//	print_r($channels);
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("msgZone", "innerHTML", "Active Calls: ".$activeCalls."<BR>Active sip calls: ".$activeSipCalls);

	$objResponse->addAssign("channels", "innerHTML", nl2br(trim($myChannels)));
	$objResponse->addAssign("sipChannels", "innerHTML",  nl2br(trim($sipChannels)));
//	$objResponse->addAlert("Active Calls: ".$activeCalls);
//	$objResponse->addAlert("Active Channels ".$activeChannels);
	return $objResponse;
}

function generateTabelHtml($aDyadicArray,$thArray = null){
	$html .= "<table class='myTable'>";
	foreach ($aDyadicArray as $myArray){
		//print_r($myArray);
		//exit;
		$html .="<tr>";
		foreach ($myArray as $field){
			$html .= "<td>";
			$html .= $field;
			$html .= "</td>";
		}
		$html .="</tr>";
	}
	$html .= "</table>";
//	print $html;
	return $html;
}

function getTimeStamp($channel){
	global $db;
	$query = "SELECT timestamp FROM events WHERE event LIKE '%$channel%' ORDER BY timestamp DESC limit 0,1";
	$res = $db->query($query);
	$res = $db->query($query);
	if ($res->numRows() == 0){
		return 0;
	}else{
		$res->fetchInto($list);
		$timestamp = $list['timestamp'];
		return $timestamp;
	}
}

function getSipChannels(){
	global $config;
	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	$channels = $myAsterisk->Command("sip show channels");	
	return  $channels['data'];
}

function getChannels(){
	global $config;
	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	$channels = $myAsterisk->Command("show channels");	
	return  $channels['data'];
}

function preDialer1(){
	global $config;
	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	$objResponse = new xajaxResponse();
	if (!$res){
		$objResponse->addAlert("connect failed");
		return $objResponse;
	}

	$phoneNum = '13909846473';
	$strChannel = "Local/".$phoneNum."@".$config['system']['outcontext']."";
	$myAsterisk->Originate($strChannel,$config['system']['preDialer_extension'],$config['system']['incontext'],1,NULL,NULL,30,$phoneNum,NULL,NULL);	
	return $objResponse;
}

function dialerStatus(){
	// Cause: 16  Cause-txt: Normal Clearing			普通挂机
	// Cause: 0  Cause-txt: Unknown
}

function preDialer(){
	//只能通过dropcall方法实现群拨
/*
	global $config;
	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	$objResponse = new xajaxResponse();
	if (!$res){
		$objResponse->addAlert("connect failed");
		return $objResponse;
	}
*/
	$phoneNum = '84350822';
	//get a phone number
	global $config;
	$sid=md5(uniqid(""));
	$objResponse = new xajaxResponse();
	$myAsterisk = new Asterisk();
	$phoneNum = '13909846473';
	$strChannel = "Local/".$phoneNum."@".$config['system']['outcontext']."";
	$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
									'WaitTime'=>30,
									'Exten'=>$config['system']['preDialer_extension'],
									'Context'=>$config['system']['incontext'],
									'Variable'=>"$strVariable",
									'Priority'=>1,
									'CallerID'=>$phoneNum));

	$objResponse->AddAlert("finished");
	return $objResponse;
	exit;
	//获取一个号码
	$query = '
			SELECT id,phonenumber 
			FROM prediallist 
			LIMIT 0,1 
			ORDER BY id DESC
			 ' ;
	
	$res = $db->query($query);
	if ($res->numRows() == 0){
		$objResponse->addAssign("msgZone", "innerHTML",  "no phone need to be called");
		return $objResponse;
	} else {
		$res->fetchInto($list);
		$id = $list['id'];
		$phoneNum = $list['phonenumber'];
//		$callerid = $list['phonenumber'];
		//remove this record from prediallist table
		$query = '
			DELETE FROM prediallist
			WHERE id = '.$id;
		$res = $db->query($query);

		//insert this record to dialresult table
		$sid=md5(uniqid(""));
		$query = '
			INSERT INTO dialresult SET
			phoneid = \''.$id.'\',
			phonenumber = \''.$phoneNum.'\',
			dialstatus = \'begin\',
			actionid = \''.$actionid.'\'
			';
		$res = $db->query($query);
		$strChannel = "Local/".$phoneNum."@".$config['system']['outcontext']."";
		$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
									'WaitTime'=>30,
									'Exten'=>$config['system']['preDialer_extension'],
									'Context'=>$config['system']['incontext'],
									'Variable'=>"$strVariable",
									'Priority'=>1,
									'CallerID'=>$phoneNum));

//		$myAsterisk->Originate($strChannel,$config['system']['preDialer_extension'],$config['system']['incontext'],1,NULL,NULL,30,$phoneNum,NULL,NULL,NULL,$actionid);

	}
	
	return $objResponse;


}

$xajax->processRequests();
?>

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
	$objResponse->addClear("grid", "innerHTML");
	$objResponse->addClear("formDiv", "innerHTML");
	$objResponse->addClear("channels", "innerHTML");
	$objResponse->addClear("sipChannels", "innerHTML");
	$objResponse->addClear("msgZone", "innerHTML");

	$html .= "<a href=# onclick='clearAll();showAccounts();return false;'>".$locate->Translate("extension_manager")."</a><br>";

	$html .= "<a href=# onclick='clearAll();showStatus();return false;'>".$locate->Translate("system_monitor")."</a><br>";

	$html .= "<a href=# onclick='clearAll();showChannelsInfo();return false;'>".$locate->Translate("active_channels")."</a><br>";

	$html .= "<a href=# onclick='clearAll();showPredictiveDialer();return false;'>".$locate->Translate("predictive_dialer")."</a><br>";

  	$html .= "<a href='customer.php' >".$locate->Translate("customer_manager")."</a><br>";

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
	
	$myChannels = generateTabelHtml($myInfo);

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("divActiveCalls", "innerHTML", $activeCalls);

	$objResponse->addAssign("channels", "innerHTML", nl2br(trim($myChannels)));
	return $objResponse;
}

function generateTabelHtml($aDyadicArray,$thArray = null){
	if (!is_Array($aDyadicArray))
		return '';
	$html .= "<table class='myTable'>";
//	print_r($aDyadicArray);
//	exit;
	$myArray = array_shift($aDyadicArray);
	foreach ($myArray as $field){
		$html .= "<th>";
		$html .= $field;
		$html .= "</th>";
	}

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

function dialerStatus(){
	// Cause: 16  Cause-txt: Normal Clearing			普通挂机
	// Cause: 0  Cause-txt: Unknown
	// 可以考虑从cdr表中读取拨号结果
}

function showPredictiveDialer($preDictiveDialerStatus){
	global $db,$locate;

	$objResponse = new xajaxResponse();
	//从数据库读取预拨号的总数
	$query = '
		SELECT COUNT(*) FROM diallist';
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

/*
		$html .= '<form name="formPreDictiveDialer" id="formPreDictiveDialer">';
		$html .= '<input type="button" value="'.$locate->Translate("dial").'" id="btnDial" name="btnDial" onclick="btnDialOnClick();">';
		$html .= '<input type="text" value="5" id="fldMaxActiveCalls" name="fldMaxActiveCalls">';
		$html .='</form>';
*/
//		$objResponse->addAssign("divPredictiveDialer", "innerHTML",$html);
//		$objResponse->addInsertInputAfter("predictiveDialerStatus", "hidden", "username", "input1");

	}
	return $objResponse;
}

function predictiveDialer($maxChannels,$curCalls,$totalRecords){
	global $config,$db,$locate;
	$objResponse = new xajaxResponse();

	if ($curCalls == -1 ){
		
		return $objResponse;
	}

	if ($curCalls > $maxChannels){
		$objResponse->addAssign("divPredictiveDialerMsg", "innerHTML", $locate->Translate("reach_maximum_concurrent_calls"));
		return $objResponse;
	}

	$myAsterisk = new Asterisk();

	//获取一个号码
	$query = '
			SELECT id,dialnumber 
			FROM diallist 
			ORDER BY id DESC
			LIMIT 0,1 
			 ' ;
	
	$res = $db->query($query);
	if ($res->numRows() == 0){
		$objResponse->addAssign("divPredictiveDialerMsg", "innerHTML", $locate->Translate("no_phonenumber_in_database"));
		$objResponse->addScript("stopDial();");
		return $objResponse;
	} else {
		$res->fetchInto($list);

		$id = $list['id'];
		$phoneNum = $list['dialnumber'];

		$query = '
			DELETE FROM diallist
			WHERE id = '.$id;
		$res = $db->query($query);

		//insert this record to dialresult table
		$sid=md5(uniqid(""));
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
	//	$myAsterisk->Originate($strChannel,$config['system']['preDialer_extension'],$config['system']['incontext'],1,NULL,NULL,30,$phoneNum,NULL,NULL,NULL,$sid);

/*		$myAsterisk->send_request('Originate',array('Channel'=>"$strChannel",
									'WaitTime'=>30,
									'Exten'=>$config['system']['preDialer_extension'],
									'Context'=>$config['system']['incontext'],
									'Variable'=>"$strVariable",
									'Priority'=>1,
									'CallerID'=>$phoneNum));
*/
//		exit;


		$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
									'WaitTime'=>30,
									'Exten'=>$config['system']['preDialer_extension'],
									'Context'=>$config['system']['preDialer_context'],
									'Variable'=>"$strVariable",
									'Priority'=>1,
									'MaxRetries'=>1,
									'CallerID'=>$phoneNum));

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

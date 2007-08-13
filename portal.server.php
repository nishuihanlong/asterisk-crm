<?php
require_once ("portal.common.php");
require_once ("db_connect.php");
require_once ('grid.customer.inc.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/asterisk.php');


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
	global $asmanager;
	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $asmanager;
	$res = $myAsterisk->connect();
	$objResponse = new xajaxResponse();
	if (!$res)
		$objResponse->addAssign("debug", "innerText", "Failed");

	//$strChannel = "Local/".$phoneNum."@".$config['OUTCONTEXT']."";
	if ($aFormValues['direction'] == 'in')		
		$myAsterisk->Redirect($aFormValues['callerChannel'],'',$aFormValues['sltExten'],$config['OUTCONTEXT'],1);
	else
		$myAsterisk->Redirect($aFormValues['calleeChannel'],'',$aFormValues['sltExten'],$config['OUTCONTEXT'],1);


//	$objResponse->addAlert("Fine");
	return $objResponse;
}

//check if call (uniqueid) hangup
function incomingCalls($myValue){
	global $db;
	$objResponse = new xajaxResponse();
	if ($myValue['callerChannel'] == ''){
		$query = "SELECT * FROM events WHERE event LIKE 'Event: Link%' AND event LIKE '%" . $myValue['uniqueid'] . "%' order by id desc ";
		$res = $db->query($query);

		if ($res->fetchInto($list)){	// call connected

			$curid	= $list['id'];
			$flds	= split("  ",$list['event']);

			$objResponse->addAssign("status","innerHTML", "connected" );
			$objResponse->addAssign("myevents","innerHTML", "talking to " . $myValue['callerid'] );
			$objResponse->addAssign("callerChannel","value", trim(substr($flds[2],9)));
			$objResponse->addAssign("calleeChannel","value", trim(substr($flds[3],9)));
			$objResponse->addAssign("curid","value", $curid );
			$transfer = '
						<SELECT id="sltExten" name="sltExten">
						';
			$query = "SELECT * FROM account WHERE extension <> '".$_SESSION['curuser']['extension']."'";
			$myres = $db->query($query);
			while ($myres->fetchInto($list)){
				$transfer .= '
								<option value="'.$list['extension'].'">'.$list['extension'].'</option>
							';
			}

			$transfer .= '
						</SELECT>
						<INPUT type="BUTTON" value="Transfer" onclick="xajax_transfer(xajax.getFormValues(\'myForm\'));return false;">
						';
			$objResponse->addAssign("transfer","innerHTML", $transfer );

//			$objResponse->addAssign("myevents","innerHTML", $flds[3] );

			return $objResponse;
		}
	}

	//$query = "SELECT * FROM events WHERE  event LIKE '%".$_SESSION['curuser']['extension']."%' AND event LIKE '%Hangup%' AND event LIKE '%" . $myValue['uniqueid'] . "%' order by id desc ";
	$query = "SELECT * FROM events WHERE event LIKE '%Hangup%' AND event LIKE '%" . $myValue['uniqueid'] . "%' order by id desc ";

	$res = $db->query($query);

	if ($res->fetchInto($list)){	// call hangup

		$curid        = $list['id'];

		$objResponse->addAssign("status","innerHTML", "hangup" );
		$objResponse->addAssign("myevents","innerHTML", "Hang up call from " . $myValue['callerid'] );
		$objResponse->addAssign("uniqueid","value", "" );
		$objResponse->addAssign("callerid","value", "" );
		$objResponse->addAssign("callerChannel","value", '');
		$objResponse->addAssign("calleeChannel","value", '');
		$objResponse->addAssign("curid","value", $curid );
		$objResponse->addAssign("transfer","innerHTML", '' );

	}
//	}
//	mysql_close($mylink);
	return $objResponse;
}

function waitingCalls($myValue){
	global $db,$config;
	$objResponse = new xajaxResponse();
	//query if there's new call since last call
	$curid = trim($myValue['curid']);


	$query = "SELECT * FROM events WHERE  event LIKE '%".$_SESSION['curuser']['extension']."%' AND event LIKE 'Event: Newchannel%State: Ringing%' AND id > " . $curid . " order by id desc";

/*	$now = date("Y-M-d H:i:s");
   		
	$fd = fopen ("/tmp/xajaxDebug.log",'a');
	$log = $now." ".$_SERVER["REMOTE_ADDR"] ." - $query \n";
	fwrite($fd,$log);
   	fclose($fd);
*/
	$res = $db->query($query);


	if ( $res->numRows() > 0 ){//有新的来电
//		$numfields = mysql_num_fields($result);
		$rows = 0;
		while ($res->fetchInto($list)) {
//			$list      = mysql_fetch_assoc($result);
			$id        = $list['id'];
			$timestamp = $list['timestamp'];
			$event     = $list['event'];
			$flds      = split("  ",$event);
			$c         = count($flds);
			$callerid  = '';
			$transferid= '';

			for($i=4;$i<$c;++$i) {
				if ($flds[3] == 'State: Ringing'){
					if (strstr($flds[$i],"CallerID:"))	//尝试获取callerid
						$transferid = substr($flds[$i],9);

					if (strstr($flds[$i],"Uniqueid:")){	//获取uniqueid
							$uniqueid = substr($flds[$i],9);
//							if ($callerid == ''){	//再次尝试获取callerid
								$dstInfo = getCallerID($uniqueid);
								$callerid = $dstInfo['CallerID'] ;
//							}
					}
				}
			}
			
			if ($callerid == '')
				$callerid = $transferid;

			if ($id > $curid) $curid = $id;
//			$rows++;
		}

		$callerid = trim($callerid);
		
		$call['phoneNum'] = $callerid;
		$call['uniqueid'] = trim($uniqueid);
		$call['callerid'] = $callerid;
		$call['curid'] = trim($curid);
		$call['info'] = "Incoming call from " . $call['phoneNum'];
		$call['direction'] = "in";
		return newCalls($call);
	}else{//没有新的来电
		if ($config['POP_UP_WHEN_DIAL_OUT']){
			$query = "SELECT * FROM events WHERE event LIKE '%".$_SESSION['curuser']['extension']."%' AND event LIKE 'Event: Dial%' AND id > " . $curid . " order by id desc";	//判断是否有拨出的电话

			$res = $db->query($query);
			if ( $res->numRows() > 0 ){//有新的拨出
				$rows = 0;
				$curid = $myValue['curid'];
				while ($res->fetchInto($list)) {

					$id        = $list['id'];
					$timestamp = $list['timestamp'];
					$event     = $list['event'];
					$flds      = split("  ",$event);
					$c         = count($flds);
					$callerid  = '';
					$transferid= '';


					if ($flds[0] == 'Event: Dial'){
						$SrcUniqueID = substr($flds[6],12);

						$srcInfo = getInfoBySrcID($SrcUniqueID);
						$callerid = $srcInfo['Extension'] ;
						//$channel = $srcInfo['Channel'] ;
					}
				}
				
				if ($id > $curid) $curid = $id;

				$callerid = trim($callerid);
				$call['phoneNum'] = $callerid;
				$call['uniqueid'] = trim($SrcUniqueID);
				$call['callerid'] = $callerid;
				$call['curid'] = trim($curid);
				$call['info'] = "Dial to " . $call['phoneNum'];
				$call['direction'] = "out";
				return newCalls($call);
			}else{
					$objResponse->addAssign("myevents","innerHTML", "waiting" );
					$objResponse->addAssign("status","innerHTML", "listening" );
					$objResponse->addAssign("uniqueid","value", "" );
					$objResponse->addAssign("callerid","value", "" );
					$objResponse->addAssign("callerChannel","value", "" );
					$objResponse->addAssign("calleeChannel","value", "" );
					$objResponse->addAssign("curid","value", $curid );
			}
		}
	}

	return $objResponse;
}

function getInfoBySrcID($SrcUniqueID){
	global $db;
	$SrcUniqueID = trim($SrcUniqueID);
	$query  = "SELECT * FROM events WHERE event LIKE '%Uniqueid: $SrcUniqueID%' AND event LIKE 'Event: Newexten%'";
	$res = $db->query($query);
	if ($res->fetchInto($list)){
		$event = $list['event'];
		$flds = split("  ",$event);

		foreach ($flds as $myFld) {
			if (strstr($myFld,"Extension:")){	
				$myArray['Extension'] = substr($myFld,10);
			} 
			if (strstr($myFld,"Channel:")){	
				$myArray['Channel'] = substr($myFld,8);
			} 

		}
	}

	return $myArray;
}

/*
get SrcUniqueID、CallerID and CallerIDName from database 
the rule is DestUniqueID = uniqueid
*/

function getCallerID($vUniqueID){
	global $db;
	$vUniqueID = trim($vUniqueID);
	$query  = "SELECT * FROM events WHERE event LIKE '%DestUniqueID: $vUniqueID%'";
	$res = $db->query($query);

	if ($res->fetchInto($list)){
		$event = $list['event'];
		$flds = split("  ",$event);

		foreach ($flds as $myFld) {
			if (strstr($myFld,"CallerID:")){	
				$myArray['CallerID'] = substr($myFld,9);
			} elseif(strstr($myFld,"CallerIDName:")){
				$myArray['CallerIDName'] = substr($myFld,13);
			} elseif(strstr($myFld,"SrcUniqueID:")){
				$myArray['SrcUniqueID'] = substr($myFld,12);
			}
		}
	}

	return $myArray;
}

//	create grid
function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){

	$_SESSION['ordering'] = $ordering;
	
	if(($filter == null) or ($content == null)){
		
		$numRows =& Customer::getNumRows();
		$arreglo =& Customer::getAllRecords($start,$limit,$order);
	}else{
		
		$numRows =& Customer::getNumRows($filter, $content);
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
	$headers[] = "Customer Name";
	$headers[] = "Category";
	$headers[] = "Contact";
	$headers[] = "Note";
	$headers[] = "Create Time";
	$headers[] = "Create By";
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
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","address","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","website","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","category","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","cretime","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","creby","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","priority","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'customer';
	$fieldsFromSearch[] = 'address';
	$fieldsFromSearch[] = 'website';
	$fieldsFromSearch[] = 'category';
	$fieldsFromSearch[] = 'contact';
	$fieldsFromSearch[] = 'cretime';
	$fieldsFromSearch[] = 'creby';
	$fieldsFromSearch[] = 'priority';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = "Customer Name";
	$fieldsFromSearchShowAs[] = "Address";
	$fieldsFromSearchShowAs[] = "Website";
	$fieldsFromSearchShowAs[] = "Category";
	$fieldsFromSearchShowAs[] = "Contact";
	$fieldsFromSearchShowAs[] = "Create Time";
	$fieldsFromSearchShowAs[] = "Create User";
	$fieldsFromSearchShowAs[] = "Priority";


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearch("customer",$fieldsFromSearch,$fieldsFromSearchShowAs);


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
		$table->addRow("customer",$rowc,1,1,1,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}



// 判断是否存在$customerName, 如果存在就显示
function confirmCustomer($customerName,$callerID = null){

	$objResponse = new xajaxResponse();

	$customerID = Customer::checkValues("customer","customer",$customerName); 
	if ($customerID){//存在
		$html = Table::Top("Adding Record","formDiv"); 
		$html .= Customer::formAdd($callerID,$customerID);
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

	$objResponse = new xajaxResponse();

	$contactID = Customer::checkValues("contact","contact",$contactName,"string","customerid",$customerID,"int"); 
	if ($contactID){//存在

		$html = Table::Top("Adding Record","formDiv"); 
		$html .= Customer::formAdd($callerID,$customerID,$contactID);
		$html .= Table::Footer();
		$objResponse->addAssign("formDiv", "style.visibility", "visible");
		$objResponse->addAssign("formDiv", "innerHTML", $html);
		//显示customer信息
		$objResponse->addScript("xajax_showCustomer($customerID)");

		//显示contact信息
		$objResponse->addScript("xajax_showContact($contactID)");

//		$objResponse->addAlert("$customerID" );
//		$objResponse->addAssign("btnConfirmContact","value", "Cancel" );
//		$objResponse->addAssign("contact","readOnly", "true" );
//		$objResponse->addAssign("contactid","value", $contactID );
//		$contact = Customer::getContactByID
//		$objResponse->addAssign("position","value", $contactID );
//		$objResponse->addScript("xajax_fillContact($customerID)");
	} else
			$objResponse->addAlert("不存在" );

	return $objResponse;
}




function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	
	$html = createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	
	return $objResponse->getXML();
}

function add($callerid = null,$customerid = null,$contactid = null){
   // Edit zone
	$objResponse = new xajaxResponse();
	$html = Table::Top("Adding Record","formDiv");  // <-- Set the title for your form.
	$html .= Customer::formAdd($callerid,$customerid,$contactid);  // <-- Change by your method
//	$objResponse->addAlert($callerid);
	// End edit zone
	$html .= Table::Footer();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	
	return $objResponse->getXML();
}

function editField($table, $field, $cell, $value, $id){
	$objResponse = new xajaxResponse();
	
	$html =' <input type="text" id="input'.$cell.'" value="'.$value.'" size="'.(strlen($value)+5).'"'
			.' onBlur="xajax_updateField(\''.$table.'\',\''.$field.'\',\''.$cell.'\',document.getElementById(\'input'.$cell.'\').value,\''.$id.'\');"'
			.' style="background-color: #CCCCCC; border: 1px solid #666666;">';
	$objResponse->addAssign($cell, "innerHTML", $html);
	$objResponse->addScript("document.getElementById('input$cell').focus();");
	return $objResponse->getXML();
}


function edit($id = null, $tblName, $type = "note"){

	$lable = "Editing $type record";


	// Edit zone
	$html = Table::Top($lable,"formEditInfo"); 	// <-- Set the title for your form.
	$html .= Customer::formEdit($id, $type); 			// <-- Change by your method
	$html .= Table::Footer();
   	// End edit zone

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("formEditInfo", "style.visibility", "visible");
	$objResponse->addAssign("formEditInfo", "innerHTML", $html);
	return $objResponse->getXML();
}

function delete($id = null, $table_DB = null){
	Customer::deleteRecord($id); 				// <-- Change by your method
	$html = createGrid(0,ROWSXPAGE);
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("grid", "innerHTML", $html);
	$objResponse->addAssign("msgZone", "innerHTML", "Record Deleted"); // <-- Change by your leyend
	return $objResponse->getXML();
}

function showCustomer($id = null, $type="customer"){
	if($id != null){
		$html = Table::Top("Customer Detail","formCustomerInfo"); 			
		$html .= Customer::showCustomerRecord($id,$type); 		
		$html .= Table::Footer();
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("formCustomerInfo", "style.visibility", "visible");
		$objResponse->addAssign("formCustomerInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}
}

function showNote($id = '', $type="customer"){
	if($id != ''){
		$html = Table::Top("Note Detail","formNoteInfo"); 			
		$html .= Customer::showNoteList($id,$type); 		
		$html .= Table::Footer();
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("formNoteInfo", "style.visibility", "visible");
		$objResponse->addAssign("formNoteInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}
}

function showContact($id = null, $type="contact"){

	if($id != null){
		$html = Table::Top("Contact Detail","formContactInfo"); 			// <-- Set the title for your form.
		$html .= Customer::showContactRecord($id,$type); 		// <-- Change by your method
		$html .= Table::Footer();
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("formContactInfo", "style.visibility", "visible");
		$objResponse->addAssign("formContactInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}
}

function save($f){
	$objResponse = new xajaxResponse();

	$message = Customer::checkAllData($f,1); // <-- Change by your method
	if(!$message){
		
		if ($f['customerid'] == '')
			$respOk = Customer::insertNewCustomer($f); // 添加一个新的客户
		else{
			$respOk = $f['customerid'];
		}

		if ($respOk != 0){

			$customerID = $respOk;

			if ($f['contactid'] == ''){
				$respOk = Customer::insertNewContact($f,$customerID); // 添加一个新的联系人
			}else{
				$respOk = Customer::updateContactRecord($f); // update contact record
				if ($respOk){
					$respOk = $f['contactid'];
				}else{
					$objResponse->addAssign("msgZone", "innerHTML", "Contact record could not be updated");
				}
			}

			if ($respOk != 0){
				$contactID = $respOk;
				$respOk = Customer::insertNewNote($f,$customerID,$contactID); // add a new Note
				if ($respOk){
					$html = createGrid(0,ROWSXPAGE);
					$objResponse->addAssign("grid", "innerHTML", $html);
					$objResponse->addAssign("msgZone", "innerHTML", "A note has been added");
					$objResponse->addAssign("formDiv", "style.visibility", "hidden");
					$objResponse->addAssign("formCustomerInfo", "style.visibility", "hidden");
					$objResponse->addAssign("formContactInfo", "style.visibility", "hidden");
				}else{
					$objResponse->addAssign("msgZone", "innerHTML", "Note record could not be added");
				}
			}else{
				$objResponse->addAssign("msgZone", "innerHTML", "Contact record could not be added");
			}
		}else{
			$objResponse->addAssign("msgZone", "innerHTML", "Customer record could not be added");
		}
	}else{
		$objResponse->addAlert($message);
	}
	return $objResponse->getXML();
	
}

function update($f, $type){
	$objResponse = new xajaxResponse();

	if ($type == 'note'){
		$respOk = Customer::updateNoteRecord($f,"append");
	}elseif ($type == 'customer'){
		if (empty($f['customer']))
			$message = "The field Customer does not have to be null";
		else
			$respOk = Customer::updateCustomerRecord($f);
	}elseif ($type == 'contact'){
		if (empty($f['contact']))
			$message = "The field Contact does not have to be null";
		else
			$respOk = Customer::updateContactRecord($f);
	}else{
		$message = 'error: no current type set';
	}

	if(!$message){
		if($respOk){
			$html = createGrid(0,ROWSXPAGE);
			$objResponse->addAssign("grid", "innerHTML", $html);
			$objResponse->addAssign("msgZone", "innerHTML", "A record has been updated");
			$objResponse->addAssign("formEditInfo", "style.visibility", "hidden");
		}else{
			$objResponse->addAssign("msgZone", "innerHTML", "The record could not be updated");
		}
	}else{
		$objResponse->addAlert($message);
	}
	
	return $objResponse->getXML();
}

function updateField($table, $field, $cell, $value, $id){
	$objResponse = new xajaxResponse();
	$objResponse->addAssign($cell, "innerHTML", $value);

	Customer::updateField($table,$field,$value,$id);
	return $objResponse->getXML();
}

# click to dial
# $phoneNum	phone to call
# $first	which phone will ring first, caller or callee

function dial($phoneNum,$first = 'caller'){
	global $config;
	global $asmanager;
	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $asmanager;
	$res = $myAsterisk->connect();
	$objResponse = new xajaxResponse();
	if (!$res)
		$objResponse->addAssign("mobileStatus", "innerText", "Failed");

	$callerid = "Web Call <" . $_SESSION['curuser']['extension'] . ">";
	$first = 'callee';

	if ($first == 'caller'){	//caller phone will ring first
		$strChannel = "Local/".$phoneNum."@".$config['OUTCONTEXT']."";
		$myAsterisk->Originate($strChannel,$_SESSION['curuser']['extension'],$config['INCONTEXT'],1,NULL,NULL,30,$_SESSION['curuser']['extension'],NULL,$_SESSION['curuser']['extension']);
	}else{
		$strChannel = "Local/".$_SESSION['curuser']['extension']."@".$config['INCONTEXT']."";
//		$objResponse->addAlert($strChannel);
//		return $objResponse;
		$myAsterisk->Originate($strChannel,$phoneNum,$config['OUTCONTEXT'],1,NULL,NULL,30,$_SESSION['curuser']['extension'],NULL,$_SESSION['curuser']['extension']);
	}
	return $objResponse->getXML();
}

function newCalls($call){
	global $db,$config;	
	$objResponse = new xajaxResponse();

	$objResponse->addScript('document.title='.$call['phoneNum'].';');
	$objResponse->addAssign("status","innerHTML", "ringing" );
	$objResponse->addAssign("uniqueid","value", $call['uniqueid'] );
	$objResponse->addAssign("callerid","value", $call['callerid'] );
	$objResponse->addAssign("curid","value", $call['curid'] );
	$objResponse->addAssign("callerChannel","value", '' );
	$objResponse->addAssign("calleeChannel","value", '' );
	$objResponse->addAssign("direction","value", $call['direction'] );
	$objResponse->addAssign("myevents","innerHTML", $call['info']);


	//check if callerid is long enough

	if (strlen($call['phoneNum'])<$config['PHONE_NUMBER_LENGTH'])
		return $objResponse;

	//判断是否有新的记录
	//check if there're phone records already

	$query = '
			SELECT id,customerid 
			FROM contact
			WHERE phone LIKE \'%'. $call['phoneNum'] . '%\'
			OR phone1 LIKE \'%'. $call['phoneNum'] . '%\'
			OR phone2 LIKE \'%'. $call['phoneNum'] . '%\'
			OR mobile LIKE \'%'. $call['phoneNum'] . '%\'
			 ' ;
	
//	$objResponse->addAssign("status","innerHTML", "$query" );
//	return $objResponse;
	$res = $db->query($query);

	if ($res->numRows() == 0){	//no match
		
		$objResponse->addScript('xajax_add(\'' . $call['phoneNum'] . '\');');

	} elseif ($res->numRows() == 1) { // one match

		$res->fetchInto($list);
		$customerid = $list['customerid'];
		$contactid = $list['id'];
		
		$html = Table::Top("Adding Record","formDiv");  // <-- Set the title for your form.
		$html .= Customer::formAdd($call['phoneNum'],$customerid,$contactid);  // <-- Change by your method
		$html .= Table::Footer();
		$objResponse->addAssign("formDiv", "style.visibility", "visible");
		$objResponse->addAssign("formDiv", "innerHTML", $html);

		$objResponse->addScript('xajax_showContact(\''.$contactid.'\');');
		$objResponse->addScript('xajax_showCustomer(\''.$customerid.'\');');

	}else {	//match a lot records... [only display the first one for now]
		$res->fetchInto($list);
		$customerid = $list['customerid'];
		$contactid = $list['id'];
		
		$html = Table::Top("Adding Record","formDiv");  // <-- Set the title for your form.
		$html .= Customer::formAdd($call['phoneNum'],$customerid,$contactid);  // <-- Change by your method
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
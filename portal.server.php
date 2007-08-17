<?php
require_once ("db_connect.php");
require_once ("portal.common.php");
require_once ('grid.customer.inc.php');
require_once ('asterevent.class.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/asterisk.php');

function init(){
	global $locate;

	$objResponse = new xajaxResponse();
	
	$html = $locate->Translate("welcome").':'.$_SESSION['curuser']['username'].',';
	$html .= $locate->Translate("extension").$_SESSION['curuser']['extension'];
	$objResponse->addAssign("userMsg","innerHTML", $html );
	$objResponse->addAssign("username","value", $_SESSION['curuser']['username'] );
	$objResponse->addAssign("extension","value", $_SESSION['curuser']['extension'] );
	$objResponse->addAssign("myevents","innerHTML", $locate->Translate("waiting") );
	$objResponse->addAssign("status","innerHTML", $locate->Translate("listening") );

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

	if ($myValue['direction'] != ''){

		$call = asterEvent::checkCallStatus($myValue['curid'],$myValue['uniqueid']);

		if ($call['status'] ==''){
			return $objResponse;
		} elseif ($call['status'] =='link'){
			$status	= 'link';
			$info	= 'talking to '.$myValue['callerid'];
			$objResponse->addAssign("callerChannel","value", $call['callerChannel'] );
			$objResponse->addAssign("calleeChannel","value", $call['calleeChannel'] );
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


		} elseif ($call['status'] =='hangup'){
			$status	= 'hang up';
			$info	= "Hang up call from " . $myValue['callerid'];
			$objResponse->addAssign("uniqueid","value", "" );
			$objResponse->addAssign("callerid","value", "" );
			$objResponse->addAssign("callerChannel","value", '');
			$objResponse->addAssign("calleeChannel","value", '');
			$objResponse->addAssign("transfer","innerHTML", '');
		}
		$objResponse->addAssign("status","innerHTML", $status );
		$objResponse->addAssign("myevents","innerHTML", $info );
	}

	return $objResponse;
}



function waitingCalls($myValue){
	global $db,$config;
	$objResponse = new xajaxResponse();
	$curid = trim($myValue['curid']);
	
	$call = asterEvent::checkNewCall($curid,$_SESSION['curuser']['extension']);

	if ($call['status'] == ''){
		$title	= 'waiting';
		$status	= 'waiting';
		$call['curid'] = $curid;
		$direction	= '';
		$info	= 'waiting, please stand by';
	} elseif ($call['status'] == 'incoming'){
		$title	= $call['callerid'];
		$stauts	= 'ringing';
		$direction	= 'in';
		$info	= 'incoming call from '. $call['callerid'];
		if ($config['POP_UP_WHEN_INCOMING'])
			if (strlen($call['callerid']) > $config['PHONE_NUMBER_LENGTH'])
				$objResponse->loadXML(getContact($call['callerid']));
	} elseif ($call['status'] == 'dialout'){
		$title	= $call['callerid'];
		$status	= 'dialing';
		$direction	= 'out';
		$info	= 'dial out to '. $call['callerid'];
		if ($config['POP_UP_WHEN_DIAL_OUT'])
			if (strlen($call['callerid']) > $config['PHONE_NUMBER_LENGTH'])
				$objResponse->loadXML(getContact($call['callerid']));
	}

	$objResponse->addScript('document.title='.$title.';');
	$objResponse->addAssign("status","innerHTML", $stauts );
	$objResponse->addAssign("uniqueid","value", $call['uniqueid'] );
	$objResponse->addAssign("callerid","value", $call['callerid'] );
	$objResponse->addAssign("curid","value", $call['curid'] );
	$objResponse->addAssign("direction","value", $direction );
	$objResponse->addAssign("myevents","innerHTML", $info);

	return $objResponse;
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
	} //else
//			$objResponse->addAlert("不存在" );

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

function getContact($callerid){
	global $db;	
	$objResponse = new xajaxResponse();


	//判断是否有新的记录
	//check if there're phone records already

	$query = '
			SELECT id,customerid 
			FROM contact
			WHERE phone LIKE \'%'. $callerid . '%\'
			OR phone1 LIKE \'%'. $callerid . '%\'
			OR phone2 LIKE \'%'. $callerid . '%\'
			OR mobile LIKE \'%'. $callerid . '%\'
			 ' ;
	
	$res = $db->query($query);

	if ($res->numRows() == 0){	//no match
		
		$objResponse->addScript('xajax_add(\'' . $callerid . '\');');

	} elseif ($res->numRows() == 1) { // one match

		$res->fetchInto($list);
		$customerid = $list['customerid'];
		$contactid = $list['id'];
		
		$html = Table::Top("Adding Record","formDiv");  // <-- Set the title for your form.
		$html .= Customer::formAdd($callerid,$customerid,$contactid);  // <-- Change by your method
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
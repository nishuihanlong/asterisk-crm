<?php
require_once ("db_connect.php");
require_once ("portal.common.php");
require_once ('grid.customer.inc.php');
require_once ('asterevent.class.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/asterisk.php');

function showDetail($recordId){
	$objResponse = new xajaxResponse();
	$objResponse->addScript('xajax_showContact(\''.$recordId.'\',\'note\');');
	$objResponse->addScript('xajax_showCustomer(\''.$recordId.'\',\'note\');');
	return $objResponse;
}

function getPrivateDialList($extension = null){
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
	$objResponse->addAssign("userMsg","innerHTML", $html );
	$objResponse->addAssign("username","value", $_SESSION['curuser']['username'] );
	$objResponse->addAssign("extension","value", $_SESSION['curuser']['extension'] );
	$objResponse->addAssign("myevents","innerHTML", $locate->Translate("waiting") );
	$objResponse->addAssign("status","innerHTML", $locate->Translate("listening") );
	$objResponse->addAssign("btnDial","value", $locate->Translate("dial") );
	$objResponse->addAssign("processingMessage","innerHTML", $locate->Translate("processing_please_wait") );

	$objResponse->loadXML(getPrivateDialList($_SESSION['curuser']['extension']));

//	echo $_SESSION['curuser']['usertype'];
//	exit;
	if ($_SESSION['curuser']['usertype'] == "admin"){
		$panelHTML = '<a href=# onclick="this.href=\'manager.php\'">'.$locate->Translate("manager").'</a>&nbsp;';
	}
	$panelHTML .="<a href='login.php'>".$locate->Translate("logout")."</a>";
	$objResponse->addAssign("panelDiv","innerHTML", $panelHTML);

	if ($config['system']['enable_external_crm'] == false){
		$objResponse->addClear("crm","innerHTML");
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
							<div id="grid" align="center"> </div>';
		$objResponse->addAppend("crm","innerHTML", $mycrm );
		$objResponse->addScript("xajax_showGrid(0,".ROWSXPAGE.",'','','')");
	} else {
		$objResponse->addIncludeScript("js/extercrm.js");
		if ($config['system']['open_new_window'] == false){
			$mycrm = '<iframe id="mycrm" name="mycrm" src="'.$config['system']['external_crm_default_url'].'" width="100%"  frameBorder=0 scrolling=auto height="100%"></iframe>';
			$objResponse->addAssign("crm","innerHTML", $mycrm );
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
			$status	= "link";
			$info	= $locate->Translate("talking_to").$myValue['callerid'];
			$objResponse->addAssign("callerChannel","value", $call['callerChannel'] );
			$objResponse->addAssign("calleeChannel","value", $call['calleeChannel'] );
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
							<INPUT type="BUTTON" value="'.$locate->Translate("transfer").'" onclick="xajax_transfer(xajax.getFormValues(\'myForm\'));return false;">
							';
				$objResponse->addAssign("transfer","innerHTML", $transfer );
			}

		} elseif ($call['status'] =='hangup'){
			$status	= 'hang up';
			$info	= "Hang up call from " . $myValue['callerid'];
			$objResponse->addScript('document.title=\'asterCrm\';');
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
	global $db,$config,$locate;
	$objResponse = new xajaxResponse();
	$curid = trim($myValue['curid']);

	$phone_html = asterEvent::checkExtensionStatus($curid);
	
	$objResponse->addAssign("extensionDiv","innerHTML", $phone_html );

	$call = asterEvent::checkNewCall($curid,$_SESSION['curuser']['extension']);


	if ($call['status'] == ''){
		$title	= $locate->Translate("waiting");
		$status	= 'waiting';
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
					}else{
						//use external link
						$myurl = $config['system']['external_crm_url'];
						$myurl = preg_replace("/\%method/","dial_in",$myurl);
						$myurl = preg_replace("/\%callerid/",$call['callerid'],$myurl);
						$myurl = preg_replace("/\%calleeid/",$_SESSION['curuser']['extension'],$myurl);

						if ($config['system']['open_new_window'] == false){
								$mycrm = '<iframe id="mycrm" name="mycrm" src="'.$myurl.'" width="100%"  frameBorder=0 scrolling=auto height="100%"></iframe>';
								$objResponse->addAssign("crm","innerHTML", $mycrm );
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
					}else{
						//use external link
						$myurl = $config['system']['external_crm_url'];
						$myurl = preg_replace("/\%method/","dial_out",$myurl);
						$myurl = preg_replace("/\%callerid/",$_SESSION['curuser']['extension'],$myurl);
						$myurl = preg_replace("/\%calleeid/",$call['callerid'],$myurl);
						if ($config['system']['open_new_window'] == false){
							$mycrm = '<iframe id="mycrm" name="mycrm" src="'.$myurl.'" width="100%"  frameBorder=0 scrolling=auto height="100%"></iframe>';
							$objResponse->addAssign("crm","innerHTML", $mycrm );
						} else {
							$javascript = "openwindow('".$myurl."')";
							$objResponse->addScript($javascript);
						}
					}
				}
			}
		}
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
/*
	$headers[] = $locate->Translate("customer_name")//"Customer Name";
	$headers[] = $locate->Translate("category")//"Category";
	$headers[] = $locate->Translate("contact")//"Contact";
	$headers[] = $locate->Translate("note")//"Note";
	$headers[] = $locate->Translate("create_time")//"Create Time";
	$headers[] = $locate->Translate("create_by")//"Create By";
*/
	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("customer_name");
	$fieldsFromSearchShowAs[] = $locate->Translate("address");
	$fieldsFromSearchShowAs[] = $locate->Translate("website");
	$fieldsFromSearchShowAs[] = $locate->Translate("category");
	$fieldsFromSearchShowAs[] = $locate->Translate("contact");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_time");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_by");
	$fieldsFromSearchShowAs[] = $locate->Translate("priority");


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




function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	
	$html = createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	
	return $objResponse->getXML();
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
		$phoneNumber = $list['dialnumber'];
		$objResponse->loadXML(getContact($phoneNumber));
		$id = $list['id'];
		$query = 'DELETE FROM diallist WHERE id ='.$id ;
		$res = $db->query($query);
	}

	$objResponse->loadXML(getPrivateDialList($_SESSION['curuser']['extension']));

	return $objResponse;
}

function add($callerid = null,$customerid = null,$contactid = null){
	global $locate;
   // Edit zone
	$objResponse = new xajaxResponse();
//	$objResponse->addAlert($customerid);
//	$objResponse->addAlert($contactid);
//	return $objResponse;
	$html = Table::Top($locate->Translate("add_record"),"formDiv");  // <-- Set the title for your form.
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
	global $locate;

	// Edit zone
	$html = Table::Top($locate->Translate("edit_record"),"formEditInfo"); 	// <-- Set the title for your form.
	$html .= Customer::formEdit($id, $type); 			// <-- Change by your method
	$html .= Table::Footer();
   	// End edit zone

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("formEditInfo", "style.visibility", "visible");
	$objResponse->addAssign("formEditInfo", "innerHTML", $html);
	return $objResponse->getXML();
}

function delete($id = null, $table_DB = null){
	global $locate;
	Customer::deleteRecord($id); 				// <-- Change by your method
	$html = createGrid(0,ROWSXPAGE);
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("grid", "innerHTML", $html);
	$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("record_deleted")); 
	return $objResponse->getXML();
}

function showCustomer($id = null, $type="customer"){
	global $locate;
	if($id != null){
		$html = Table::Top($locate->Translate("customer_detail"),"formCustomerInfo"); 			
		$html .= Customer::showCustomerRecord($id,$type); 		
		$html .= Table::Footer();
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("formCustomerInfo", "style.visibility", "visible");
		$objResponse->addAssign("formCustomerInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}
}

function showNote($id = '', $type="customer"){
	global $locate;
	if($id != ''){
		$html = Table::Top($locate->Translate("note_detail"),"formNoteInfo"); 			
		$html .= Customer::showNoteList($id,$type); 		
		$html .= Table::Footer();
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("formNoteInfo", "style.visibility", "visible");
		$objResponse->addAssign("formNoteInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}
}

function showContact($id = null, $type="contact"){
	global $locate;
	$objResponse = new xajaxResponse();

	if($id != null ){
		$html = Table::Top($locate->Translate("contact_detail"),"formContactInfo"); 
		$contactHTML .= Customer::showContactRecord($id,$type);

		if ($contactHTML == '')
			return $objResponse->getXML();
		else
			$html .= $contactHTML;

		$html .= Table::Footer();
		$objResponse->addAssign("formContactInfo", "style.visibility", "visible");
		$objResponse->addAssign("formContactInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}
}

function save($f){
	$objResponse = new xajaxResponse();
	global $locate;
//	print "save";
//	exit;
	if (empty($f['customer']) && empty($f['contact']))
		return $objResponse;

	if(empty($f['customer'])) {
//		$objResponse->addAlert($locate->Translate("customer_cant_null"));
//		return $objResponse;
		$customerID = 0;
	} else{

		if ($f['customerid'] == ''){
			$respOk = Customer::insertNewCustomer($f); // insert a new customer record
//			$objResponse->addAlert($respOk);
//			return $objResponse;
			if (!$respOk){
				$objResponse->addAlert($locate->Translate("customer_add_error"));
				return $objResponse;
			}
			$objResponse->addAlert($locate->Translate("a_new_customer_added"));
		} else{
//			$respOk = Customer::updateCustomerRecord($f); // update a customer record
//			if (!$respOk){
//				$objResponse->addAlert($locate->Translate("customer_update_error"));
//				return $objResponse;
//			}
			$respOk = $f['customerid'];
		}
		$customerID = $respOk;
	}

	if(empty($f['contact'])) {
		$contactID = 0;
	} else{
		if ($f['contactid'] == ''){
			$respOk = Customer::insertNewContact($f,$customerID); // insert a new contact record
			if (!$respOk){
				$objResponse->addAlert($locate->Translate("contact_add_error"));
				return $objResponse;
			}
			$objResponse->addAlert($locate->Translate("a_new_contact_added"));
		}else{
//			$respOk = Customer::updateContactRecord($f); // update a contact record
//			if (!$respOk){
//				$objResponse->addAlert($locate->Translate("contact_update_error"));
//				return $objResponse;
//			}
			$respOk = $f['contactid'];
			//获取该contact的customerid
			$res =& Customer::getContactByID($respOk);
			if ($res){
				$contactCustomerID = $res['customerid'];
				if ($contactCustomerID == 0 && $customerID ==0)
				{
				}else{
					$res =& Customer::updateField('contact','customerid',$customerID,$f['contactid']
					);
					if ($res){
						$objResponse->addAlert($locate->Translate("a_contact_binding"));
					}
				}
			}


		}
		$contactID = $respOk;
	}



	if(empty($f['note'])) {

	} else{
		$respOk = Customer::insertNewNote($f,$customerID,$contactID); // add a new Note
		if ($respOk){
			$objResponse->addAlert($locate->Translate("a_new_note_added"));
		}else{
			$objResponse->addAlert($locate->Translate("note_add_error"));
			return $objResponse;
		}
	}


	$objResponse->addAssign("formDiv", "style.visibility", "hidden");
	$objResponse->addAssign("formCustomerInfo", "style.visibility", "hidden");
	$objResponse->addAssign("formContactInfo", "style.visibility", "hidden");

	$objResponse->addClear("formDiv", "innerHTML");
	$objResponse->addClear("formCustomerInfo", "innerHTML");
	$objResponse->addClear("formContactInfo", "innerHTML");

	return $objResponse->getXML();

//	$message = Customer::checkAllData($f,1); // <-- Change by your method
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
					$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("contact_update_error"));
				}
			}

			if ($respOk != 0){
				$contactID = $respOk;
				$respOk = Customer::insertNewNote($f,$customerID,$contactID); // add a new Note
				if ($respOk){
					$html = createGrid(0,ROWSXPAGE);
					$objResponse->addAssign("grid", "innerHTML", $html);
					$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("note_add_success"));
					$objResponse->addAssign("formDiv", "style.visibility", "hidden");
					$objResponse->addAssign("formCustomerInfo", "style.visibility", "hidden");
					$objResponse->addAssign("formContactInfo", "style.visibility", "hidden");
				}else{
					$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("note_add_error"));
				}
			}else{
				$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("contact_update_error"));
			}
		}else{
			$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("customer_add_error"));
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
//		print_r($f);
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
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res)
		$objResponse->addAssign("mobileStatus", "innerText", "Failed");

//	$callerid = "Web Call <" . $_SESSION['curuser']['extension'] . ">";
//	$first = 'callee';
//	echo "ok";
	if ($config['system']['firstring'] == 'caller'){	//caller will ring first
		$strChannel = "Local/".$_SESSION['curuser']['extension']."@".$config['system']['incontext']."/n";

/*
		$temp=		$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
									'WaitTime'=>30,
									'Exten'=>$phoneNum,
									'Context'=>$config['system']['outcontext'],
									'Variable'=>"$strVariable",
									'Priority'=>1,
									'MaxRetries'=>1,
									'CallerID'=>$phoneNum));
*/
//	$objResponse->addAlert("dial to ".$temp);
		$myAsterisk->Originate($strChannel,$phoneNum,$config['system']['outcontext'],1,NULL,NULL,30,$phoneNum,NULL,NULL);
	}else{
		$strChannel = "Local/".$phoneNum."@".$config['system']['outcontext']."";
/*
				$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
									'WaitTime'=>30,
									'Exten'=>$_SESSION['curuser']['extension'],
									'Context'=>$config['system']['incontext'],
									'Variable'=>"$strVariable",
									'Priority'=>1,
									'MaxRetries'=>1,
									'CallerID'=>$phoneNum));
*/
		$myAsterisk->Originate($strChannel,$_SESSION['curuser']['extension'],$config['system']['incotext'],1,NULL,NULL,30,$phoneNum,NULL,NULL);
	}
	$myAsterisk->disconnect();
	return $objResponse->getXML();
}

function getContact($callerid){
	global $db,$locate;	
	$objResponse = new xajaxResponse();


	//check if there're phone records already

	//check contact table first
	$query = '
			SELECT id,customerid 
			FROM contact
			WHERE phone LIKE \'%'. $callerid . '%\'
			OR phone1 LIKE \'%'. $callerid . '%\'
			OR phone2 LIKE \'%'. $callerid . '%\'
			OR mobile LIKE \'%'. $callerid . '%\'
			 ' ;
//	print $query;
	$res = $db->query($query);

	if ($res->numRows() == 0){	//no match
		//try get customer
		$customerid = getCustomer($callerid);
//		print 'no match in contact';
		if ($customerid == 0){
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

function getCustomer($callerid){
	global $db,$locate;
	$query = '
			SELECT id 
			FROM customer
			WHERE phone LIKE \'%'. $callerid . '%\'';
	$res = $db->query($query);
//	print $res->numRows();
	if ($res->numRows() == 0){	//no match
		return 0;
	}else{
		$res->fetchInto($list);
		return $list['id'];
	}
}

$xajax->processRequests();

?>
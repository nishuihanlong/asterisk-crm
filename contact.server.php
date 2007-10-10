<?php
require_once ("db_connect.php");
require_once ("contact.common.php");
require_once ('grid.contact.manager.inc.php');
require_once ('asterevent.class.php');
require_once ('include/xajaxGrid.inc.php');

function export(){
	$objResponse = new xajaxResponse();

	$objResponse->addAssign("type","value","contact");
	$objResponse->addScript("xajax.$('frmDownload').submit();");
	$objResponse->addAlert("downloading, please wait");
	return $objResponse;
}

function init(){
	global $locate;//,$config,$db;

	$objResponse = new xajaxResponse();
	$html .= "<a href=# onclick=\"self.location.href='manager.php';return false;\">".$locate->Translate('back_to_mi')."</a><br>";
	$objResponse->addAssign("divPanel","innerHTML",$html);

	$objResponse->addScript("xajax_showGrid(0,".ROWSXPAGE.",'','','')");

	return $objResponse;
}

//	create grid
function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	global $locate;
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
	$fields[] = 'contact';
	$fields[] = 'gender';
	$fields[] = 'position';
	$fields[] = 'phone';
	$fields[] = 'mobile';
	$fields[] = 'email';
	$fields[] = 'customer';
//	$fields[] = 'cretime';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("contact");//"Customer Name";
	$headers[] = $locate->Translate("gender");//"Customer Name";
	$headers[] = $locate->Translate("position");//"Category";
	$headers[] = $locate->Translate("phone");//"Contact";
	$headers[] = $locate->Translate("mobile");//"Category";
	$headers[] = $locate->Translate("email");//"Note";
	$headers[] = $locate->Translate("customer_name");
//	$headers[] = $locate->Translate("create_time");//"Create Time";

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="7%"';
	$attribsHeader[] = 'width="8%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="25%"';
//	$attribsHeader[] = 'width="15%"';
//	$attribsHeader[] = 'width="7%"';
//	$attribsHeader[] = 'width="5%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'nowrap style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
//	$attribsCols[] = 'style="text-align: left"';
//	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","customer","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","state","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","city","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","phone","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","contact","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","website","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","category","'.$divName.'","ORDERING");return false;\'';
//	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","cretime","'.$divName.'","ORDERING");return false;\'';
//	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","creby","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'contact';
	$fieldsFromSearch[] = 'gender';
	$fieldsFromSearch[] = 'position';
	$fieldsFromSearch[] = 'phone';
	$fieldsFromSearch[] = 'mobile';
	$fieldsFromSearch[] = 'email';
	$fieldsFromSearch[] = 'customer';
	$fieldsFromSearch[] = 'creby';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("contact");
	$fieldsFromSearchShowAs[] = $locate->Translate("gender");
	$fieldsFromSearchShowAs[] = $locate->Translate("position");
	$fieldsFromSearchShowAs[] = $locate->Translate("phone");
	$fieldsFromSearchShowAs[] = $locate->Translate("mobile");
	$fieldsFromSearchShowAs[] = $locate->Translate("email");
	$fieldsFromSearchShowAs[] = $locate->Translate("customer_name");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_by");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearch("contact",$fieldsFromSearch,$fieldsFromSearchShowAs);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['contact'];
		$rowc[] = $row['gender'];
		$rowc[] = $row['position'];
		$rowc[] = $row['phone'];
		$rowc[] = $row['mobile'];
		$rowc[] = $row['email'];
		$rowc[] = $row['customer'];
//		$rowc[] = $row['creby'];
//		$rowc[] = 'Detail';
		$table->addRow("contact",$rowc,1,1,1,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}




function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	
	$html = createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	
	return $objResponse->getXML();
}

function add($callerid = null,$customerid = null,$contactid = null){
	global $locate;
	$objResponse = new xajaxResponse();
	return $objResponse;

	$html = Table::Top($locate->Translate("add_record"),"formDiv");  // <-- Set the title for your form.
//	$html .= Customer::formAdd($callerid,$customerid,$contactid);  // <-- Change by your method
	$html .= Customer::formAdd();
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


function edit($id = null, $tblName, $type = "contact"){
	global $locate;

	// Edit zone
	$html = Table::Top($locate->Translate("edit_record"),"formEditInfo");
	$html .= Customer::formEdit($id, $type);
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

function showDetail($recordID){
	global $locate;
	if($recordID != null){
		$html = Table::Top($locate->Translate("contact_detail"),"formContactInfo"); 			
		$html .= Customer::showContactRecord($recordID); 		
		$html .= Table::Footer();
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("formContactInfo", "style.visibility", "visible");
		$objResponse->addAssign("formContactInfo", "innerHTML", $html);	
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

function showNote($id = '', $type="contact"){
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

function save($f){
	$objResponse = new xajaxResponse();
	global $locate;

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

$xajax->processRequests();

?>
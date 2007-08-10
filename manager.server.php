<?php
require_once ("manager.common.php");
require_once ("db_connect.php");
require_once ('grid.account.inc.php');
require_once ('include/xajaxGrid.inc.php');


function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	$html = createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);

	return $objResponse;
}


function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){

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

	// HTML table: Headers showed
	$headers = array();
	$headers[] = "User Name";
	$headers[] = "Password";
	$headers[] = "Extension";

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="30%"';
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="50%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","username","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","password","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","extension","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'username';
	$fieldsFromSearch[] = 'passowrd';
	$fieldsFromSearch[] = 'extension';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = "User Name";
	$fieldsFromSearchShowAs[] = "Password";
	$fieldsFromSearchShowAs[] = "Extension";


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
		$table->addRow("account",$rowc,1,1,1,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}

function add(){
   // Edit zone
	$objResponse = new xajaxResponse();
	$html = Table::Top("Adding Account","formDiv");  // <-- Set the title for your form.
	$html .= Account::formAdd();  // <-- Change by your method
	// End edit zone
	$html .= Table::Footer();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	
	return $objResponse->getXML();
}

function save($f){
	$objResponse = new xajaxResponse();

	$message = Account::checkAllData($f,1); // <-- Change by your method

	$respOk = Account::insertNewAccount($f); // add a new account
	if ($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", "A note has been added");
		$objResponse->addAssign("formDiv", "style.visibility", "hidden");
		$objResponse->addAssign("msgZone", "innerHTML", "New account record added");
	}else{
		$objResponse->addAlert($message);
	}
	return $objResponse->getXML();
	
}

function update($f){
	$objResponse = new xajaxResponse();

	$respOk = Account::updateRecord($f);

	if($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", "A record has been updated");
		$objResponse->addAssign("formDiv", "style.visibility", "hidden");
	}else{
		$objResponse->addAssign("msgZone", "innerHTML", "The record could not be updated");
	}
	
	return $objResponse->getXML();
}

function delete($id = null){
	$respOk = Account::deleteRecord($id); 				// <-- Change by your method
	$objResponse = new xajaxResponse();
	if($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", "Record Deleted"); // <-- Change by your leyend
	}else{
		$objResponse->addAssign("msgZone", "innerHTML", "The record could not be deleted");
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


$xajax->processRequests();
?>
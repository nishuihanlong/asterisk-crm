<?php
require_once ("db_connect.php");
require_once ("customer.common.php");
require_once ('grid.note.manager.inc.php');
require_once ('asterevent.class.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('grid.common.php');

function export(){
	$objResponse = new xajaxResponse();

	$objResponse->addAssign("type","value","note");
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
	$fields[] = 'note';
	$fields[] = 'priority';
	$fields[] = 'contact';
	$fields[] = 'customer';
	$fields[] = 'cretime';
	$fields[] = 'creby';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("note");
	$headers[] = $locate->Translate("priority");
	$headers[] = $locate->Translate("contact");
	$headers[] = $locate->Translate("customer_name");//"Customer Name";
	$headers[] = $locate->Translate("create_time");//"Create By";
	$headers[] = $locate->Translate("create_by");

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="25%"';
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="10%"';
//	$attribsHeader[] = 'width="5%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","customer","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","state","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","city","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","phone","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","contact","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","website","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'note';
	$fieldsFromSearch[] = 'priority';
	$fieldsFromSearch[] = 'contact';
	$fieldsFromSearch[] = 'customer';
	$fieldsFromSearch[] = 'cretime';
	$fieldsFromSearch[] = 'creby';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("note");
	$fieldsFromSearchShowAs[] = $locate->Translate("priority");
	$fieldsFromSearchShowAs[] = $locate->Translate("contact");
	$fieldsFromSearchShowAs[] = $locate->Translate("customer_name");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_time");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_by");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,1,0);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearch("note",$fieldsFromSearch,$fieldsFromSearchShowAs);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['note'];
		$rowc[] = $row['priority'];
		$rowc[] = $row['contact'];
		$rowc[] = $row['customer'];
		$rowc[] = $row['cretime'];
		$rowc[] = $row['creby'];
//		$rowc[] = 'Detail';
		$table->addRow("note",$rowc,0,1,0,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}

function delete($id = null, $table_DB = null){
	global $locate;
	Customer::deleteRecord($id,'note'); 				// <-- Change by your method
	$html = createGrid(0,ROWSXPAGE);
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("grid", "innerHTML", $html);
	$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("record_deleted")); 
	return $objResponse->getXML();
}

$xajax->processRequests();

?>
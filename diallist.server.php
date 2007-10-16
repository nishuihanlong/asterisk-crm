<?php
require_once ("db_connect.php");
require_once ("diallist.common.php");
require_once ('grid.diallist.manager.inc.php');
require_once ('asterevent.class.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('grid.common.php');


function export(){
	$objResponse = new xajaxResponse();

	$objResponse->addAssign("type","value","customer");
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
		
		$numRows =& Diallist::getNumRows();
		$arreglo =& Diallist::getAllRecords($start,$limit,$order);
	}else{
		
		$numRows =& Diallist::getNumRows($filter, $content);
		$arreglo =& Diallist::getRecordsFiltered($start, $limit, $filter, $content, $order);	
	}

	// Editable zone

	// Databse Table: fields
	$fields = array();
	$fields[] = 'dialnumber';
	$fields[] = 'assign';
	//$fields[] = 'assign';
	
	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("number");//"Customer Name";
	$headers[] = $locate->Translate("area");//"Customer Name";
	//$headers[] = $locate->Translate("area");//"Customer Name";
	
	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="50%"';
	$attribsHeader[] = 'width="50%"';
	//$attribsHeader[] = 'width="5%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	//$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","dialnumber","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","assign","'.$divName.'","ORDERING");return false;\'';
	

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'dialnumber';
	$fieldsFromSearch[] = 'assign';
	//$fieldsFromSearch[] = 'assign';
	

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("number");
	$fieldsFromSearchShowAs[] = $locate->Translate("area");
	//$fieldsFromSearchShowAs[] = $locate->Translate("area");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearch("diallist",$fieldsFromSearch,$fieldsFromSearchShowAs);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['dialnumber'];
		$rowc[] = $row['assign'];
		
//		$rowc[] = 'Detail';
		$table->addRow("diallist",$rowc,1,1,1,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}


function edit($id = null, $tblName, $type = "diallist"){
	global $locate;

	// Edit zone
	$html = Table::Top($locate->Translate("edit_record"),"formEditInfo");
	$html .= Diallist::formEdit($id, $type);
	$html .= Table::Footer();
   	// End edit zone

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("formEditInfo", "style.visibility", "visible");
	$objResponse->addAssign("formEditInfo", "innerHTML", $html);
	return $objResponse->getXML();
}

function delete($id = null, $table_DB = null){
	global $locate;
	Diallist::deleteRecord($id); 				// <-- Change by your method
	$html = createGrid(0,ROWSXPAGE);
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("grid", "innerHTML", $html);
	$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("record_deleted")); 
	return $objResponse->getXML();
}

function showDetail($recordID){
	global $locate;
	if($recordID != null){
		$html = Table::Top($locate->Translate("customer_detail"),"formCustomerInfo"); 			
		$html .= Diallist::showCustomerRecord($recordID); 		
		$html .= Table::Footer();
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("formCustomerInfo", "style.visibility", "visible");
		$objResponse->addAssign("formCustomerInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}
}

function importCsv(){
	$objResponse = new xajaxResponse();
	//$objResponse->addScript("gotourl('./index.html');");
	$objResponse->addScript("window.location.href='./importcsv.php'");
	return $objResponse->getXML();
}

$xajax->processRequests();

?>
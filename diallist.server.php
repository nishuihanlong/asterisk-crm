<?php
require_once ("db_connect.php");
require_once ("diallist.common.php");
require_once ('include/xajaxGrid.inc.php');
require_once ('diallist.grid.inc.php');
require_once ('astercrm.server.common.php');
require_once ('include/common.class.php');


function init(){
	global $locate;

	$objResponse = new xajaxResponse();

	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

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
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,1,0);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearchDial("diallist",$fieldsFromSearch,$fieldsFromSearchShowAs);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['dialnumber'];
		$rowc[] = $row['assign'];
		
//		$rowc[] = 'Detail';
		$table->addRow("diallist",$rowc,0,1,0,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}


$xajax->processRequests();

?>
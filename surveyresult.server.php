<?php
/*******************************************************************************
* surveyresult.server.php

* Function Desc
	provide surveyresult management script

* 功能描述
	提供问卷管理脚本

* Function Desc

	showGrid
	init				初始化页面元素
	createGrid			生成grid的HTML代码
	delete				删除一条问卷结果
	add					null
	showDetail			null

* Revision 0.045  2007/10/18 15:38:00  last modified by solo
* Desc: comment added

********************************************************************************/
require_once ("db_connect.php");
require_once ("survey.common.php");
require_once ('surveyresult.grid.inc.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/common.class.php');


function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	
	$html = createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	
	return $objResponse->getXML();
}

/**
*  initialize page elements
*
*/

function init(){
	global $locate;//,$config,$db;

	$objResponse = new xajaxResponse();

	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	$objResponse->addScript("xajax_showGrid(0,".ROWSXPAGE.",'','','')");

	return $objResponse;
}

/**
*  generate grid HTML code
*  @param	start		int			record start
*  @param	limit		int			how many records need
*  @param	filter		string		the field need to search
*  @param	content		string		the contect want to match
*  @param	divName		string		which div grid want to be put
*  @param	order		string		data order
*  @return	html		string		grid HTML code
*/
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
	$fields[] = 'surveyname';
	$fields[] = 'surveyoption';
	$fields[] = 'surveynote';
	$fields[] = 'customer';
	$fields[] = 'contact';
	$fields[] = 'cretime';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("survey_title");
	$headers[] = $locate->Translate("survey_result");
	$headers[] = $locate->Translate("survey_note");
	$headers[] = $locate->Translate("customer");
	$headers[] = $locate->Translate("contact");
	$headers[] = $locate->Translate("create_time");

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="10%"';

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
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","surveyname","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","surveyoption","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","surveynote","'.$divName.'","ORDERING");return false;\'';
 	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","customer","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","contact","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","cretime","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'surveyname';
	$fieldsFromSearch[] = 'surveyoption';
	$fieldsFromSearch[] = 'surveynote';
	$fieldsFromSearch[] = 'customer';
	$fieldsFromSearch[] = 'contact.contact';
	$fieldsFromSearch[] = 'surveyresult.cretime';
	$fieldsFromSearch[] = 'surveyresult.creby';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("survey_title");
	$fieldsFromSearchShowAs[] = $locate->Translate("survey_result");
	$fieldsFromSearchShowAs[] = $locate->Translate("survey_note");
	$fieldsFromSearchShowAs[] = $locate->Translate("customer");
	$fieldsFromSearchShowAs[] = $locate->Translate("contact");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_time");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_by");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,1,1);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearch("surveyresult",$fieldsFromSearch,$fieldsFromSearchShowAs);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['surveyname'];
		$rowc[] = $row['surveyoption'];
		$rowc[] = $row['surveynote'];
		$rowc[] = $row['customer'];
		$rowc[] = $row['contact'];
		$rowc[] = $row['cretime'];

		$table->addRow("surveyresult",$rowc,0,1,1,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}

/**
*  delete survey result
*  @param	id				int			record start
*  @param	table_DB		string		table name
*  @return	objResponse		object		xajax response object
*/

function delete($id = null, $table_DB = null){
	global $locate;
	Customer::deleteRecord($id,$table_DB); 				// <-- Change by your method
	$html = createGrid(0,ROWSXPAGE);
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("grid", "innerHTML", $html);
	$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("record_deleted")); 
	return $objResponse->getXML();
}

/**
*  return null
*/
function add($surveyid = 0){
	global $locate;
	$objResponse = new xajaxResponse();
	return $objResponse;
}

/**
*  return null
*/
function showDetail($surveyid){
	$objResponse = new xajaxResponse();
	return $objResponse;
}

$xajax->processRequests();

?>
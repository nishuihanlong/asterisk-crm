<?php
/*******************************************************************************
* diallist.server.php

* 拨号列表管理系统后台文件
* diallist table background management script

* Function Desc
	provide diallist management script

* 功能描述
	提供帐户管理脚本

* Function Desc
		init				初始化页面元素
		createGrid
		showGrid
		add
		save
		delete
		searchFormSubmit    多条件搜索

* Revision 0.045  2007/10/18 20:43:00  last modified by solo
* Desc: add function add, showGrid, save, delete

* Revision 0.045  2007/10/18 12:40:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once ("db_connect.php");
require_once ("diallist.common.php");
require_once ('include/xajaxGrid.inc.php');
require_once ('diallist.grid.inc.php');
require_once ('include/common.class.php');


function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	
	$html = createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	
	return $objResponse->getXML();
}

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
	
	if($filter == null or $content == null or $content == 'Array' or $filter == 'Array'){
		$numRows =& Customer::getNumRows();
		$arreglo =& Customer::getAllRecords($start,$limit,$order);
		$content = null;
		$filter = null;
	}else{
		foreach($content as $value){
			if(trim($value) != ""){  //搜索内容有值
				$flag = "1";
				break;
			}
		}
		foreach($filter as $value){
			if(trim($value) != ""){  //搜索条件有值
				$flag2 = "1";
				break;
			}
		}
		if($flag != "1" || $flag2 != "1"){  //无值
			$order = null;
			$numRows =& Customer::getNumRows();
			$arreglo =& Customer::getAllRecords($start,$limit,$order);
		}else{
			$order = "id";
			$numRows =& Customer::getNumRowsMore($filter, $content,"diallist");
			$arreglo =& Customer::getRecordsFilteredMore($start, $limit, $filter, $content, $order,"diallist");
		}
	}


	// Editable zone

	// Databse Table: fields
	$fields = array();
	$fields[] = 'dialnumber';
	$fields[] = 'assign';
	$fields[] = 'groupid';
	
	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("number");
	$headers[] = $locate->Translate("assign_to");
	$headers[] = $locate->Translate("Group ID");
	
	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="50%"';
	$attribsHeader[] = 'width="30%"';
	$attribsHeader[] = 'width="20%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","dialnumber","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","assign","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","groupid","'.$divName.'","ORDERING");return false;\'';
	

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'dialnumber';
	$fieldsFromSearch[] = 'assign';
	$fieldsFromSearch[] = 'groupid';


	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("number");
	$fieldsFromSearchShowAs[] = $locate->Translate("assign_to");
	$fieldsFromSearchShowAs[] = $locate->Translate("Group ID");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,1,0);
	$table->setAttribsCols($attribsCols);
	//$table->addRowSearch("diallist",$fieldsFromSearch,$fieldsFromSearchShowAs);
	
	$table->exportFlag = '1';//对导出标记进行赋值
	$table->addRowSearchMore("diallist",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['dialnumber'];
		$rowc[] = $row['assign'];
		$rowc[] = $row['groupid'];
		
		$table->addRow("diallist",$rowc,0,1,0,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}

function add(){
	global $locate;
	$objResponse = new xajaxResponse();

	$html = Table::Top($locate->Translate("add_diallist"),"formDiv");  
	$html .= Customer::formAdd();
	$html .= Table::Footer();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);

	return $objResponse->getXML();
}

function save($f){
	global $locate;
	$objResponse = new xajaxResponse();
	$surveyid = Customer::insertNewDiallist($f); 
	$html = createGrid(0,ROWSXPAGE);
	$objResponse->addAssign("grid", "innerHTML", $html);
	$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("diallist_added"));
	$objResponse->addAssign("formDiv", "style.visibility", "hidden");
	$objResponse->addClear("formDiv", "innerHTML");
	return $objResponse->getXML();
}

function searchFormSubmit($searchFormValue,$numRows = null,$limit = null,$id = null,$type = null){
	global $locate,$db;
	$objResponse = new xajaxResponse();
	$searchField = array();
	$searchContent = array();
	$exportFlag = $searchFormValue['exportFlag'];
	$searchContent = $searchFormValue['searchContent'];  //搜索内容 数组
	$searchField = $searchFormValue['searchField'];      //搜索条件 数组
	$divName = "grid";
	if($exportFlag == "1"){
		$sql = astercrm::getSql($searchContent,$searchField,'diallist'); //得到要导出的sql语句
		$_SESSION['export_sql'] = $sql;
		$objResponse->addAssign("hidSql", "value", $sql); //赋值隐含域
		$objResponse->addScript("document.getElementById('exportForm').submit();");
	}else{
		if($type == "delete"){
			$res = Customer::deleteRecord($id,'cdr_horoscope');
			if ($res){
				$html = createGrid($searchFormValue['numRows'], $searchFormValue['limit'],$searchField, $searchContent, $searchField, $divName, "");
				$objResponse = new xajaxResponse();
				$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("delete_rec")); 
			}else{
				$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("rec_cannot_delete")); 
			}
		}else{
			$html = createGrid($numRows, $limit,$searchField, $searchContent, $searchField, $divName, "");
		}
		$objResponse->addClear("msgZone", "innerHTML");
		$objResponse->addAssign($divName, "innerHTML", $html);
	}
	return $objResponse->getXML();
}

$xajax->processRequests();

?>
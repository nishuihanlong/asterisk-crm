<?php
/*******************************************************************************
* note.server.php

* Function Desc
	provide note management script

* 功能描述
	提供备注管理脚本

* Function Desc

	export				提交表单, 导出contact数据
	init				初始化页面元素
	createGrid			生成grid的HTML代码
	searchFormSubmit    根据提交的搜索信息重构显示页面

* Revision 0.045  2007/10/22 16:45:00  last modified by solo
* Desc: remove function "export"

* Revision 0.045  2007/10/18 14:08:00  last modified by solo
* Desc: comment added

********************************************************************************/
require_once ("db_connect.php");
require_once ('note.grid.inc.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/common.class.php');
require_once ('astercrm.server.common.php');


/**
*  initialize page elements
*
*/

function init(){
	global $locate;

	$objResponse = new xajaxResponse();

	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	$objResponse->addScript("xajax_showGrid(0,".ROWSXPAGE.",'','','')");

	$objResponse->addAssign("btnCustomer","value",$locate->Translate("customer"));
	$objResponse->addAssign("btnContact","value",$locate->Translate("contact"));

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
	
	if($filter == null or $content == null){
		$numRows =& Customer::getNumRows();
		$arreglo =& Customer::getAllRecords($start,$limit,$order);
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
			$numRows =& Customer::getNumRowsMore($filter, $content,"note");
			$arreglo =& Customer::getRecordsFilteredMore($start, $limit, $filter, $content, $order,"note");
		}
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
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","note","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","priority","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","contact","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","customer","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","cretime","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","creby","'.$divName.'","ORDERING");return false;\'';

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
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader,1,1,0);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearchMore("note",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content);

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
		$table->addRow("note",$rowc,1,1,0,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}

function searchFormSubmit($searchFormValue,$numRows,$limit){
	global $locate,$db;

	$objResponse = new xajaxResponse();
	$searchField = array();
	$searchContent = array();
	$searchContent = $searchFormValue['searchContent'];  //搜索内容 数组
	$searchField = $searchFormValue['searchField'];      //搜索条件 数组
	$divName = "grid";
	$html = createGrid($numRows, $limit,$searchField, $searchContent, $searchField, $divName, "");
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	return $objResponse->getXML();
}

$xajax->processRequests();

?>
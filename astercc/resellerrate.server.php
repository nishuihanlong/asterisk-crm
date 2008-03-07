<?php
/*******************************************************************************
* resellerrate.server.php


* Function Desc

* 功能描述

* Function Desc


* Revision 0.01  2007/11/21 12:40:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once ("db_connect.php");
require_once ('resellerrate.grid.inc.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/common.class.php');
require_once ("resellerrate.common.php");

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

	return $objResponse;
}

/**
*  show grid HTML code
*  @param	start		int			record start
*  @param	limit		int			how many records need
*  @param	filter		string		the field need to search
*  @param	content		string		the contect want to match
*  @param	divName		string		which div grid want to be put
*  @param	order		string		data order
*  @return	objResponse	object		xajax response object
*/

function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	$html .= createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);

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

function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = "", $exportFlag=""){
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
			$numRows =& Customer::getNumRowsMore($filter, $content,"resellerrate");
			$arreglo =& Customer::getRecordsFilteredMore($start, $limit, $filter, $content, $order,"resellerrate");
		}
	}

	// Editable zone

	// Databse Table: fields
	$fields = array();
	$fields[] = 'dialprefix';
	$fields[] = 'numlen';
	$fields[] = 'destination';
	$fields[] = 'connectcharge';
	$fields[] = 'initblock';
	$fields[] = 'rateinitial';
	$fields[] = 'billingblock';
	$fields[] = 'resellername';
	$fields[] = 'addtime';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = "Prefix";
	$headers[] = "Number length";
	$headers[] = "Dest";
	$headers[] = "Connect Charge";
	$headers[] = "Init Block";
	$headers[] = "Rate";
	$headers[] = "Billing Block";
	$headers[] = "Reseller";
	$headers[] = "Add Time";

	// HTML table: fieldsFromSearch showed
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'dialprefix';
	$fieldsFromSearch[] = 'numlen';
	$fieldsFromSearch[] = 'destination';
	$fieldsFromSearch[] = 'rateinitial';
	$fieldsFromSearch[] = 'initblock';
	$fieldsFromSearch[] = 'billingblock';
	$fieldsFromSearch[] = 'connectcharge';
	$fieldsFromSearch[] = 'resellername';
	$fieldsFromSearch[] = 'addtime';

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="10%"';
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
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","dialprefix","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","numlen","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","destination","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","rateinitial","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","initblock","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","billingblock","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","connectcharge","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","resellername","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","addtime","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'dialprefix';
	$fieldsFromSearch[] = 'numlen';
	$fieldsFromSearch[] = 'destination';
	$fieldsFromSearch[] = 'rateinitial';
	$fieldsFromSearch[] = 'initblock';
	$fieldsFromSearch[] = 'billingblock';
	$fieldsFromSearch[] = 'connectcharge';
	$fieldsFromSearch[] = 'resellername';
	$fieldsFromSearch[] = 'addtime';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = 'dialprefix';
	$fieldsFromSearchShowAs[] = 'numlen';
	$fieldsFromSearchShowAs[] = 'destination';
	$fieldsFromSearchShowAs[] = 'rateinitial';
	$fieldsFromSearchShowAs[] = 'initblock';
	$fieldsFromSearchShowAs[] = 'billingblock';
	$fieldsFromSearchShowAs[] = 'connectcharge';
	$fieldsFromSearchShowAs[] = 'resellername';
	$fieldsFromSearchShowAs[] = 'addtime';


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	if ($_SESSION['curuser']['usertype'] == 'admin' )
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader,1,1,0);
	else
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,0,0);

	$table->setAttribsCols($attribsCols);
	$table->exportFlag = '1';//对导出标记进行赋值

	if ($_SESSION['curuser']['usertype'] == 'admin')
		$table->addRowSearchMore("resellerrate",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit);
	else
		$table->addRowSearchMore("resellerrate",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,0);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['dialprefix'];
		$rowc[] = $row['numlen'];
		$rowc[] = $row['destination'];
		$rowc[] = $row['connectcharge'];
		$rowc[] = $row['initblock'];
		$rowc[] = $row['rateinitial'];
		$rowc[] = $row['billingblock'];
		$rowc[] = $row['resellername'];
		$rowc[] = $row['addtime'];
		if ($_SESSION['curuser']['usertype'] == 'admin')
			$table->addRow("resellerrate",$rowc,1,1,0,$divName,$fields);
		else
			$table->addRow("resellerrate",$rowc,0,0,0,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}

/**
*  generate account add form HTML code
*  @return	html		string		account add HTML code
*/

function add(){
   // Edit zone
	global $locate;
	$objResponse = new xajaxResponse();
	$html = Table::Top("add rate","formDiv");  // <-- Set the title for your form.
	$html .= Customer::formAdd();  // <-- Change by your method
	// End edit zone
	$html .= Table::Footer();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	
	return $objResponse->getXML();
}

/**
*  save account record
*  @param	f			array		account record
*  @return	objResponse	object		xajax response object
*/

function save($f){
	global $locate;
	$objResponse = new xajaxResponse();

	// check if clid duplicate
	$res = astercrm::checkRateDuplicate("resellerrate",$f,"insert");
	if ($res != ''){
		$objResponse->addAlert("rate duplicate");
		return $objResponse->getXML();
	}


	$respOk = Customer::insertNewRate($f); // add a new account
	if ($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", "add a rate");
		$objResponse->addAssign("formDiv", "style.visibility", "hidden");
		$objResponse->addClear("formDiv", "innerHTML");
	}else{
		$objResponse->addAssign("msgZone", "innerHTML", "can not insert rate");
	}
	return $objResponse->getXML();
	
}

/**
*  update account record
*  @param	f			array		account record
*  @return	objResponse	object		xajax response object
*/

function update($f){
	global $locate;
	$objResponse = new xajaxResponse();

	$res = astercrm::checkRateDuplicate("resellerrate",$f,"update");
	if ($res != ''){
		$objResponse->addAlert("rate duplicate");
		return $objResponse->getXML();
	}

	$respOk = Customer::updateRateRecord($f);

	if($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", "update rate");
		$objResponse->addAssign("formDiv", "style.visibility", "hidden");
	}else{
		$objResponse->addAssign("msgZone", "innerHTML", "record cannot be updated");
	}
	
	return $objResponse->getXML();
}

/**
*  show account edit form
*  @param	id			int			account id
*  @return	objResponse	object		xajax response object
*/

function edit($id){
	global $locate;
	$html = Table::Top( "edit rate","formDiv"); 
	$html .= Customer::formEdit($id);
	$html .= Table::Footer();
	// End edit zone

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	return $objResponse->getXML();
}

/**
*  show account record detail
*  @param	accountid	int			account id
*  @return	objResponse	object		xajax response object
*/

function showDetail($accountid){
	$objResponse = new xajaxResponse();
	global $locate;
	$html = Table::Top( "rate detail","formDiv"); 
	$html .= Customer::showAccountDetail($accountid);
	$html .= Table::Footer();

	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	return $objResponse;
}

function searchFormSubmit($searchFormValue,$numRows,$limit,$id,$type){
	global $locate,$db;
	$objResponse = new xajaxResponse();
	$searchField = array();
	$searchContent = array();
	$exportFlag = $searchFormValue['exportFlag'];
	$searchContent = $searchFormValue['searchContent'];  //搜索内容 数组
	$searchField = $searchFormValue['searchField'];      //搜索条件 数组
	$divName = "grid";
	if($exportFlag == "1"){
		$sql = astercrm::getSql($searchContent,$searchField,'resellerrate'); //得到要导出的sql语句
		$_SESSION['export_sql'] = $sql;
		$objResponse->addAssign("hidSql", "value", $sql); //赋值隐含域
		$objResponse->addScript("document.getElementById('exportForm').submit();");
	}else{
		if($type == "delete"){
			$res = Customer::deleteRecord($id,'resellerrate');
			if ($res){
				$html = createGrid($searchFormValue['numRows'], $searchFormValue['limit'],$searchField, $searchContent, $searchField, $divName, "");
				$objResponse = new xajaxResponse();
				$objResponse->addAssign("msgZone", "innerHTML", "record deleted"); 
			}else{
				$objResponse->addAssign("msgZone", "innerHTML", "record cannot be deleted"); 
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

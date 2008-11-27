<?php
/*******************************************************************************
* resellergroup.server.php

* 账户管理系统后台文件
* resellergroup background management script

* Function Desc
	provide resellergroup management script

* 功能描述
	提供帐户管理脚本

* Function Desc
		init				初始化页面元素
		showGrid			显示grid
		createGrid			生成grid的HTML代码
		add					显示添加resellergroup的表单
		save				保存resellergroup信息
		update				更新resellergroup信息
		edit				显示修改resellergroup的表单
		delete				删除resellergroup信息
		showDetail			显示resellergroup详细信息
							当前返回空值
		searchFormSubmit    根据提交的搜索信息重构显示页面

* Revision 0.0456  2007/10/30 13:47:00  last modified by solo
* Desc: modify function showDetail, make it show resellergroup detail when click detail


* Revision 0.045  2007/10/19 10:01:00  last modified by solo
* Desc: modify extensions description

* Revision 0.045  2007/10/18 12:40:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once ("db_connect.php");
require_once ('resellergroup.grid.inc.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/astercrm.class.php');
require_once ('include/asterevent.class.php');
require_once ('include/common.class.php');
require_once ("resellergroup.common.php");

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

function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = "",$stype=array()){
	global $locate;
	$_SESSION['ordering'] = $ordering;
	
	if($filter == null or $content == null || (!is_array($content) && $content == 'Array') || (!is_array(filter) && $filter == 'Array')){
		$content = null;
		$filter = null;
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
		foreach($stype as $value){
			if(trim($value) != ""){  //搜索方式有值
				$flag3 = "1";
				break;
			}
		}

		if($flag != "1" || $flag2 != "1"){  //无值
			$order = null;
			$numRows =& Customer::getNumRows();
			$arreglo =& Customer::getAllRecords($start,$limit,$order);
		}elseif($flag3 != 1 ){
			$order = "id";
			$numRows =& Customer::getNumRowsMore($filter, $content,"resellergroup");
			$arreglo =& Customer::getRecordsFilteredMore($start, $limit, $filter, $content, $order,"resellergroup");
		}else{
			$order = "id";
			$numRows =& Customer::getNumRowsMorewithstype($filter, $content,$stype,$table);
			$arreglo =& Customer::getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table);
		}
	}
		
	// Select Box: type table.
	$typeFromSearch = array();
	$typeFromSearch[] = 'like';
	$typeFromSearch[] = 'equal';
	$typeFromSearch[] = 'more';
	$typeFromSearch[] = 'less';

	// Selecct Box: Labels showed on searchtype select box.
	$typeFromSearchShowAs = array();
	$typeFromSearchShowAs[] = $locate->Translate("like");
	$typeFromSearchShowAs[] = '=';
	$typeFromSearchShowAs[] = '>';
	$typeFromSearchShowAs[] = '<';

	// Editable zone

	// Databse Table: fields
	$fields = array();
	$fields[] = 'id';
	$fields[] = 'resellername';
	$fields[] = 'accountcode';
	$fields[] = 'callback';
	$fields[] = 'creditlimit';	
	$fields[] = 'limittype';
	$fields[] = 'curcredit';
	$fields[] = 'credit_clid';
	$fields[] = 'credit_group';
	$fields[] = 'credit_reseller';
	$fields[] = 'addtime';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("ID").'<br>';
	$headers[] = $locate->Translate("Name").'<br>';
	$headers[] = $locate->Translate("Account Code").'<br>';
	$headers[] = $locate->Translate("Callback").'<br>';
	$headers[] = $locate->Translate("Credit Limit").'<br>';
	$headers[] = $locate->Translate("Limit Status").'<br>';
	$headers[] = $locate->Translate("Curcredit").'<br>';
	$headers[] = $locate->Translate("Clid Credit").'<br>';
	$headers[] = $locate->Translate("Group Credit").'<br>';
	$headers[] = $locate->Translate("Reseller Credit").'<br>';
	$headers[] = $locate->Translate("Last Update").'<br>';

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width=""';
	$attribsHeader[] = 'width=""';
	$attribsHeader[] = 'width=""';
	$attribsHeader[] = 'width=""';
	$attribsHeader[] = 'width=""';
	$attribsHeader[] = 'width=""';
	$attribsHeader[] = 'width=""';
	$attribsHeader[] = 'width=""';
	$attribsHeader[] = 'width=""';
	$attribsHeader[] = 'width=""';
	$attribsHeader[] = 'width=""';

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
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","id","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","resellername","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","accountcode","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","allowcallback","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","creditlimit","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","limittype","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","curcredit","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","credit_clid","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","credit_group","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","credit_reseller","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","addtime","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'resellername';
	$fieldsFromSearch[] = 'accountcode';
	$fieldsFromSearch[] = 'callback';
	$fieldsFromSearch[] = 'creditlimit';
	$fieldsFromSearch[] = 'curcredit';
	$fieldsFromSearch[] = 'limittype';
	$fieldsFromSearch[] = 'credit_clid';
	$fieldsFromSearch[] = 'credit_group';
	$fieldsFromSearch[] = 'credit_reseller';
	$fieldsFromSearch[] = 'addtime';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("Reseller Name");
	$fieldsFromSearchShowAs[] = $locate->Translate("Account Code");
	$fieldsFromSearchShowAs[] = $locate->Translate("Callback");
	$fieldsFromSearchShowAs[] = $locate->Translate("Credit Limit");
	$fieldsFromSearchShowAs[] = $locate->Translate("Cur Credit");
	$fieldsFromSearchShowAs[] = $locate->Translate("Limit Status");
	$fieldsFromSearchShowAs[] = $locate->Translate("Clid Credit");
	$fieldsFromSearchShowAs[] = $locate->Translate("Group Credit");
	$fieldsFromSearchShowAs[] = $locate->Translate("Reseller Credit");
	$fieldsFromSearchShowAs[] = $locate->Translate("Last Update");

	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader,1,1,0);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearchMore("resellergroup",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,1,$typeFromSearch,$typeFromSearchShowAs,$stype);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['id'];
		$rowc[] = $row['resellername'];
		$rowc[] = $row['accountcode'];
		$rowc[] = $row['allowcallback'];
		$rowc[] = $row['creditlimit'];
		$rowc[] = $row['limittype'];
		$rowc[] = $row['curcredit'];
		$rowc[] = $row['credit_clid'];
		$rowc[] = $row['credit_group'];
		$rowc[] = $row['credit_reseller'];
		$rowc[] = $row['addtime'];
		$table->addRow("resellergroup",$rowc,1,1,0,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	return $html;
}

/**
*  generate resellergroup add form HTML code
*  @return	html		string		resellergroup add HTML code
*/

function add(){
   // Edit zone
	global $locate;
	$objResponse = new xajaxResponse();


	$html = Table::Top($locate->Translate("Add group"),"formDiv");  // <-- Set the title for your form.
	$html .= Customer::formAdd();  // <-- Change by your method
	// End edit zone
	$html .= Table::Footer();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	
	return $objResponse->getXML();
}

/**
*  save group record
*  @param	f			array		group record
*  @return	objResponse	object		xajax response object
*/

function save($f){
	global $locate,$db;
	$objResponse = new xajaxResponse();

	if (trim($f['resellername']) == ''){
		$objResponse->addAlert($locate->Translate("please enter the resellername"));
		return $objResponse;
	}

	$f['creditlimit'] = trim($f['creditlimit']);
	if ($f['creditlimit'] == '' or !is_numeric($f['creditlimit'])){
		$f['creditlimit'] = 0;
	}
	$respOk = Customer::insertNewResellergroup($f); // add a new group
	if ($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("add_reseller"));
		$objResponse->addAssign("formDiv", "style.visibility", "hidden");
		$objResponse->addClear("formDiv", "innerHTML");
	}else{
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("rec_cannot_insert"));
	}
	
	//generate include file
	if ($_SESSION['curuser']['usertype'] == 'admin'){
		astercc::generateResellerFile();
	}

	return $objResponse->getXML();
	
}

/**
*  update group record
*  @param	f			array		group record
*  @return	objResponse	object		xajax response object
*/

function update($f){
	global $locate;
	$objResponse = new xajaxResponse();

	$respOk = Customer::updateResellergroupRecord($f);
	if($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("update_rec"));
		$objResponse->addAssign("formDiv", "style.visibility", "hidden");
	}else{
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("rec_cannot_update"));
	}
	
	return $objResponse->getXML();
}

/**
*  show group edit form
*  @param	id			int			group id
*  @return	objResponse	object		xajax response object
*/

function edit($id){
	global $locate;
	$html = Table::Top( $locate->Translate("edit_reseller"),"formDiv"); 
	$html .= Customer::formEdit($id);
	$html .= Table::Footer();
	// End edit zone

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	return $objResponse->getXML();
}

/**
*  show group record detail
*  @param	groupid	int			group id
*  @return	objResponse	object		xajax response object
*/

function showDetail($groupid){
	$objResponse = new xajaxResponse();
	global $locate;
	$html = Table::Top( $locate->Translate("group_detail"),"formDiv"); 
	$html .= Customer::showGroupDetail($groupid);
	$html .= Table::Footer();

	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	return $objResponse;
}

function updateBillingtime($id,$billingtime){
	$objResponse = new xajaxResponse();
	global $locate;
	astercrm::updateField("resellergroup","billingtime",$billingtime,$id);
	astercrm::updateField("resellergroup","addtime",date("Y-m-d H:i:s"),$id);
	$objResponse->addScript("xajax_edit('".$id."')");
	return $objResponse;
}

function searchFormSubmit($searchFormValue,$numRows,$limit,$id,$type){
	global $locate,$db;
	$objResponse = new xajaxResponse();
	$searchField = array();
	$searchContent = array();
	$searchContent = $searchFormValue['searchContent'];  //搜索内容 数组
	$searchField = $searchFormValue['searchField'];      //搜索条件 数组
	$searchType =  $searchFormValue['searchType'];			//搜索方式 数组
	$divName = "grid";
	if($type == "delete"){
		$res = Customer::deleteRecords('resellerid',$id,'clid');
		$res = Customer::deleteRecords('resellerid',$id,'accountgroup');
		$res = Customer::deleteRecords('resellerid',$id,'rate');
		$res = Customer::deleteRecords('resellerid',$id,'callshoprate');
		$res = Customer::deleteRecords('resellerid',$id,'resellerrate');
		$res = Customer::deleteRecords('resellerid',$id,'account');

		$res = Customer::deleteRecord($id,'resellergroup');
		if ($res){
			$html = createGrid($searchFormValue['numRows'], $searchFormValue['limit'],$searchField, $searchContent, $searchField, $divName, "",$searchType);
			$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("delete_rec")); 
		}else{
			$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("rec_cannot_delete")); 
		}
	}else{
		$html = createGrid($numRows, $limit,$searchField, $searchContent, $searchField, $divName, "",$searchType);
	}
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	return $objResponse->getXML();
}

$xajax->processRequests();
?>

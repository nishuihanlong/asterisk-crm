<?php
/*******************************************************************************
* clid.server.php

* 账户管理系统后台文件
* clid background management script

* Function Desc
	provide clid management script

* 功能描述
	提供帐户管理脚本

* Function Desc
		init				初始化页面元素
		showGrid			显示grid
		createGrid			生成grid的HTML代码
		add					显示添加clid的表单
		save				保存clid信息
		update				更新clid信息
		edit				显示修改clid的表单
		delete				删除clid信息
		showDetail			显示clid详细信息
							当前返回空值
		searchFormSubmit    根据提交的搜索信息重构显示页面

* Revision 0.0456  2007/10/30 13:47:00  last modified by solo
* Desc: modify function showDetail, make it show clid detail when click detail


* Revision 0.045  2007/10/19 10:01:00  last modified by solo
* Desc: modify extensions description

* Revision 0.045  2007/10/18 12:40:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once ("db_connect.php");
require_once ('clid.grid.inc.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/astercrm.class.php');
require_once ('include/asterevent.class.php');
require_once ('include/asterisk.class.php');
require_once ('include/common.class.php');
require_once ("clid.common.php");

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

function generateSipFile(){
	$objResponse = new xajaxResponse();
	astercc::generatePeersFile();
	$objResponse->addAlert("sip conf file generated");
	return $objResponse;
}

function reloadSip(){
	$objResponse = new xajaxResponse();
	$myAsterisk = new Asterisk();
	$myAsterisk->execute("sip reload");
	$objResponse->addAlert("sip conf reloaded");
	return $objResponse;
}

function setGroup($resellerid){
	global $locate;
	$objResponse = new xajaxResponse();
	$res = astercrm::getAll("accountgroup",'resellerid',$resellerid);
	//添加option
	while ($res->fetchInto($row)) {
		$objResponse->addScript("addOption('groupid','".$row['id']."','".$row['groupname']."');");
	}
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

function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
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
		if($flag != "1" || $flag2 != "1"){  //无值
			$order = null;
			$numRows =& Customer::getNumRows();
			$arreglo =& Customer::getAllRecords($start,$limit,$order);
		}else{
			$order = "id";
			$numRows =& Customer::getNumRowsMore($filter, $content,"clid");
			$arreglo =& Customer::getRecordsFilteredMore($start, $limit, $filter, $content, $order,"clid");
		}
	}

	// Editable zone

	// Databse Table: fields
	$fields = array();
	$fields[] = 'clid';
	$fields[] = 'pin';
	$fields[] = 'display';
	$fields[] = 'status';
	$fields[] = 'groupname';
	$fields[] = 'resellername';
	$fields[] = 'addtime';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("Clid");
	$headers[] = $locate->Translate("Pin");
	$headers[] = $locate->Translate("Display");
	$headers[] = $locate->Translate("Status");
	$headers[] = $locate->Translate("Group");
	$headers[] = $locate->Translate("Reseller");
	$headers[] = $locate->Translate("Last Update");

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="15%"';
	$attribsHeader[] = 'width="15%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","clid","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","pin","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","display","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","status","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","groupname","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","resellername","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","addtime","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'clid';
	$fieldsFromSearch[] = 'pin';
	$fieldsFromSearch[] = 'display';
	$fieldsFromSearch[] = 'status';
	$fieldsFromSearch[] = 'groupname';
	$fieldsFromSearch[] = 'resellername';
	$fieldsFromSearch[] = 'clid.addtime';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("Clid");
	$fieldsFromSearchShowAs[] = $locate->Translate("Pin");
	$fieldsFromSearchShowAs[] = $locate->Translate("Diaplay");
	$fieldsFromSearchShowAs[] = $locate->Translate("Status");
	$fieldsFromSearchShowAs[] = $locate->Translate("Group");
	$fieldsFromSearchShowAs[] = $locate->Translate("Reseller");
	$fieldsFromSearchShowAs[] = $locate->Translate("Addtime");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'reseller'){
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader,1,1,0);
	}else{
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader,1,0,0);
	}
	$table->setAttribsCols($attribsCols);
	if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'reseller'){
		$table->addRowSearchMore("clid",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit);
	}else{
		$table->addRowSearchMore("clid",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,0);
	}

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = $row['clid'];
		$rowc[] = $row['pin'];
		$rowc[] = $row['display'];
		$rowc[] = $row['status'];
		$rowc[] = $row['groupname'];
		$rowc[] = $row['resellername'];
		$rowc[] = $row['addtime'];
	if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'reseller'){
			$table->addRow("clid",$rowc,1,1,0,$divName,$fields);
		}else{
			$table->addRow("clid",$rowc,1,0,0,$divName,$fields);
		}
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
	$html = Table::Top($locate->Translate("adding_pin"),"formDiv");  // <-- Set the title for your form.
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
	global $locate,$db;
	$objResponse = new xajaxResponse();
	//check clid could only be numuric
	if (!is_numeric($f['clid'])){
		$objResponse->addAlert("clid must be numeric");
		return $objResponse;
	}

	if ( trim($f['pin']) == '' ){
		$objResponse->addAlert("pin field cant be null");
		return $objResponse;
	}

	if ($f['groupid'] == 0 || $f['resellerid'] == 0){
		$objResponse->addAlert($locate->Translate("Please choose reseller and group"));
		return $objResponse->getXML();
	}

	// check if clid duplicate
	$res = astercrm::checkValues("clid","clid",$f['clid']);

	if ($res != ''){
		$objResponse->addAlert($locate->Translate("clid duplicate"));
		return $objResponse->getXML();
	}

	if ($f['display'] == '') {
		$f['display'] = $f['clid'];
	}

	// check if pin duplicate
	if ($f['pin'] != ''){
		$res = astercrm::checkValues("clid","pin",$f['pin'],"string","groupid",$f['groupid']);
		if ($res != ''){
			$objResponse->addAlert($locate->Translate("pin duplicate in same group"));
			return $objResponse->getXML();
		}
	}


	$respOk = Customer::insertNewClid($f); // add a new account
	if ($respOk){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("add_clid"));
		$objResponse->addAssign("formDiv", "style.visibility", "hidden");
		$objResponse->addClear("formDiv", "innerHTML");
	}else{
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("rec_cannot_insert"));
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

	if (!is_numeric($f['clid'])){
		$objResponse->addAlert("clid must be numeric");
		return $objResponse;
	}

	if ( trim($f['pin']) == '' ){
		$objResponse->addAlert("pin field cant be null");
		return $objResponse;
	}

	if ($f['groupid'] == 0 || $f['resellerid'] == 0){
		$objResponse->addAlert($locate->Translate("Please choose reseller and group"));
		return $objResponse->getXML();
	}

	// check if clid duplicate
	$res = astercrm::checkValuesNon($f['id'],"clid","clid",$f['clid']);

	if ($res != ''){
		$objResponse->addAlert($locate->Translate("clid duplicate"));
		return $objResponse->getXML();
	}


	// check if pin duplicate
	if ($f['pin'] != ''){
		$res = astercrm::checkValuesNon($f['id'],"clid","pin",$f['pin'],"string","groupid",$f['groupid']);
		if ($res != ''){
			$objResponse->addAlert($locate->Translate("pin duplicate in same group"));
			return $objResponse->getXML();
		}
	}

	if ($f['display'] == '') {
		$f['display'] = $f['clid'];
	}

//	$res = astercrm::checkValues("clid","clid",$f['clid']);

	$respOk = Customer::updateClidRecord($f);

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
*  show account edit form
*  @param	id			int			account id
*  @return	objResponse	object		xajax response object
*/

function edit($id){
	global $locate;
	$html = Table::Top( $locate->Translate("edit_clid"),"formDiv"); 
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
	$html = Table::Top( $locate->Translate("account_detail"),"formDiv"); 
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
	$searchContent = $searchFormValue['searchContent'];  //搜索内容 数组
	$searchField = $searchFormValue['searchField'];      //搜索条件 数组
	$divName = "grid";
	if($type == "delete"){
		$res = Customer::deleteRecord($id,'clid');
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
	return $objResponse->getXML();
}

$xajax->processRequests();
?>

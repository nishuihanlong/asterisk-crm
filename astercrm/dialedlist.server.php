<?php
/*******************************************************************************
* dialedlist.server.php

* Function Desc
	provide dialedlist management script

* 功能描述
	提供问卷管理脚本

* Function Desc

	showGrid
	export				提交表单, 导出contact数据
	init				初始化页面元素
	createGrid			生成grid的HTML代码
	delete
	edit
	editField
	updateField
	showDetail
	add
	save


* Revision 0.045  2007/10/18 15:38:00  last modified by solo
* Desc: comment added

********************************************************************************/
require_once ("db_connect.php");
require_once ("dialedlist.common.php");
require_once ('dialedlist.grid.inc.php');
require_once ('include/asterevent.class.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/common.class.php');


function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	
	$html = createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	
	return $objResponse->getXML();
}

function init(){
	global $locate;//,$config,$db;

	$objResponse = new xajaxResponse();

	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin,$_SESSION['curuser']['country'],$_SESSION['curuser']['language']));
	$objResponse->addAssign("btnDial","value",$locate->Translate("Dial list"));
	$objResponse->addAssign("btnCampaign","value",$locate->Translate("Campaign"));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	$objResponse->addScript("xajax_showGrid(0,".ROWSXPAGE.",'','','')");

	$noanswer = Customer::getNoanswerCallsNumber();
	$objResponse->addAssign("spanRecycle","innerHTML","No answer calls: $noanswer");

	return $objResponse;
}

function recycle($f){
	global $locate;
	$objResponse = new xajaxResponse();
	if(is_array($f['ckb'])){
		foreach($f['ckb'] as $value){
			$num = Customer::recycleDialedlistById($value);
		}
	}else{
		$num = Customer::recycleDialedlist();
	}
	$objResponse->addALert($num." ".$locate->Translate("number have been recycled"));
	$objResponse->addScript("init()");
	return $objResponse;
}

//	create grid
function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = "",$stype=array()){
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
		foreach($stype as $value){
			if(trim($value) != ""){  //搜索方式有值
				$flag3 = "1";
				break;
			}
		}
		if($flag != "1" || $flag2 != "1"){  //无值
			$order = null;
			$numRows =& Customer::getNumRows($_SESSION['curuser']['groupid']);
			$arreglo =& Customer::getAllRecords($start,$limit,$order,$_SESSION['curuser']['groupid']);
		}elseif($flag3 != 1){
			$order = "id";
			$numRows =& Customer::getNumRowsMore($filter, $content,"dialedlist");
			$arreglo =& Customer::getRecordsFilteredMore($start, $limit, $filter, $content, $order,"survey");
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
	$fields[] = 'dialednumber';
	$fields[] = 'answertime';
	$fields[] = 'duration';
	$fields[] = 'response';
	$fields[] = 'dialedby';
	$fields[] = 'dialedtime';
	$fields[] = 'groupname';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("ALL")."<input type='checkbox' onclick=\"ckbAllOnClick(this);\"><BR \>";
	$headers[] = $locate->Translate("Dialed Number");
	$headers[] = $locate->Translate("Answer Time");
	$headers[] = $locate->Translate("Duration");
	$headers[] = $locate->Translate("Response");
	$headers[] = $locate->Translate("Dial by");
	$headers[] = $locate->Translate("Dial time");
	$headers[] = $locate->Translate("Group");
	$headers[] = $locate->Translate("Campaign");

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

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","dialednumber","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","answertime","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","duration","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","response","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","dialedby","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","dialedtime","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","groupname","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","campaignname","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'dialednumber';
	$fieldsFromSearch[] = 'answertime';
	$fieldsFromSearch[] = 'duration';
	$fieldsFromSearch[] = 'response';
	$fieldsFromSearch[] = 'dialedby';
	$fieldsFromSearch[] = 'dialedtime';
	$fieldsFromSearch[] = 'groupname';
	$fieldsFromSearch[] = 'campaignname';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("Dialed Number");
	$fieldsFromSearchShowAs[] = $locate->Translate("Answer Time");
	$fieldsFromSearchShowAs[] = $locate->Translate("Duration");
	$fieldsFromSearchShowAs[] = $locate->Translate("Response");
	$fieldsFromSearchShowAs[] = $locate->Translate("Dial by");
	$fieldsFromSearchShowAs[] = $locate->Translate("Dial time");
	$fieldsFromSearchShowAs[] = $locate->Translate("Group");
	$fieldsFromSearchShowAs[] = $locate->Translate("Campaign");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,1,0);
	$table->setAttribsCols($attribsCols);
	$table->exportFlag = '1';//对导出标记进行赋值
	$table->deleteFlag = '1';//对删除标记进行赋值
	$table->addRowSearchMore("dialedlist",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,0,1,$typeFromSearch,$typeFromSearchShowAs,$stype);
	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc['select_id'] = $row['id'];
		$rowc[] = $row['dialednumber'];
		$rowc[] = $row['answertime'];
		$rowc[] = $row['duration'];
		$rowc[] = $row['response'];
		$rowc[] = $row['dialedby'];
		$rowc[] = $row['dialedtime'];
		$rowc[] = $row['groupname'];
		$rowc[] = $row['campaignname'];
		$table->addRow("dialedlist",$rowc,0,1,0,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render('delGrid');
 	
 	return $html;
}

function searchFormSubmit($searchFormValue,$numRows = null,$limit = null,$id = null,$type = null){
	global $locate,$db;
	$objResponse = new xajaxResponse();
	$searchField = array();
	$searchContent = array();
	$optionFlag = $searchFormValue['optionFlag'];
	$exportFlag = $searchFormValue['exportFlag'];
	$searchContent = $searchFormValue['searchContent'];  //搜索内容 数组
	$searchField = $searchFormValue['searchField'];      //搜索条件 数组
	$searchType =  $searchFormValue['searchType'];
	$divName = "grid";
	if($optionFlag == "export"){
		$sql = astercrm::getSql($searchContent,$searchField,$searchType,'dialedlist'); //得到要导出的sql语句
		$_SESSION['export_sql'] = $sql;
		$objResponse->addAssign("hidSql", "value", $sql); //赋值隐含域
		$objResponse->addScript("document.getElementById('exportForm').submit();");
	}elseif($optionFlag == "delete"){
		astercrm::deletefromsearch($searchContent,$searchField,$searchType,'dialedlist');
		$html = createGrid($searchFormValue['numRows'], $searchFormValue['limit'],'','','',$divName,"",'');
		$objResponse->addClear("msgZone", "innerHTML");
		$objResponse->addAssign($divName, "innerHTML", $html);
	}elseif($optionFlag == "recycle"){
		$num = Customer::recyclefromsearch($searchContent,$searchField,$searchType,'dialedlist');
		$html = createGrid($searchFormValue['numRows'], $searchFormValue['limit'],'','','',$divName,"",'');
		$objResponse->addClear("msgZone", "innerHTML");
		$objResponse->addALert($num." ".$locate->Translate("number have been recycled"));
		$objResponse->addAssign($divName, "innerHTML", $html);
		$noanswer = Customer::getNoanswerCallsNumber();
		$objResponse->addAssign("spanRecycle","innerHTML","No answer calls: $noanswer");
	}else{
		if($type == "delete"){
			$res = Customer::deleteRecord($id,'dialedlist');
			if ($res){
				$html = createGrid($searchFormValue['numRows'], $searchFormValue['limit'],$searchField, $searchContent, $searchField, $divName, "",$searchType);
				$objResponse = new xajaxResponse();
				$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("delete_rec")); 
			}else{
				$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("rec_cannot_delete")); 
			}
		}else{
			$html = createGrid($numRows, $limit,$searchField, $searchContent, $searchField, $divName, "",$searchType);
		}
		$objResponse->addClear("msgZone", "innerHTML");
		$objResponse->addAssign($divName, "innerHTML", $html);
	}
	return $objResponse->getXML();
}

function deleteByButton($f,$searchFormValue){
	$objResponse = new xajaxResponse();
	if(is_array($f['ckb'])){
		foreach($f['ckb'] as $vaule){
			$res_customer = astercrm::deleteRecord($vaule,'dialedlist');
		}
	}
	$searchContent = $searchFormValue['searchContent'];  //搜索内容 数组
	$searchField = $searchFormValue['searchField'];      //搜索条件 数组
	$numRows = $searchFormValue['numRows'];
	$limit = $searchFormValue['limit'];     
	$html = createGrid($numRows, $limit,$searchField, $searchContent, $searchField,'grid');
	$objResponse->addAssign('grid', "innerHTML", $html);
	return $objResponse->getXML();
}

$xajax->processRequests();

?>
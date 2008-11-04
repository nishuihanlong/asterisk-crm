<?php
/*******************************************************************************
* trunkinfo.server.php

* Function Desc
	provide trunkinfo management script

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
require_once ("cdr.common.php");
require_once ('cdr.grid.inc.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/common.class.php');


function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	
	$html = createGrid('','',$start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	
	return $objResponse->getXML();
}

function init(){
	global $locate;//,$config,$db;

	$objResponse = new xajaxResponse();

	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin,$_SESSION['curuser']['country'],$_SESSION['curuser']['language']));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	$objResponse->addScript("xajax_showGrid(0,".ROWSXPAGE.",'','','')");

	return $objResponse;
}

//	create grid
function createGrid($customerid='',$cdrtype='',$start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = "",$stype=array()){
	global $locate;

		$_SESSION['ordering'] = $ordering;
		if($filter == null || $content == null || (!is_array($content) && $content == 'Array') || (!is_array(filter) && $filter == 'Array')){
			$content = null;
			$filter = null;
			$numRows =& Customer::getCdrNumRows($customerid,$cdrtype);
			$arreglo =& Customer::getAllCdrRecords($customerid,$cdrtype,$start,$limit,$order);
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
			if($flag != "1" || $flag2 != "1" ){  //无值	
				$order = null;
				$numRows =& Customer::getCdrNumRows($customerid,$cdrtype);
				$arreglo =& Customer::getAllCdrRecords($customerid,$cdrtype,$start,$limit,$order);
			}elseif($flag3 != 1 ){  //未选择搜索方式
				$order = "calldate";
				$numRows =& Customer::getCdrNumRowsMore($customerid,$cdrtype,$filter, $content);
				$arreglo =& Customer::getCdrRecordsFilteredMore($customerid,$cdrtype,$start, $limit, $filter, $content, $order);
			}else{
				$order = "calldate";
				$numRows =& Customer::getCdrNumRowsMorewithstype($customerid,$cdrtype,$filter, $content,$stype);
				$arreglo =& Customer::getCdrRecordsFilteredMorewithstype($customerid,$cdrtype,$start, $limit, $filter, $content, $stype,$order);
			}
		}	
		// Databse Table: fields
		$fields = array();
		$fields[] = 'calldate';
		$fields[] = 'src';
		$fields[] = 'dst';
		$fields[] = 'didnumber';
		$fields[] = 'dstchannel';
		$fields[] = 'duration';
		$fields[] = 'billsec';
		$fields[] = 'disposition';
		$fields[] = 'credit';
		$fileds[] = 'destination';
		$fileds[] = 'memo';

		// HTML table: Headers showed
		$headers = array();
		$headers[] = $locate->Translate("Calldate")."<br>";
		$headers[] = $locate->Translate("Src")."<br>";
		$headers[] = $locate->Translate("Dst")."<br>";
		$headers[] = $locate->Translate("Callee Id")."<br>";
		$headers[] = $locate->Translate("Dynamic agent").'<br>';
		$headers[] = $locate->Translate("Duration")."<br>";
		$headers[] = $locate->Translate("Billsec")."<br>";
		$headers[] = $locate->Translate("Disposition")."<br>";
		$headers[] = $locate->Translate("Credit")."<br>";
		$headers[] = $locate->Translate("Destination")."<br>";
		$headers[] = $locate->Translate("Memo")."<br>";

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
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","calldate","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","src","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","dst","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","didnumber","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","dstchannel","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","duration","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","billsec","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","disposition","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","credit","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","destination","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","memo","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		
		// Select Box: type table.
		$typeFromSearch = array();
		$typeFromSearch[] = 'like';
		$typeFromSearch[] = 'equal';
		$typeFromSearch[] = 'more';
		$typeFromSearch[] = 'less';

		// Selecct Box: Labels showed on searchtype select box.
		$typeFromSearchShowAs = array();
		$typeFromSearchShowAs[] = 'like';
		$typeFromSearchShowAs[] = '=';
		$typeFromSearchShowAs[] = '>';
		$typeFromSearchShowAs[] = '<';

		// Select Box: fields table.
		$fieldsFromSearch = array();
		$fieldsFromSearch[] = 'src';
		$fieldsFromSearch[] = 'calldate';
		$fieldsFromSearch[] = 'dst';
		$fieldsFromSearch[] = 'didnumber';
		$fieldsFromSearch[] = 'billsec';
		$fieldsFromSearch[] = 'disposition';
		$fieldsFromSearch[] = 'credit';
		$fieldsFromSearch[] = 'destination';
		$fieldsFromSearch[] = 'memo';

		// Selecct Box: Labels showed on search select box.
		$fieldsFromSearchShowAs = array();
		$fieldsFromSearchShowAs[] = $locate->Translate("src");
		$fieldsFromSearchShowAs[] = $locate->Translate("calldate");
		$fieldsFromSearchShowAs[] = $locate->Translate("dst");
		$fieldsFromSearchShowAs[] = $locate->Translate("callee id");
		$fieldsFromSearchShowAs[] = $locate->Translate("billsec");
		$fieldsFromSearchShowAs[] = $locate->Translate("disposition");
		$fieldsFromSearchShowAs[] = $locate->Translate("credit");
		$fieldsFromSearchShowAs[] = $locate->Translate("destination");
		$fieldsFromSearchShowAs[] = $locate->Translate("memo");


		// Create object whit 5 cols and all data arrays set before.
		$table = new ScrollTable(9,$start,$limit,$filter,$numRows,$content,$order,$customerid,$cdrtype);
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader,$edit=false,$delete=false,$detail=false);
		$table->setAttribsCols($attribsCols);
		$table->addRowSearchMore("mycdr",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,0,0,$typeFromSearch,$typeFromSearchShowAs,$stype);

		while ($arreglo->fetchInto($row)) {
		// Change here by the name of fields of its database table
			$rowc = array();
			$rowc[] = $row['id'];
			$rowc[] = $row['calldate'];
			$rowc[] = $row['src'];
			$rowc[] = $row['dst'];
			$rowc[] = $row['didnumber'];
			if(strstr($row['dstchannel'],'AGENT')){
				$agent = split('/',$row['dstchannel']);
				$rowc[] = $agent['1'];
			}else{
				$rowc[]='';
			}
			$rowc[] = $row['duration'];
			$rowc[] = $row['billsec'];
			$rowc[] = $row['disposition'];
			$rowc[] = $row['credit'];
			$rowc[] = $row['destination'];
			$rowc[] = $row['memo'];
			$table->addRow("mycdr",$rowc,false,false,false,$divName,$fields);
		}
		
		// End Editable Zone
		
		$html = $table->render();
		
		return $html;
	}

function searchFormSubmit($searchFormValue,$numRows = null,$limit = null,$id = null,$type = null,$order= ''){
	global $locate,$db;
		$objResponse = new xajaxResponse();
		$searchField = array();
		$searchContent = array();
		$searchType = array();
		$searchContent = $searchFormValue['searchContent'];  //搜索内容 数组
		$searchField = $searchFormValue['searchField'];      //搜索条件 数组
		$searchType =  $searchFormValue['searchType'];			//搜索方式 数组
		$divName = "grid";

		//print_r($searchFormValue);exit;
		if($type == "delete"){
			$res = Customer::deleteRecord($id,'account');
			if ($res){
				$html = createGrid('','',$searchFormValue['numRows'], $searchFormValue['limit'],$searchField, $searchContent, $searchField, $divName, "",$searchType);
				$objResponse = new xajaxResponse();
				$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("delete_rec")); 
			}else{
				$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("rec_cannot_delete")); 
			}
		}else{
			$html .= createGrid('','',$numRows, $limit,$searchField, $searchContent,  $order, $divName, "",$searchType);
		}

		$objResponse->addClear("msgZone", "innerHTML");
		$objResponse->addAssign($divName, "innerHTML", $html);
		return $objResponse->getXML();
}

$xajax->processRequests();

?>
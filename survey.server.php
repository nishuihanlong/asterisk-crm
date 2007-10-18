<?php
/*******************************************************************************
* survey.server.php

* Function Desc
	provide survey management script

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
	setSurvey
	save


* Revision 0.045  2007/10/18 15:38:00  last modified by solo
* Desc: comment added

********************************************************************************/
require_once ("db_connect.php");
require_once ("survey.common.php");
require_once ('survey.grid.inc.php');
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
	$fields[] = 'surveyname';
	$fields[] = 'cretime';
	$fields[] = 'creby';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("survey_title");
	$headers[] = $locate->Translate("create_time");
	$headers[] = $locate->Translate("create_by");

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="65%"';
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="15%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","surveyname","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","create_time","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","create_by","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'surveyname';
	$fieldsFromSearch[] = 'cretime';
	$fieldsFromSearch[] = 'creby';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("survey_title");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_time");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_by");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader,1,1,1);
	$table->setAttribsCols($attribsCols);
	$table->addRowSearch("survey",$fieldsFromSearch,$fieldsFromSearchShowAs);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		if ($row['enable'] == 1)
			$rowc[] = $row['surveyname'];
		else
			$rowc[] = "<font color=gray>".$row['surveyname']."</font>";
		$rowc[] = $row['cretime'];
		$rowc[] = $row['creby'];
//		$rowc[] = 'Detail';
		$table->addRow("survey",$rowc,1,1,1,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}

function delete($id = null, $table_DB = null){
	global $locate;
	Customer::deleteSurvey($id); 				// <-- Change by your method
	$html = createGrid(0,ROWSXPAGE);
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("grid", "innerHTML", $html);
	$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("record_deleted")); 
	return $objResponse->getXML();
}

function edit($surveyid = 0){
	global $locate;
	if ($surveyid == 0)
		return ;
	$objResponse = new xajaxResponse();
	$html = Table::Top($locate->Translate("edit_survey"),"formDiv");  
	$html .= Customer::formAdd($surveyid);
	$html .= Table::Footer();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	$objResponse->addScript("xajax.$('surveyoption').focus();");

	return $objResponse->getXML();
}

function editField($table, $field, $cell, $value, $id){
	$objResponse = new xajaxResponse();
	
	$html =' <input type="text" id="input'.$cell.'" value="'.$value.'" size="'.(strlen($value)+5).'"'
			.' onBlur="xajax_updateField(\''.$table.'\',\''.$field.'\',\''.$cell.'\',document.getElementById(\'input'.$cell.'\').value,\''.$id.'\');"'
			.' style="background-color: #CCCCCC; border: 1px solid #666666;">';
	$objResponse->addAssign($cell, "innerHTML", $html);
	$objResponse->addScript("document.getElementById('input$cell').focus();");
	return $objResponse->getXML();
}

function updateField($table, $field, $cell, $value, $id){
	global $locate;
	$objResponse = new xajaxResponse();
	$objResponse->addAssign($cell, "innerHTML", $value);
	Customer::updateField($table,$field,$value,$id);
	if ($table == 'survey'){
		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("survey_updated"));
	}else{
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("option_updated"));
	}

	return $objResponse->getXML();
}

function add($surveyid = 0){
	global $locate;
	$objResponse = new xajaxResponse();

	$html = Table::Top($locate->Translate("add_survey"),"formDiv");  
	$html .= Customer::formAdd($surveyid);
	$html .= Table::Footer();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);

	if ($surveyid == 0 ){
		$objResponse->addScript("xajax.$('surveyname').focus();");
	}else{
		$objResponse->addScript("xajax.$('surveyoption').focus();");
	}

	return $objResponse->getXML();
}

function setSurvey($survey){
	global $locate;
//	print_r($survey);
//	exit;
	$objResponse = new xajaxResponse();
	if ($survey['radEnable'] == 1)
		Customer::setSurveyEnable(0);

	Customer::setSurveyEnable($survey['surveyid'],$survey['radEnable']);
//	print $surveyenable;

	$html = createGrid(0,ROWSXPAGE);
	$objResponse->addAssign("grid", "innerHTML", $html);
	$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("survey_updated"));
	$objResponse->addAssign("formDiv", "style.visibility", "hidden");
	$objResponse->addClear("formDiv", "innerHTML");

//	$objResponse->addAlert($locate->Translate("survey_updated"));
	return $objResponse;
}

function showDetail($surveyid){
	global $db,$locate;
	$objResponse = new xajaxResponse();

	//get all accounts
	$sql = "SELECT * FROM account";
	$resAccount =& $db->query($sql);
	if (!$resAccount)
		return;
//	return $objResponse;
	$html .= "<table width='98%'>";

	$ind = 0;

	while ($resAccount->fetchInto($account)){
//		print_r($account);
//		exit;
		if ($ind % 3 == 0)
			$html .= "<tr>";

		$html .= "<td valign='top' width='30%' align='left'>";

		$html  .= "<table>";
		$html .= "<tr><th align='left'>".$locate->Translate("agent").": ".$account['username']."</th></tr>";
		$sql = "SELECT COUNT(*) as number, surveyoption, surveynote FROM surveyresult WHERE creby = '".$account['username']."' AND surveyid = $surveyid GROUP BY surveyoption,surveynote";
		
		$res =& $db->query($sql);
		if ($res){
			$html .= "<tr><td>";
			$html .= "<table>";
			$html .= "<tr><td>".$locate->Translate("option")."</td><td>".$locate->Translate("note")."</td><td>".$locate->Translate("number")."</td></tr>";

			while ($res->fetchInto($row)){
				$html .= "<tr><td>".$row['surveyoption']."</td><td>".$row['surveynote']."</td><td>".$row['number']."</td></tr>";
			}
			$html .= "</table>";
			$html .= "</td></tr>";
		}
		$html .= "</table>";

		$html .= "</td>";

		$ind ++;
		if ($ind % 3 == 0)
			$html .= "<tr><td colspan=3 height=2 bgcolor=#000000></td></tr></tr>";
	}
	$rest = $ind % 3;
//	print $rest;
//	exit;
	if ($rest != 0){
		for ($i=0;$i<(3-$rest);$i++){
			$html .= "<td></td>";
		}
		$html .= "</tr>";
	}

	$html .= "</table>";
//	print $html;
//	exit;
	
	$sql = "SELECT COUNT(*) as number, surveyoption FROM surveyresult WHERE surveyid = $surveyid GROUP BY surveyoption";
	$totalrecords = 0;
	$res =& $db->query($sql);

		if ($res){
			$html .= "<table width=200 align=left>";
			$html .= "<tr><td colspan=2 align=left><strong>".$locate->Translate("total")."</strong></td></tr>";
			$html .= "<tr><td>Option</td><td>Number</td></tr>";

			while ($res->fetchInto($row)){
				$html .= "<tr><td>".$row['surveyoption']."</td><td>".$row['number']."</td></tr>";
				$totalreocrds += $row['number'];
			}
			$html .= "<tr><td colspan=2>".$locate->Translate("total").": ".$totalreocrds."</td></tr>";
			$html .= "</table>";
		}

	$objResponse->addAssign("divSurveyStatistc", "innerHTML", $html);

	return $objResponse;
}

	function save($f){
		global $locate;
		$objResponse = new xajaxResponse();
		if ($f['surveyid'] == 0){
			if ($f['surveyname'] == ''){
				$objResponse->addAlert($locate->Translate("please_enter_survey"));
				$objResponse->addScript("xajax.$('surveyname').focus();");
				return $objResponse;
			}else{
				$surveyid = Customer::insertNewSurvey($f['surveyname'],$f['radEnable']); 
				$html = createGrid(0,ROWSXPAGE);
				$objResponse->addAssign("grid", "innerHTML", $html);
				$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("survey_added"));
			}

		}
		else
			$surveyid = $f['surveyid'];


		if ($surveyid == 0){
			return $objResponse;
		}else{
			if ($f['surveyoption'] != ''){
				$surveyoptionid = Customer::insertNewOption($f['surveyoption'],$surveyid);
				$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("option_added"));
//				$objResponse->addAlert($locate->Translate("option_added"));
			}
		}
		$objResponse->addScript("xajax_add('".$surveyid."')");

		return $objResponse->getXML();
	}

$xajax->processRequests();

?>
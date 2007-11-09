<?php
/*******************************************************************************
* contact.server.php

* 联系人管理系统后台文件
* contact background management script

* Function Desc
	provide contact management script

* 功能描述
	提供联系人管理脚本

* Function Desc

	init				初始化页面元素
	createGrid			生成grid的HTML代码
	showDetail			显示contact信息
	searchFormSubmit    根据提交的搜索信息重构显示页面

* Revision 0.0451  2007/10/22 16:45:00  last modified by solo
* Desc: remove Edit and Detail tab in xajaxGrid

* Revision 0.045  2007/10/22 16:45:00  last modified by solo
* Desc: remove function "importCSV", "export"

* Revision 0.045  2007/10/18 14:30:00  last modified by solo
* Desc: remove function "edit"

* Revision 0.045  2007/10/18 12:40:00  last modified by solo
* Desc: page created

********************************************************************************/
require_once ("db_connect.php");
require_once ("contact.common.php");
require_once ('contact.grid.inc.php');
require_once ('asterevent.class.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('include/common.class.php');
require_once ('astercrm.server.common.php');


/**
*  initialize page elements
*
*/

function init(){
	global $locate;//,$config,$db;

	$objResponse = new xajaxResponse();

	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	$objResponse->addAssign("btnCustomer","value",$locate->Translate("customer"));
	$objResponse->addAssign("btnNote","value",$locate->Translate("note"));

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
			$numRows =& Customer::getNumRowsMore($filter, $content,"contact");
			$arreglo =& Customer::getRecordsFilteredMore($start, $limit, $filter, $content, $order,"contact");
		}
	}

	// Editable zone

	// Databse Table: fields
	$fields = array();
	$fields[] = 'contact';
	$fields[] = 'gender';
	$fields[] = 'position';
	$fields[] = 'phone';
	$fields[] = 'mobile';
	$fields[] = 'email';
	$fields[] = 'customer';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("contact");//"Customer Name";
	$headers[] = $locate->Translate("gender");//"Customer Name";
	$headers[] = $locate->Translate("position");//"Category";
	$headers[] = $locate->Translate("phone");//"Contact";
	$headers[] = $locate->Translate("mobile");//"Category";
	$headers[] = $locate->Translate("email");//"Note";
	$headers[] = $locate->Translate("customer_name");

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="7%"';
	$attribsHeader[] = 'width="8%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="25%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'nowrap style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","contact","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","gender","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","position","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","phone","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","mobile","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","email","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","customer","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'contact';
	$fieldsFromSearch[] = 'gender';
	$fieldsFromSearch[] = 'position';
	$fieldsFromSearch[] = 'phone';
	$fieldsFromSearch[] = 'mobile';
	$fieldsFromSearch[] = 'email';
	$fieldsFromSearch[] = 'customer';
	$fieldsFromSearch[] = 'creby';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("contact");
	$fieldsFromSearchShowAs[] = $locate->Translate("gender");
	$fieldsFromSearchShowAs[] = $locate->Translate("position");
	$fieldsFromSearchShowAs[] = $locate->Translate("phone");
	$fieldsFromSearchShowAs[] = $locate->Translate("mobile");
	$fieldsFromSearchShowAs[] = $locate->Translate("email");
	$fieldsFromSearchShowAs[] = $locate->Translate("customer_name");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_by");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,1,0);
	$table->setAttribsCols($attribsCols);
	//$table->addRowSearch("contact",$fieldsFromSearch,$fieldsFromSearchShowAs);
	$table->addRowSearchMore("contact",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content);
	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = '<a href=? onclick="xajax_showContact(\''.$row['id'].'\');return false;">'.$row['contact'].'</a>';
		$rowc[] = $row['gender'];
		$rowc[] = $row['position'];
		$rowc[] = $row['phone'];
		$rowc[] = $row['mobile'];
		$rowc[] = $row['email'];
		if ($row['customer'] == '')
			$rowc[] = $row['customer'];
		else
			$rowc[] = "<a href=? onclick=\"xajax_showCustomer('".$row['customerid']."','customer');return false;\"
		>".$row['customer']."</a>";

		$table->addRow("contact",$rowc,0,1,0,$divName,$fields);
 	}
 	
 	// End Editable Zone
 	
 	$html = $table->render();
 	
 	return $html;
}


/**
*  show contact record detail
*  @param	contactid	int			contact id
*  @return	objResponse	object		xajax response object
*/

function showDetail($contactid){
	global $locate;
	$objResponse = new xajaxResponse();
	if($contactid != null){
		$html = Table::Top($locate->Translate("contact_detail"),"formContactInfo"); 			
		$html .= Customer::showContactRecord($contactid); 		
		$html .= Table::Footer();
		$objResponse->addAssign("formContactInfo", "style.visibility", "visible");
		$objResponse->addAssign("formContactInfo", "innerHTML", $html);	
	}
	return $objResponse->getXML();
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
	$objResponse->addAssign($divName, "innerHTML", $html);
	return $objResponse->getXML();
}

$xajax->processRequests();

?>
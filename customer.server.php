<?php
/*******************************************************************************
* customer.server.php

* 客户管理系统后台文件
* customer background management script

* Function Desc
	provide customer management script

* 功能描述
	提供客户管理脚本

* Function Desc

	export				提交表单, 导出contact数据
	init				初始化页面元素
	createGrid			生成grid的HTML代码
	showDetail			显示contact信息
	searchFormSubmit    显示搜索信息
	addSearchTr         增加搜索条件

* Revision 0.0451  2007/10/22 16:45:00  last modified by solo
* Desc: remove Edit and Detail tab in xajaxGrid

* Revision 0.045  2007/10/22 16:45:00  last modified by solo
* Desc: remove function "importCSV" and "export"

* Revision 0.045  2007/10/18 14:30:00  last modified by solo
* Desc: remove function "edit"

* Revision 0.045  2007/10/18 14:08:00  last modified by solo
* Desc: comment added

********************************************************************************/
require_once ("db_connect.php");
require_once ("customer.common.php");
require_once ('customer.grid.inc.php');
require_once ('include/xajaxGrid.inc.php');
require_once ('astercrm.server.common.php');
require_once ('include/common.class.php');

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

	$objResponse->addAssign("btnContact","value",$locate->Translate("contact"));
	$objResponse->addAssign("btnNote","value",$locate->Translate("note"));

	//*******
	$objResponse->addAssign("by","value",$locate->Translate("by"));  //搜索条件
	$objResponse->addAssign("search","value",$locate->Translate("search")); //搜索内容
	//*******

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
			$numRows =& Customer::getNumRowsMore($filter, $content,"customer");
			$arreglo =& Customer::getRecordsFilteredMore($start, $limit, $filter, $content, $order,"customer");
		}
	}
	// Editable zone

	// Databse Table: fields
	$fields = array();
	$fields[] = 'customer';
	$fields[] = 'state';
	$fields[] = 'city';
	$fields[] = 'phone';
	$fields[] = 'contact';
	$fields[] = 'website';
	$fields[] = 'category';
	$fields[] = 'cretime';
	$fields[] = 'creby';

	// HTML table: Headers showed
	$headers = array();
	$headers[] = $locate->Translate("customer_name")."<BR \>";//"Customer Name";
	$headers[] = $locate->Translate("state")."<BR \>";//"Customer Name";
	$headers[] = $locate->Translate("city")."<BR \>";//"Category";
	$headers[] = $locate->Translate("phone")."<BR \>";//"Contact";
	$headers[] = $locate->Translate("contact")."<BR \>";//"Category";
	$headers[] = $locate->Translate("website")."<BR \>";//"Note";
	$headers[] = $locate->Translate("category")."<BR \>";//"Create Time";
	$headers[] = $locate->Translate("create_time")."<BR \>";//"Create By";
	$headers[] = $locate->Translate("create_by")."<BR \>";

	// HTML table: hearders attributes
	$attribsHeader = array();
	$attribsHeader[] = 'width="17%"';
	$attribsHeader[] = 'width="7%"';
	$attribsHeader[] = 'width="8%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="20%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="10%"';
	$attribsHeader[] = 'width="8%"';
//	$attribsHeader[] = 'width="5%"';

	// HTML Table: columns attributes
	$attribsCols = array();
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'nowrap style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';
	$attribsCols[] = 'style="text-align: left"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","customer","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","state","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","city","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","phone","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","contact","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","website","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","category","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","cretime","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","creby","'.$divName.'","ORDERING");return false;\'';

	// Select Box: fields table.
	$fieldsFromSearch = array();
	$fieldsFromSearch[] = 'customer';
	$fieldsFromSearch[] = 'state';
	$fieldsFromSearch[] = 'city';
	$fieldsFromSearch[] = 'phone';
	$fieldsFromSearch[] = 'contact';
	$fieldsFromSearch[] = 'website';
	$fieldsFromSearch[] = 'category';
	$fieldsFromSearch[] = 'cretime';
	$fieldsFromSearch[] = 'creby';

	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("customer_name");
	$fieldsFromSearchShowAs[] = $locate->Translate("state");
	$fieldsFromSearchShowAs[] = $locate->Translate("city");
	$fieldsFromSearchShowAs[] = $locate->Translate("phone");
	$fieldsFromSearchShowAs[] = $locate->Translate("contact");
	$fieldsFromSearchShowAs[] = $locate->Translate("website");
	$fieldsFromSearchShowAs[] = $locate->Translate("category");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_time");
	$fieldsFromSearchShowAs[] = $locate->Translate("create_by");


	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	$table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,1,0);
	$table->setAttribsCols($attribsCols);

	$table->addRowSearchMore("customer",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content);

//	$table->addRowSearchCustomer("customer",$fieldsFromSearch,$fieldsFromSearchShowAs);

	while ($arreglo->fetchInto($row)) {
	// Change here by the name of fields of its database table
		$rowc = array();
		$rowc[] = $row['id'];
		$rowc[] = '<a href=? onclick="xajax_showDetail(\''.$row['id'].'\');return false;">'.$row['customer'].'</a>';
		$rowc[] = $row['state'];
		$rowc[] = $row['city'];
		$rowc[] = $row['phone'];
		$rowc[] = $row['contact'];
		$rowc[] = $row['website'];
		$rowc[] = $row['category'];
		$rowc[] = $row['cretime'];
		$rowc[] = $row['creby'];
//		$rowc[] = 'Detail';
		$table->addRow("customer",$rowc,0,1,0,$divName,$fields);
 	}

 	// End Editable Zone

 	$html = $table->render();

 	return $html;
}

/**
*  show customer record detail
*  @param	contactid	int			contact id
*  @return	objResponse	object		xajax response object
*/

function showDetail($customerid){
	global $locate;
	$objResponse = new xajaxResponse();
	if($customerid != null){
		$html = Table::Top($locate->Translate("customer_detail"),"formCustomerInfo");
		$html .= Customer::showCustomerRecord($customerid);
		$html .= Table::Footer();
		$objResponse->addAssign("formCustomerInfo", "style.visibility", "visible");
		$objResponse->addAssign("formCustomerInfo", "innerHTML", $html);
	}
	return $objResponse->getXML();
}

/*function addSearchTr($search_str,$search_table){
	global $locate;
	$objResponse = new xajaxResponse();
	//$objResponse->addAlert($search_table);
	$searth_tr = '<br />'.$locate->Translate("search").' : &nbsp;<input type="text" size="30" id="searchContent"  name="searchContent[]">
				&nbsp;&nbsp;'.$locate->Translate("by").' &nbsp;
					<select id="searchField" name="searchField[]">';
	$searth_tr .= $search_str;
	$searth_tr .= '</select>';
	$add_search_str = $search_table.$searth_tr;
	$objResponse->addAppend("addSearth","innerHTML",$searth_tr);
	//$objResponse->addAssign("addSearth", "innerHTML", $add_search_str);
	return $objResponse->getXML();
}*/

function searchFormSubmit($searchFormValue,$numRows,$limit){
	global $locate,$db;
	$objResponse = new xajaxResponse();
	$searchField = array();
	$searchContent = array();
	$searchContent = $searchFormValue['searchContent'];  //搜索内容 数组
	$searchField = $searchFormValue['searchField'];      //搜索条件 数组
	
	//$limit = $searchFormValue['limit'];  // limit 
	//$numRowsToShow = $searchFormValue['numRowsToShow']; // start

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
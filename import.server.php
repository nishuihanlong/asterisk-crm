<?php
/*******************************************************************************
* import.server.php
* import函数信息文件
* import parameter file
* 功能描述
* Function Desc
	init()  页面初始化
	selectTable()  选择表
	submitForm()  将csv，xsl格式文件数据插入数据库
	showDivMainRight() 显示csv，xsl格式文件数据
	showCsv()  显示csv格式文件数据
	showXls()  显示xls格式文件数据
	getDiallistBar() 得到显示diallist导入框的HTML语法

* Revision 0.046  2007/11/8 8:33:00  modified by yunshida
* 描述: 取消了session的使用, 重新整理了流程

* Revision 0.045  2007/10/22 13:39:00  modified by yunshida
* Desc:
* 描述: 增加了包含include/common.class.php, 在init函数中增加了初始化对象divNav和divCopyright


* Revision 0.045  2007/10/18 15:25:00  modified by yunshida
* Desc: page create
* 描述: 页面建立

********************************************************************************/
require_once ("db_connect.php");
require_once ("import.common.php");
require_once ('include/excel.class.php');
require_once ('include/common.class.php');
require_once ('include/astercrm.class.php');
/**
*  function to init import page
*
*
*  @return $objResponse
*
*/
function init($fileName){
	global $locate,$config;
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("spanSelectFile","innerHTML", $locate->Translate("please_select_file"));

	$objResponse->addAssign("btnUpload","value",$locate->Translate("upload"));
	$objResponse->addAssign("btnImportDatas","value",$locate->Translate("import"));

	$objResponse->addAssign("spanFileManager","innerHTML", $locate->Translate("file_manager"));

	$objResponse->addAssign("hidAssignAlertMsg","value",$locate->Translate("assign_automaticly"));
	$objResponse->addAssign("hidOnUploadMsg","value",$locate->Translate("uploading"));
	$objResponse->addAssign("hidOnSubmitMsg","value",$locate->Translate("data_importing"));

	$tableList = "<select name='sltTable' id='sltTable' onchange='selectTable(this.value);' >
						<option value=''>".$locate->Translate("selecttable")."</option>
						<option value='customer'>customer</option>
						<option value='contact'>contact</option>
				  </select>";

	$objResponse->addAssign("divTables","innerHTML",$tableList);
	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divGrid", "innerHTML", '');
	//$objResponse->addScript("xajax_showDivMainRight(document.getElementById('hidFileName').value);");
	$objResponse->loadXML(showDivMainRight($fileName));
	//$objResponse->loadXML(showDivMainRight($fileName));
	//$objResponse->addAssign("divDiallistImport", "innerHTML", '');
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));
	return $objResponse;
}
/**
*  function to show divMainRight
*
*  @para	$filename		string
*  @return	$objResponse	object
*
*/
function showDivMainRight($filename){
	global $locate,$config;
	$objResponse = new xajaxResponse();

	$filePath = $config['system']['upload_excel_path'].$filename;

	if(is_file($filePath)){	//check if file exsits
//		print ($filePath);
//		exit;
		$dataContent = getGridDatas($filePath);
		$objResponse->addAssign("divGrid", "innerHTML", $dataContent['gridHTML']);

		$diallistBar = getDiallistBar($dataContent['columnNumber']);
		$objResponse->addAssign("divDiallistImport", "innerHTML", $diallistBar);
		$objResponse->addAssign("btnImportDatas", "disabled", false);
	}else{
		$objResponse->addAssign("divDiallistImport", "innerHTML", '');
		$objResponse->addAssign("divGrid", "innerHTML", '');
		$objResponse->addAssign("divMessage", "innerHTML",'');
	}

	return $objResponse;
}

/**
*  function to show table div
*
*  	@param $table	string		tablename
															customer
															contact
*  @return $objResponse
*
*/

function selectTable($tableName){
	global $locate,$db;

	$tableStructure = astercrm::getTableStructure($tableName);

	$HTML .= "<ul class='ulstyle'>";
	$i = 0;
	foreach($tableStructure as $row){
		$type_arr = explode(' ',$row['flags']);
		if(!in_array('auto_increment',$type_arr))
		{
			$HTML .= "<li height='20px'>";
			$HTML .= $i.":&nbsp;&nbsp;".$row['name'];
			$HTML .= "</li>";
		}
		$i++;
	}
	$HTML .= "</ul>";
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("divTableFields", "innerHTML", $HTML);
	$objResponse->addAssign("hidTableName","value",$tableName);
	$objResponse->addAssign("hidMaxTableColumnNum","value",$i-1);
	return $objResponse;
}

/**
*  function to insert data to database from excel
*
*  	@param $aFormValues	(array)			insert form excel
															$aFormValues['chkAdd']
															$aFormValues['chkAssign']
															$aFormValues['assign']
															$aFormValues['dialListField']
*	@return $objResponse
*
*/

function submitForm($aFormValues){
	global $locate,$db,$config;
	$objResponse = new xajaxResponse();
	$order = $aFormValues['order']; //得到的排序数字，数组形式，要添加到数据库的列
	$fileName = $aFormValues['hidFileName'];
	$tableName = $aFormValues['hidTableName'];

	$flag = 0;
	foreach($order as $value){
		if(trim($value) != ''){
			$flag = 1;
			break;
		}
	}
	if($flag != 1){
		if(trim($aFormValues['dialListField'])=='' && trim($aFormValues['assign'])==''){
			$flag = 0;
		}else{
			$flag = 1;
		}
	}
	//如果没有任何选择, 就退出
	if($flag != 1){
		$objResponse->addScript('init();');
//$objResponse->addAssign("btnImportDatas","disabled",false);
		return $objResponse;
	}
	
//	if ($fileName == '' )
	
	//对提交的数据进行校验
	$repeat_flag = 0;
	$order_num = count($order);
	if($order_num != 0)
	{
		$order_repeat = array_count_values($order);
		foreach($order_repeat as $key=>$value){
			if($key != '' && $value > 1){	//数据重复
				$objResponse->addAlert($locate->Translate('field_cant_repeat'));
				//$objResponse->addAssign("btnImportDatas","disabled",false);
				$objResponse->addScript('init();');
				return $objResponse;
			}
		}
	}
	for($j=0;$j<$order_num;$j++){
		if(trim($order[$j]) != ''){
			if(trim($order[$j]) > $aFormValues['hidMaxTableColumnNum']){  //最大值校验
				$objResponse->addAlert($locate->Translate('field_overflow'));
				//$objResponse->addAssign("btnImportDatas","disabled",false);
				$objResponse->addScript('init();');
				return $objResponse;
			}
			if (!ereg("[0-9]+",trim($order[$j]))){ //是否为数字
				$objResponse->addAlert($locate->Translate('field_must_digits'));
				//$objResponse->addAssign("btnImportDatas","disabled",false);
				$objResponse->addScript('init();');
				return $objResponse;
			}
		}
	}

	$tableStructure = astercrm::getTableStructure($tableName);
//	print_r($tableStructure);
	$filePath = $config['system']['upload_excel_path'].$fileName;//excel文件存放路径

	$affectRows= 0;  //计数据库影响结果变量
	$x = 0;  //计数变量
	$date = date('Y-m-d H:i:s'); //当前时间

	if($aFormValues['chkAdd'] != '' && $aFormValues['chkAdd'] == '1'){ //是否添加到拨号列表
		$dialListField = trim($aFormValues['dialListField']); //数字,得到将哪列添加到拨号列表

		if($aFormValues['chkAssign'] != '' && $aFormValues['chkAssign'] == '1'){ //是否添加分区assign
			$tmpStr = trim($aFormValues['assign']); //分区,以','号分隔的字符串
			if($tmpStr != ''){
				$area_array = explode(',',$tmpStr);
				$area_num = count($area_array);//得到手动添加分区个数
			}else{
				$sql_account = "SELECT extension FROM account";  //get extension from account
				$res = & $db->query($sql_account);  //get result
				while ($row = $res->fetchRow()) {
					$area_array[] = $row['extension']; //$array_extension数组,存放extension数据
				}
				$area_num = count($area_array); //extension数据的个数
			}
		}else{
			$area_array[] = '';
			$area_num = 1;
		}
	}

	$all_data_arr = getData($filePath,$order,$tableName,$tableStructure,$dialListField,$date);
	foreach($all_data_arr as $data){
		$sql_str = $data['sql_str'];  //得到插入选择表的sql语句
		$dialListValue = $data['dialListValue'];
		if(isset($dialListField) && trim($dialListField) != ''){  //是否存在添加到拨号列表
			if($x < $area_num){
				$assigned = $area_array[$x];
			}else{
				$x = 0;
				$assigned = $area_array[$x];
			}

			$x++;
			$tmpRs =@ $db->query("INSERT INTO diallist (dialnumber,assign) VALUES ('".$dialListValue."','".$assigned."')");  // 插入diallist表

		}
		if($tableName != ''){
//			print $sql_str;
//			exit;
			$rs = @ $db->query($sql_str);  //插入customer或contact表
			$affectRows+= mysql_affected_rows();   //得到影响的数据条数
		}
	}
	if($affectRows< 0){
		$affectRows= 0;
	}
	$overMsg = $tableName.' : '.$affectRows.' '.$locate->Translate('data');
	//delete upload file
	//@ unlink($filePath);
	$objResponse->addAlert($locate->Translate('success'));
	$objResponse->addScript("document.getElementById('btnImportDatas').disabled = false;");
	//$objResponse->addAssign("submitButton","value",$locate->Translate("submit"));
	$objResponse->addAssign("overMsg", "innerHTML",$overMsg);
	//$objResponse->addAssign("divGrid", "innerHTML", '');
	//$objResponse->addAssign("divMessage", "innerHTML",'');
	//$objResponse->addAssign("divDiallistImport", "innerHTML", '');
	$objResponse->addScript("init();");
	return $objResponse;
}

/**
*  function to show divDiallistImport
*/
function getDiallistBar($num){
	global $locate;
	$show_submit = "";
	$show_submit .= "<br />";
	$show_submit .= "
					<table cellspacing='0' cellpadding='0' border='0' width='100%' style='text-align:center;'>
						<tr>
							<td>
								<input type='checkbox' value='1' name='chkAdd' id='chkAdd' onclick='chkAddOnClick();'/>
								&nbsp;&nbsp; ".$locate->Translate('add')."
								<select name='dialListField' id='dialListField' disabled>
									<option value=''></option>";
	for ($c=0; $c < $num; $c++) {
		$show_submit .= "<option value='$c'>$c</option>";
	}
	$show_submit .= "
								</select> ".$locate->Translate('todiallist')." &nbsp;&nbsp;
								<input type='checkbox' value='1' name='chkAssign' id='chkAssign' onclick='chkAssignOnClick();' disabled/> ".$locate->Translate('area')."
								<input type='text' name='assign' id='assign' style='border:1px double #cccccc;width:200px;heiht:12px;' disabled />
							</td>
						</tr>
					</table>";
	return $show_submit;
}


function getData($filePath,$order,$tableName,$tableStructure,$dialListField,$date){
	$data_array = getRowDataArr($filePath);
	foreach($data_array as $data_arr){
		$sql_string = parseExcelToSql($data_arr,$order,$dialListField,$tableStructure,$tableName,$date);
		$sqlAndDialListValue_arr = explode('&&&&',$sql_string);
		$all_arr[] = array("sql_str"=>$sqlAndDialListValue_arr[0],"dialListValue"=>$sqlAndDialListValue_arr[1]);
	}
	return $all_arr;
}

function parseExcelToSql($data,$order,$dialListField,$tableStructure,$tableName,$date){//循环行数据，得到sql
	$mysql_field_name = '';
	$data_str = '';
	$row_num = count($data);  //列数
	for ($j=0;$j<$row_num;$j++)
	{
		if ($data[$j] != mb_convert_encoding($data[$j],"UTF-8","UTF-8"))
			$data[$j]=mb_convert_encoding($data[$j],"UTF-8","GB2312");
		$field_order = trim($order[$j]);//得到字段顺序号
		if($field_order != ''){
			$mysql_field_name .= $tableStructure[$field_order]['name'].',';
			$data_str .= '"'.$data[$j].'"'.',';
		}
		if(isset($dialListField) && $dialListField != ''){
			if($dialListField == $j)
				$dialListValue = $data[$j];
		}
	}
	$mysql_field_name = substr($mysql_field_name,0,strlen($mysql_field_name)-1);
	$data_str = substr($data_str,0,strlen($data_str)-1);
	$sql_str = "INSERT INTO $tableName ($mysql_field_name,cretime,creby) VALUES ($data_str,'".$date."','".$_SESSION['curuser']['username']."')";
	
	return $sql_str.'&&&&'.$dialListValue;
}

function getRowDataArr($filePath){  //得到excel文件的所有行数据，返回数组
	$type = substr($filePath,-3);
	if($type == 'csv'){  //csv 格式文件
		$handle = fopen($filePath,"r");  //打开excel文件,得到句柄
		while($data = fgetcsv($handle, 1000, ",")){
			$data_array[] = $data;
		}
	}elseif($type == 'xls'){  //xls格式文件
		Read_Excel_File($filePath,$return);
		for ($i=0;$i<count($return[Sheet1]);$i++){
			$data_array[] = $return[Sheet1][$i];
		}
	}
	return $data_array;
}

function getGridDatas($filePath){
	$data_array = getRowDataArr($filePath);
	$row = 0;
	$show_msg .= "<table cellspacing='1' cellpadding='0' border='0' width='100%'		style='text-align:left'>";
	foreach($data_array as $data_arr){
		$num = count($data_arr);
		$row++;
		$show_msg .= "<input type='hidden' name='CHECK' value='1'/>";
		
		$show_msg .= "<tr>";
		for ($c=0; $c < $num; $c++)
		{
			if ($data_arr[$c] != mb_convert_encoding($data_arr[$c],"UTF-8","UTF-8"))
					$data_arr[$c]=mb_convert_encoding($data_arr[$c],"UTF-8","GB2312");
			if($row % 2 != 0){
				$show_msg .= "<td bgcolor='#ffffff' height='25px'>&nbsp;".trim($data_arr[$c])."</td>";
			}else{
				$show_msg .= "<td bgcolor='#efefef' height='25px'>&nbsp;".trim($data_arr[$c])."</td>";
			}
		}
		$show_msg .= "</tr>";
		if($row == 8)
			break;
	}
	$show_msg .= "<tr>";
	for ($c=0; $c < $num; $c++) {
		$show_msg .= "<td bgcolor='#F0F8FF' height='25px'>
						&nbsp;<input type='text' style='width:20px;border:1px double #cccccc;height:12px;' name='order[]'  />
					  </td>";
	}
	$show_msg .= "</tr>";
	$show_msg .= "<tr>";
	for ($c=0; $c < $num; $c++) {
		$show_msg .= "<td height='20px' align='left'><font color='#000000'><b>$c</b></font></td>";
	}
	$show_msg .= "</tr>";
	$show_msg .= "</table>";

	
	return array('gridHTML'=>$show_msg,'columnNumber'=>$num);
}

$xajax->processRequests();

?>

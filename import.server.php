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
	showDivSubmitForm() 显示divSubmitForm

*
* 检查SESSION是否必要
* 导入结束后更新assign和add XX to diallist部分
*

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
/**
*  function to init import page
*
*
*  @return $objResponse
*
*/
function init(){
	global $locate,$config;
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("divFileName","innerHTML", $locate->Translate("file_name"));
	$objResponse->addAssign("btnUpload","value",$locate->Translate("upload"));
	$objResponse->addAssign("spanFileManager","innerHTML", $locate->Translate("filemanager"));
	$objResponse->addAssign("hidAssignAlertMsg","value",$locate->Translate("assign_automaticly"));
	$objResponse->addAssign("hidOnUploadMsg","value",$locate->Translate("uploading"));
	$objResponse->addAssign("onsubmitMsg","value",$locate->Translate("onsubmitMsg"));

	$showtable = "<ul style='list-style:none;'>
						<li>
							<select name='table' id='table' onchange='selectTable(this.value);' >
								<option value=''>".$locate->Translate("selecttable")."</option>
								<option value='customer'>customer</option>
								<option value='contact'>contact</option>
							</select>
						</li>
					</ul>
					<div id='tablefield' name='tablefield'></div>";

	$objResponse->addAssign("divShowTable","innerHTML",$showtable);
	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divShowExcel", "innerHTML", '');
	$objResponse->addAssign("divSubmitForm", "innerHTML", '');
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));
	return $objResponse;
}
/**
*  function to show divMainRight
*
*
*  @return $objResponse
*
*/
function showDivMainRight($filename){
	global $locate,$config;
	$objResponse = new xajaxResponse();
	/*
	* show divShowExcel
	*/
	$show_msg = "";
	$i=0;
	$file_path = $config['system']['upload_excel_path'].$filename;
	$file_name = $filename;
	$type = substr($file_name,-3);
	//检查文件是否存在
	if(file_exists($file_path)){
		$show_msgAndNum = showDatas($file_path,$type);
		$show_msgAndNum_arr = explode('&&&&&&',$show_msgAndNum);
		$show_msg = $show_msgAndNum_arr[0];
	}else{
		$show_msg = '';
		$show_msg = $locate->Translate("nofilechoose");
		return $objResponse;
	}
	/*
	* show divSubmitForm
	*/
	$show_submit = showDivSubmitForm($show_msgAndNum_arr[1]);
	$objResponse->addAssign("divShowExcel", "innerHTML", $show_msg);
	$objResponse->addAssign("divSubmitForm", "innerHTML", $show_submit);
	return $objResponse;
}

/**
*  function to show table div
*
*  	@param $table form select
															customer
															contact
*  @return $objResponse
*
*/

function selectTable($table){
	global $locate,$db;
	$sql = "select * from $table LIMIT 0,2";
	$res =& $db->query($sql);
	$tableInfo = $db->tableInfo($res);
	$show_msg .= "<ul class='ulstyle'>";
	$i = 0;
	foreach($tableInfo as $tablename){
		$type_arr = explode(' ',$tablename['flags']);
		$i++;
		$num = $i-1;
		if(!in_array('auto_increment',$type_arr))
		{
			$show_msg .= "<li height='20px'>";
			$show_msg .= $num.":&nbsp;&nbsp;".$tablename['name'];
			$show_msg .= "</li>";
		}
	}
	$show_msg .= "</ul>";
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("tablefield", "innerHTML", $show_msg);
	$objResponse->addAssign("TABLE_NAME","value",$table);
	$objResponse->addAssign("MAX_NUM","value",$num);
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
	foreach($order as $value){
		if(trim($value) != ''){
			$flag = '1';
			break;
		}
	}
	if($flag != '1'){
		if(trim($aFormValues['dialListField'])=='' && trim($aFormValues['assign'])==''){
			$flag = '0';
		}else{
			$flag = '1';
		}
	}
	//如果没有任何选择, 就退出
	if($flag != 1){
		$objResponse->addScript("init();");
		return $objResponse;
	}

	$file_name = $aFormValues['FILE_NAME'];
	$type = substr($file_name,-3);
	$table = $aFormValues['TABLE_NAME'];
	
	//对提交的数据进行校验
	$order_num = count($order);
	if($order_num!=0)
	{
		$order_repeat = array_count_values($order);
		foreach($order_repeat as $key=>$value){
			if($key != '' && $value > 1){
				$repeat_flag = '1';
			}
		}
	}
	for($j=0;$j<$order_num;$j++){
		if(trim($order[$j]) != ''){
			if(trim($order[$j]) > $aFormValues['MAX_NUM']){  //最大值校验
				$objResponse->addAlert($locate->Translate('fielderr'));
				$objResponse->addScript('init();');
				return $objResponse;
			}
			if (!ereg("[0-9]+",trim($order[$j]))){ //是否为数字
				$objResponse->addAlert($locate->Translate('fielderr'));
				$objResponse->addScript('init();');
				return $objResponse;
			}
			if( $repeat_flag == '1'){ //是否重复
				$objResponse->addAlert($locate->Translate('fieldcountrepeat'));
				$objResponse->addScript('init();');
				return $objResponse;
			}
		}
	}

	$sql = "SELECT * FROM $table LIMIT 0,2 ";
	$res =& $db->query($sql);
	$tableInfo = $db->tableInfo($res);  //得到要倒入数据的表结构
	$file_path = $config['system']['upload_excel_path'].$file_name;//excel文件存放路径
	//$objResponse->addAlert($file_path);
	$handle = fopen($file_path,"r");  //打开excel文件
	$affectRows= 0;  //计数据库影响结果变量
	$x = 0;  //计数变量
	$date = date('Y-m-d H:i:s'); //当前时间

	if($aFormValues['chkAdd'] != '' && $aFormValues['chkAdd'] == '1'){ //是否添加到拨号列表
		$dialListField = trim($aFormValues['dialListField']); //数字,得到将哪列添加到拨号列表
	}

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

	$all_data_arr = getData($file_path,$order,$table,$tableInfo,$dialListField,$date,$type);
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
		if($table != ''){
			$rs = @ $db->query($sql_str);  //插入customer或contact表
			$affectRows+= mysql_affected_rows();   //得到影响的数据条数
		}
	}
	if($affectRows< 0){
		$affectRows= 0;
	}
	$overMsg = $table.' : '.$affectRows.$locate->Translate('data');
	//delete upload file
	@ unlink($file_path);
	$objResponse->addAlert($locate->Translate('success'));
	$objResponse->addAssign("divMessage", "innerHTML",'');
	$objResponse->addAssign("overMsg", "innerHTML",$overMsg);
	$objResponse->addScript("document.getElementById('submitButton').disabled = false;");
	$objResponse->addAssign("submitButton","value",$locate->Translate("submit"));
	$objResponse->addAssign("divShowExcel", "innerHTML", '');
	$objResponse->addAssign("divSubmitForm", "innerHTML", '');
	$objResponse->addScript("init();");
	return $objResponse;
}

/**
*  function to show divSubmitForm
*/
function showDivSubmitForm($num){
	global $locate;
	$show_submit = "";
	$show_submit .= "<br />";
	$show_submit .= "
					<table cellspacing='0' cellpadding='0' border='0' width='100%' style='text-align:center;'>
						<tr>
							<td>
								<input type='hidden' value='0000' name='TEST' />
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
	$show_submit .= "
				<table cellspacing='0' cellpadding='0' border='0' width='100%' style='text-align:center;'>
					<tr>
						<td height='30px'>
							<input type='submit' value=".$locate->Translate('submit')." style='border:1px double #cccccc;width:200px' id='submitButton' name='submitButton'/>
						</td>
					</tr>
					<tr>
						<td height='30px'>
							<div style='width:100%;height:auto;lin-height:30px;text-align:center;' id='overMsg' name='overMsg'></div>
						</td>
					</tr>
				</table>
			</form>";
	return $show_submit;
}


function getData($file_path,$order,$table,$tableInfo,$dialListField,$date,$type){
	$data_array = getRowDataArr($type,$file_path);
	foreach($data_array as $data_arr){
		$sql_string = parseExcelToSql($data_arr,$order,$dialListField,$tableInfo,$table,$date);
		$sqlAndDialListValue_arr = explode('&&&&',$sql_string);
		$all_arr[] = array("sql_str"=>$sqlAndDialListValue_arr[0],"dialListValue"=>$sqlAndDialListValue_arr[1]);
	}
	return $all_arr;
}

function parseExcelToSql($data,$order,$dialListField,$tableInfo,$table,$date){//循环行数据，得到sql
	$mysql_field_name = '';
	$data_str = '';
	$row_num = count($data);  //列数
	for ($j=0;$j<$row_num;$j++)
	{
		if ($data[$j] != mb_convert_encoding($data[$j],"UTF-8","UTF-8"))
			$data[$j]=mb_convert_encoding($data[$j],"UTF-8","GB2312");
		$field_order = trim($order[$j]);//得到字段顺序号
		if($field_order != ''){
			$mysql_field_name .= $tableInfo[$field_order]['name'].',';
			$data_str .= '"'.$data[$j].'"'.',';
		}
		if(isset($dialListField) && $dialListField != ''){
			if($dialListField == $j)
				$dialListValue = $data[$j];
		}
	}
	$mysql_field_name = substr($mysql_field_name,0,strlen($mysql_field_name)-1);
	$data_str = substr($data_str,0,strlen($data_str)-1);
	$sql_str = "INSERT INTO $table ($mysql_field_name,cretime,creby) VALUES ($data_str,'".$date."','".$_SESSION['curuser']['username']."')";
	
	return $sql_str.'&&&&'.$dialListValue;
}

function getRowDataArr($type,$file_path){  //得到excel文件的所有行数据，返回数组
	if($type == 'csv'){  //csv 格式文件
		$handle = fopen($file_path,"r");  //打开excel文件,得到句柄
		while($data = fgetcsv($handle, 1000, ",")){
			$data_array[] = $data;
		}
	}elseif($type == 'xls'){  //xls格式文件
		Read_Excel_File($file_path,$return);
		for ($i=0;$i<count($return[Sheet1]);$i++){
			$data_array[] = $return[Sheet1][$i];
		}
	}
	return $data_array;
}

function showDatas($file_path,$type){
	$data_array = getRowDataArr($type,$file_path);
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
	return $show_msg.'&&&&&&'.$num;
}

$xajax->processRequests();

?>

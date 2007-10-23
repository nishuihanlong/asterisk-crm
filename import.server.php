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
	$objResponse->addAssign("alertmsg","value",$locate->Translate("by"));

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
	if(isset($_SESSION['filename']) && $_SESSION['filename'] != ''){
		$objResponse->addScript("showDivMainRight();");
	}
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
function showDivMainRight(){
	global $locate,$config;
	$objResponse = new xajaxResponse();
	/*
	* show divShowExcel
	*/
	$show_msg = "";
	$i=0;
	$row = 0;
	$file_path = $config['system']['upload_excel_path'].$_SESSION['filename'];
	$file_name = $_SESSION['filename'];
	$type = substr($file_name,-3);
	//需要检查文件是否存在
	$show_msg .= "<form method='post' name='formImport' id='formImport'>
					<input type='hidden' name='CHECK' value='1'/>";
	$show_msg .= "
					
						<table cellspacing='1' cellpadding='0' border='0' width='100%' style='text-align:left'>";
	if($type == 'csv'){
		$show_msg .= showCsv($file_path,$show_msg,$row);
	}elseif($type == 'xls'){
		$show_msg .= showXls($file_path,$row,$show_msg);
	}
	$show_msg .= "<tr>";
	for ($c=0; $c < $_SESSION['num']; $c++) {
		$show_msg .= "<td bgcolor='#0099cc' height='25px'>
						&nbsp;<input type='text' style='width:20px;border:1px double #cccccc;height:12px;' name='order[]'  />
					  </td>";
	}
	$show_msg .= "</tr>";
	$show_msg .= "<tr>";
	for ($c=0; $c < $_SESSION['num']; $c++) {
		$show_msg .= "<td height='20px' align='left'><font color='#000000'><b>$c</b></font></td>";
	}
	$show_msg .= "</tr>";
	$show_msg .= "</table>";

	if($show_msg == ""){
		$show_msg = $locate->Translate("nofilechoose");
	}
	/*
	* show divSubmitForm
	*/
	$show_submit = showDivSubmitForm($_SESSION['num']);

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
	$_SESSION['table'] = $table;
	$sql = "select * from $table";
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
		$_SESSION['MAX_NUM'] = $num;
	}
	$show_msg .= "</ul>";
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("tablefield", "innerHTML", $show_msg);
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
	$file_name = $_SESSION['filename'];
	$type = substr($file_name,-3);
	$table = $_SESSION['table'];
	$order = $aFormValues['order'];
	//对提交的数据进行校验
	for($j=0;$j<count($order);$j++){
		if(trim($order[$j]) != ''){
			if(trim($order[$j]) > $_SESSION['MAX_NUM']){
				$objResponse->addAlert($locate->Translate('fielderr'));
				$objResponse->addScript('init();');
				return $objResponse;
			}
			if (!ereg("[0-9]+",trim($order[$j]))){
				$objResponse->addAlert($locate->Translate('fielderr'));
				$objResponse->addScript('init();');
				return $objResponse;
			}
			if($_SESSION['edq'] == $order[$j]){
				$objResponse->addAlert($locate->Translate('fieldcountrepeat'));
				$objResponse->addScript('init();');
				return $objResponse;
			}else{
				$_SESSION['edq'] = $order[$j];
			} 
		}
	}
	$sql = "SELECT * FROM $table LIMIT 0,2 ";
	$res =& $db->query($sql);
	$tableInfo = $db->tableInfo($res); 
	$file_path = $config['system']['upload_excel_path'].$_SESSION['filename'];
	$handle = fopen($file_path,"r");
	$v = 0;
	$date = date('Y-m-d H:i:s');
	
	if($aFormValues['chkAdd'] != '' && $aFormValues['chkAdd'] == '1'){
		$mytext = trim($aFormValues['dialListField']); //数字
	}
	if($aFormValues['chkAssign'] != '' && $aFormValues['chkAssign'] == '1'){
		$mytext2 = trim($aFormValues['assign']); //分区,以','号分隔的字符串
		$area_array = explode(',',$mytext2);
		$area_num = count($area_array);//得到分区数
	}
	
	if($type == 'csv'){
		while($data = fgetcsv($handle, 1000, ",")){
			$row_num_csv = count($data);  
			$v++;
			$mysql_field_name = '';
			$data_str = '';
			for($i=0;$i<$row_num_csv;$i++){
				if ($data[$i] != mb_convert_encoding($data[$i],"UTF-8","UTF-8"))
					$data[$i]=mb_convert_encoding($data[$i],"UTF-8","GB2312");
				$field_order = trim($order[$i]);//字段顺序号
				if($field_order != ''){
					$mysql_field_name .= $tableInfo[$field_order]['name'].',';
					$data_str .= '"'.$data[$i].'"'.',';
				}
				if(isset($mytext) && $mytext != ''){
					if($mytext == $i)
						$array = $data[$i];
				}
			}
			$mysql_field_name = substr($mysql_field_name,0,strlen($mysql_field_name)-1);
			$data_str = substr($data_str,0,strlen($data_str)-1);
			$sql_str = "INSERT INTO $table ($mysql_field_name,cretime,creby) VALUES ($data_str,'".$date."','".$_SESSION['curuser']['username']."')";

			if(isset($mytext) && trim($mytext) != ''){
				if($mytext2 != '' && isset($mytext2)){
					$random_num = rand(0,$area_num-1);
					$random_area = $area_array[$random_num];
					$sql_string = "INSERT INTO diallist (dialnumber,assign) VALUES ('".$array."','".$random_area."')";
				}else{
					$sql_account = "SELECT extension FROM account";
					$res = & $db->query($sql_account);
					while ($row = $res->fetchRow()) { 
						$array_extension[] = $row['extension']; 
					}
					$extension_num = count($array_extension);
					$random_num = rand(0,$extension_num-1);
					$random_area = $array_extension[$random_num];
					$sql_string = "INSERT INTO diallist (dialnumber,assign) VALUES ('".$array."','".$random_area."')";
				}
			}
			$rs = & $db->query($sql_str);  //插入customer或contact表
			$rs2 = & $db->query($sql_string);  // 插入diallist表
		}
	}elseif($type == 'xls'){
		Read_Excel_File($file_path,$return);
		for ($i=0;$i<count($return[Sheet1]);$i++)
		{
			$mysql_field_name = '';
			$data_str = '';
			$row_num_xls = count($return[Sheet1][$i]);  //列数
			for ($j=0;$j<$row_num_xls;$j++)
			{
				if ($return[Sheet1][$i][$j] != mb_convert_encoding($return[Sheet1][$i][$j],"UTF-8","UTF-8"))
					$return[Sheet1][$i][$j]=mb_convert_encoding($return[Sheet1][$i][$j],"UTF-8","GB2312");
				$field_order = trim($order[$j]);//得到字段顺序号
				if($field_order != ''){
					$mysql_field_name .= $tableInfo[$field_order]['name'].',';
					$data_str .= '"'.$return[Sheet1][$i][$j].'"'.',';
				}
				if(isset($mytext) && $mytext != ''){
					if($mytext == $i)
						$array = $return[Sheet1][$i][$j];
				}
			}
			$mysql_field_name = substr($mysql_field_name,0,strlen($mysql_field_name)-1);
			$data_str = substr($data_str,0,strlen($data_str)-1);
			$sql_str = "INSERT INTO $table ($mysql_field_name,cretime,creby) VALUES ($data_str,'".$date."','".$_SESSION['curuser']['username']."')";

			if(isset($mytext) && trim($mytext) != ''){
				if($mytext2 != '' && isset($mytext2)){
					$random_num = rand(0,$area_num-1);
					$random_area = $area_array[$random_num];
					$sql_string = "INSERT INTO diallist (dialnumber,assign) VALUES ('".$array."','".$random_area."')";
				}else{
					$sql_account = "SELECT extension FROM account";
					$res = & $db->query($sql_account);
					while ($row = $res->fetchRow()) { 
						$array_extension[] = $row['extension']; 
					}
					$extension_num = count($array_extension);
					$random_num = rand(0,$extension_num-1);
					$random_area = $array_extension[$random_num];
					$sql_string = "INSERT INTO diallist (dialnumber,assign) VALUES ('".$array."','".$random_area."')";
				}
			}
			$rs = & $db->query($sql_str);  //插入customer或contact表
			$rs2 = & $db->query($sql_string);  // 插入diallist表
		}
	}
	//delete upload file
	//@ unlink($file_path);
	unset($_SESSION['filename']);
	unset($_SESSION['num']);
	unset($_SESSION['MAX_NUM']);
	unset($_SESSION['edq']);
	$objResponse->addAlert($locate->Translate('success'));
	$objResponse->addScript("init();");
	$objResponse->addScript("showDivMainRight();");
	return $objResponse;
}
/**
*  function to show csv file data
*/
function showCsv($file_path,$show_msg,$row){
	$handle = fopen($file_path,"r");
	while($data = fgetcsv($handle, 1000, ",")){
		$num = count($data);
		$row++;
		$show_msg .= "<tr>";
		for ($c=0; $c < $num; $c++) {
			if ($data[$c] != mb_convert_encoding($data[$c],"UTF-8","UTF-8"))
					$data[$c]=mb_convert_encoding($data[$c],"UTF-8","GB2312");
			if($row % 2 != 0){
				$show_msg .= "<td bgcolor='#ffffff' height='25px'>&nbsp;".$data[$c]."</td>";
			}else{
				$show_msg .= "<td bgcolor='#efefef' height='25px'>&nbsp;".$data[$c]."</td>";
			}
		}
		$show_msg .= "</tr>";
		if($row == 8)
			break;
	}
	fclose($handle);
	$_SESSION['num'] = $num;
	return $show_msg;
}
/**
*  function to show xls file data
*/
function showXls($file_path,$row,$show_msg){
	Read_Excel_File($file_path,$return);
	for ($i=0;$i<count($return[Sheet1]);$i++)
	{
		$row++;
		$show_msg .= "<tr>";
		$num = count($return[Sheet1][$i]);
		for ($j=0;$j<count($return[Sheet1][$i]);$j++)
		{
			if ($return[Sheet1][$i][$j] != mb_convert_encoding($return[Sheet1][$i][$j],"UTF-8","UTF-8"))
					$return[Sheet1][$i][$j]=mb_convert_encoding($return[Sheet1][$i][$j],"UTF-8","GB2312");
			if($row % 2 != 0){
				$show_msg .= "<td bgcolor='#ffffff' height='25px'>&nbsp;".$return[Sheet1][$i][$j]."</td>";
			}else{
				$show_msg .= "<td bgcolor='#efefef'
				height='25px'>&nbsp;".$return[Sheet1][$i][$j]."</td>";
			}
		}
		$show_msg .= "</tr>";
		if($row == 8)
			break;
	}
	$_SESSION['num'] = $num;
	return $show_msg;
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
							<input type='button' value=".$locate->Translate('submit')." style='border:1px double #cccccc;width:150px' onclick='submitFormOnSubmit();'/>
						</td>
					</tr>
				</table>
			</form>";
	return $show_submit;
}

$xajax->processRequests();

?>
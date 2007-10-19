<?php
/*******************************************************************************
* import.server.php
* import函数信息文件
* import parameter file
* 功能描述
* Function Desc

* Revision 0.045  2007/10/18 15:25:00  modified by yunshida
* Desc: page create
* 描述: 页面建立
  
********************************************************************************/
require_once ("db_connect.php");
require_once ("import.common.php");

function init(){
	global $locate;
	include('config.php');
	$file_name = $locate->Translate("file_name");
	$upload = $locate->Translate("upload");
	$filemanager = $locate->Translate("filemanager");
	$by = $locate->Translate("by");
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("file_name","innerHTML", $file_name );
	$objResponse->addAssign("upload","value",$upload);
	$objResponse->addAssign("spanFileManager","innerHTML", $filemanager );
	$objResponse->addAssign("alertmsg","value",$by);
	//************************************************************
	require_once ('include/excel.class.php');
	$show_msg = "";
	$i=0;
	$row = 0;
	$file_path = $config['system']['upload_excel_path'].$_SESSION['filename'];
	$file_name = $_SESSION['filename'];
	$type = substr($file_name,-3);
	$handle = fopen($file_path,"r");
	$show_msg .= "<form method='post' name='formImport' id='formImport'>
					<input type='hidden' name='CHECK' value='1'/>
					<table class='imagetable'>
						<tr>";
	$show_msg .= "<td style='border:0;width:25%;height:400px;' align='left' valign='top'>
					<ul style='list-style:none;'>
						<li>
							<select name='table' id='table' onchange='selectTable(this.value);' >
								<option value=''>".$locate->Translate("selecttable")."</option>
								<option value='customer'>customer</option>
								<option value='contact'>contact</option>
							</select>
						</li>
					</ul>
					<div id='tablefield' name='tablefield'></div>
				  </td>";
	$show_msg .= "<td style='border:0;' valign='top' width='75%'>
					<div style='width:650px;height:auto;margin:0;overflow:scroll;border:1px double #cccccc;'>
						<table cellspacing='1' cellpadding='0' border='0' width='100%' style='text-align:left'>";
	if($type == 'csv'){
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
	}elseif($type == 'xls'){
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
	}
	$show_msg .= "<tr>";
	for ($c=0; $c < $num; $c++) {
		$show_msg .= "<td bgcolor='#0099cc' height='25px'>
						&nbsp;<input type='text' style='width:20px;border:1px double #cccccc;height:12px;' name='order[]'  />
					  </td>";
	}
	$show_msg .= "</tr>";
	$show_msg .= "<tr>";
	for ($c=0; $c < $num; $c++) {
		$show_msg .= "<td height='20px' align='left'><font color='#000000'><b>$c</b></font></td>";
	}
	$show_msg .= "</tr>";
	$show_msg .= "</table></div></td>";
	fclose($handle);
	//*************************************************************
	if($show_msg == "") 
	{
		$show_msg = $locate->Translate("nofilechoose");
	}
	else 
	{
		$show_msg .= "</tr></table>";
	}
	$show_msg .= "
					<table cellspacing='0' cellpadding='0' border='0' width='100%' style='text-align:center;'>
						<tr>
							<td>
								<input type='checkbox' value='1' name='myCheckBox' id=name='myCheckBox' onclick='btnOnClick();'/> 
								&nbsp;&nbsp; ".$locate->Translate('add')."  
								<select name='dialListField' id='dialListField' disabled>
									<option value=''></option>";
	for ($c=0; $c < $num; $c++) {
		$show_msg .= "<option value='$c'>$c</option>";
	}
	$show_msg .= "
				</select> ".$locate->Translate('todiallist')." &nbsp;&nbsp; 
				<input type='checkbox' value='1' name='myCheckBox2' id=name='myCheckBox2' onclick='btnOnClick2();' disabled/> ".$locate->Translate('area')."  
				<input type='text' name='assign' id='assign' style='border:1px double #cccccc;width:200px;heiht:12px;' disabled />
						</td>
					</tr>
				</table>";
	$show_msg .= "
				<table cellspacing='0' cellpadding='0' border='0' width='100%' style='text-align:center;'>
					<tr>
						<td>
							<input type='button' value=".$locate->Translate('submit')." style='border:1px double #cccccc;' onclick='confirmMsg();'/>
						</td>
					</tr>
				</table>
			</form>";
	
	//************************************************************
	$objResponse->addAssign("iframeShowExcel", "innerHTML", $show_msg);
	return $objResponse;
}

function selectTable($table){
	global $locate,$db;
	$_SESSION['table'] = $table;
	$sql = "select * from $table";
	$res =& $db->query($sql);
	$tableInfo = $db->tableInfo($res); 
	$show_msg .= "<ul class='ulstyle'>";
	$i = 0;
	foreach($tableInfo as $tablename){
		$i++;
		$num = $i-1;
		$show_msg .= "<li height='20px'>";
		$show_msg .= $num.":&nbsp;&nbsp;".$tablename['name'];
		$show_msg .= "</li>";
		$_SESSION['MAX_NUM'] = $num;
	}
	$show_msg .= "</ul>";
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("tablefield", "innerHTML", $show_msg);
	return $objResponse;
}

function submitForm($aFormValues){
	global $locate,$db;
	require_once ("include/excel.class.php");
	$objResponse = new xajaxResponse();
	$file_name = $_SESSION['filename'];
	$type = substr($file_name,-3);
	$table = $_SESSION['table'];
	$order = $aFormValues['order'];
	for($j=0;$j<count($order);$j++){
		if(trim($order[$j]) != ''){
			if(trim($order[$j]) > $_SESSION['MAX_NUM']){
				$objResponse->addAlert($locate->Translate('font'));
				$objResponse->addScript('init();');
				return $objResponse;
			}
			if (!ereg("[0-9]+",trim($order[$j]))){
				$objResponse->addAlert($locate->Translate('font'));
				$objResponse->addScript('init();');
				return $objResponse;
			}
			if($_SESSION['edq'] == $order[$j]){
				$objResponse->addAlert($locate->Translate('repeat'));
				$objResponse->addScript('init();');
				return $objResponse;
			}else{
				$_SESSION['edq'] = $order[$j];
			} 
		}
	}
	$sql = "select * from $table";
	$res =& $db->query($sql);
	$tableInfo = $db->tableInfo($res); 
	$file_path = './upload/'.$_SESSION['filename'];
	$handle = fopen($file_path,"r");
	$v = 0;
	$date = date('Y-m-d H:i:s');
	//********************************
	if($aFormValues['myCheckBox'] != '' && $aFormValues['myCheckBox'] == '1'){
		$mytext = trim($aFormValues['dialListField']); //数字
	}
	if($aFormValues['myCheckBox2'] != '' && $aFormValues['myCheckBox2'] == '1'){
		$mytext2 = trim($aFormValues['assign']); //分区,以','号分隔的字符串
		$area_array = explode(',',$mytext2);
		$area_num = count($area_array);//得到分区数
	}
	//********************************
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
			$sql_str = "insert into $table ($mysql_field_name,cretime,creby) values ($data_str,'".$date."','".$_SESSION['curuser']['username']."')";

			if(isset($mytext) && trim($mytext) != ''){
				if($mytext2 != '' && isset($mytext2)){
					$random_num = rand(0,$area_num-1);
					$random_area = $area_array[$random_num];
					$sql_string = "insert into diallist (dialnumber,assign) values ('".$array."','".$random_area."')";
				}else{
					$sql_account = "select extension from account";
					$res = & $db->query($sql_account);
					while ($row = $res->fetchRow()) { 
						$array_extension[] = $row['extension']; 
					}
					$extension_num = count($array_extension);
					$random_num = rand(0,$extension_num-1);
					$random_area = $array_extension[$random_num];
					$sql_string = "insert into diallist (dialnumber,assign) values ('".$array."','".$random_area."')";
				}
			}
			$rs = & $db->query($sql_str);
			$rs2 = & $db->query($sql_string);
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
			$sql_str = "insert into $table ($mysql_field_name,cretime,creby) values ($data_str,'".$date."','".$_SESSION['curuser']['username']."')";

			if(isset($mytext) && trim($mytext) != ''){
				if($mytext2 != '' && isset($mytext2)){
					$random_num = rand(0,$area_num-1);
					$random_area = $area_array[$random_num];
					$sql_string = "insert into diallist (dialnumber,assign) values ('".$array."','".$random_area."')";
				}else{
					$sql_account = "select extension from account";
					$res = & $db->query($sql_account);
					while ($row = $res->fetchRow()) { 
						$array_extension[] = $row['extension']; 
					}
					$extension_num = count($array_extension);
					$random_num = rand(0,$extension_num-1);
					$random_area = $array_extension[$random_num];
					$sql_string = "insert into diallist (dialnumber,assign) values ('".$array."','".$random_area."')";
				}
			}
			$rs = & $db->query($sql_str);
			$rs2 = & $db->query($sql_string);
		}
	}
	unset($_SESSION['filename']);
	$objResponse->addAlert($locate->Translate('success'));
	$objResponse->addScript("init();");
	return $objResponse;
}

$xajax->processRequests();

?>
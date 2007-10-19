<?php
/*******************************************************************************
* show_excel.php
* excel信息文件
* import parameter file
* 功能描述
* Function Desc

* Revision 0.045  2007/10/18 15:25:00  modified by yunshida
* Desc: page create
* 描述: 页面建立
  
********************************************************************************/
header("content-type:text/html;charset=utf-8");
session_start();
require_once ('include/localization.class.php');
require_once ('include/excel.class.php');
include_once('config.php');
include("./include/template.php");
require_once('import.common.php');
$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'csv');
$t=new Template('./template/');
$t->caching = false;
$t->left_delimiter = "[##";
$t->right_delimiter = "##]";
$t->set_file("show_image", "show_image.tpl");
$show_msg = "";
$i=0;
$row = 0;
$file_path = $config['system']['upload_excel_path'].$_SESSION['filename'];
$file_name = $_SESSION['filename'];
$type = substr($file_name,-3);
$handle = fopen($file_path,"r");
$selecttable = $locate->Translate("selecttable");
if ($selecttable != mb_convert_encoding($selecttable,"UTF-8","UTF-8"))
			$selecttable=mb_convert_encoding($selecttable,"UTF-8","GB2312");
$show_msg .= "<form method='post' name='formImport' id='formImport'><input type='hidden' name='CHECK' value='1'/><table class='imagetable'><tr>";
$show_msg .= "<td style='border:0;width:25%;height:400px;' align='left' valign='top'><ul style='list-style:none;'><li><select name='table' id='table' onchange='selectTable(this.value);' ><option value=''>".$selecttable."</option><option value='customer'>customer</option><option value='contact'>contact</option></select></li></ul><div id='tablefield' name='tablefield' style='width:auto;height:auto;'>";
$show_msg .= "</div></td>";
$show_msg .= "<td style='border:0;' valign='top' width='75%'><div style='width:650px;height:auto;margin:0;overflow:scroll;border:1px double #cccccc;'><table cellspacing='1' cellpadding='0' border='0' width='100%' style='text-align:left'>";
if($type == 'csv'){
	while($data = fgetcsv($handle, 1000, ",")){
		$num = count($data);
		$row++;
		$show_msg .= "<tr>";
		for ($c=0; $c < $num; $c++) {
			if ($data[$c] != mb_convert_encoding($data[$c],"UTF-8","UTF-8"))
					$data[$c]=mb_convert_encoding($data[$c],"UTF-8","GB2312");
			if($row % 2 != 0){
				$show_msg .= "<td bgcolor='#ffffff'>".$data[$c]."</td>";
			}else{
				$show_msg .= "<td bgcolor='#efefef'>".$data[$c]."</td>";
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
				$show_msg .= "<td bgcolor='#ffffff'>".$return[Sheet1][$i][$j]."</td>";
			}else{
				$show_msg .= "<td bgcolor='#efefef'>".$return[Sheet1][$i][$j]."</td>";
			}
		}
		$show_msg .= "</tr>";
		if($row == 8)
			break;
	}
}
$show_msg .= "<tr>";
for ($c=0; $c < $num; $c++) {
	$show_msg .= "<td bgcolor='#0099cc' height='20px'><input type='text' style='width:20px;border:1px double #cccccc;height:12px;' name='order[]'  /></td>";
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
$add = $locate->Translate('add');
$to = $locate->Translate('todiallist');
$area = $locate->Translate('area');
$submit = $locate->Translate('submit');
if ($add != mb_convert_encoding($add,"UTF-8","UTF-8"))
			$add=mb_convert_encoding($add,"UTF-8","GB2312");
if ($to != mb_convert_encoding($to,"UTF-8","UTF-8"))
			$to=mb_convert_encoding($to,"UTF-8","GB2312");
if ($area != mb_convert_encoding($area,"UTF-8","UTF-8"))
			$area=mb_convert_encoding($area,"UTF-8","GB2312");
if ($submit != mb_convert_encoding($submit,"UTF-8","UTF-8"))
			$submit=mb_convert_encoding($submit,"UTF-8","GB2312");
$show_msg .= "<table cellspacing='0' cellpadding='0' border='0' width='100%' style='text-align:center;'><tr><td><input type='checkbox' value='1' name='myCheckBox' id=name='myCheckBox' onclick='btnOnClick();'/> &nbsp;&nbsp; $add  <select name='dialListField' id='dialListField' disabled><option value=''></option>";
for ($c=0; $c < $num; $c++) {
	$show_msg .= "<option value='$c'>$c</option>";
}
$show_msg .= "</select> $to &nbsp;&nbsp; <input type='checkbox' value='1' name='myCheckBox2' id=name='myCheckBox2' onclick='btnOnClick2();' disabled/> $area  <input type='text' name='assign' id='assign' style='border:1px double #cccccc;width:200px;heiht:12px;' disabled /></td></tr></table>";
$show_msg .= "<table cellspacing='0' cellpadding='0' border='0' width='100%' style='text-align:center;'><tr><td><input type='button' value=' submit ' style='border:1px double #cccccc;' onclick='confirmMsg();'/></td></tr></table></form>";
$show_js_function="callbackMessage(\"$show_msg\");";
$t->set_var(array("show_js_function"=> $show_js_function));
$t->parse("show_imageout","show_image");
$t->p("show_imageout");
?>
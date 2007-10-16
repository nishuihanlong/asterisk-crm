<?php
header("content-type:text/html;charset=utf-8");
//header("content-type:text/html;charset=gb2312");

session_start();
require_once ('include/Localization.php');
$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'csv');

include_once('config.php');

include("template.php");
$t=new Template('./template/');
$t->caching = false;
//$t->unknowns = "keep";
$t->left_delimiter = "[##";
$t->right_delimiter = "##]";

$t->set_file("show_image", "show_image.tpl");
//$t -> set_block("show_image", "rowlist", "rowpart");

//$image_exten = array('jpg','jpeg','png','gif');

//$current_dir = @opendir(UPLOAD_IMAGE_PATH);

$show_msg = "";
$i=0;
$row = 0;
$file_path = UPLOAD_IMAGE_PATH.$_SESSION['filename'];
$handle = fopen($file_path,"r");
$show_msg .= "<form action='./insert.php' method='post' name='submitForm'><input type='hidden' name='CHECK' value='1'/><table class='imagetable'><tr>";
$show_msg .= "<td style='border:0;width:15%;height:270px;' align='left' valign='top'><ul style='width:100%;height:20px;line-height:20px;list-style:none;text-align:left;'>";
//*************************
$link = mysql_connect('localhost', 'asteriskuser', 'movingon');
$fields = mysql_list_fields("asterisk", "customer", $link);
$columns = mysql_num_fields($fields);
for ($i = 0; $i < $columns; $i++) {
	$show_msg .= "<li height='20px'>";
	$show_msg .= $i.":&nbsp;&nbsp;".mysql_field_name($fields, $i);
	$show_msg .= "</li>";
	$_SESSION['MAX_NUM'] = $i;
} 
//*************************
$show_msg .= "</ul></td>";
$show_msg .= "<td style='border:0;' valign='top' width='85%'><div style='width:700px;height:auto;margin:0;overflow:scroll;border:1px double #cccccc;'><table cellspacing='1' cellpadding='0' border='0' width='100%'>";
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
$show_msg .= "<tr>";

for ($c=0; $c < $num; $c++) {
	$show_msg .= "<td bgcolor='#0099cc' height='20px'><input type='text' style='width:20px;border:1px double #cccccc;height:12px;' name='order[]'  /></td>";
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
if ($add != mb_convert_encoding($add,"UTF-8","UTF-8"))
			$add=mb_convert_encoding($add,"UTF-8","GB2312");
if ($to != mb_convert_encoding($to,"UTF-8","UTF-8"))
			$to=mb_convert_encoding($to,"UTF-8","GB2312");
if ($area != mb_convert_encoding($area,"UTF-8","UTF-8"))
			$area=mb_convert_encoding($area,"UTF-8","GB2312");
$show_msg .= "<table cellspacing='0' cellpadding='0' border='0' width='100%' ><tr><td><input type='checkbox' value='1' name='myCheckBox' id=name='myCheckBox' onclick='btnonclick();'/> &nbsp;&nbsp; $add  <input type='text' name='mytext' id='mytext' style='border:1px double #cccccc;width:20px;heiht:12px;' disabled /> $to &nbsp;&nbsp; <input type='checkbox' value='1' name='myCheckBox2' id=name='myCheckBox2' onclick='btnonclick2();' disabled/> $area  <input type='text' name='mytext2' id='mytext2' style='border:1px double #cccccc;width:200px;heiht:12px;' disabled /></td></tr></table>";

$show_msg .= "<table cellspacing='0' cellpadding='0' border='0' width='100%' ><tr><td><input type='submit' value=' submit ' style='border:1px double #cccccc;'></td></tr></table></form>";
//$show_msg = iconv($show_msg,'GB2312','utf-8');

$show_js_function="callbackMessage(\"$show_msg\");";

$t->set_var(array("show_js_function"=> $show_js_function));
$t->parse("show_imageout","show_image");
$t->p("show_imageout");
?>
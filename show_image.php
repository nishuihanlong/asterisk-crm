<?php
//header("content-type:text/html;charset=utf-8");
header("content-type:text/html;charset=gb2312");

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
//*********************************************************back
/*while($entryname = @readdir($current_dir)){
	$exten = pathinfo($entryname, PATHINFO_EXTENSION);
	if(in_array($exten,$image_exten)){
		if($i==0) $show_msg = "<table class='imagetable'><tr>";
		$filename = UPLOAD_IMAGE_PATH.$entryname;
		$show_msg .= "<td style='border:0;width:30%;'><img src='$filename' /></td>";
		$i++;
		if($i%3==0) $show_msg .="</tr><tr>";
	}
}
@closedir($current_dir);*/
//*************************************************************new
/***将test1…………作为表头生成excel文件
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:filename=test.csv");
echo "test1,";
echo "test2,";
echo "test1,";
echo "test2,";
echo "test1,";
echo "test2,";
echo "test1,";
echo "test2,";
echo "test1,";
echo "test2,";
echo "test1,";
echo "test2\t\n";
$row   =   1;   */
//$show_msg = '';
$row = 0;
$file_path = UPLOAD_IMAGE_PATH.$_SESSION['filename'];
$handle = fopen($file_path,"r");
$show_msg .= "<form action='./insert.php' method='post' name='submitForm'><input type='hidden' name='CHECK' value='1'/><table class='imagetable' style=''><tr>";
$show_msg .= "<td style='border:0;width:15%;height:270px;' align='left' valign='top'><ul style='width:100%;height:20px;line-height:20px;list-style:none;text-align:left;'><li height='20px'>1: customer</li><li height='20px'>2: address</li><li height='20px'>3: state</li><li height='20px'>4: city</li><li height='20px'>5: contact</li><li height='20px'>6: contactgender</li><li height='20px'>7: phone</li><li height='20px'>8: zipcode</li><li height='20px'>9: website</li><li height='20px'>10: category</li></ul></td>";
$show_msg .= "<td style='border:0;' valign='top'><table cellspacing='0' cellpadding='0' border='0' width='100%'>";
while($data = fgetcsv($handle, 1000, ",")){
    $num = count($data);
    $row++;
	$show_msg .= "<tr>";
	for ($c=0; $c < $num; $c++) {
		if($row == 1){
			$show_msg .= "<td bgcolor='orange'><font color='#0033cc'>".$data[$c]."</font><input type='text' style='width:20px;border:1px double #cccccc;' name='order[]'/></td>";
		}else{
			$show_msg .= "<td bgcolor='#ffffff'>".$data[$c]."</td>";
		}
    }
	$show_msg .= "</tr>";
	if($row == 8)
		break;
}
$show_msg .= "</table></td>";
fclose($handle);
//*************************************************************
if($show_msg == "") 
{
	$show_msg = "没有选中文件";
}
else 
{
	$show_msg .= "</tr></table>";
}
$show_msg .= "<table cellspacing='0' cellpadding='0' border='0' width='100%' ><tr><td><input type='submit' value=' submit ' style='border:1px double #cccccc;'></td></tr></table></form>";
//$show_msg = iconv($show_msg,'GB2312','utf-8');

$show_js_function="callbackMessage(\"$show_msg\");";

$t->set_var(array("show_js_function"=> $show_js_function));
$t->parse("show_imageout","show_image");
$t->p("show_imageout");
?>
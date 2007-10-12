<?php
header("content-type:text/html;charset=utf-8");

include_once('config.php');

include("template.php");
$t=new Template('./template/');
$t->caching = false;
//$t->unknowns = "keep";
$t->left_delimiter = "[##";
$t->right_delimiter = "##]";

$t->set_file("del", "upload.tpl");


$current_dir = @opendir(UPLOAD_IMAGE_PATH);
while($entryname = @readdir($current_dir)){
	@unlink(UPLOAD_IMAGE_PATH.$entryname);
}
@closedir($current_dir);

$del_msg = "删除成功";
$del_js_function="callbackMessage(\"$del_msg\");";


$t->set_var(array("upload_js_function"=> $del_js_function));
$t->parse("delout","del");
$t->p("delout");
?>
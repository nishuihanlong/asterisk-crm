<?php
header("content-type:text/html;charset=utf-8");
include_once('db_connect.php');

// get username/passwd first
$username = $_REQUEST['username'];
$passwd = $_REQUEST['passwd'];
if ($username == "" || $passwd == "") die;
if (ereg("[0-9a-zA-Z\@\.]+",$username) && ereg("[0-9a-zA-Z]+",$passwd)){
	$query = "SELECT * FROM account WHERE username = '$username' ";
	$account = $db->getRow($query);
	$url = $_SERVER['SERVER_NAME'];
	$url .= substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],"/"));
	if ($passwd == $account['password']){
		echo "200|http://$url/astercrmclient.php|$url/astercrmclientstatus.php";
	}else{
		echo "404";
	}
}
?>
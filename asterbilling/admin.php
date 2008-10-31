<?
	session_start();
	if (!isset($_SESSION['curuser'])) 
		header("Location: manager_login.php");
	else
		header("Location: systemstatus.php");
?>
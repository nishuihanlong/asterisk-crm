<?
	session_start();
	if (!isset($_SESSION['curuser'])) 
		header("Location: login.php");
	else
		header("Location: systemstatus.php");
?>
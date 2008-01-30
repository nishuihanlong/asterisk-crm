<?php
/*******************************************************************************
* statistics.php
* 统计

* Function Desc

* javascript function:		

* Revision asterCC 0.01  2007/11/21 17:55:00  last modified by solo
* Desc: page created
********************************************************************************/

require_once('statistics.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		function init(){

		}
		//-->
		</SCRIPT>

		<script language="JavaScript" src="js/astercrm.js"></script>

		<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>

	</head>

	<body onload="init();">
		<div id="divNav"></div><br>
		<div>
			<form name="frmFilter" id="frmFilter" method="post">
				<select id="sltGroup" name="sltGroup">
					<option value="">All</option>
				</select>
				<select id="sltType" name="sltType">
					<option value="prefix">Prefix</option>
				</select>
				From: <input type="text" name="sdate" size="20" value="<?echo date("Y-m-d H:i:s",time()-86400);?>">
				To:<input type="text" name="edate" size="20" value="<?echo date("Y-m-d H:i:s",time());?>">
					<input type="button" onclick="" value="List">
			</form>
		</div>
		<div id="divPrefix" name="divPrefix">
			<table>
		</div>
		<div id="divCopyright"></div>
	</body>
</html>

vmare workstation 6.0 english 盒装			2092
ibm pc dos 2003 日文版			盒装			3800
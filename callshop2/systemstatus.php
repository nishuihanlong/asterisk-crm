<?php
/*******************************************************************************
* systemstatus.php
* 系统状态文件
* systerm status interface
* 功能描述
	 显示分机状态和正在进行的通话

TO-DO

1.增加 print invoice 的button
2.asterrc 增加一级计费引擎
3.每个booth可以自定义名称
4.callshop 可以显示自己的信息

* Function Desc


* javascript function:		
						showStatus				show sip extension status
						showChannelsInfo		show asterisk channels information
						init					initialize function after page loaded

* Revision 0.045  2007/10/18 17:55:00  last modified by solo
* Desc: page created
********************************************************************************/

require_once('systemstatus.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			function init(){
				xajax_init();
				showStatus();
				dragresize.apply(document);
			}
		//-->
		</SCRIPT>
		<script language="JavaScript" src="js/astercrm.js"></script>
		<LINK href="skin/default/css/layout.css" type=text/css rel=stylesheet>
		<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>

		<script type="text/javascript" src="js/dragresize.js"></script>
		<script type="text/javascript" src="js/dragresizeInit.js"></script>

	</head>
	<body onload="init();">
		<div id="divNav"></div>
		<div id="AMIStatudDiv" name="AMIStatudDiv"></div>

		<div id="divPanel" name="divPanel" class="divPanel">
			<a href="rate.php" target="_blank">Rate</a><br>
			<a href="checkout.php" target="_blank">Report</a><br>
			<a href="clid.php" target="_blank">Clid</a><br>
			<a href="login.php">Logout</a>
		</div>
		<div>
		Limit Status:<span id="spanLimitStatus" name="spanLimitStatus"></span><br>
		Amount:&nbsp;<span id="spanAmount" name="spanAmount"></span>&nbsp;&nbsp;&nbsp;&nbsp;Limit:&nbsp;<span id="spanLimit" name="spanLimit"> </span><br>
		Last refresh time: <span id="spanLastRefresh" name="spanLastRefresh"></span>
		</div>
	<?if ($_SESSION['curuser']['allowcallback'] == 'yes'){?>
		<div id="divCallback" name="divCallback" class="formDiv drsElement" style="left: 450px; top: 50px;visibility:visible">
			<table width="100%" border="1" align="center" class="adminlist" >
			<tr class="drsMoveHandle">
				<th align="right" valign="center" >
					&nbsp;
				</th>
			</tr>
			<tr >
			<td>
				<fieldset><legend>Call back</legend>
			<form action="" method="post">
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td>Ori:</td>
					<td><input type="text" size="17" maxlength="17" id="iptLegB" name="iptLegB"></td>
				</tr>
				<tr>
					<td>Dest:</td>
					<td><input type="text" size="17" maxlength="17" id="iptLegA" name="iptLegA"></td>
				</tr>
				<tr>
					<td>Credit:</td>
					<td><input type="text" size="6" maxlength="6" id="creditLimit" name="creditLimit"></td>
				</tr>
				<tr>
					<td colspan=2>
						<input type="button" onclick="invite();return false;" value="start" >
					</td>
				</tr>
			</table>
			</form>
				</fieldset>
			</td></tr>
			</table>
		</div>
	<?}?>
		<form method="post" id="peerStatus">
			<div class="container" id="divMainContainer">
			</div>
		</form>

		<input type="hidden" name="curid" id="curid" value="0"/>

		<div id="divCopyright"></div>
	</body>
</html>

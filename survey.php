<?php
/*******************************************************************************
* survey.php
* 调查信息管理界面
* note information management interface
* 功能描述
	 提供对调查信息进行管理的功能

* Function Desc
	survey management

* Page elements
* div:							
									formDiv			-> add/edit form div in xgrid
									grid				-> main div
									msgZone		-> message from xgrid class
* javascript function:		
									init	


* Revision 0.045  2007/10/1 12:55:00  modified by solo
* Desc: create page
* 描述: 建立
********************************************************************************/

require_once('survey.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<LINK href="css/style.css" type=text/css rel=stylesheet>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		function init(){
			xajax_init();
		}
		
		function addOption(formName){
			xajax_save(xajax.getFormValues(formName));
		}


		//-->
		</SCRIPT>
		<script language="JavaScript" src="js/astercrm.js"></script>
	</head>
	<body onload="init();">
	<div id="divPanel" name="divPanel" class="divPanel"></div>
	<br>
	<table width="100%" border="0" style="background: #F9F9F9; padding: 0px;">
		<tr>
			<td style="padding: 0px;">
				<fieldset>
					<div id="formDiv" class="formDiv"></div>
					<div id="grid" align="center"> </div>
					<div id="msgZone" name="msgZone" align="left"> </div>
					<div id="divSurveyStatistc" align="divSurveyStatistc"> </div>
				</fieldset>
			</td>
		</tr>
	</table>
	</body>
</html>
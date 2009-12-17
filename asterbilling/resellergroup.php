<?php
/*******************************************************************************
* resellergroup.php

* 账户管理界面文件
* resellergroup management interface

* Function Desc
	provide an resellergroup management interface

* 功能描述
	提供帐户管理界面

* Page elements

* div:							
				divNav				show management function list
				formDiv				show add/edit resellergroup form
				grid				show accout grid
				msgZone				show action result
				divCopyright		show copyright

* javascript function:		

				init				page onload function			 


* Revision 0.045  2007/10/18 11:44:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once('resellergroup.common.php');
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
			dragresize.apply(document);
		}

		function searchFormSubmit(numRows,limit,id,type){
			//alert(xajax.getFormValues("searchForm"));
			xajax_searchFormSubmit(xajax.getFormValues("searchForm"),numRows,limit,id,type);
			return false;
		}

		function setBillingtime(id,billingtime){
			if (confirm("are u sure set billingtime to " + billingtime +"?")){
				xajax_updateBillingtime(id,billingtime);
			}else{
				return false;
			}
		}
		
		function showComment(obj){
			var tval = obj.value;
			if(tval == "add" || tval== "reduce"){
				xajax.$("creditmod").disabled = false;
				xajax.$("comment").disabled = false;
			}else{
				xajax.$("creditmod").disabled = true;
				xajax.$("comment").disabled = true;
			}
		}
		function showTrunk(obj){
			var Tvalue = document.getElementById(obj).value;
			if(Tvalue == 'auto' || Tvalue == 'default'){
				xajax.$("trunk").style.display = 'none';
			} else if(Tvalue == 'customize'){
				xajax.$("trunk").style.display = 'block';
			}
		}
		function EditShowTrunk(obj){
			var Tvalue = document.getElementById(obj).value;
			if(Tvalue == 'auto' || Tvalue == 'default'){
				xajax.$("trunk").style.display = 'none';

			} else if(Tvalue == 'customize'){
				xajax.$("trunk").style.display = 'block';
			}
		}
		function CheckNumeric(obj)
		{
			var Timeval = document.getElementById(obj).value;
			var rum = /^[0-9]*$/;
			if(!rum.exec(Timeval)) {
				alert("只能输入数字");
			}
		}

		//-->
		</SCRIPT>
		<script type="text/javascript" src="js/dragresize.js"></script>
		<script type="text/javascript" src="js/dragresizeInit.js"></script>
		<script type="text/javascript" src="js/astercrm.js"></script>
		<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>
		<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>

	</head>
	<body onload="init();" id="resellergroup">
		<div id="divNav"></div><br>
		<table width="100%" border="0" style="background: #F9F9F9; padding: 0px;">
			<tr>
				<td style="padding: 0px;">
					<fieldset>
			<div id="formDiv"  class="formDiv drsElement" 
				style="left: 450px; top: 50px;width:500px;"></div>
			<div id="grid" name="grid" align="center"> </div>
			<div id="msgZone" name="msgZone" align="left"> </div>
					</fieldset>
				</td>
			</tr>
		</table>
		<form name="exportForm" id="exportForm" action="dataexport.php" >
			<input type="hidden" value="" id="hidSql" name="hidSql" />
		</form>
		<div id="divCopyright"></div>
	</body>
</html>

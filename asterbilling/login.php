<?php
/*******************************************************************************
********************************************************************************/

require_once('login.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Language" content="utf-8" />
		<?php $xajax->printJavascript('include/'); ?>
		<script type="text/javascript">
		/**
		*  login function, launched when user click login button
		*
		*  	@param null
		*	@return false
		*/
		function loginSignup()
		{
			xajax.$('loginButton').disabled=true;
			xajax.$('loginButton').value=xajax.$('onclickMsg').value;
			xajax_processForm(xajax.getFormValues("loginForm"));
			return false;
		}

		/**
		*  init function, launched after page load
		*
		*  	@param null
		*	@return false
		*/
		function init(){
			xajax_init(xajax.getFormValues("loginForm"));
			return false;
		}
		</script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>

</head>
	<body onload="init();" style="margin-top: 80px;">
	 <div align="center">
	 		<div id="formDiv">
			<form id="loginForm" action="javascript:void(null);" onsubmit="loginSignup();">
		  <div class="login_in">
				<div id="titleDiv"></div>
				<div class="left">
				<div class="left">
			<table width="385" height="143" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<th width="100" height="58" scope="col">&nbsp;</th>
				<th width="100" valign="bottom" scope="col"><div name="usernameDiv" id="usernameDiv" align="left"></div></th>
				<th width="201" valign="bottom" scope="col"><div align="left">
				  <input name="username" type="text" id="username" style="width:150px;height:14px" />
			    </div></th>
			  </tr>
			  <tr>
				<td height="49">&nbsp;</td>
				<th><div name="passwordDiv" id="passwordDiv" align="left"></div></th>
				<td><div align="left">
				  <input type="password" name="password" id="password" style="width:150px;height:14px" />
			    </div></td>
			  </tr>
			  <tr>
				<td height="49">&nbsp;</td>
				<th><div name="validcodeDiv" id="validcodeDiv" align="left"></div></th>
				<td><div align="left">
				  <input type="text" name="code" id="code" style="width:150px;height:14px" />
			    </div></td>
			  </tr>
			  <tr>
				<td height="49">&nbsp;</td>
				<th></th>
				<td><div align="left"><img id="imgCode" name="imgCode" src=""></div></td>
			  </tr>
			  <tr>
				<td height="36" colspan="2">&nbsp;</td>
				<td><div name="locateDiv" id="locateDiv">
						<div align="left">
						  <SELECT name="locate" id="locate" onchange="init();">
						    <OPTION value="en_US">English</OPTION>
						    <OPTION value="cn_ZH">简体中文</OPTION>
						    <OPTION value="ch_FR">Français</OPTION>
				      </SELECT>
				      <input id="loginButton" name="loginButton" type="submit" value=""/>
				  <input id="onclickMsg" name="onclickMsg" type="hidden" value=""/>
			    </div>
				</div></td>
			  </tr>
				  </table>

				</div>
				<div class="right"></div><div id="outputDiv"></div>
		  </div></form></div>
	    </div>
	</body>
</html>
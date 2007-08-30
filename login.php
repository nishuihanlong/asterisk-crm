<?php
require_once('login.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Language" content="utf-8" />
		<?php $xajax->printJavascript('include/'); ?>
		<LINK href="css/style.css" type=text/css rel=stylesheet>
		<script type="text/javascript">
		function loginSignup()
		{
			xajax.$('loginButton').disabled=true;
			xajax.$('loginButton').value=xajax.$('onclickMsg').value;
			xajax_processForm(xajax.getFormValues("loginForm"));
			return false;
		}

		function init(){
			xajax_init(xajax.getFormValues("loginForm"));
		}
		</script>
	</head>
	<body onload="init();">
		<div id="formWrapper">
		
			<div id="titleDiv"></div>
			
			<div id="formDiv">
				<form id="loginForm" action="javascript:void(null);" onsubmit="loginSignup();">
					<div name="usernameDiv" id="usernameDiv"></div><div><input type="text" name="username" /></div>
					<div name="passwordDiv" id="passwordDiv"></div><div><input type="password" name="password" /></div>
					<div name="locateDiv" id="locateDiv">
						<SELECT name="locate" id="locate" onchange="init();">
							<OPTION value="en_US">English</OPTION>
							<OPTION value="cn_ZH">简体中文</OPTION>
						</SELECT>
					</div>
					<div class="submitDiv">
					<input id="loginButton" name="loginButton" type="submit" value=""/></div>
					<input id="onclickMsg" name="onclickMsg" type="hidden" value=""/></div>
				</form>
			</div>
			
		</div>
		
		<div id="outputDiv">
		</div>
	</body>
</html>
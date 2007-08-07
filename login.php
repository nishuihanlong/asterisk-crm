<?php
require_once('login.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<LINK href="css/style.css" type=text/css rel=stylesheet>
		<script type="text/javascript">
		function loginSignup()
		{
			xajax.$('loginButton').disabled=true;
			xajax.$('loginButton').value="please wait...";
			xajax_processForm(xajax.getFormValues("loginForm"));
			return false;
		}
		</script>
		<meta http-equiv="Content-Language" content="utf-8" />
	</head>
	<body>
		<div id="formWrapper">
		
			<div id="title">User Login</div>
			
			<div id="formDiv">
				<form id="loginForm" action="javascript:void(null);" onsubmit="loginSignup();">
					<div>Username:</div><div><input type="text" name="username" /></div>
					<div>Password:</div><div><input type="password" name="password" /></div>
					<div class="submitDiv">
					<input id="loginButton" type="submit" value="continue ->"/></div>
				</form>
			</div>
			
		</div>
		
		<div id="outputDiv">
		</div>
	</body>
</html>
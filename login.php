<?php
/*******************************************************************************
* login.php
* 用户登入界面文件
* user login page
* 功能描述
	 首先载入所有元素，然后调用javascripr的init函数初始化页面上的文字信息

* Function Desc
	first load all elements, and then call javascript init function to initialize words on this page

* Page elements
* Form:							
									loginForm
* input field:					
									username			->	 username
									password			->	 password
									locate				->	 language
* hidden field:				
									onclickMsg			-> save message when user click login button
* button:						
									loginButton		-> user login button
* div:							
									titleDiv				->	 login form title
									usernameDiv		-> username
									passwordDiv		-> password
									locateDiv			-> locate
* javascript function:		
									loginSignup	
									init					 



* Revision 0.044  2007/09/7 17:55:00  last modified by solo
* Desc: add some comments
* 描述: 增加了一些注释信息
********************************************************************************/

require_once('login.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Language" content="utf-8" />
		<?php $xajax->printJavascript('include/'); ?>
		<LINK href="css/style.css" type=text/css rel=stylesheet>
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
	</head>
	<body onload="init();">
			<div id="titleDiv"></div>
			
			<div id="formDiv">
				<form id="loginForm" action="javascript:void(null);" onsubmit="loginSignup();">
					<div name="usernameDiv" id="usernameDiv"></div><div><input type="text" name="username" id="username" /></div>
					<div name="passwordDiv" id="passwordDiv"></div><div><input type="password" name="password" id="password"/></div>
					<div name="locateDiv" id="locateDiv">
						<SELECT name="locate" id="locate" onchange="init();">
							<OPTION value="en_US">English</OPTION>
							<OPTION value="cn_ZH">简体中文</OPTION>
						</SELECT>
					</div>
					<input id="loginButton" name="loginButton" type="submit" value=""/>
					<input id="onclickMsg" name="onclickMsg" type="hidden" value=""/>
				</form>
			</div>
			
		<div id="outputDiv"></div>
		<div align="center"><?require_once('copyright.php');?></div>
	</body>
</html>
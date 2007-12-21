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


* Revision 0.0443  2007/10/8 17:55:00  last modified by solo
* Desc: add a div to display copyright



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
				<div class="login_in_logo" align="right"><br /><div id="titleDiv"></div></div>
				<div class="left">
			<table width="385" height="143" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<th width="92" height="58" scope="col">&nbsp;</th>
				<th width="92" valign="bottom" scope="col"><div name="usernameDiv" id="usernameDiv" align="left"></div></th>
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
				<td height="36" colspan="2">&nbsp;</td>
				<td><div name="locateDiv" id="locateDiv">
						<div align="left">
						  <SELECT name="locate" id="locate" onchange="init();">
						    <OPTION value="en_US">English</OPTION>
						    <OPTION value="cn_ZH">简体中文</OPTION>
						    <OPTION value="de_GER">Germany</OPTION>
				      </SELECT>
				      <input id="loginButton" name="loginButton" type="submit" value=""/>
				  <input id="onclickMsg" name="onclickMsg" type="hidden" value=""/>
			    </div>
				</div></td>
			  </tr>
				  </table>

				</div>
				<div class="right"></div><div id="outputDiv"></div>
                <div align="center">
					<img src="skin/default/images/login_in_03.gif" width="277" height="7"></div>
		  </div></form></div>

	    </div>
				<div id="divCopyright"></div>                      
	</body>
</html>
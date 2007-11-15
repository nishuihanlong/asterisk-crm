<?
/*******************************************************************************
* preferences.php

* 配置文件管理文件
* config management interface

* Function Desc
	provide an config management interface

* 功能描述
	提供配置管理界面

* Page elements

* div:							
				divNav				show management function list
				divCopyright		show copyright

* javascript function:		
				init				page onload function			 


* Revision 0.0456  2007/11/12 15:44:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once('preferences.common.php');
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

			function display(id){

				var traget=document.getElementById(id);
				 if(traget.style.display=="none"){
						 traget.style.display="";
				 }else{
						 traget.style.display="none";
			   }
			}

			function savePreferences(){
				xajax_savePreferences(xajax.getFormValues("formPreferences"));
			}
			
			function checkDb(){
				xajax_checkDb(xajax.getFormValues("formPreferences"));
			}

			function checkAMI(){
				xajax_checkAMI(xajax.getFormValues("formPreferences"));
			}

			function checkSys(){
				xajax_checkSys(xajax.getFormValues("formPreferences"));
			}
		//-->
		</SCRIPT>
		<script type="text/javascript" src="js/dragresize.js"></script>
		<script type="text/javascript" src="js/dragresizeInit.js"></script>
		<script type="text/javascript" src="js/astercrm.js"></script>
		<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>
		<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>

	</head>
	<body onload="init();">
		<div id="divNav"></div><br>
<form name="formPreferences" id="formPreferences" method="post">
<center>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" width="780">
  <tr>
    <td height="39" class="td font" id="Database" name="Database" align="left">
		&nbsp;&nbsp;&nbsp;Database 
        <input type="button" onclick="display('menu')"  value="+"/>
		<input type="button" onclick="checkDb();return false;"  value="check"/>
		<div name="divDbMsg" id="divDbMsg"></div>
    </td>
  </tr>
    <tr><td height="10" class="td"></td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" id="menu" width="780">
  <tr>
    <td width="230" align="left" valign="top"  id="DbDbtype" name="DbDbtype">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;dbtype</td>
    <td width="200" align="left" valign="top" >
		<select id="iptDbDbtype" name="iptDbDbtype">
			<option value="mysql">mysql</option>
		</select>
    </td>
    <td align="left" valign="top" >
		<div id="divDbDbtype" name="divDbDbtype">
		</div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="DbDbhost" name="DbDbhost">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;dbhost</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" size="30" size="30" id="iptDbDbhost" name="iptDbDbhost" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divDbDbhost" name="divDbDbhost"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="DbDbname" name="DbDbname">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;dbname</td>
    <td width="200" align="left" valign="top" >
		<input type="text" size="30" size="30" id="iptDbDbname" name="iptDbDbname" />
	</td>
    <td align="left" valign="top" >
		<div id="divDbDbname" name="divDbDbname"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="DbUsername" name="DbUsername">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;username</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7"><input type="text" size="30" id="iptDbUsername" name="iptDbUsername" /></td>
    <td align="left" valign="top" bgcolor="#F7F7F7"><div id="divDbUsername" name="divDbUsername"></div></td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="DBPassWord" name="DbPassword">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;password</td>
    <td width="200" align="left" valign="top" ><input type="text" size="30" id="iptDbPassword" name="iptDbPassword" /></td>
    <td align="left" valign="top" ><div id="divDbPassword" name="divDbPassword"></div></td>
  </tr>
</table>


<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" width="780">
  <tr>
    <td height="39" class="td font" id="Asterisk" name="Asterisk"  align="left">
		&nbsp;&nbsp;&nbsp;Asterisk 
		<input type="button" onclick="display('menu1')"  value="+"/>
		<input type="button" onclick="checkAMI();return false;"  value="check"/>
		<div name="divAsMsg" id="divAsMsg"></div>
    </td>
  </tr>
  <tr>
    <td height="10" class="td"></td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" id="menu1" width="780">
  <tr>
    <td width="230" align="left" valign="top" id="AsServer" name="AsServer">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;server</td>
    <td width="200" align="left" valign="top" >
      <input type="text" size="30" id="iptAsServer" name="iptAsServer" />
	</td>
    <td align="left" valign="top" >
		<div id="divAsServer" name="divAsServer">
		</div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="AsPort" name="AsPort">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;port</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" size="30" id="iptAsPort" name="iptAsPort" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divAsPort" name="divAsPort"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="AsUsername" name="AsUsername">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;username</td>
    <td width="200" align="left" valign="top" >
		<input type="text" size="30" id="iptAsUsername" name="iptAsUsername" />
	</td>
    <td align="left" valign="top" >
		<div id="divAsUsername" name="divAsUsername"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="AsSecret" name="AsSecret">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;secret</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" size="30" id="iptAsSecret" name="iptAsSecret" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divAsSecret" name="divAsSecret"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="AsMonitorpath" name="AsMonitorpath">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;monitorpath</td>
    <td width="200" align="left" valign="top" >
		<input type="text" size="30" id="iptAsMonitorpath" name="iptAsMonitorpath" />
	</td>
    <td align="left" valign="top" >
		<div id="divAsMonitorpath" name="divAsMonitorpath"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="AsMonitorformat" name="AsMonitorformat">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;monitorformat</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<select id="iptAsMonitorformat" name="iptAsMonitorformat">
			<option value="gsm">gsm</option>
			<option value="wav">wav</option>
			<option value="wav49">wav49</option>
		</select>
 	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divAsMonitorformat" name="divAsMonitorformat"></div>
	</td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" width="780">
  <tr>
    <td height="39" class="td font" id="System" name="System"  align="left">
		&nbsp;&nbsp;&nbsp;System 
      <input type="button" onclick="display('menu2')"  value="+"/>
		<input type="button" onclick="checkSys();return false;"  value="check"/>
		<div name="divSysMsg" id="divSysMsg"></div>

	</td>
  </tr>
  <tr>
    <td height="10" class="td"></td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" id="menu2" width="780">
  <tr>
    <td width="230" align="left" valign="top"  id="SysLogEnabled" name="SysLogEnabled">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;log_enabled
	</td>
    <td width="200" align="left" valign="top" >
        <select name="iptSysLogEnabled" id="iptSysLogEnabled">
          <option value="0">0</option>
          <option value="1">1</option>
        </select>
	</td>
    <td align="left" valign="top" >
		<div id="divSysLogEnabled" name="divSysLogEnabled"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="SysLogFilePath" name="SysLogFilePath">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;log_file_path
	</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" size="30" id="iptSysLogFilePath" name="iptSysLogFilePath" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysLogFilePath" name="divSysLogFilePath"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="SysOutcontext" name="SysOutcontext">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;outcontext
	</td>
    <td width="200" align="left" valign="top" >
		<input type="text" size="30" id="iptSysOutcontext" name="iptSysOutcontext" />
	</td>
    <td align="left" valign="top" >
		<div id="divSysOutcontext" name="divSysOutcontext"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="SysIncontext" name="SysIncontext">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;incontext
	</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" size="30" id="iptSysIncontext" name="iptSysIncontext" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysIncontext" name="divSysIncontext"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="SysPredialerContext" name="SysPredialerContext">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;preDialer_context
	</td>
    <td width="200" align="left" valign="top" >
		<input type="text" size="30" id="iptSysPredialerContext" name="iptSysPredialerContext" />
	</td>
    <td align="left" valign="top" >
		<div id="divSysPredialerContext" name="divSysPredialerContext"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="SysPredialerExtension" name="SysPredialerExtension">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;preDialer_extension
	</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7"><input type="text" size="30" id="iptSysPredialerExtension" name="iptSysPredialerExtension" /></td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysPredialerExtension" name="divSysPredialerExtension"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" id="SysPhoneNumberLength" name="SysPhoneNumberLength">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;phone_number_length
	</td>
    <td width="200" align="left" valign="top" >
		<input type="text" size="30" id="iptSysPhoneNumberLength" name="iptSysPhoneNumberLength" />
	</td>
    <td align="left" valign="top" >
		<div id="divSysPhoneNumberLength" name="divSysPhoneNumberLength"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="SysTrimPrefix" name="SysTrimPrefix">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;trim_prefix
	</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" size="30" id="iptSysTrimPrefix" name="iptSysTrimPrefix" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysTrimPrefix" name="divSysTrimPrefix"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="SysAllowDropcall" name="SysAllowDropcall">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;allow_dropcall</td>
    <td width="200" align="left" valign="top" >
		<select name="iptSysAllowDropcall" id="iptSysAllowDropcall">
			<option value="0">0</option>
			<option value="1">1</option>
		</select>
	</td>
    <td align="left" valign="top" >
		<div id="divSysAllowDropcall" name="divSysAllowDropcall"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="SysAllowSameDate" name="SysAllowSameDate">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;allow_same_data</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<select name="iptSysAllowSameData" id="iptSysAllowSameData">
			<option value="0">0</option>
			<option value="1">1</option>
        </select>
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysAllowSameData" name="divSysAllowSameData"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="SysPortalDisplayType" name="SysPortalDisplayType">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;portal_display_type</td>
    <td width="200" align="left" valign="top" >
		<select id="iptSysPortalDisplayType" name="iptSysPortalDisplayType">
			<option value="customer">customer</option>
			<option value="note">note</option>
		</select>
	</td>
    <td align="left" valign="top" ><div id="divSysPortalDisplayType" name="divSysPortalDisplayType"></div></td>
  </tr>
  <tr bgcolor="#F7F7F7">
    <td width="230" align="left" valign="top"  id="SysEnableContact" name="SysEnableContact">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;enable_contact
	</td>
    <td width="200" align="left" valign="top" >
		<select name="iptSysEnableContact" id="iptSysEnableContact">
			<option value="0">0</option>
			<option value="1">1</option>
		</select></td>
    <td align="left" valign="top" >
		<div id="divSysEnableContact" name="divSysEnableContact"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="SysPopUpWhenDialIn" name="SysPopUpWhenDialIn">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;pop_up_when_dial_in
	</td>
    <td width="200" align="left" valign="top" ><select name="iptSysPopUpWhenDialIn" id="iptSysPopUpWhenDialIn">
      <option value="0">0</option>
      <option value="1">1</option>
    </select></td>
    <td align="left" valign="top" >
		<div id="divSysPopUpWhenDialIn" name="divSysPopUpWhenDialIn"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="SysPopUpWhenDialOut" name="SysPopUpWhenDialOut">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;pop_up_when_dial_out</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<select name="iptSysPopUpWhenDialOut" id="iptSysPopUpWhenDialOut">
		  <option value="0">0</option>
		  <option value="1">1</option>
		</select>
	</td>
    <td align="left" valign="top">
		<div id="divSysPopUpWhenDialOut" name="divSysPopUpWhenDialOut"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="SysBrowserMaximizeWhenPopUp" name="SysBrowserMaximizeWhenPopUp">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;browser_maximize_when_pop_up
	</td>
    <td width="200" align="left" valign="top">
		<select name="iptSysBrowserMaximizeWhenPopUp" id="iptSysBrowserMaximizeWhenPopUp">
		  <option value="0">0</option>
		  <option value="1">1</option>
		</select>
	</td>
    <td align="left" valign="top">
		<div id="divSysBrowserMaximizeWhenPopUp" name="divSysBrowserMaximizeWhenPopUp"></div>
	</td>
  </tr>

  <tr>
    <td width="230" align="left" valign="top"  id="SysFirstring" name="SysFirstring"  bgcolor="#F7F7F7">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;firstring
	</td>
    <td width="200" align="left" valign="top" >
		<select id="iptSysFirstring" name="iptSysFirstring">
			<option value="caller">caller</option>
			<option value="callee">callee</option>
		</select>
    </td>
    <td align="left" valign="top"  bgcolor="#F7F7F7">
		<div id="divSysFirstring" name="divSysFirstring"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="SysUploadExcelPath" name="SysUploadExcelPath">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;upload_file_path</td>
    <td width="200" align="left" valign="top" >
		<input type="text" size="30" id="iptSysUploadFilePath" name="iptSysUploadFilePath" />
	</td>
    <td align="left" valign="top" >
		<div id="divSysUploadFilePath" name="divSysUploadFilePath"></div>
	</td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" width="780">
  <tr>
    <td height="39" class="td font" id="ExternalCRM" name="ExternalCRM"  align="left">
		&nbsp;&nbsp;&nbsp;External CRM 
      <input type="button" onclick="display('menu3')"  value="+"/>
    </td>
  </tr>
  <tr>
    <td height="10" class="td"></td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" id="menu3" width="780">
  <tr>
    <td width="230" align="left" valign="top" id="SysEnableExternalCrm" name="SysEnableExternalCrm">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;enable_external_crm</td>
    <td width="200" align="left" valign="top" >
		<select name="iptSysEnableExternalCrm" id="iptSysEnableExternalCrm">
		  <option value="0">0</option>
		  <option value="1">1</option>
		</select></td>
    <td align="left" valign="top" >
		<div id="divSysEnableExternalCrm" name="divSysEnableExternalCrm"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="SysOpenNewWindow" name="SysOpenNewWindow">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;open_new_window</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<select name="iptSysOpenNewWindow" id="iptSysOpenNewWindow">
		  <option value="0">0</option>
		  <option value="1">1</option>
		</select>
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysOpenNewWindow" name="divSysOpenNewWindow"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top"  id="SysExternalCrmDefaultUrl" name="SysExternalCrmDefaultUrl">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;external_crm_default_url</td>
    <td width="200" align="left" valign="top" >
		<input type="text" size="30" id="iptSysExternalCrmDefaultUrl" name="iptSysExternalCrmDefaultUrl" />
	</td>
    <td align="left" valign="top" >
		<div id="divSysExternalCrmDefaultUrl" name="divSysExternalCrmDefaultUrl"></div>
	</td>
  </tr>
  <tr>
    <td width="230" align="left" valign="top" bgcolor="#F7F7F7" id="SysExternalCrmUrl" name="SysExternalCrmUrl">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;external_crm_url</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" size="30" id="iptSysExternalCrmUrl" name="iptSysExternalCrmUrl" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysExternalCrmUrl" name="divSysExternalCrmUrl"></div>
	</td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" width="780">
  <tr>
    <td height="39" class="td font" id="ExternalCRM" name="ExternalCRM"  align="left">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="btnSave" id="btnSave"  value="Save" onclick="savePreferences();return false;"/>
    </td>
  </tr>
  <tr>
    <td height="10" class="td"></td>
  </tr>
</table>
</center>
</form>
		<div id="divCopyright"></div>
	</body>
</html>
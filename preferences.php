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

<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" width="700">
  <tr>
    <td width="180" height="39" class="td font" id="Database" name="Database">
		&nbsp;&nbsp;&nbsp;Database 
        <input type="button" onclick="display('menu')"  value="+"/>
    </td>
  </tr>
    <tr><td height="10" class="td"></td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" id="menu" width="700">
  <tr>
    <td width="180" align="left" valign="top"  id="DbDbType" name="DbDbType">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;dbtype</td>
    <td width="200" align="left" valign="top" >
		<select id="iptDbDbType" name="iptDbDbType">
			<option value="mysql">mysql</option>
		</select>
    </td>
    <td align="left" valign="top" >
		<div id="divDbDbType" name="divDbDbType">
		</div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="DbDbHost" name="DbDbHost">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;dbhost</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" id="iptDbDbHost" name="iptDbDbHost" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divDbDbHost" name="divDbDbHost"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="DbDbName" name="DbDbName">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;dbname</td>
    <td width="200" align="left" valign="top" >
		<input type="text" id="iptDbDbName" name="iptDbDbName" />
	</td>
    <td align="left" valign="top" >
		<div id="divDbDbName" name="divDbDbName"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="DbUserName" name="DbUserName">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;username</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7"><input type="text" id="iptDbUserName" name="iptDbUserName" /></td>
    <td align="left" valign="top" bgcolor="#F7F7F7"><div id="divDbUserName" name="divDbUserName"></div></td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="DBPassWord" name="DbPassWord">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;password</td>
    <td width="200" align="left" valign="top" ><input type="text" id="iptDbPassWord" name="iptDbPassWord" /></td>
    <td align="left" valign="top" ><div id="divDbPassWord" name="divDbPassWord"></div></td>
  </tr>
</table>


<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" width="700">
  <tr>
    <td height="39" class="td font" id="Asterisk" name="Asterisk">
		&nbsp;&nbsp;&nbsp;Asterisk 
      <input type="button" onclick="display('menu1')"  value="+"/>
    </td>
  </tr>
  <tr>
    <td height="10" class="td"></td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" id="menu1" width="700">
  <tr>
    <td width="180" align="left" valign="top" id="AsServer" name="AsServer">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;server</td>
    <td width="200" align="left" valign="top" >
      <input type="text" id="iptAsServer" name="iptAsServer" />
	</td>
    <td align="left" valign="top" >
		<div id="divAsServer" name="divAsServer">
		</div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="AsPort" name="AsPort">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;port</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" id="iptAsPort" name="iptAsPort" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divAsPort" name="divAsPort"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="AsUserName" name="AsUserName">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;username</td>
    <td width="200" align="left" valign="top" >
		<input type="text" id="iptAsUserName" name="iptAsUserName" />
	</td>
    <td align="left" valign="top" >
		<div id="divAsUserName" name="divAsUserName"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="AsSecret" name="AsSecret">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;secret</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" id="iptAsSecret" name="iptAsSecret" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divAsSecret" name="divAsSecret"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="DbDbName" name="AsMonitorPath">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;monitorpath</td>
    <td width="200" align="left" valign="top" >
		<input type="text" id="iptAsMonitorPath" name="iptAsMonitorPath" />
	</td>
    <td align="left" valign="top" >
		<div id="divAsMonitorPath" name="divAsMonitorPath"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="AsMonitorFormat" name="AsMonitorFormat">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;monitorformat</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" id="iptAsMonitorFormat" name="iptAsMonitorFormat" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divAsMonitorFormat" name="divAsMonitorFormat"></div>
	</td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" width="700">
  <tr>
    <td height="39" class="td font" id="System" name="System">
		&nbsp;&nbsp;&nbsp;System 
      <input type="button" onclick="display('menu2')"  value="+"/>
	</td>
  </tr>
  <tr>
    <td height="10" class="td"></td>
  </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" id="menu2" width="700">
  <tr>
    <td width="180" align="left" valign="top"  id="SysLogEnabled" name="SysLogEnabled">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;log_enabled
	</td>
    <td width="200" align="left" valign="top" >
        <select name="iptSysLogEnabled" id="iptSysLogEnabled">
          <option>0</option>
          <option selected="selected">1</option>
        </select>
	</td>
    <td align="left" valign="top" >
		<div id="divSysLogEnabled" name="divSysLogEnabled"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="SysLogFilePath" name="SysLogFilePath">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;log_file_path
	</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" id="iptSysLogFilePath" name="iptSysLogFilePath" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysLogFilePath" name="divSysLogFilePath"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="SysOutContext" name="SysOutContext">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;outcontext
	</td>
    <td width="200" align="left" valign="top" >
		<input type="text" id="iptSysOutContext" name="iptSysOutContext" />
	</td>
    <td align="left" valign="top" >
		<div id="divSysOutContext" name="divSysOutContext"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="SysInContext" name="SysInContext">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;incontext
	</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" id="iptSysInContext" name="iptSysInContext" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysInContext" name="divSysInContext"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="SysPreDialerContext" name="SysPreDialerContext">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;preDialer_context
	</td>
    <td width="200" align="left" valign="top" >
		<input type="text" id="iptSysPreDialerContext" name="iptSysPreDialerContext" />
	</td>
    <td align="left" valign="top" >
		<div id="divSysPreDialerContext" name="divSysPreDialerContext"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="SysPreDialerExtension" name="SysPreDialerExtension">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;preDialer_extension
	</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7"><input type="text" id="iptSysPreDialerExtension" name="iptSysPreDialerExtension" /></td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysPreDialerExtension" name="divSysPreDialerExtension"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" id="SysPhoneNumberLength" name="SysPhoneNumberLength">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;phone_number_length
	</td>
    <td width="200" align="left" valign="top" >
		<input type="text" id="iptSysPhoneNumberLength" name="iptSysPhoneNumberLength" />
	</td>
    <td align="left" valign="top" >
		<div id="divSysPhoneNumberLength" name="divSysPhoneNumberLength"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="SysTrimPrefix" name="SysTrimPrefix">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;trim_prefix
	</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" id="iptSysTrimPrefix" name="iptSysTrimPrefix" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysTrimPrefix" name="divSysTrimPrefix"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="SysAllowDropcall" name="SysAllowDropcall">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;allow_dropcall</td>
    <td width="200" align="left" valign="top" >
		<select name="iptSysAllowDropcall" id="iptSysAllowDropcall">
			<option>0</option>
			<option>1</option>
		</select>
	</td>
    <td align="left" valign="top" >
		<div id="divSysAllowDropcall" name="divSysAllowDropcall"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="SysAllowSameDate" name="SysAllowSameDate">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;allow_same_data</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<select name="iptSysAllowSameDate" id="iptSysAllowSameDate">
			<option>0</option>
			<option selected="selected">1</option>
        </select>
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysAllowSameData" name="divSysAllowSameData"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="SysPortalDisplayType" name="SysPortalDisplayType">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;portal_display_type</td>
    <td width="200" align="left" valign="top" >
		<input type="text" id="iptSysPortalDisplayType" name="iptSysPortalDisplayType" />
	</td>
    <td align="left" valign="top" ><div id="divSysPortalDisplayType" name="divSysPortalDisplayType"></div></td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="SysEnableContact" name="SysEnableContact">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;enable_contact
	</td>
    <td width="200" align="left" valign="top" >
		<select name="iptSysEnableContact" id="iptSysEnableContact">
			<option selected="selected">0</option>
			<option>1</option>
		</select></td>
    <td align="left" valign="top" >
		<div id="divSysEnableContact" name="divSysEnableContact"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="SysPopUpWhenDialOut" name="SysPopUpWhenDialOut">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;pop_up_when_dial_out</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<select name="iptSysPopUpWhenDialOut" id="iptSysPopUpWhenDialOut">
			<option>0</option>
			<option selected="selected">1</option>
		</select>
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysPopUpWhenDialOut" name="divSysPopUpWhenDialOut"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="SysPopUpWhenDialIn" name="SysPopUpWhenDialIn">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;pop_up_when_dial_in
	</td>
    <td width="200" align="left" valign="top" ><select name="iptSysPopUpWhenDialIn" id="iptSysPopUpWhenDialIn">
      <option>0</option>
      <option selected="selected">1</option>
    </select></td>
    <td align="left" valign="top" >
		<div id="divSysPopUpWhenDialIn" name="divSysPopUpWhenDialIn"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="SysMaximizeWhenPopUp" name="SysMaximizeWhenPopUp">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;maximize_when_pop_up
	</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<select name="iptSysMaximizeWhenPopUp" id="iptSysMaximizeWhenPopUp">
			<option selected="selected">0</option>
			<option>1</option>
		</select>
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysMaximizeWhenPopUp" name="divSysMaximizeWhenPopUp"></div>
	</td>
  </tr>

  <tr>
    <td width="180" align="left" valign="top"  id="SysFirstRing" name="SysFirstRing">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;firstring
	</td>
    <td width="200" align="left" valign="top" >
      <input type="text" id="iptSysFirstRing" name="iptSysFirstRing" />
    </td>
    <td align="left" valign="top" >
		<div id="divSysFirstRing" name="divSysFirstRing"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="SysEnableExternalCrm" name="SysEnableExternalCrm">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;enable_external_crm</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<select name="iptSysEnableExternalCrm" id="iptSysEnableExternalCrm">
		  <option selected="selected">0</option>
		  <option>1</option>
		</select></td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysEnableExternalCrm" name="divSysEnableExternalCrm"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="SysOpenNewWindow" name="SysOpenNewWindow">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;open_new_window</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<select name="iptSysOpenNewWindow" id="iptSysOpenNewWindow">
		  <option selected="selected">0</option>
		  <option>1</option>
		</select>
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysOpenNewWindow" name="divSysOpenNewWindow"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="SysExternalCrmDefaultUrl" name="SysExternalCrmDefaultUrl">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;external_crm_default_url</td>
    <td width="200" align="left" valign="top" >
		<input type="text" id="iptSysExternalCrmDefaultUrl" name="iptSysExternalCrmDefaultUrl" />
	</td>
    <td align="left" valign="top" >
		<div id="divSysExternalCrmDefaultUrl" name="divSysExternalCrmDefaultUrl"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top" bgcolor="#F7F7F7" id="SysExternalCrmUrl" name="SysExternalCrmUrl">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;external_crm_url</td>
    <td width="200" align="left" valign="top" bgcolor="#F7F7F7">
		<input type="text" id="iptSysExternalCrmUrl" name="iptSysExternalCrmUrl" />
	</td>
    <td align="left" valign="top" bgcolor="#F7F7F7">
		<div id="divSysExternalCrmUrl" name="divSysExternalCrmUrl"></div>
	</td>
  </tr>
  <tr>
    <td width="180" align="left" valign="top"  id="SysUploadExcelPath" name="SysUploadExcelPath">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;upload_excel_path</td>
    <td width="200" align="left" valign="top" >
		<input type="text" id="iptSysUploadExcelPath" name="iptSysUploadExcelPath" />
	</td>
    <td align="left" valign="top" >
		<div id="divSysUploadFilePath" name="divSysUploadFilePath"></div>
	</td>
  </tr>
</table>
		<div id="divCopyright"></div>
	</body>
</html>
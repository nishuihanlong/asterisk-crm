<?
/*******************************************************************************
* common.class.php
* 通用类
* common class

* Public Functions List

			generateCopyright	生成版权信息HTML代码
			generateManageNav	生成管理界面导航HTML代码
			generateTabelHtml	生成表格HTML代码

* Private Functions List


* Revision 0.045  2007/10/18  modified by solo
* Desc: page created


********************************************************************************/


require_once ('localization.class.php');

if ($_SESSION['curuser']['country'] != '' ){
	$GLOBALS['locate_common']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['country'],'common.class');
}else{
	$GLOBALS['locate_common']=new Localization('en','US','common.class');
}


class common{

	function generateCopyright($skin){
		global $locate_common;

		$html .='
				<div align="center">
					<table class="copyright" id="tblCopyright">
					<tr>
						<td>
							©2007 asterCRM - <a href="http://www.astercrm.org" target="_blank">asterCRM home</a><br>
							version: 0.045
						</td>
					</tr>
					</table>
				</dvi>
				';
		return $html;
	}

	function generateManageNav($skin){
		global $locate_common;
/*



	
		$html .= "<a href='contact.php' >".$locate_common->Translate("contact_manager")."</a> | ";
		
		$html .= "<a href='note.php' >".$locate_common->Translate("note_manager")."</a> | ";
		
		$html .= "<a href='diallist.php' >".$locate_common->Translate("diallist_manager")."</a> | ";

*/		
		$html = '
<div class="top_banner">
	<ul>
		<li><a href="import.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage(\'import\',\'\',\'skin/default/images/import.gif\',1)"><img src="skin/default/images/import_sml.gif" alt="import" name="import" width="71" height="126" border="0" id="import" /></a></li>
		<li><a href="export.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage(\'export\',\'\',\'skin/default/images/export.gif\',1)"><img src="skin/default/images/export_sml.gif" alt="export" name="export" width="71" height="126" border="0" id="export" /></a></li>
		<li><a href="surveyresult.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage(\'statisic\',\'\',\'skin/default/images/statisic.gif\',1)"><img src="skin/default/images/statisic_sml.gif" alt="statisic" name="statisic" width="71" height="126" border="0" id="statisic" /></a></li>
		<li><a href="account.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage(\'extension\',\'\',\'skin/default/images/extension.gif\',1)"><img src="skin/default/images/extension_sml.gif" alt="extension" name="extension" width="71" height="126" border="0" id="extension" /></a></li>
		<li><a href="customer.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage(\'customer\',\'\',\'skin/default/images/customer.gif\',1)"><img src="skin/default/images/customer_sml.gif" alt="customer" name="customer" width="71" height="126" border="0" id="customer" /></a></li>
		<li><a href="predictivedialer.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage(\'dialer\',\'\',\'skin/default/images/dialer.gif\',1)"><img src="skin/default/images/dialer_sml.gif" alt="dialer" name="dialer" width="71" height="126" border="0" id="dialer" /></a></li>
		<li><a href="systemstatus.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage(\'system\',\'\',\'skin/default/images/system.gif\',1)"><img src="skin/default/images/system_sml.gif" alt="system" name="system" width="71" height="126" border="0" id="system" /></a></li>
		<li><a href="survey.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage(\'survey\',\'\',\'skin/default/images/survey.gif\',1)"><img src="skin/default/images/survey_sml.gif" alt="survey" name="survey" width="71" height="126" border="0" id="survey" /></a></li>
		<li><a href="portal.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage(\'back\',\'\',\'skin/default/images/back.gif\',1)"><img src="skin/default/images/back_sml.gif" alt="back" name="back" width="71" height="126" border="0" id="back" /></a></li>
		<li><a href="login.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage(\'logout\',\'\',\'skin/default/images/logout.gif\',1)"><img src="skin/default/images/logout_sml.gif" alt="logout" name="logout" width="71" height="126" border="0" id="logout" /></a></li>
		<li><img src="skin/default/images/logo_bn.gif"/></li>
	</ul>
</div>
				';
$html .= '

<SCRIPT country="JavaScript">
<!--
	MM_preloadImages(\'skin/default/images/import.gif\',\'skin/default/images/export.gif\',\'skin/default/images/statisic.gif\',\'skin/default/images/extension.gif\',\'skin/default/images/customer.gif\',\'skin/default/images/dialer.gif\',\'skin/default/images/system.gif\',\'skin/default/images/survey.gif\',\'skin/default/images/back.gif\',\'skin/default/images/logout.gif\'
//-->
</SCRIPT>
		';
		return $html;
	}

	function generateTabelHtml($aDyadicArray,$thArray = null){
		if (!is_Array($aDyadicArray))
			return '';
		$html .= "<table class='myTable'>";
		$myArray = array_shift($aDyadicArray);
		foreach ($myArray as $field){
			$html .= "<th>";
			$html .= $field;
			$html .= "</th>";
		}

		foreach ($aDyadicArray as $myArray){
			$html .="<tr>";
			foreach ($myArray as $field){
				$html .= "<td>";
				$html .= $field;
				$html .= "</td>";
			}
			$html .="</tr>";
		}
		$html .= "</table>";
		return $html;
	}
}
?>
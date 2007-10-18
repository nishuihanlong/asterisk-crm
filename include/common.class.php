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
	$GLOBALS['locate_common']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'common.class');
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
							version: 0.045 beta
						</td>
					</tr>
					</table>
				</dvi>
				';
		return $html;
	}

	function generateManageNav($skin){
		global $locate_common;

		$html .= "<a href='account.php'>".$locate_common->Translate("extension_manager")."</a> | ";

		$html .= "<a href='systemstatus.php'>".$locate_common->Translate("system_status")."</a> | ";

		$html .= "<a href='predictivedialer.php'>".$locate_common->Translate("predictive_dialer")."</a> | ";

		$html .= "<a href='customer.php' >".$locate_common->Translate("customer_manager")."</a> | ";
	
		$html .= "<a href='contact.php' >".$locate_common->Translate("contact_manager")."</a> | ";
		
		$html .= "<a href='note.php' >".$locate_common->Translate("note_manager")."</a> | ";
		
		$html .= "<a href='diallist.php' >".$locate_common->Translate("diallist_manager")."</a> | ";
		
		$html .= "<a href='survey.php' >".$locate_common->Translate("survey_manager")."</a> | ";
		
		$html .= "<a href='surveyresult.php' >".$locate_common->Translate("survey_reslut")."</a> | ";

		$html .= "<a href=# onclick=\"self.location.href='portal.php';return false;\">".$locate_common->Translate("back")."</a> | ";

		$html .= "<a href=# onclick=\"self.location.href='login.php';return false;\">".$locate_common->Translate("log_out")."</a>";
		
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
<?php
require_once ("login.common.php");
require_once ("db_connect.php");

function processForm($aFormValues)
{
	if (array_key_exists("username",$aFormValues))
	{
		return processAccountData($aFormValues);
	}
}

function init($aFormValue){
	global $locate;
	list($_SESSION['curuser']['country'],$_SESSION['curuser']['language']) = split ("_", $aFormValue['locate']);

	$locate=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'login');

	$objResponse = new xajaxResponse();
//	global $locate;

	$objResponse->addAssign("titleDiv","innerHTML",$locate->Translate("title"));
	$objResponse->addAssign("usernameDiv","innerHTML",$locate->Translate("username"));
	$objResponse->addAssign("passwordDiv","innerHTML",$locate->Translate("password"));
	$objResponse->addAssign("loginButton","value",$locate->Translate("submit"));
	$objResponse->addAssign("onclickMsg","value",$locate->Translate("please_waiting"));
	$objResponse->addScript("xajax.$('username').focus();");
	$_SESSION['curuser']['username'] = '';
	$_SESSION['curuser']['extension'] = '';


	return $objResponse;
}

function processAccountData($aFormValues)
{
	global $db,$locate;

	$objResponse = new xajaxResponse();
	
	$bError = false;
	
	if (trim($aFormValues['username']) == "")
	{
		$objResponse->addAlert($locate->Translate("username_cannot_be_blank"));
		$bError = true;
	}
	if (trim($aFormValues['password']) == "")
	{
		$objResponse->addAlert($locate->Translate("password_cannot_be_blank"));
		$bError = true;
	}

	$loginError = false;

	if (!$bError)
	{
		$query = "SELECT * FROM account WHERE username='" . $aFormValues['username'] . "'";
		$res = $db->query($query);

		if ($res->fetchInto($list)){
			if ($list['password'] == $aFormValues['password'])
			{
				$_SESSION = array();
				$_SESSION['curuser']['username'] = trim($aFormValues['username']);
				$_SESSION['curuser']['extension'] = $list['extension'];
				list($_SESSION['curuser']['country'],$_SESSION['curuser']['language']) = split ("_", $aFormValues['locate']);
				//$objResponse->addAlert($_SESSION['curuser']['country']);
				//$objResponse->addAlert($_SESSION['curuser']['language']);
				$objResponse->addAlert($locate->Translate("login_success"));
				$objResponse->addScript("location.href='portal.php';");

			} else{
				$loginError = true;
			}
		} else{
				$loginError = true;
		}


		if (!$loginError){
			return $objResponse;
		} else {
			$objResponse->addAlert($locate->Translate("login_failed"));
			$objResponse->addAssign("loginButton","value",$locate->Translate("submit"));
			$objResponse->addAssign("loginButton","disabled",false);
			return $objResponse;
		}
	} else {
		$objResponse->addAssign("loginButton","value",$locate->Translate("submit"));
		$objResponse->addAssign("loginButton","disabled",false);
	}
	
	return $objResponse;
}


$xajax->processRequests();
?>